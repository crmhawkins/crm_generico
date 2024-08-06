<?php

namespace App\Http\Controllers\Tesoreria;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Accounting\Gasto;
use App\Models\Accounting\AssociatedExpenses;
use App\Models\Accounting\Ingreso;
use App\Models\Accounting\UnclassifiedExpenses;
use App\Models\Budgets\Budget;
use App\Models\Invoices\Invoice;
use App\Models\Other\BankAccounts;
use App\Models\PaymentMethods\PaymentMethod;
use App\Models\PurcharseOrde\PurcharseOrder;
use Carbon\Carbon;

class TesoreriaController extends Controller
{
    public function indexIngresos(){

        return view('tesoreria.ingresos.index');
    }

    public function indexGastos(){
        return view('tesoreria.gastos.index');
    }

    public function indexUnclassifiedExpensese(Request $request){
        return view('tesoreria.gastos-sin-clasificar.index');
    }

    public function indexAssociatedExpenses(){
        return view('tesoreria.gastos-asociados.index');
    }

    public function createIngresos(){
        $banks = BankAccounts::all();
        $invoices = Invoice::all();
        return view('tesoreria.ingresos.create', compact('banks','invoices'));
    }

    public function createGastos(){
        $banks = BankAccounts::all();
        $paymentMethods = PaymentMethod::all();
        return view('tesoreria.gastos.create',compact( 'banks', 'paymentMethods'));
    }

    public function createAssociatedExpenses(){
        $banks = BankAccounts::all();
        $paymentMethods = PaymentMethod::all();
        $purchaseOrders = PurcharseOrder::all();
        return view('tesoreria.gastos-asociados.create',compact( 'banks', 'paymentMethods','purchaseOrders'));
    }

    public function editIngresos(string $id){
        $ingreso = Ingreso::find($id);
        if (!$ingreso) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El Ingreso no existe'
            ]);
            return redirect()->route('ingreso.index');
        }
        $banks = BankAccounts::all();
        $invoices = Invoice::all();
        return view('tesoreria.ingresos.edit',compact('ingreso','banks','invoices'));
    }

    public function editGastos(string $id){

        $gasto = Gasto::find($id);
        if (!$gasto) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El gasto no existe'
            ]);
            return redirect()->route('gastos.index');
        }
        // Obtener listas de opciones necesarias para el formulario
        $banks = BankAccounts::all();
        $paymentMethods = PaymentMethod::all();

        return view('tesoreria.gastos.edit', compact('gasto', 'banks', 'paymentMethods'));

    }

    public function editUnclassifiedExpensese(string $id){

        $unclassifiedExpense = UnclassifiedExpenses::find($id);
        if (!$unclassifiedExpense) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El gasto no existe'
            ]);
            return redirect()->route('gastos-sin-clasificar.index');
        }

        // Obtener listas de opciones necesarias para el formulario
        $banks = BankAccounts::all();
        $paymentMethods = PaymentMethod::all();
        $budgets = Budget::all();
        $purchaseOrders = PurcharseOrder::all();

        return view('tesoreria.gastos-sin-clasificar.edit', compact('unclassifiedExpense', 'banks', 'paymentMethods', 'budgets', 'purchaseOrders'));
    }

    public function editAssociatedExpenses(string $id){
        $gasto = AssociatedExpenses::find($id);
        if (!$gasto) {
            session()->flash('toast', [
                'icon' => 'error',
                'mensaje' => 'El gasto no existe'
            ]);
            return redirect()->route('gastos-asociados.index');
        }

        // Obtener listas de opciones necesarias para el formulario
        $banks = BankAccounts::all();
        $paymentMethods = PaymentMethod::all();
        $budgets = Budget::all();
        $purchaseOrders = PurcharseOrder::all();

        return view('tesoreria.gastos-asociados.edit',compact('gasto', 'banks', 'paymentMethods', 'budgets', 'purchaseOrders'));
    }

    public function storeIngresos(Request $request ){
        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'quantity' => 'required',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'invoice_id' => 'nullable|integer|exists:invoices,id',

        ],[
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'invoice_id.integer' => 'El ID de la factura debe ser un número entero.',
            'invoice_id.exists' => 'El ID de la factura proporcionado no existe.',
        ]);

        $ingreso = new Ingreso( $validated);
        $ingreso->date = Carbon::now();
        //$ingreso->budget_date = Invoice::find( $ingreso->invoice_id)->expiration_date;
        $ingreso->save();
        return redirect()->route('ingreso.edit', $ingreso->id)->with('toast',[
            'icon' => 'success',
            'mensaje' => 'Ingreso creado exitosamente'
        ]);

    }

    public function storeGastos(Request $request){

        $unclassifiedExpenses = UnclassifiedExpenses::find($request->id);
        // Validar los datos del formulario
        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'reference' => 'required|string|max:255',
            'quantity' => 'required',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'date' => 'nullable',
            'received_date' => 'required',
            'payment_method_id' => 'required|integer|exists:payment_method,id',
            'transfer_movement' => 'nullable',
            'state' => 'required|string|max:255',
            'documents' => 'nullable',
        ],[
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'reference.required' => 'La referencia es obligatoria.',
            'reference.string' => 'La referencia debe ser una cadena de texto.',
            'reference.max' => 'La referencia no debe exceder los 255 caracteres.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'date.date' => 'La fecha debe ser una fecha válida.',
            'received_date.required' => 'La fecha de recepción es obligatoria.',
            'received_date.date' => 'La fecha de recepción debe ser una fecha válida.',
            'payment_method_id.required' => 'El método de pago es obligatorio.',
            'payment_method_id.integer' => 'El ID del método de pago debe ser un número entero.',
            'payment_method_id.exists' => 'El ID del método de pago proporcionado no existe.',
            'state.required' => 'El estado es obligatorio.',
            'state.string' => 'El estado debe ser una cadena de texto.',
            'state.max' => 'El estado no debe exceder los 255 caracteres.',
        ]);

        // Crear el gasto asociado
        $gasto = new Gasto( $validated);

        // Asignar valores adicionales si es necesario
        if ($request->hasFile('documents') && $request->file('documents')->isValid()) {
            $path = $request->file('documents')->store('documents', 'public');
            $gasto->documents = $path;
        }

        // Guardar el nuevo gasto
        $gasto->save();

        if(isset($unclassifiedExpenses)){
            $unclassifiedExpenses->accepted = true;
            $unclassifiedExpenses->save();
        }
        // Redireccionar con mensaje de éxito
        return redirect()->route('gasto.edit', $gasto->id)->with('success', 'Gasto asociado creado exitosamente.');
    }

    public function storeAssociatedExpenses(Request $request)
    {

        $unclassifiedExpenses = UnclassifiedExpenses::find($request->id);

        // Validar los datos del formulario
        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'quantity' => 'required',
            'received_date' => 'required',
            'reference' => 'required|string|max:255',
            'date' => 'nullable',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'purchase_order_id' => 'required|integer|exists:purchase_order,id',
            'state' => 'required|string|max:255',
            'payment_method_id' => 'required|integer|exists:payment_method,id',
            'aceptado_gestor' => 'nullable|boolean',
            'documents' => 'nullable',
        ],[
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'reference.required' => 'La referencia es obligatoria.',
            'reference.string' => 'La referencia debe ser una cadena de texto.',
            'reference.max' => 'La referencia no debe exceder los 255 caracteres.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'date.date' => 'La fecha debe ser una fecha válida.',
            'received_date.required' => 'La fecha de recepción es obligatoria.',
            'received_date.date' => 'La fecha de recepción debe ser una fecha válida.',
            'payment_method_id.required' => 'El método de pago es obligatorio.',
            'payment_method_id.integer' => 'El ID del método de pago debe ser un número entero.',
            'payment_method_id.exists' => 'El ID del método de pago proporcionado no existe.',
            'purchase_order_id.required' => 'El ID de la orden de compra es obligatorio.',
            'purchase_order_id.integer' => 'El ID de la orden de compra debe ser un número entero.',
            'purchase_order_id.exists' => 'El ID de la orden de compra proporcionado no existe.',
            'state.required' => 'El estado es obligatorio.',
            'state.string' => 'El estado debe ser una cadena de texto.',
            'state.max' => 'El estado no debe exceder los 255 caracteres.',
            'aceptado_gestor.boolean' => 'El campo aceptado gestor debe ser verdadero o falso.',
        ]);

        // Crear el gasto asociado
        $associatedExpense = new AssociatedExpenses( $validated);

        // Asignar valores adicionales si es necesario
        // Ejemplo: Manejar la carga de archivos
        if ($request->hasFile('documents')) {
            $path = $request->file('documents')->store('documents', 'public');
            $associatedExpense->documents = $path;
        }
        // Guardar el nuevo gasto
        $associatedExpense->save();

        if(isset($unclassifiedExpenses)){
            $unclassifiedExpenses->accepted = true;
            $unclassifiedExpenses->save();
        }
        // Redireccionar con mensaje de éxito
        return redirect()->route('gasto-asociado.edit', $associatedExpense->id)->with('success', 'Gasto asociado creado exitosamente.');
    }

    public function storeUnclassifiedExpensese(Request $request){
        // Crear un gasto sin clasificar
       if ($request->hasFile('file')) {
           $path = $request->file('file')->store('documents', 'public');
                   $unclassifiedExpense = UnclassifiedExpenses::create(array_merge(
           $request->all(),
           ['documents' => $path]
           ));
       }else{
            $unclassifiedExpense = UnclassifiedExpenses::create($request->all());
       }

       if(isset($unclassifiedExpense->order_number)){

           $purchaseOrder = PurcharseOrder::where('id', $request->order_number)
           ->where('amount', $request->amount)
           ->first();

           if ($purchaseOrder){
               $associatedExpense = AssociatedExpenses::create([
                   'title' => $unclassifiedExpense->pdf_file_name,
                   'reference' => $unclassifiedExpense->invoice_number,
                   'quantity' => $unclassifiedExpense->amount,
                   'bank_id' => $purchaseOrder->bank_id,
                   'received_date' => $unclassifiedExpense->received_date,
                   'payment_method_id' => $purchaseOrder->payment_method_id,
                   'budget_id' => $purchaseOrder->concepto->presupuesto->id,
                   'purchase_order_id' => $purchaseOrder->id,
                   'state' => 'PENDIENTE', // Estado inicial, puedes cambiarlo si es necesario
                   'aceptado_gestor' => false,
                   'documents' => $unclassifiedExpense->documents
               ]);
               if($associatedExpense){
                   $unclassifiedExpense->message = 'Generado Gasto Asociado';
                   $unclassifiedExpense->save();
                   return response()->json([
                       'success' => true,
                       'message' => 'Gasto registrado y gasto asociado creado correctamente.'
                   ], 201);
               }
           }else{
               $purchaseOrder = PurcharseOrder::where('id', $request->order_number)
               ->first();
               if($purchaseOrder){
                   $unclassifiedExpense->message = 'El valor de la factura no coincide con la orden de compra';
                   $unclassifiedExpense->save();
               }else{
                   $unclassifiedExpense->message = 'No se encontro orden de compra con este numero de orden';
                   $unclassifiedExpense->save();
               }
               return response()->json([
                   'success' => true,
                   'message' => 'Gasto registrado correctamente.'
               ], 201);
           }
       }else{
           $unclassifiedExpense->message = 'No se encontro numero de orden en la factura';
           $unclassifiedExpense->save();
           return response()->json([
               'success' => true,
               'message' => 'Gasto registrado correctamente.'
           ], 201);
       }
    }

    public function updateIngresos(Request $request, string $id){
        $ingreso = Ingreso::find($id);

        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'quantity' => 'required',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'invoice_id' => 'nullable|integer|exists:invoices,id',

        ],[
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'invoice_id.integer' => 'El ID de la factura debe ser un número entero.',
            'invoice_id.exists' => 'El ID de la factura proporcionado no existe.',
        ]);
        $ingresoUpdated = $ingreso->update($validated);

        if($ingresoUpdated){
            return redirect()->route('ingresos.index', $ingreso->id)->with('toast',[
                'icon' => 'success',
                'mensaje' => 'Ingreso actualizado exitosamente'
            ]);
        }else{
            return redirect()->back()->with('toast',[
                'icon' => 'error',
                'mensaje' => 'Error al actuañizar el ingreso'
            ]);

        }

    }

    public function updateGastos(Request $request, string $id){
        $gasto = Gasto::find($id);

        // Validar los datos del formulario
        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'reference' => 'required|string|max:255',
            'quantity' => 'required',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'date' => 'nullable|date',
            'received_date' => 'required|date',
            'payment_method_id' => 'required|integer|exists:payment_method,id',
            'transfer_movement' => 'nullable',
            'state' => 'required|string|max:255',
        ], [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'reference.required' => 'La referencia es obligatoria.',
            'reference.string' => 'La referencia debe ser una cadena de texto.',
            'reference.max' => 'La referencia no debe exceder los 255 caracteres.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'date.date' => 'La fecha debe ser una fecha válida.',
            'received_date.required' => 'La fecha de recepción es obligatoria.',
            'received_date.date' => 'La fecha de recepción debe ser una fecha válida.',
            'payment_method_id.required' => 'El método de pago es obligatorio.',
            'payment_method_id.integer' => 'El ID del método de pago debe ser un número entero.',
            'payment_method_id.exists' => 'El ID del método de pago proporcionado no existe.',
            'state.required' => 'El estado es obligatorio.',
            'state.string' => 'El estado debe ser una cadena de texto.',
            'state.max' => 'El estado no debe exceder los 255 caracteres.',
        ]);

        // Actualizar el gasto con los datos validados
        $gasto->update($validated);

        // Manejar la carga de archivos
        if ($request->hasFile('documents') && $request->file('documents')->isValid()) {
            $path = $request->file('documents')->store('documents', 'public');
            $gasto->documents = $path;
        }

        // Guardar los cambios
        $gasto->save();

        // Redireccionar con mensaje de éxito
        return redirect()->route('gastos.index')->with('success', 'Gasto actualizado exitosamente.');
    }

    public function updateAssociatedExpenses(Request $request, string $id){
        $gasto = AssociatedExpenses::find($id);


        // Validar los datos del formulario
        $validated = $this->validate($request, [
            'title' => 'required|string|max:255',
            'quantity' => 'required',
            'received_date' => 'required|date',
            'reference' => 'required|string|max:255',
            'date' => 'nullable|date',
            'bank_id' => 'required|integer|exists:bank_accounts,id',
            'purchase_order_id' => 'required|integer|exists:purchase_order,id',
            'state' => 'required|string|max:255',
            'payment_method_id' => 'required|integer|exists:payment_method,id',
            'aceptado_gestor' => 'nullable|boolean',
        ], [
            'title.required' => 'El título es obligatorio.',
            'title.string' => 'El título debe ser una cadena de texto.',
            'title.max' => 'El título no debe exceder los 255 caracteres.',
            'reference.required' => 'La referencia es obligatoria.',
            'reference.string' => 'La referencia debe ser una cadena de texto.',
            'reference.max' => 'La referencia no debe exceder los 255 caracteres.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.numeric' => 'La cantidad debe ser un número.',
            'bank_id.required' => 'El ID del banco es obligatorio.',
            'bank_id.integer' => 'El ID del banco debe ser un número entero.',
            'bank_id.exists' => 'El ID del banco proporcionado no existe.',
            'date.date' => 'La fecha debe ser una fecha válida.',
            'received_date.required' => 'La fecha de recepción es obligatoria.',
            'received_date.date' => 'La fecha de recepción debe ser una fecha válida.',
            'payment_method_id.required' => 'El método de pago es obligatorio.',
            'payment_method_id.integer' => 'El ID del método de pago debe ser un número entero.',
            'payment_method_id.exists' => 'El ID del método de pago proporcionado no existe.',
            'purchase_order_id.required' => 'El ID de la orden de compra es obligatorio.',
            'purchase_order_id.integer' => 'El ID de la orden de compra debe ser un número entero.',
            'purchase_order_id.exists' => 'El ID de la orden de compra proporcionado no existe.',
            'state.required' => 'El estado es obligatorio.',
            'state.string' => 'El estado debe ser una cadena de texto.',
            'aceptado_gestor.boolean' => 'El campo aceptado gestor debe ser verdadero o falso.',
        ]);

        // Actualizar el gasto asociado con los datos validados
        $gasto->update($validated);

        // Manejar la carga de archivos
        if ($request->hasFile('documents') && $request->file('documents')->isValid()) {
            $path = $request->file('documents')->store('documents', 'public');
            $gasto->documents = $path;
        }

        // Guardar los cambios
        $gasto->save();

        // Redireccionar con mensaje de éxito
        return redirect()->route('gastos-asociados.index')->with('success', 'Gasto asociado actualizado exitosamente.');
    }


    public function destroyIngresos(Request $request){
         $id = $request->id;
        if ($id != null) {
            $gasto = Ingreso::find($id);
            if ($gasto != null) {
                $gasto->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "El ingreso fue borrado con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra el ingreso."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }
    }

    public function destroyGastos(Request $request){
        $id = $request->id;
        if ($id != null) {
            $gasto = Gasto::find($id);
            if ($gasto != null) {
                $gasto->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "El gasto fue borrado con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra el gasto."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }

    }

    public function destroyAssociatedExpenses(Request $request){
        $id = $request->id;
        if ($id != null) {
            $gasto = AssociatedExpenses::find($id);
            if ($gasto != null) {
                $gasto->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "El gasto fue borrado con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra el gasto."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }
    }

    public function destroyUnclassifiedExpensese(Request $request){
        $id = $request->id;
        if ($id != null) {
            $gasto = UnclassifiedExpenses::find($id);
            if ($gasto != null) {
                $gasto->delete();
                return response()->json([
                    'status' => true,
                    'mensaje' => "El gasto fue borrado con éxito."
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'mensaje' => "Error 500 no se encuentra el gasto."
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'mensaje' => "Error 500 no se encuentra el ID en la petición."
            ]);
        }
    }

    public function showIngresos(){
        return view('tesoreria.ingresos.index');
    }
}
