<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Charge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id', 'owner_id', 'titre', 'categorie',
        'statut', 'periodicite', 'montant', 'date_charge',
        'date_paiement', 'fournisseur', 'numero_facture',
        'document', 'description',
    ];

    protected $casts = [
        'date_charge'   => 'date',
        'date_paiement' => 'date',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function getStatutBadgeAttribute(): string
    {
        return match($this->statut) {
            'paye'      => 'success',
            'annule'    => 'secondary',
            default     => 'warning',
        };
    }

    public function getCategorieLabelAttribute(): string
    {
        return match($this->categorie) {
            'entretien'   => 'Entretien',
            'reparation'  => 'Réparation',
            'taxe'        => 'Taxe',
            'assurance'   => 'Assurance',
            'syndic'      => 'Syndic',
            'eau'         => 'Eau',
            'electricite' => 'Électricité',
            'internet'    => 'Internet',
            'gardiennage' => 'Gardiennage',
            default       => 'Autre',
        };
    }

    public function getCategorieBadgeAttribute(): string
    {
        return match($this->categorie) {
            'taxe', 'assurance'  => 'danger',
            'entretien','reparation' => 'warning',
            'eau','electricite'  => 'info',
            'syndic'             => 'primary',
            default              => 'secondary',
        };
    }
}
