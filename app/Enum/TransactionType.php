<?php

namespace App\Enum;

enum TransactionType: string
{
    case WITHDRAWAL = 'WITHDRAWAL';
    case DEPOSIT = 'DEPOSIT';
    case TRANSFER = 'TRANSFER';
}
