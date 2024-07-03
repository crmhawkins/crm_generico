<div>
    {{-- Filtros --}}
    <div class="filtros row mb-4">
        <div class="col-md-6">
            <div class="flex flex-row justify-start">
                <div class="mr-3">
                    <label for="">Nª por paginas</label>
                    <select wire:model="perPage" class="form-select">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="15">50 por página</option>
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

    @if ( $categorias )

        {{-- Tabla --}}
        <div class="table-responsive">
            <table class="table">
                <thead class="header-table">
                    <tr>
                        <th class="px-3" style="font-size:0.75rem">NOMBRE</th>
                        <th class="" style="font-size:0.75rem">TIPO</th>
                        <th class="text-center" style="font-size:0.75rem">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Recorremos los servicios --}}
                    @foreach ( $categorias as $categoria )
                        <tr>
                            <td class="px-3" style="width: 70%">{{$categoria->name}}</td>
                            <td style="width: 20%">{{$categoria->type}}</td>
                            <td class="flex flex-row justify-evenly align-middle" style="min-width: 120px">
                                <a class="" href="{{route('servicios.show', $categoria->id)}}"><img src="{{asset('assets/icons/eye.svg')}}" alt="Mostrar servicio"></a>
                                <a class="" href="{{route('servicios.edit', $categoria->id)}}"><img src="{{asset('assets/icons/edit.svg')}}" alt="Mostrar servicio"></a>
                                <a class="delete" data-id="{{$categoria->id}}" href=""><img src="{{asset('assets/icons/trash.svg')}}" alt="Mostrar servicio"></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- Si los servicios vienen vacio --}}
            @if( count($categorias) == 0 )
                <div class="text-center py-4">
                    <h3 class="text-center fs-3">No se encontraron registros de <strong>CATEGORIA DE SERVICIOS</strong></h3>
                    <p class="mt-2">Pulse el boton superior para crear algun servicio.</p>
                </div>
            @endif

            {{-- Paginacion --}}
            @if($perPage !== 'all')
                {{ $categorias->links() }}
            @endif
        </div>
    @else
        <div class="text-center py-4">
            <h3 class="text-center fs-3">No se encontraron registros de <strong>CATEGORIA DE SERVICIOS</strong></h3>
            <p class="mt-2">Pulse el boton superior para crear algun servicio.</p>
        </div>
    @endif
    {{-- {{$users}} --}}
</div>
@section('scripts')


    @include('partials.toast')

    <script>
        $(document).ready(() => {
            $('.delete').on('click', function(e) {
                e.preventDefault();
                let id = $(this).data('id'); // Usa $(this) para obtener el atributo data-id
                botonAceptar(id);
            });
        });

        function botonAceptar(id){
            // Salta la alerta para confirmar la eliminacion
            Swal.fire({
                title: "¿Estas seguro que quieres eliminar esta categoria de servicio?",
                html: "<p>Esta acción es irreversible.</p>", // Corrige aquí
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Borrar",
                cancelButtonText: "Cancelar",
                // denyButtonText: `No Borrar`
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    // Llamamos a la funcion para borrar el usuario
                    $.when( getDelete(id) ).then(function( data, textStatus, jqXHR ) {
                        if (data.error) {
                            // Si recibimos algun error
                            Toast.fire({
                                icon: "error",
                                title: data.mensaje
                            })
                        } else {
                            // Todo a ido bien
                            Toast.fire({
                                icon: "success",
                                title: data.mensaje
                            })
                            .then(() => {
                                location.reload()
                            })
                        }
                    });
                }
            });
        }
        function getDelete(id) {
            // Ruta de la peticion
            const url = '{{route("servicios.delete")}}'
            // Peticion
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
