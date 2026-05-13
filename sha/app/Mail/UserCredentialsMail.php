<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userId;
    public $password;
    public $name;
    public $role;

    /**
     * Create a new message instance.
     */
    public function __construct($userId, $password, $name, $role)
    {
        $this->userId = $userId;
        $this->password = $password;
        $this->name = $name;
        $this->role = $role;
    }

    public function build()
    {
        return $this->subject($this->role. ' Credentials Details')
                    ->view('emails.user_credentials');
    }
}
