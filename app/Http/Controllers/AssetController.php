<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Http\Resources\AssetResource;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Symfony\Component\HttpFoundation\Response;

#[Group('Assets')]
class AssetController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Asset::class);
    }

    #[ResponseFromApiResource(AssetResource::class, Asset::class, collection: true, paginate: 10)]
    public function index(Request $request): JsonResponse
    {
        return AssetResource::collection($request->user()->assets()->paginate())->response();
    }

    #[ResponseFromApiResource(AssetResource::class, Asset::class, with: ['transactions'])]
    public function show(Asset $asset)
    {
        return new AssetResource($asset->load('transactions'));
    }

    #[ResponseFromApiResource(AssetResource::class, Asset::class, status: Response::HTTP_CREATED)]
    public function store(StoreAssetRequest $request): JsonResponse
    {
        $asset = new Asset($request->validated());
        $asset->user()->associate($request->user());
        $asset->save();

        return (new AssetResource($asset))->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
