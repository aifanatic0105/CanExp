<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\RateLimitedMailable;

class InfoLetterMail extends Mailable implements ShouldQueue
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
        $subject = 'Welcome to Canadian Exports';
        $service = app(EmailTemplateService::class);
        $rendered = $service->render('info_letter', ['data' => $this->data], $subject, null);

        if (!empty($rendered['body_html'])) {
            return $this->markdown('mails.dynamic-markdown')
                ->subject($rendered['subject'] ?: $subject)
                ->with([
                    'body_html' => $rendered['body_html'],
                    'data' => ['data' => $this->data],
                ]);
        }

        return $this->markdown('mails/info-letter')
            ->subject($subject)
            ->with('data', $this->data);
    }
}
