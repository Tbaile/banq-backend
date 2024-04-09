<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class AssetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Asset::class);

        return AssetResource::collection($request->user()->assets()->paginate())->response();
    }

    public function show(Asset $asset)
    {
        Gate::authorize('view', $asset);

        return new AssetResource($asset->load('transactions'));
    }

    public function store(StoreAssetRequest $request): JsonResponse
    {
        Gate::authorize('create', Asset::class);

        $asset = new Asset($request->validated());
        $asset->user()->associate($request->user());
        $asset->save();

        return (new AssetResource($asset))->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
