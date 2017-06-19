<?php

namespace Modules\NewsletterPlugin\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Modules\NewsletterPlugin\Models\Newsletter;

class NewsletterController extends AdminController
{
    /**
     * NewsletterController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:module_newsletterplugin-show')->only(['index', 'export']);
        $this->middleware('permission:module_newsletterplugin-edit')->only(['showEditForm', 'update']);
        $this->middleware('permission:module_newsletterplugin-delete')->only('delete');
    }


    /**
     * List of forms
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $this->setTitleDescription("Newsletter", "přehled adres");

        $newsletters = Newsletter::all();

        return view('module-newsletterplugin::admin.index', compact('newsletters'));
    }


    /**
     * Show form for newsletter address edit.
     *
     * @param  Newsletter $newsletter
     * @return \Illuminate\View\View
     */
    public function showEditForm(Newsletter $newsletter)
    {
        return view('module-newsletterplugin::admin.edit', compact('newsletter'));
    }


    /**
     * Update newsletter address.
     *
     * @param  Newsletter $newsletter
     * @return \Illuminate\View\View
     */
    public function update(Newsletter $newsletter, Request $request)
    {
        $this->validate($request, [
            'email' => [
                'required',
                Rule::unique(Newsletter::getTableName(), 'email')
                    ->whereNot('id', $newsletter->id)
            ]
        ], [
            'email.required' => 'Zadejte prosím emailovou adresu.',
            'email.unique' => 'Tato emailová adresa je již přihlášena.'
        ]);

        $newsletter->update($request->all());

        flash('Adresa byla úspěšně upravena.', 'success');
        return redirect()->route('admin.module.newsletter_plugin');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  Newsletter $newsletter
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function delete(Newsletter $newsletter)
    {
        $newsletter->delete();

        flash('Adresa byla úspěšně odstraněna!', 'success');
        return $this->refresh();
    }


    /**
     * Usage example.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function example() {
        $this->setTitleDescription("Newsletter", "ukázka použití");

        return view('module-newsletterplugin::admin.example');
    }


    /**
     * Export newsletter addresses.
     */
    public function export() {
        $data = Newsletter::all('email', 'created_at');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['email', 'subscribed_at'], ';');

        foreach($data as $line)
        {
            fputcsv($out, [$line->email, $line->created_at->toDateTimeString()], ';');
        }
        fclose($out);

        header('Content-Disposition: attachment; filename="newsletter.csv"');
        header("Cache-control: private");
        header("Content-type: application/force-download");
        header("Content-transfer-encoding: binary\n");

        exit;
    }
}
