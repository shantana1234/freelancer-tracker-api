<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoggedEightHours extends Notification
{
    protected $date;
    protected $totalHours;

    public function __construct($date, $totalHours)
    {
        $this->date = $date;
        $this->totalHours = $totalHours;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You’ve Logged 8+ Hours Today')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line("You've logged a total of **{$this->totalHours} hours** on **{$this->date}**.")
            ->line('Remember to take breaks and take care of yourself!')
            ->salutation('— Freelance Tracker');
    }
}
