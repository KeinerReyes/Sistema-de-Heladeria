<template>
  <br>
  <button 
    onclick="location.href='/PedidosCasa'" 
    style="background-color: #212529; color: white; padding: 10px 20px; margin-left: 10px; border: none; border-radius: 5px; cursor: pointer;">
    Pedidos
</button>

    <br>
    <div class="container mt-1">
    <div class="card shadow">
        <div class="card-header bg-dark text-white fw-bold">
            Gestión de Domicilios
        </div>

        <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr class="fw-bold">
            <th class="text-center">#</th>
            <th class="text-center">Nombre</th>
            <th class="text-center">Telefono</th>
            <th class="text-center">Dirección</th>
            <th class="text-center">Descripción</th>
            <th class="text-center">Precio</th>
            <th class="text-center">Precio Domi</th>
            <th class="text-center">Opciones</th>
             <th class="text-center">Estado</th>
        </tr>

            </thead>
            <tbody>
            <tr v-for="domicilio in domicilios" :key="domicilio.id">
            <td class="text-center">{{ domicilio.id }}</td>
            <td class="text-center">{{ domicilio.Nombre }}</td>
            <td class="text-center">{{ domicilio.Telefono }}</td>
            <td class="text-center">{{ domicilio.DIrrecion }}</td>
            <td class="text-center">
              <button class="btn btn-outline-primary btn-sm" title="Ver detalles"
                      data-toggle="modal" data-target="#exampleModal"
                      @click="SacarDescripcion(domicilio.id)">
                <i class="fas fa-eye"></i>
              </button>
            </td>
            <td class="text-center">${{ domicilio.total.toLocaleString() }}</td>
             <td class="text-center">${{ domicilio.ValorDomi.toLocaleString() }}</td>
            <td class="text-center">
            <button 
                class="btn btn-sm fw-bold text-white me-1" 
                style="background-color: #353A3F;" 
                @click="Entrega(domicilio.id)"
            >
                Entregado
            </button>

            <button 
                class="btn btn-sm fw-bold text-white" 
                style="background-color: #dc3545;" 
                @click="confirmarEliminar(domicilio.id)"
            >
                X
            </button>
        </td>


            <td class="text-center">
                <i v-if="domicilio.Domi == 1" class="bi bi-check-circle-fill text-success" title="Entregado"></i>
                <i v-else class="bi bi-x-circle-fill text-danger" title="No entregado"></i>
            </td>

        </tr>

            </tbody>

        </table>
        </div>
    </div>
    </div>
      <ul class="pagination">
  <li v-if="pagination.current_page > 1">
    <button @click="cambiarPagina(pagination.current_page - 1)">
      Anterior
    </button>
  </li>

  <li v-for="page in pagesNumber" :key="page"
      :class="{ active: page === isActived }">
    <button @click="cambiarPagina(page)">
      {{ page }}
    </button>
  </li>

  <li v-if="pagination.current_page < pagination.last_page">
    <button @click="cambiarPagina(pagination.current_page + 1)">
      Siguiente
    </button>
  </li>
</ul>



  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
        <!-- Contenedor con scroll -->
        <div style="max-height: 400px; overflow-y: auto;">
          <ul class="list-group">
            <li 
              v-for="(item, index) in PorductosDes" 
              :key="index" 
              class="list-group-item"
            >
              <div class="row text-center">
                <div class="col-5 text-start">{{ item.Nombre }}</div>
                <div class="col-3">{{ item.cantidad }}</div>
                <div class="col-4 text-end">
                  <span class="badge bg-primary">${{ item.Precio.toLocaleString() }}</span>
                </div>
              </div>
            </li>
          </ul>
        </div>





        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</template>
  <script>
  import axios from 'axios';
  import Swal from 'sweetalert2';
  
  export default {
    data() {
      return {
      domicilios: [],
      PorductosDes: [],
      pagination: {
                'total'   :   0,
                'current_page'  :0,
                'per_page'   :  0,
                'last_page'   : 0,
                'from'       : 0,
                'to'    : 0,
        },
              offset : 3,
      };
    },
     computed: {
       isActived() {
        return this.pagination.current_page;
        },
        pagesNumber() {
        if (!this.pagination.to) {
            return [];
        }

        let from = this.pagination.current_page - this.offset;
        if (from < 1) {
            from = 1;
        }

        let to = from + (this.offset * 2);
        if (to >= this.pagination.last_page) {
            to = this.pagination.last_page;
        }

        let pagesArray = [];
        while (from <= to) {
            pagesArray.push(from);
            from++;
        }
        return pagesArray;
    },
  },
  
    methods: {
    Domi(pagina = 1) {
      axios.get("/Domis", {
        params: {
          page: pagina
        }
      })
      .then(response => {
        this.domicilios = response.data.resultados;
        this.pagination = response.data.pagination; // Asegúrate de que sea 'pagination'
        this.isActived = this.pagination.current_page;

      
      })
      .catch(error => {
       
      });
    },
    Entrega(id) {
        Swal.fire({
            title: '¿Confirmar entrega?',
            text: "¿Está seguro de que este pedido ha sido entregado correctamente?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, entregado',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Preguntar tipo de pago
                Swal.fire({
                    title: 'Selecciona el tipo de pago',
                    input: 'radio',
                    inputOptions: {
                        efectivo: ' Efectivo',
                        transferencia: 'Transferencia'
                    },
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Debes seleccionar un tipo de pago';
                        }
                    },
                    confirmButtonText: 'Confirmar'
                }).then((pagoResult) => {
                    if (pagoResult.isConfirmed) {
                        axios.post('/Entrega', {
                            id: id,
                            metodo_pago: pagoResult.value // Mandamos el método de pago seleccionado
                        })
                        .then(response => {
                            Swal.fire({
                                title: '✅ Pedido entregado',
                                html: 'El pedido ha sido marcado como entregado.<br><b>Método de pago:</b> ' + (pagoResult.value === 'efectivo' ? 'Efectivo' : 'Transferencia'),
                                icon: 'success',
                                timer: 2500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        })
                        .catch(error => {
                            Swal.fire('Error', 'No se pudo marcar como entregado.', 'error');
                        });
                    }
                });
            }
        });
    },
    SacarDescripcion(id) {
      axios.get("/SacarDescripcion",{
        params: { id: id }
      })
        .then(response => {
          this.PorductosDes = response.data.descripcion;
       
          
        })
        .catch(error => {
        
        });
    },
     confirmarEliminar(id) {
        // SweetAlert2
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esta acción eliminará el registro!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Enviar POST usando fetch o axios
                axios.post('/eliminarCasa', { id: id })
                    .then(response => {
                        Swal.fire(
                            'Eliminado!',
                            'El registro ha sido eliminado.',
                            'success'
                        );
                        location.reload();
                        // Aquí puedes refrescar la lista o eliminar el item del array
                    })
                    .catch(error => {
                        Swal.fire(
                            'Error!',
                            'No se pudo eliminar el registro.',
                            'error'
                        );
                    });
            }
        });
    },
    cambiarPagina(page) {
        this.pagination.current_page = page;
        this.isActived = page;
        this.Domi(page); // <- esto funciona si Domi está definido correctamente
      },
     
    },
    mounted() {
     this.Domi();
    },
  };
  </script>
<style>
style>
    .tabla-moderna {
      border-radius: 20px;
      overflow: hidden;
    }

    .tabla-moderna .card-header {
      border-top-left-radius: 20px;
      border-top-right-radius: 20px;
      font-size: 1.3rem;
    }

    .tabla-moderna .table {
      margin-bottom: 0;
    }

    .tabla-moderna .btn {
      border-radius: 10px;
    }

    @media (max-width: 576px) {
      .tabla-moderna th,
      .tabla-moderna td {
        font-size: 0.9rem;
        padding: 0.5rem;
      }
    }
    
  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    list-style: none;
    padding: 0;
    margin-top: 20px;
    gap: 6px;
    flex-wrap: wrap; /* Permite que los botones bajen a otra línea en pantallas muy pequeñas */
  }

  .pagination li button {
    background-color: #353A3F;
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: normal;
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    min-width: 40px; /* Asegura ancho mínimo para botones */
    white-space: nowrap; /* Evita que el texto de los botones se divida en varias líneas */
  }

  @media (max-width: 400px) {
    .pagination li button {
      padding: 6px 10px;
      font-size: 12px;
      min-width: 30px;
    }
  }

</style>