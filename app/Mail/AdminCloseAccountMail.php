<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;
use App\Traits\RateLimitedMailable;

class AdminCloseAccountMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, RateLimitedMailable;

    public $emailDelay = 3; // seconds
    
    public $emailData;

    public function __construct($emailData)
    {
        $this->emailData = $emailData;
        $this->applyRateLimit();
    }

    public function build()
    {
        $subject = 'A member has closed their account on Canadian Exports';
        $service = app(EmailTemplateService::class);
        $rendered = $service->render('admin_close_account', ['emailData' => $this->emailData], $subject, null);

        if (!empty($rendered['body_html'])) {
            return $this->markdown('mails.dynamic-markdown')
                ->subject($rendered['subject'] ?: $subject)
                ->with([
                    'body_html' => $rendered['body_html'],
                    'data' => ['emailData' => $this->emailData],
                ]);
        }

        return $this->markdown('mails.admin_close_account')
            ->subject($subject)
            ->with('emailData', $this->emailData);
    }
}
