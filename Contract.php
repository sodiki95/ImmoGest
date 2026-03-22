<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'property_id', 'type', 'statut',
        'date_debut', 'date_fin', 'loyer_mensuel', 'caution',
        'jour_paiement', 'periodicite', 'conditions', 'document',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
    ];

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
            'actif'      => 'success',
            'en_attente' => 'warning',
            'termine'    => 'secondary',
            'resilie'    => 'danger',
            default      => 'secondary',
        };
    }

    public function getDureeAttribute(): string
    {
        if (!$this->date_fin) return 'Indéterminée';
        return $this->date_debut->diffForHumans($this->date_fin, true);
    }

    public function rentCalls()
    {
        return $this->hasMany(RentCall::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
