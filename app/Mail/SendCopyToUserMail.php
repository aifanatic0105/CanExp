<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;
use App\Traits\RateLimitedMailable;

class SendCopyToUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, RateLimitedMailable;

    public $emailDelay = 3; // seconds
    
    public $customerProfile;
    public $data;

    public function __construct($customerProfile, $data)
    {
        $this->customerProfile = $customerProfile;
        $this->data = $data;
        $this->applyRateLimit();
    }

    public function build()
    {
            $subject = 'Your message has been delivered';
            $service = app(EmailTemplateService::class);
            $payload = [
                'advertiserName' => $this->customerProfile->company_name,
                'messageContent' => $this->data['message'],
            ];
            $rendered = $service->render('send_copy_to_user', $payload, $subject, null);

            if (!empty($rendered['body_html'])) {
                return $this->markdown('mails.dynamic-markdown')
                    ->subject($rendered['subject'] ?: $subject)
                    ->with([
                        'body_html' => $rendered['body_html'],
                        'data' => $payload,
                    ]);
            }

            return $this->markdown('mails/sendCopyToUser')
                ->subject($subject)
                ->with($payload);
    }
}
