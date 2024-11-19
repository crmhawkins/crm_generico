<?php
namespace App\Models\Raffles;

use App\Models\Raffles\Ticket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raffle extends Model
{
    use HasFactory;

    protected $table = 'raffles';
    protected $dates = ['start_date', 'end_date'];

    protected $fillable = [
        'year',
        'raffle_name',
        'ticket_digits',
        'start_date',
        'end_date',
        'winner_number', // Se usa para almacenar el número del ticket ganador
    ];

    // Relación con los boletos
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'raffle_id');
    }

    // Método para obtener el ticket ganador basado en el número
    public function getWinnerTicket()
    {
        return $this->tickets()->where('ticket_number', $this->winner_number)->first();
    }
}



