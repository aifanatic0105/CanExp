<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;
use App\Traits\RateLimitedMailable;

class CommentResponseMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, RateLimitedMailable;

    public $emailDelay = 3; // seconds
    
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->applyRateLimit();
    }

    public function build()
    {
        $subject = 'Thank you for your comments / suggestions';
        $service = app(EmailTemplateService::class);
        $rendered = $service->render('comments_response', ['data' => $this->data], $subject, null);

        if (!empty($rendered['body_html'])) {
            return $this->markdown('mails.dynamic-markdown')
                ->subject($rendered['subject'] ?: $subject)
                ->with([
                    'body_html' => $rendered['body_html'],
                    'data' => ['data' => $this->data],
                ]);
        }

        return $this->markdown('mails.comments-response')
            ->subject($subject)
            ->with('data', $this->data);
    }
}
