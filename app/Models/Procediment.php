<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Procediment extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $casts = [
        'ativo' => 'boolean',
    ];

    protected $fillable = [
      'codigo',
      'name',
      'tipo_relate',
      'ativo'
    ];


    public function Procediment_eventos()
    {
      return $this->hasMany(eventos::class, 'procediments_id');
    }
}
