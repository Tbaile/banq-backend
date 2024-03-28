<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Asset::class);
    }

    public function index(Request $request): JsonResponse
    {
        return AssetResource::collection($request->user()->assets()->paginate())->response();
    }

    public function show(Asset $asset)
    {
        return new AssetResource($asset->load('transactions'));
    }

    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = new Asset($request->validated());
        $asset->user()->associate($request->user());
        $asset->save();

        return (new AssetResource($asset))->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
