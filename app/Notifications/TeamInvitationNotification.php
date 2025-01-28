<?php

namespace App\Notifications;

use App\Models\TeamInvitation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitationNotification extends Notification
{
    public function __construct(public TeamInvitation $invitation)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('You have been invited to join ' . $this->invitation->team->name)
            ->action('Accept Invitation', route('team-invitations.accept', $this->invitation))
            ->line('This invitation will expire in 7 days.');
    }
}
