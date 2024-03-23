<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'date',
        'description',
        'credit',
        'debit',
        'note',
        'financial_service_id'
    ];

    public function financial_service()
    {
        return $this->hasOne(FinancialService::class);
    }
}
