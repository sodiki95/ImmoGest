<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'rent_call_id', 'contract_id', 'reference',
        'date_paiement', 'montant', 'mode_paiement',
        'numero_transaction', 'notes',
    ];

    protected $casts = [
        'date_paiement' => 'date',
    ];

    public function rentCall()
    {
        return $this->belongsTo(RentCall::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    // Générer une référence unique
    public static function generateReference(): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');
        $last  = static::withTrashed()->whereYear('created_at', $year)->count() + 1;
        return "QUI-{$year}{$month}-" . str_pad($last, 3, '0', STR_PAD_LEFT);
    }
}
