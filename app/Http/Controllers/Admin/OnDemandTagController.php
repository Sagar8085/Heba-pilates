<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tags\OnDemand\Store;
use App\Http\Requests\Tags\OnDemand\Update;
use App\Http\Resources\TagResource;
use App\Models\OnDemand;
use App\Models\OnDemandTag;
use App\Models\Tag;

class OnDemandTagController extends Controller
{
    public function index()
    {
        return TagResource::collection(Tag::all())->additional([
            'status' => __('response.success'),
        ]);
    }

    public function show(OnDemand $ondemand)
    {
        return TagResource::collection($ondemand->tags)->additional([
            'status' => __('response.success'),
        ]);
    }

    public function store(Store $request)
    {
        $tag = Tag::firstOrCreate(
            $request->only('slug'),
            $request->only(['name', 'category']),
        );

        OnDemandTag::updateOrCreate([
            'ondemand_id' => $request->input('ondemand_id'),
            'tag_id' => $tag->id,
        ]);

        return response()->json([
            'status' => __('response.success'),
            'tag' => TagResource::make($tag),
        ]);
    }

    public function update(OnDemand $ondemand, Update $request)
    {
        $ids = collect($request->tags)->pluck('id');

        $ondemand->tags()->sync($ids);

        return response()->json([
            'status' => __('response.success'),
            'message' => __('ondemand.update', ['name' => $ondemand->name]),
        ]);
    }
}
