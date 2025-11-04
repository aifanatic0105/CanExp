<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;
use App\Traits\HasUnsubscribeLink;
use App\Traits\RateLimitedMailable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, HasUnsubscribeLink, RateLimitedMailable;
    private $data = [];
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        
        // Apply rate limiting delay (10 seconds)
        // This prevents "Too many emails per second" errors when sent with other emails
        $this->applyRateLimit(10);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Welcome to Canadian Exports";
        $service = app(EmailTemplateService::class);
        $rendered = $service->render('customer_welcome', ['data' => $this->data], $subject, null);

        // Generate unsubscribe link for customer email
        $recipientEmail = $this->data['email'] ?? null;
        if ($recipientEmail) {
            $this->withUnsubscribeLink($recipientEmail, 'customer');
        }

        if (!empty($rendered['body_html'])) {
            return $this->markdown('mails.dynamic-markdown')
                ->subject($rendered['subject'] ?: $subject)
                ->with([
                    'body_html' => $rendered['body_html'],
                    'data' => ['data' => $this->data],
                    'unsubscribeLink' => $this->unsubscribeLink,
                ]);
        }

        return $this->markdown('mails/customer-welcome')
            ->subject($subject)
            ->with([
                "data" => $this->data,
                "unsubscribeLink" => $this->unsubscribeLink,
            ]);
    }
}
