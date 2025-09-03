<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Note;
class NoteController extends Controller
{
    /**
     * Mengambil semua catatan yang ditujukan untuk user yang sedang login.
     */
    public function index()
    {
        $userId = Auth::id();
        $notes = Note::where('recipient_id', $userId)
                     ->with('sender_id,nama') // Mengambil ID dan nama pengirim
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
            'recipient_id' => 'required|exists:sap_users,id',
            'message' => 'required|string|max:255',
        ]);

        $note = Note::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'message' => $request->message,
            'completed' => 0, // Default status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil disimpan.',
            'data' => $note
        ], 201); // 201 Created
    }

    /**
     * Menandai catatan sebagai selesai.
     */
    public function markAsComplete(Note $note)
    {
        // Otorisasi: Hanya penerima yang boleh menyelesaikan catatan
        if ($note->recipient_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan.'], 403); // 403 Forbidden
        }

        $note->completed = 1;
        $note->save();

        return response()->json([
            'success' => true,
            'message' => 'Catatan ditandai selesai.'
        ]);
    }
}
