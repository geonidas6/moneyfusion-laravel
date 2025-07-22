<?php

namespace Vendor\MoneyFusion\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Vendor\MoneyFusion\Models\MoneyFusionTransaction;

class PaymentStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public MoneyFusionTransaction $transaction;
    public string $statusType; // 'success' or 'failure'

    /**
     * Create a new message instance.
     *
     * @param MoneyFusionTransaction $transaction
     * @param string $statusType
     */
    public function __construct(MoneyFusionTransaction $transaction, string $statusType)
    {
        $this->transaction = $transaction;
        $this->statusType = $statusType;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        $subject = ($this->statusType === 'success')
            ? __('moneyfusion.email_subject_success', ['id' => $this->transaction->token_pay])
            : __('moneyfusion.email_subject_failure', ['id' => $this->transaction->token_pay]);

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'moneyfusion::emails.payment-status',
            with: [
                'transaction' => $this->transaction,
                'statusType' => $this->statusType,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        return [];
    }
}