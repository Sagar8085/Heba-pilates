<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\CreditPackResource;
use App\Models\CreditPack;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CreditPackController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        return CreditPackResource::collection(CreditPack::all());
    }
}
