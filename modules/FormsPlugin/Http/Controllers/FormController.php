<?php

namespace Modules\FormsPlugin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Modules\FormsPlugin\Emails\FormMail;
use Modules\FormsPlugin\Models\Configuration;
use Modules\FormsPlugin\Models\Field;
use Modules\FormsPlugin\Models\Form;
use Modules\FormsPlugin\Models\Response;

class FormController extends Controller
{
    /**
     * On form submit (general)
     *
     * @param Form $form
     * @param Request $request
     * @return mixed
     */
    public function formSubmit(Form $form, Request $request){

        if (!$request->exists('email_honeypot') || $request->input('email_honeypot')) {
            return redirect()->back();
        }

        $inputs = $request->input('input');
        if(!$inputs) return 'no data';

        reset($inputs);
        $fieldId = key($inputs);

        $language = Field::findOrFail($fieldId)->language;
        $form->setLanguage($language);

        $validator = \Validator::make(
            $request->all(),
            $form->getValidationRules($language)
        );

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }

        $inputs = $request->input('input');

        foreach($form->fields()->where('type', 'file')->get() as $fileField){
            if(!$request->hasFile($fileField->error_key)) continue;

            $file = $request->file($fileField->error_key);

            $inputs[$fileField->id] = [
                'name' => $file->getClientOriginalName(),
                'file' => $fileField->file_name
            ];

            $file->move($fileField->storage_path, $fileField->file_name);
        }

        /** @var Response $response */
        $response = $form->responses()->create([
            'values' => $inputs
        ]);

        if($form->send_to_email) {
            $mail = new FormMail($response, \URL::previous());
            $mail->subject($form->name);

            \Mail::to($form->send_to_email)
                ->queue($mail);
        }

        if($form->localised->success_message) {
            \Session::flash($form->successMessageId, $form->localised->success_message);
        }

        return redirect()->back();
    }
}
