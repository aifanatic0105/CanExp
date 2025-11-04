<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Lottery;
use Illuminate\Queue\Middleware\RateLimited;
use App\Traits\RateLimitedMailable;

class CustomerVerifyEmailMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, RateLimitedMailable;
    
    public $emailDelay = 10; // seconds
    
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
    //     return $this->markdown('mails/customer-verify-email')
    //         ->subject("Verify your email address. Complete your registration")
    //         ->with("data", $this->data);
    // }
    public function build()
    {
        $service = app(EmailTemplateService::class);
        $rendered = $service->render('customer_verify_email', $this->data, 'Verify your email address. Complete your registration', null);

        if (!empty($rendered['body_html'])) {
            return $this->markdown('mails.dynamic-markdown')
                ->subject($rendered['subject'] ?: 'Verify your email address. Complete your registration')
                ->with([
                    'body_html' => $rendered['body_html'],
                    'data' => $this->data,
                ]);
        }

        return $this->markdown('mails/customer-verify-email')
            ->subject("Verify your email address. Complete your registration")
            ->with("data", $this->data);
    }
}
