<?php

namespace App\Models;

use App\Models\Traits\HasOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yajra\Auditable\AuditableTrait;

class FinancialTransaction extends Model
{
    use HasFactory, AuditableTrait, HasOwnership;

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
        return $this->belongsTo(FinancialService::class);
    }
}
