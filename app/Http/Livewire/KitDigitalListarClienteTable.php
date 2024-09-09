<?php

namespace App\Http\Livewire;

use App\Models\Clients\Client;
use App\Models\KitDigital;
use App\Models\KitDigitalEstados;
use App\Models\KitDigitalServicios;
use App\Models\Users\User;
use Livewire\Component;
use Livewire\WithPagination;

class KitDigitalListarClienteTable extends Component
{
    use WithPagination;

    public $buscar;
    public $selectedCliente = '';
    public $selectedEstado;
    public $selectedGestor;
    public $selected;
    public $selectedServicio;
    public $selectedEstadoFactura;
    public $selectedComerciales;
    public $clientes;
    public $estados;
    public $gestores;
    public $servicios;
    public $comerciales;
    public $estados_facturas;
    public $segmentos;
    public $Sumatorio;
    public $perPage = 10;
    public $sortColumn = 'cliente_id'; // Columna por defecto
    public $sortDirection = 'asc'; // Dirección por defecto

    protected $kitDigitals; // Propiedad protegida para los usuarios

    public function mount(){

        $this->gestores = User::Where('access_level_id',4)->get();
        $this->comerciales = User::Where('access_level_id',6)->get();
        $this->servicios = KitDigitalServicios::all();
        $this->estados = KitDigitalEstados::all();
        $this->clientes = Client::where('is_client',true)->get();
        $this->estados_facturas = [
            ['id' => '0', 'nombre' => 'No abonada'],
            ['id' => '1', 'nombre' => 'Abonada'],
        ];
        $this->segmentos  = [
            ['id' => '1', 'nombre' => '1'],
            ['id' => '2', 'nombre' => '2'],
            ['id' => '3', 'nombre' => '3'],
            ['id' => '30', 'nombre' => '3 Extra'],
            ['id' => '4', 'nombre' => '4'],
            ['id' => '5', 'nombre' => '5'],
            ['id' => 'A', 'nombre' => 'A'],
            ['id' => 'B', 'nombre' => 'B'],
            ['id' => 'C', 'nombre' => 'C']
        ];
    }


    public function render()
    {
        $this->actualizarKitDigital(); // Ahora se llama directamente en render para refrescar los clientes.
        return view('livewire.kit-digital-listar', [
            'kitDigitals' => $this->kitDigitals
        ]);
    }

    protected function actualizarKitDigital()
    {
        // Comprueba si se ha seleccionado "Todos" para la paginación
        $buscarLower = mb_strtolower($this->buscar);

        $query = KitDigital::when($this->buscar, function ($query) use ($buscarLower) {
            $query->whereRaw('LOWER(contratos) LIKE ?', ["%{$buscarLower}%"])
                ->orWhereRaw('LOWER(cliente) LIKE ?', ["%{$buscarLower}%"])
                ->orWhereRaw('LOWER(expediente) LIKE ?', ["%{$buscarLower}%"])
                ->orWhereRaw('LOWER(contacto) LIKE ?', ["%{$buscarLower}%"])
                ->orWhereRaw('LOWER(telefono) LIKE ?', ["%{$buscarLower}%"]);
        })
                ->when($this->selectedComerciales, function ($query) {
                    $query->where('comercial_id', $this->selectedComerciales);
                })
                ->when($this->selectedEstadoFactura, function ($query) {
                    $query->where('estado_factura', $this->selectedEstadoFactura);
                })
                ->when($this->selectedServicio, function ($query) {
                    $query->where('servicio_id', $this->selectedServicio);
                })
                ->when($this->selected, function ($query) {
                    $query->where('', $this->selected);
                })
                ->when($this->selectedGestor, function ($query) {
                    $query->where('gestor', $this->selectedGestor);
                })
                ->when($this->selectedCliente, function ($query) {
                    $query->where('cliente_id', $this->selectedCliente);
                })
                ->when($this->selectedEstado, function ($query) {
                    $query->where('estado', $this->selectedEstado);
                });


        $query->orderBy($this->sortColumn, $this->sortDirection);

        // Verifica si se seleccionó 'all' para mostrar todos los registros
        $this->kitDigitals = $this->perPage === 'all' ? $query->get() : $query->paginate(is_numeric($this->perPage) ? $this->perPage : 10);
        $this->Sumatorio = $this->kitDigitals->reduce(function ($carry, $item) {
            $cleanImporte = preg_replace('/[^\d,]/', '', $item->importe); // Elimina todo excepto números y coma
            $cleanImporte = str_replace(',', '.', $cleanImporte); // Convierte comas a puntos para decimales
            return $carry + (float)$cleanImporte;
        }, 0);
    }

    public function getCategorias()
    {
        // Si es necesario, puedes incluir lógica adicional aquí antes de devolver los usuarios
        return $this->kitDigitals;
    }

    public function aplicarFiltro()
    {
        // Aquí aplicarías los filtros
        // Por ejemplo: $this->filtroEspecifico = 'valor';

        $this->actualizarKitDigital(); // Luego actualizas la lista de usuarios basada en los filtros
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }
    public function updating($propertyName)
    {
        if ($propertyName === 'buscar' || $propertyName === 'selectedCliente' || $propertyName === 'selectedEstado') {
            $this->resetPage(); // Resetear la paginación solo cuando estos filtros cambien.
        }
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }
}
