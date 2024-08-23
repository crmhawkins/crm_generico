<?php

namespace App\Models\Incidence;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Incidences extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'holidays';

    /**
     * Atributos asignados en masa.
     *
     * @var array
     */
    protected $fillable = [
        'titulo',
        'descripcion',
        'budget_id',

    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];


    /**
     * Obtener el usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adminUser()
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }
}
