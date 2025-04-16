<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentAssigned extends Notification
{
    use Queueable;

    public function __construct(public $company){}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have been assigned to a new company')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have been assigned as the registered agent for the company: ' . $this->company->name)
            ->line('State: ' . $this->company->state);
    }

}
