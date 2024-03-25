<?php

namespace App\Models;

use App\Models\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yajra\Auditable\AuditableTrait;

class FinancialService extends Model
{
    use HasFactory, AuditableTrait, HasOwnership;

    protected $fillable = [
        'id',
        'name'
    ];

    public function financial_transactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }
}
