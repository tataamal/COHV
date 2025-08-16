<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KorlapController extends Controller
{
    public function index()
    {
        // Di sini Anda bisa mengambil data yang spesifik untuk korlap yang sedang login
        // Contoh: $user = Auth::user();
        //         $myTasks = Task::where('assigned_to', $user->id)->get();

        // Kemudian kirim data tersebut ke view
        return view('Korlap.dashboard'); // Pastikan view ada di resources/views/korlap/dashboard.blade.php
    }
}
