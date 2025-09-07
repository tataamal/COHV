<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    /**
     * Menampilkan semua catatan yang relevan untuk user yang sedang login.
     * (Catatan yang dikirim olehnya atau ditujukan kepadanya)
     */
    public function index()
    {
        $userId = Auth::id();
        $notes = Note::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->with(['sender', 'recipient']) // Eager load untuk efisiensi
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notes);
    }

    /**
     * Menyimpan catatan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $note = Note::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'message' => $request->message,
            'status' => 'Baru', // Status default saat dibuat
        ]);
        
        // Memuat relasi agar bisa dikirim kembali ke frontend
        $note->load(['sender', 'recipient']);

        return response()->json($note, 201);
    }

    /**
     * [MODIFIKASI] Mengubah fungsi update status menjadi lebih fleksibel.
     * Fungsi ini akan mengubah status ke tahap berikutnya.
     */
    public function updateStatus(Note $note)
    {
        // Hanya penerima atau pengirim yang bisa mengubah status
        if (Auth::id() != $note->recipient_id && Auth::id() != $note->sender_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Logika untuk mengubah status secara berurutan
        switch ($note->status) {
            case 'Baru':
                $note->status = 'Dikerjakan';
                break;
            case 'Dikerjakan':
                $note->status = 'Selesai';
                break;
            case 'Selesai':
                // Opsional: bisa dikembalikan ke 'Baru' atau tetap 'Selesai'
                $note->status = 'Baru'; 
                break;
            default:
                $note->status = 'Baru';
                break;
        }

        $note->save();
        $note->load(['sender', 'recipient']);

        return response()->json($note);
    }
}
