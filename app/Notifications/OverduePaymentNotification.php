<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverduePaymentNotification extends Notification
{
    use Queueable;

    public function __construct(protected Payment $payment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $payment = $this->payment;
        $contract = $payment->contract;

        return (new MailMessage)
            ->subject('Kechikkan to\'lov: '.$contract->customer->full_name)
            ->greeting('Assalomu alaykum, '.$notifiable->name)
            ->line('Quyidagi to\'lov muddati o\'tib ketdi:')
            ->line('Mijoz: '.$contract->customer->full_name)
            ->line('Obyekt: '.$contract->property->type.' — '.$contract->property->project->name)
            ->line('Summa: '.number_format($payment->amount, 0, '.', ' ').' so\'m')
            ->line('Muddat: '.$payment->due_date->format('Y-m-d'))
            ->action('To\'lovni ko\'rish', route('payments.edit', $payment))
            ->line('Iltimos, mijoz bilan bog\'lanib, to\'lovni kuzatib boring.');
    }

    public function toArray(object $notifiable): array
    {
        $payment = $this->payment;
        $contract = $payment->contract;

        return [
            'payment_id' => $payment->id,
            'contract_id' => $contract->id,
            'customer_name' => $contract->customer->full_name,
            'amount' => $payment->amount,
            'due_date' => $payment->due_date->format('Y-m-d'),
        ];
    }
}
