<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductionTData1;
use App\Models\ProductionTData2;
use App\Models\ProductionTData3;
use App\Models\ProductionTData4;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(Request $request, $kode)
    {
        $TData1 = ProductionTData1::count();
        $TData2 = ProductionTData2::count();
        $TData3 = ProductionTData3::count();
        $TData4 = ProductionTData4::count();

        $outstandingReservasi = ProductionTData4::whereColumn('KALAB', '<', 'BDMNG')->count();
    
        return view('Admin.dashboard', compact('TData1', 'TData2', 'TData3', 'outstandingReservasi'));
    }


    // /**
    //  * Show orders list page.
    //  */
    // public function orders()
    // {
    //     // Add logic for orders listing
    //     return view('admin.orders.index');
    // }

    // /**
    //  * Show users management page.
    //  */
    // public function users()
    // {
    //     // Add logic for user management
    //     return view('admin.users.index');
    // }

    // /**
    //  * Show reports page.
    //  */
    // public function reports()
    // {
    //     // Add logic for reports
    //     return view('');
    // }
}