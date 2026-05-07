<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case BankTransfer = 'bank_transfer';
    case MobilePayment = 'mobile_payment';
    case Cheque = 'cheque';
}
