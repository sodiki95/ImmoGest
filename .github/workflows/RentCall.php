<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentCall extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id', 'periode', 'montant',
        'montant_paye', 'statut', 'date_echeance', 'notes', 'reference','date_appel'
    ];

    protected $casts = [
        'periode'       => 'date',
        'date_echeance' => 'date',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function getStatutBadgeAttribute(): string
    {
        return match($this->statut) {
            'paye'      => 'success',
            'partiel'   => 'warning',
            'en_retard' => 'danger',
            default     => 'secondary',
        };
    }

    public function getMontantResteAttribute(): float
    {
        return $this->montant_appel - $this->montant_paye;
    }

    // Mettre à jour le statut automatiquement
    public function updateStatut(): void
    {
        if ($this->montant_paye <= 0) {
            $statut = now()->gt($this->date_echeance) ? 'en_retard' : 'en_attente';
        } elseif ($this->montant_paye >= $this->montant_appel) {
            $statut = 'paye';
        } else {
            $statut = 'partiel';
        }
        $this->update(['statut' => $statut]);
    }
}
