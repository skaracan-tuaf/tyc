<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'origin',
        'description',
        'image',
        'status',
    ];

    /**
     * Platformun ilişkilendirilmiş mühimmatları.
     */
    public function munitions()
    {
        return $this->belongsToMany(Munition::class, 'munition_platforms');
    }

    /**
     * Tipin kullanıcı dostu adı
     */
    public function getTypeLabelAttribute()
    {
        return match ($this->type) {
            'multi_role_fighter' => 'Çok Amaçlı Savaş Uçağı',
            'air_superiority_fighter' => 'Hava Üstünlük Uçağı',
            'bomber' => 'Bombardıman Uçağı',
            'attack_aircraft' => 'Taarruz Uçağı',
            'reconnaissance_aircraft' => 'Keşif Uçağı',
            'electronic_warfare_aircraft' => 'Elektronik Harp Uçağı',
            'tanker_aircraft' => 'Yakıt İkmal Uçağı',
            'trainer_aircraft' => 'Eğitim Uçağı',
            'transport_aircraft' => 'Nakliye Uçağı',
            'attack_helicopter' => 'Taarruz Helikopteri',
            'transport_helicopter' => 'Nakliye Helikopteri',
            'uav' => 'İHA',
            'ucav' => 'SİHA',
            default => 'Diğer',
        };
    }

    /**
     * Görselin tam URL’si
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : asset('backend_assets/images/no-image.png');
    }
}
