<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Services\EmailTemplateService;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\RateLimitedMailable;

class FinancingProgramMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, RateLimitedMailable;

    public $emailDelay = 3; // seconds
    
    private $data = [];
    private $businessCategories = [];

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct(array $data, $businessCategories)
    {
        $this->data = $data;
        $this->businessCategories = $businessCategories;
        $this->applyRateLimit();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Inquiry for Financing Programs on Canadian Exports Website';
        $service = app(EmailTemplateService::class);
        $payload = [
            'data' => $this->data,
            'businessCategories' => $this->businessCategories,
        ];
        $rendered = $service->render('financing_program', $payload, $subject, null);

        if (!empty($rendered['body_html'])) {
            return $this->markdown('mails.dynamic-markdown')
                ->subject($rendered['subject'] ?: $subject)
                ->with([
                    'body_html' => $rendered['body_html'],
                    'data' => $payload,
                ]);
        }

        return $this->markdown('mails/financing-program')
            ->subject($subject)
            ->with($payload);
    }
}
