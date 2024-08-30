<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Focus;

class FocusController extends Controller
{
    public function index()
    {
        $focuses = Focus::all();
        return response()->json($focuses);
    }
}
