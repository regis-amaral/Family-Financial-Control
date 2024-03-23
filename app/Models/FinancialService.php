<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialService extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name'
    ];

    public function financial_transactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }
}
