<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

class AssetTransactionController extends Controller
{
    #[Group('Transactions')]
    #[ResponseFromApiResource(TransactionResource::class, Transaction::class, collection: true, paginate: 10)]
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
