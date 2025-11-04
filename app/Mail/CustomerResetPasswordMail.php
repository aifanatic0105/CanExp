<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\RateLimitedMailable;

class CustomerResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, RateLimitedMailable;
    
    public $emailDelay = 3; // seconds
    
    private $data = [];
    /**
     * Create a new message instance.
     *
     * @return void
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
    // public function build()
    // {
    //     return $this->markdown('mails/customer-reset-password')
    //         ->subject('Reset your password')
    //         ->with("data", $this->data);
    // }
    public function build()
{
    $subject = 'Reset your password';
    $service = app(EmailTemplateService::class);
    $rendered = $service->render('customer_reset_password', ['data' => $this->data], $subject, null);

    if (!empty($rendered['body_html'])) {
        return $this->markdown('mails.dynamic-markdown')
            ->subject($rendered['subject'] ?: $subject)
            ->with([
                'body_html' => $rendered['body_html'],
                'data' => ['data' => $this->data],
            ]);
    }

    return $this->markdown('mails.customer-reset-password')
        ->subject($subject)
        ->with("data", $this->data);
}
}
