<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PendingRegistrationVerification extends Notification
{
    use Queueable;

    protected $token;
    protected $name;
    protected $isCoach;

    public function __construct($token, $name, $isCoach = false)
    {
        $this->token = $token;
        $this->name = $name;
        $this->isCoach = $isCoach;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationUrl = url('/verify-registration/' . $this->token);
        $expiryHours = 48; // 48 hours expiry

        return (new MailMessage)
            ->subject('Complete Your Sports Camp Registration')
            ->greeting('Hello ' . $this->name . '!')
            ->line('Thank you for registering for Falcon Teams. You\'re almost done!')
            ->line('Please click the button below to verify your email address and complete your registration.')
            ->action('Complete Registration', $verificationUrl)
            ->line('This link will expire in ' . $expiryHours . ' hours.')
            ->line('After verification, you\'ll be able to log in and ' .
                ($this->isCoach ? 'access your coach dashboard.' : 'register your children for camps.'))
            ->line('If you did not create an account, no further action is required.');
    }
}
