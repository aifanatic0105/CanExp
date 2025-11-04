<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\RateLimitedMailable;

class CloseAccountMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, RateLimitedMailable;

    public $emailDelay = 3; // seconds
    
    private $data = [];

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->applyRateLimit();
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
{
    $subject = 'Farewell from Canadian Exports';
    $service = app(EmailTemplateService::class);
    $rendered = $service->render('close_account', ['data' => $this->data], $subject, null);

    if (!empty($rendered['body_html'])) {
        return $this->markdown('mails.dynamic-markdown')
            ->subject($rendered['subject'] ?: $subject)
            ->with([
                'body_html' => $rendered['body_html'],
                'data' => ['data' => $this->data],
            ]);
    }

    return $this->markdown('mails.close-account')
        ->subject($subject)
        ->with('data', $this->data);
}

}
