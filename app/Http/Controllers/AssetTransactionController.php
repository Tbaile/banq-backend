<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Asset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class AssetTransactionController extends Controller
{
    public function index(Asset $asset): JsonResponse
    {
        Gate::authorize('view', $asset);

        return TransactionResource::collection(
            $asset->transactions()
                ->orderByDesc('date')
                ->orderByDesc('id')
                ->paginate()
        )->response();
    }
}
