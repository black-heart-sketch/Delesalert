<?php

namespace App\Notifications;

use App\Models\Outage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OutageStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public Outage $outage) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__($this->titleKey()))
            ->line(__($this->messageKey(), ['zone' => $this->outage->zone->name]))
            ->action(__('View outage'), route('outages.index', ['status' => $this->outage->status]));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'outage_id' => $this->outage->id,
            'status' => $this->outage->status,
            'zone' => $this->outage->zone->name,
            'title_key' => $this->titleKey(),
            'message_key' => $this->messageKey(),
        ];
    }

    private function titleKey(): string
    {
        return match ($this->outage->status) {
            'PLANNED' => 'Scheduled outage announced',
            'ONGOING' => 'Outage in progress',
            'RESOLVED' => 'Power restored',
            default => 'Outage update',
        };
    }

    private function messageKey(): string
    {
        return match ($this->outage->status) {
            'PLANNED' => 'A scheduled outage has been announced for :zone.',
            'ONGOING' => 'An electricity outage is affecting :zone.',
            'RESOLVED' => 'Electricity has been restored in :zone.',
            default => 'The outage information for :zone has been updated.',
        };
    }
}
