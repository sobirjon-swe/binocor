<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\User;
use App\Notifications\OverduePaymentNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class CheckOverduePayments extends Command
{
    protected $signature = 'payments:check-overdue';

    protected $description = 'Muddati o\'tgan to\'lovlarni "overdue" holatiga o\'tkazadi va mas\'ul xodimlarga bildirishnoma yuboradi';

    public function handle(): int
    {
        $overduePayments = Payment::with('contract.customer', 'contract.property.project')
            ->where('status', 'pending')
            ->whereDate('due_date', '<', today())
            ->get();

        if ($overduePayments->isEmpty()) {
            $this->info('Kechikkan to\'lovlar topilmadi.');

            return self::SUCCESS;
        }

        $recipients = User::role(['admin', 'manager', 'accountant'])->get();

        foreach ($overduePayments as $payment) {
            $payment->update(['status' => 'overdue']);

            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new OverduePaymentNotification($payment));
            }
        }

        $this->info("{$overduePayments->count()} ta to'lov kechikkan deb belgilandi va bildirishnoma yuborildi.");

        return self::SUCCESS;
    }
}
