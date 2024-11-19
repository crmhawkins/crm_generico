<?php

namespace App\Http\Controllers\Raffles;

use App\Http\Controllers\Controller;
use App\Models\Raffles\Raffle;
use App\Models\Raffles\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;



class RaffleController extends Controller
{
    public function index()
{
    // Obtener solo los sorteos que están activos (status = 0)
    $raffles = Raffle::where('status', 0)->get();
    return view('raffles.index', compact('raffles'));
}


    public function create()
    {
        return view('raffles.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'year' => 'required|integer',
        'raffle_name' => 'required|string|max:255',
        'ticket_digits' => 'required|integer',
        'start_date' => 'nullable|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ], [
        'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
    ]);

    Raffle::create($request->all());
    return redirect()->route('raffles.index')->with('success', 'Sorteo creado correctamente.');
}

    public function show(Raffle $raffle)
    {
        return view('raffles.show', compact('raffle'));
    }

    public function edit(Raffle $raffle)
    {
        return view('raffles.edit', compact('raffle'));
    }

    public function update(Request $request, Raffle $raffle)
{
    $request->validate([
        'year' => 'required|integer',
        'raffle_name' => 'required|string|max:255',
        'ticket_digits' => 'required|integer',
        'start_date' => 'nullable|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $raffle->update($request->all());
    return redirect()->route('raffles.index')->with('success', 'Sorteo actualizado correctamente.');
}

    public function destroy(Raffle $raffle)
    {
        $raffle->delete();
        return redirect()->route('raffles.index')->with('success', 'Sorteo eliminado correctamente.');
    }

    public function assignWinner(Request $request, Raffle $raffle)
{
    $request->validate([
        'ticket_number' => 'required|integer',
    ]);

    // Guardar el número del ganador directamente
    $raffle->winner_number = $request->ticket_number;
    $raffle->save();

    return redirect()->route('raffles.index')->with('success', 'Numero Ganador guardado correctamente.');
}



public function showWinner(Raffle $raffle)
{
    // Verificar si hay un número ganador asignado
    if (!$raffle->winner_number) {
        return redirect()->route('raffles.index')->with('error', 'No se ha asignado un número de ganador para este sorteo.');
    }

    // Buscar el ticket ganador
    $ticket = Ticket::where('raffle_id', $raffle->id)
        ->where('ticket_number', $raffle->winner_number)
        ->first();

    if ($ticket) {
        $client = $ticket->client; // Obtener el cliente asociado
        return view('raffles.winner', compact('raffle', 'ticket', 'client'));
    } else {
        return redirect()->route('raffles.index')->with('error', 'El número ganador no corresponde a ningún ticket vendido para este sorteo.');
    }
}



    public function showTickets(Raffle $raffle)
{
    $ticketsSold = $raffle->tickets()->count();
    $totalTickets = pow(10, $raffle->ticket_digits);
    $tickets = $raffle->tickets;

    return view('raffles.show_tickets', compact('raffle', 'tickets', 'ticketsSold', 'totalTickets'));
}

    public function updateRaffle(Request $request, Raffle $raffle)
{
    $request->validate([
        'raffle_name' => 'required|string|max:255',
        'year' => 'required|integer',
        'ticket_digits' => 'required|integer',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ], [
        'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
    ]);

    $raffle->update($request->all());

    return redirect()->route('raffles.index')->with('success', 'Sorteo actualizado correctamente.');
}

    public function finalize(Raffle $raffle)
{
    // Cambiar el estado de 'activo' (0) a 'finalizado' (1)
    $raffle->status = 1;  // 1 significa finalizado
    $raffle->save();

    return redirect()->route('raffles.index')->with('success', 'Sorteo finalizado correctamente.');
}

    public function showCompleted()
{
    // Obtener todos los sorteos finalizados (status = 1)
    $completedRaffles = Raffle::where('status', 1)->get();
    return view('raffles.completed_raffles', compact('completedRaffles'));
}

public function assignRandomTickets(Request $request)
{
    $request->validate([
        'raffle_id' => 'required|exists:raffles,id',
        'client_id' => 'required|exists:clients,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $raffle = Raffle::findOrFail($request->raffle_id);

    // Generar rango de tickets posibles
    $totalTickets = pow(10, $raffle->ticket_digits) - 1;
    $availableTickets = range(0, $totalTickets);

    // Filtrar tickets asignados
    $assignedTickets = Ticket::where('raffle_id', $raffle->id)->pluck('ticket_number')->toArray();
    $availableTickets = array_diff($availableTickets, $assignedTickets);

    // Validar si hay suficientes tickets
    if (count($availableTickets) < $request->quantity) {
        return response()->json(['error' => 'No hay suficientes tickets disponibles.'], 400);
    }

    // Seleccionar tickets aleatorios de forma única
    $selectedTickets = $request->quantity === 1
        ? [array_rand(array_flip($availableTickets))]
        : array_rand(array_flip($availableTickets), $request->quantity);

    // Asegurarnos de que siempre sea un array
    if (!is_array($selectedTickets)) {
        $selectedTickets = [$selectedTickets];
    }

    // Usar transacción para asegurar consistencia
    DB::beginTransaction();
    try {
        $assignedTicketNumbers = [];
        foreach ($selectedTickets as $ticketNumber) {
            $ticket = Ticket::create([
                'raffle_id' => $raffle->id,
                'client_id' => $request->client_id,
                'ticket_number' => intval($ticketNumber), // Convertir a entero
            ]);
            $assignedTicketNumbers[] = $ticketNumber;
        }
        DB::commit();

        return response()->json([
            'raffle_name' => $raffle->raffle_name,
            'year' => $raffle->year,
            'assigned_tickets' => $assignedTicketNumbers,
        ], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error al asignar tickets.', 'message' => $e->getMessage()], 500);
    }
}




}





