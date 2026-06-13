<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemAlert extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $type;
    protected $url;

    public function __construct(string $title, string $message, string $type = 'info', ?string $url = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->url = $url;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'url' => $this->url,
            'icon' => $this->getIcon(),
        ];
    }

    protected function getIcon(): string
    {
        return match($this->type) {
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-triangle',
            'danger' => 'fa-exclamation-circle',
            'info' => 'fa-info-circle',
            'vacation' => 'fa-umbrella-beach',
            'attendance' => 'fa-clock',
            default => 'fa-bell',
        };
    }
}
