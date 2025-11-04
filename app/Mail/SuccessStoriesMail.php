<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;
use App\Traits\RateLimitedMailable;

class SuccessStoriesMail extends Mailable implements ShouldQueue
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
        $subject = 'A new Success stories submitted on Canadian Exports  ';
        $service = app(EmailTemplateService::class);
        $rendered = $service->render('success_stories', ['data' => $this->data], $subject, null);

        if (!empty($rendered['body_html'])) {
            return $this->markdown('mails.dynamic-markdown')
                ->subject($rendered['subject'] ?: $subject)
                ->with([
                    'body_html' => $rendered['body_html'],
                    'data' => ['data' => $this->data],
                ]);
        }

        return $this->markdown('mails/success-stories')
            ->subject($subject)
            ->with('data', $this->data);
    }
}
