<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class eventos extends Model
{

    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    protected $fillable = [
        'client_id',
        'user_id',
        'procediments_id',
        'title',
        'start',
        'end',
        'color',
        'descricao',
        'status'
    
    ];

    public function user_eventos()
    {
      return $this->belongsTo(User::class, 'user_id');
    }

    public function client_eventos()
    {
      return $this->belongsTo(Client::class, 'client_id');
    }

    public function Procediment_eventos()
    {
      return $this->belongsTo(Procediment::class, 'procediments_id');
    }
}
