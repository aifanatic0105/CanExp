<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\EmailTemplateService;
use App\Traits\RateLimitedMailable;

class ContactCompanyMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, RateLimitedMailable;
    
    public $emailDelay = 3; // seconds
    
    private $data = [];
    private $company_name = '';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data, String $company_name)
    {
        $this->data = $data;
        // $this->data['company_name'] = $company_name;
        $this->company_name = $company_name;
        $this->applyRateLimit();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Response to your "' . $this->company_name . '" profile on Canadian Exports';
        $service = app(EmailTemplateService::class);
        $payload = ["data" => $this->data, "company_name" => $this->company_name];
        $rendered = $service->render('contact_company', $payload, $subject, null);

        if (!empty($rendered['body_html'])) {
            return $this->markdown('mails.dynamic-markdown')
                ->subject($rendered['subject'] ?: $subject)
                ->with([
                    'body_html' => $rendered['body_html'],
                    'data' => $payload,
                ]);
        }

        return $this->markdown('mails.contact-company')
            ->subject($subject)
            ->with($payload);
    }
}
