<?php
namespace App\Models\Raffles;

use App\Models\Raffles\Ticket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    use HasFactory;

    protected $table = 'raffles';

    protected $fillable = [
        'year',
        'raffle_name',
        'ticket_digits',
        'start_date',
        'end_date',
        'winner_ticket_id',
    ];

    // Relación con los boletos
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    // Relación con el boleto ganador
    public function winnerTicket()
    {
        return $this->belongsTo(Ticket::class, 'winner_ticket_id');
    }
}


