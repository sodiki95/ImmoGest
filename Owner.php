<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Owner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'telephone2',
        'type',
        'entreprise',
        'adresse',
        'ville',
        'code_postal',
        'cni',
        'photo',
        'notes',
    ];

    // Nom complet
    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    // Nombre de biens
    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
