<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrainerSpecialisation;

class TrainerSpecialisationController extends Controller
{
    // Get all Trainer Specialisations
    public function all()
    {
        return response()->json(TrainerSpecialisation::all());
    }
}
