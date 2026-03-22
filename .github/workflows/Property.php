<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Owner;
use Illuminate\Support\Facades\Storage;
use App\Models\Contract;
use App\Models\Tenant;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'titre',
        'type',
        'statut',
        'description',
        'adresse',
        'ville',
        'code_postal',
        'superficie',
        'nb_pieces',
        'nb_chambres',
        'prix',
        'image',
        'owner_id',
    ];

    public function imageUrl(): string
    {
        return $this->image
            ? Storage::url($this->image)
            : asset('back_auth/assets/default-property.jpg');
    }


    // Badge couleur pour le statut
    public function getStatutBadgeAttribute(): string
    {
        return match($this->statut) {
            'disponible' => 'success',
            'loue'       => 'warning',
            'en_vente'   => 'info',
            'vendu'      => 'danger',
            default      => 'secondary',
        };
    }


    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function activeContract()
    {
        return $this->hasOne(Contract::class)->where('statut', 'actif')->latest();
    }

    public function tenants()
    {
        return $this->hasManyThrough(Tenant::class, Contract::class);
    }
    public function charges()
    {
        return $this->hasMany(Charge::class);
    }
}
