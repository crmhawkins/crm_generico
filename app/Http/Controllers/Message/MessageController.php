<?php

namespace App\Http\Controllers\Message;

use App\Http\Controllers\Controller;
use App\Models\Clients\Client;
use App\Models\Todo\Messages;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'mensaje' => 'required|string',
            'archivo' => 'nullable', // Ajusta los MIME types según necesidad
        ],[
            'mensaje.required' => 'No pùede mandar el mensaje vacio'
        ]);

        $message = new Messages;
        $message->mensaje = $request->mensaje;
        $message->todo_id = $request->todo_id;
        $message->admin_user_id = $request->admin_user_id;


        if ($request->hasFile('archivo') && $request->file('archivo')->isValid()) {
            $filename = $request->file('archivo')->store('messages', 'public');
            $message->archivo = $filename;
        }

        $message->save();

        $message->reads()->create([
            'admin_user_id' => $message->admin_user_id,
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect()->back()->with('toast', [
            'icon' => 'success',
            'mensaje' => 'Mensaje enviado con éxito!'
        ]);
    }

    public function markAsRead($todoId, Request $request) {
        $user = auth()->user();  // Asegúrate de que el usuario está autenticado

        // Encuentra todos los mensajes no leídos de este 'to-do' para el usuario
        $messages = Messages::where('todo_id', $todoId)
                            ->whereDoesntHave('reads', function($query) use ($user) {
                                $query->where('admin_user_id', $user->id);
                            })->get();

        foreach ($messages as $message) {
            // Marcar cada mensaje como leído
            $message->reads()->create([
                'admin_user_id' => $user->id,
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }

}
