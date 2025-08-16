<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        // Sample data - replace with your actual data queries
        $dashboardData = [
            'total_orders' => 3,
            'completed_orders' => 1,
            'in_progress_orders' => 1,
            'pending_orders' => 1,
            'chart_data' => [
                'labels' => ['Order 1002345', 'Order 1002346', 'Order 1002347'],
                'target_quantities' => [150, 200, 50],
                'confirmed_quantities' => [150, 120, 0],
            ],
            'recent_orders' => [
                [
                    'order_id' => '1002345',
                    'material' => 'RM-00123',
                    'description' => 'Rangka Kayu Jati Utama',
                    'target_qty' => 150.000,
                    'confirmed_qty' => 150.000,
                    'remaining_qty' => 0.000,
                    'status' => 'completed'
                ],
                [
                    'order_id' => '1002346',
                    'material' => 'SF-00567',
                    'description' => 'Panel Pintu Finishing',
                    'target_qty' => 200.000,
                    'confirmed_qty' => 120.000,
                    'remaining_qty' => 80.000,
                    'status' => 'in_progress'
                ],
                [
                    'order_id' => '1002347',
                    'material' => 'FG-00890',
                    'description' => 'Kursi Rakit Siap Jual',
                    'target_qty' => 50.000,
                    'confirmed_qty' => 0.000,
                    'remaining_qty' => 50.000,
                    'status' => 'pending'
                ]
            ]
        ];
        
        return view('Admin.dashboard', compact('dashboardData'));
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