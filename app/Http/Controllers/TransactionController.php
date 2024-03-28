<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Transaction::class);
    }

    public function store(StoreTransactionRequest $request): JsonResponse
    {
        $transaction = Transaction::create($request->validated());

        return (new TransactionResource($transaction))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
