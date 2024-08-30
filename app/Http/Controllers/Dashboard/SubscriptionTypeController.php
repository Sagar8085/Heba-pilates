<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class SubscriptionTypeController
 *
 * @package App\Http\Controllers\Dashboard
 */
class SubscriptionTypeController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            collect(Subscription::ALL)
                ->map(fn (string $slug) => [
                    'slug' => $slug,
                    'name' => collect(explode('-', $slug))
                        ->map(fn (string $segment) => ucfirst($segment))
                        ->join(' '),
                ])
                ->toArray()
        );
    }
}
