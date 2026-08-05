<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tps;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tps = Tps::count();

        return view('admin.dashboard', [
            'title' => 'Dashboard Admin',
            'active' => 'dashboard',
            'tps' => $tps,
            'user' => Auth::user(),
        ]);
    }
}
