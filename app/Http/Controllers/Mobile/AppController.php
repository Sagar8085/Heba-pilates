<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;

class AppController extends Controller
{
    public function ondemandEnabled()
    {
        return response()->json([
            'enabled' => false,
        ]);

        if (request('show') === 'yes') {
            return response()->json([
                'enabled' => true,
            ]);
        }

        if (request('show') === 'no') {
            return response()->json([
                'enabled' => false,
            ]);
        }

        return response()->json([
            'enabled' => true,
        ]);
    }
}
