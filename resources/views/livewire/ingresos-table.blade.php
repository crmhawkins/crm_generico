@section('css')
<link rel="stylesheet" href="{{asset('assets/vendors/choices.js/choices.min.css')}}" />
@endsection

<div>
    <div class="filtros row mb-4">
        <div class="col-md-6 col-sm-12">
            <div class="flex flex-row justify-start">
                <div class="mr-3">
                    <label for="">Nª por paginas</label>
                    <select wire:model="perPage" class="form-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="all">Todo</option>
                    </select>
                </div>
                <div class="w-75">
                    <label for="">Buscar</label>
                    <input wire:model.debounce.300ms="buscar" type="text" class="form-control w-100" placeholder="Escriba la palabra a buscar...">
                </div>
            </div>
        </div>
    </div>

    @if ($ingresos->count())
        <div class="table-responsive">
            <table class="table">
                <thead class="header-table">
                    <tr>
                        <th class="px-3" style="font-size:0.75rem">Banco</th>
                        <th class="" style="font-size:0.75rem">Titulo</th>
                        <th class="" style="font-size:0.75rem">Cantidad</th>
                        <th class="" style="font-size:0.75rem">Factura asociada</th>
                        <th class="" style="font-size:0.75rem">Fecha</th>
                        <th class="text-center" style="font-size:0.75rem">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ingresos as $ingreso)
                        <tr>
                            <td>{{$ingreso->bankAccount->name ?? ($ingreso->bank_id ? 'Banco Eliminado' : 'Banco No Asignado')}}</td>
                            <td>{{$ingreso->title}}</td>
                            <td>{{ number_format($ingreso->quantity, 2) }}€</td>
                            <td ><a href="{{route('factura.edit',$ingreso->invoice_id)}}">{{$ingreso->invoice_id}}</a> </td>
                            <td>{{ \Carbon\Carbon::parse($ingreso->date)->format('d/m/Y') }}</td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{route('ingreso.edit', $ingreso->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Editar gasto"></a>
                                <a class="delete" data-id="{{$ingreso->id}}" href=""><img src="{{asset('assets/icons/trash.svg')}}" alt="Eliminar gasto"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="1"></td>
                        <th>Sumatorio:</th>
                        <td>{{number_format((float)$ingresos->sum('quantity'), 2, '.', '') }} €</td>
                        <td colspan="3"></td>

                    </tr>
                </tfoot>
            </table>
            @if($perPage !== 'all')
                {{ $ingresos->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>Ingresos</strong></h3>
        </div>
    @endif
</div>

@section('scripts')
    @include('partials.toast')
    <script src="{{asset('assets/vendors/choices.js/choices.min.js')}}"></script>
    <script>
        $(document).ready(() => {
            $('.delete').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                botonAceptar(id);
            });
        });

        function botonAceptar(id){
            Swal.fire({
                title: "¿Estas seguro que quieres eliminar este gasto?",
                html: "<p>Esta acción es irreversible.</p>",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Borrar",
                cancelButtonText: "Cancelar",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.when(getDelete(id)).then(function(data, textStatus, jqXHR) {
                        if (!data.status) {
                            Toast.fire({
                                icon: "error",
                                title: data.mensaje
                            });
                        } else {
                            Toast.fire({
                                icon: "success",
                                title: data.mensaje
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }

        function getDelete(id) {
            const url = '{{route("gasto.delete")}}';
            return $.ajax({
                type: "POST",
                url: url,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'id': id,
                },
                dataType: "json"
            });
        }
    </script>
@endsection
