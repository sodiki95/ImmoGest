<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nom', 'prenom', 'email', 'telephone', 'telephone2',
        'adresse', 'ville', 'code_postal', 'cni',
        'profession', 'employeur', 'photo', 'notes',
    ];

    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }


    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function activeContract()
    {
        return $this->hasOne(Contract::class)->where('statut', 'actif')->latest();
    }

}
