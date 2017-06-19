<?php

namespace Modules\FormsPlugin\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\FormsPlugin\Models\Configuration;
use Modules\FormsPlugin\Models\Response;

class FormMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var string
     */
    private $previousUrl;


    /**
     * Create a new message instance.
     *
     * @param Response $response
     * @param string|null $previousUrl
     */
    public function __construct(Response $response, $previousUrl = null)
    {
        $this->response = $response;
        $this->previousUrl = $previousUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('module-formsplugin::email.default')
            ->with([
                'values' => $this->response->getNamedValues(),
                'previousUrl' => $this->previousUrl
            ]);

        foreach($this->response->getFiles() as $file){
            $mail->attach($file->file, [ 'as' => $file->name ]);
        }

        return $mail;
    }
}
