<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id', 'tenant_id', 'property_id', 'reference',
        'periode', 'date_echeance', 'date_paiement',
        'montant_du', 'montant_paye', 'penalite', 'statut',
        'mode_paiement', 'numero_transaction', 'notes',
    ];

    protected $casts = [
        'periode'        => 'date',
        'date_echeance'  => 'date',
        'date_paiement'  => 'date',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getStatutBadgeAttribute(): string
    {
        return match($this->statut) {
            'paye'      => 'success',
            'partiel'   => 'warning',
            'en_retard' => 'danger',
            'impaye'    => 'dark',
            default     => 'secondary',
        };
    }

    public function getMontantResteAttribute(): float
    {
        return max(0, $this->montant_du - $this->montant_paye);
    }

    public function getMontantTotalAttribute(): float
    {
        return $this->montant_du + $this->penalite;
    }

    public function isEnRetard(): bool
    {
        return $this->statut !== 'paye' && now()->gt($this->date_echeance);
    }

    public function updateStatut(): void
    {
        if ($this->montant_paye <= 0) {
            $statut = now()->gt($this->date_echeance) ? 'en_retard' : 'en_attente';
        } elseif ($this->montant_paye >= $this->montant_du) {
            $statut = 'paye';
        } else {
            $statut = 'partiel';
        }
        $this->update(['statut' => $statut]);
    }

    // Générer référence unique
    public static function generateReference(): string
    {
        $year  = now()->format('Y');
        $month = now()->format('m');
        $count = static::withTrashed()->whereYear('created_at', $year)->count() + 1;
        return "PAY-{$year}{$month}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
