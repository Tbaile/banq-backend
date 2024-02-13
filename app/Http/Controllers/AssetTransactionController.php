<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Asset;

class AssetTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Asset $asset)
    {
        $this->authorize('view', $asset);

        return TransactionResource::collection($asset->transactions()->orderByDesc('date')->orderByDesc('id')->get());
    }
}
