<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntensityMET;

class IntensityMETController extends Controller
{
    public function index()
    {
        $focuses = IntensityMET::all();
        return response()->json($focuses);
    }
}
