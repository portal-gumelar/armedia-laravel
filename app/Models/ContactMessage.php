<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subject',
        'message',
    ];

    protected static function booted()
    {
        static::created(function ($message) {
            \App\Services\TelegramService::sendMessage(
                "💬 <b>PESAN BARU DARI WEBSITE</b>\n\n" .
                "<b>Dari:</b> {$message->name}\n" .
                "<b>Email:</b> {$message->email}\n" .
                "<b>Subjek:</b> {$message->subject}\n" .
                "<b>Pesan:</b> {$message->message}"
            );
        });
    }
}
