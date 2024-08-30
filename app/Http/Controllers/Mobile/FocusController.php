<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Focus;

class FocusController extends Controller
{
    public function all()
    {
        $focuses = Focus::all();

        return response()->json($focuses);
    }
}
