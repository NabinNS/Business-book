<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDetails extends Model
{
    use HasFactory;
    protected $fillable = [
        'company_name', 'vat_number', 'phone_number', 'address', 'email_address', 'opening_balance', 'date'
    ];
    public function accountLedger()
    {
        return $this->hasMany(AccountLedger::class);
    }

    public function accountRemainingBalance()
    {
        return $this->hasOne(AccountRemainingBalance::class);
    }
    public function order()
    {
        return $this->hasMany(Order::class);
    }
    public function calculateRemainingBalance()
    {
        $debitTotal = $this->accountLedger()->sum('debit');
        $creditTotal = $this->accountLedger()->sum('credit');
        return $creditTotal - $debitTotal;
    }
}
