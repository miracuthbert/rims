<?php

namespace Rims\Http\Admin\Controllers;

use Illuminate\Http\Request;
use Rims\App\Controllers\Controller;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard view.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.dashboard.index');
    }
}
