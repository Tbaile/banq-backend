<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Asset::class, 'asset');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = new Asset($request->validated());
        $asset->user()->associate($request->user());
        $asset->save();

        return (new AssetResource($asset))->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
