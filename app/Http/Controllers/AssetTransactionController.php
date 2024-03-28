<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;

class AssetTransactionController extends Controller
{
    public function index(Asset $asset): JsonResponse
    {
        $this->authorize('view', $asset);

        return TransactionResource::collection(
            $asset->transactions()
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->paginate()
        )->response();
    }
}
