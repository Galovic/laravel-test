<?php

namespace Modules\NewsletterPlugin\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\NewsletterPlugin\Http\Requests\NewsletterRequest;
use Modules\NewsletterPlugin\Models\Newsletter;

class NewsletterController extends Controller
{
    /**
     * Submit newsletter form.
     *
     * @param NewsletterRequest $request
     * @return mixed
     */
    public function newsletterSubmit(NewsletterRequest $request){

        $trashedExisting = Newsletter::onlyTrashed()->where('email', $request->email)->first();

        if ($trashedExisting) {
            $trashedExisting->restore();
        } else {
            Newsletter::create($request->getValues());
        }

        Newsletter::flashSuccess();

        return redirect()->back();
    }
}
