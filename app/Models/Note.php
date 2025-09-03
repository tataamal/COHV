<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Note extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     * Sesuaikan jika nama tabel Anda berbeda.
     */
    protected $table = 'notes';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'sender_id',
        'recipient_id',
        'message',
        'completed',
    ];

    /**
     * Relasi untuk mendapatkan data pengirim (sender).
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relasi untuk mendapatkan data penerima (recipient).
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
