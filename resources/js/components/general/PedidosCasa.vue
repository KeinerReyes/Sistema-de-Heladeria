<template>
    <br>
    <div class="container my-4">
    <div class="bg-white rounded shadow p-4" id="cuerpo">
    <div class="mesas-container d-flex flex-wrap gap-3 justify-content-start">  
          <div
          v-for="(mesa, index) in Mesas"
          :key="index"
          class="mesa-card"
          :class="{ 'libre': mesa.estado === 0, 'ocupada': mesa.estado === 1 }"
          @click="abrirModal(mesa.id)"
        >
          {{ mesa.Nombre }}
        </div>
        </div>
        <br>
        <hr>
       <div class="d-flex justify-content-between">
        <button class="btn btn-success agregar-btn" @click="agregarMesa">Agregar Pager</button>
       <a href="/Domicilio" class="btn btn-primary agregar-btn text-white text-decoration-none">Domicilios</a>

      </div>



    </div>
    
    </div>
    
<div class="modal fade" id="modalMesa" tabindex="-1" aria-labelledby="modalMesaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-custom">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalMesaLabel">Pager seleccionada</h5>
           
          </div>
         <div class="modal-body">
            <p><strong>Pager:</strong> {{ datosMesa.Mesa }}</p>

            <div v-if="datosMesa.descripcion && datosMesa.descripcion.length">
              <div style="max-height: 300px; overflow-y: auto; border: none;">
                <ul class="list-group mb-3">
                <li
                  v-for="(producto, index) in datosMesa.descripcion"
                  :key="index"
                  class="list-group-item"
                >
                  <div class="row text-center align-items-center">
                    <div class="col-6 text-start">
                      {{ producto.Nombre }}
                    </div>
                    <div class="col-3 fw-bold">
                      {{ producto.cantidad }}
                    </div>
                    <div class="col-3">
                      <span class="badge bg-primary rounded-pill w-100">
                        ${{ producto.Precio.toLocaleString() }}
                      </span>
                    </div>
                  </div>
                </li>
              </ul>

              </div>

              <div class="text-end">
                <h5 class="fw-bold">Total: ${{ datosMesa.total.toLocaleString() }}</h5>
              </div>
            </div>

            <div v-else>
              <p class="text-muted">No hay productos cargados.</p>
            </div>
          </div>

         <div class="modal-footer d-flex justify-content-end">
            <div>
              <button type="button" id="Boton1" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-success" @click="liberarMesa(datosMesa.Mesa)">Pagado</button>
            </div>
          </div>

        </div>
      </div>
    </div>

</template>

<script >
 import axios from 'axios';
 import Swal from 'sweetalert2';
  
  export default {
    data() {
      return {
      Mesa: '',
      Mesas: [],
      datosMesa: [],
      };
    },
  methods: {
  agregarMesa() {
    Swal.fire({
      title: '¿Estás seguro?',
      text: "¿Deseas agregar una nueva mesa?",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#353A3F',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, agregar'
    }).then((confirmacion) => {
      if (confirmacion.isConfirmed) {
        Swal.fire({
          title: 'Nombre de la mesa',
          input: 'text',
          inputLabel: 'Ingresa el nombre o número de la nueva mesa:',
          inputPlaceholder: 'Ej: Mesa 13',
          showCancelButton: true,
          confirmButtonColor: '#353A3F',
          cancelButtonColor: '#d33',
          // Aquí se desactiva el autocompletado
          didOpen: () => {
            const input = Swal.getInput();
            if (input) input.setAttribute("autocomplete", "off");
          },
          inputValidator: (value) => {
            if (!value) {
              return '¡Debes escribir algo!';
            }
          }
        }).then((resultado) => {
          const nombreMesa = resultado.value;
          if (nombreMesa) {
            const nuevaMesa = {
              nombre: nombreMesa.trim(),
              estado: 0 
            };
            axios.post('/MesaG', {
              nombre: nuevaMesa.nombre,
            })
            .then((response) => {
              if (response && (response.status === 200 || response.status === 201)) {
                Swal.fire({
                  icon: 'success',
                  title: 'Mesa agregada',
                  text: `La mesa se agregó correctamente.`,
                  confirmButtonColor: '#353A3F'
                }).then(() => {
                  // Refrescar la página o recargar los datos
                  location.reload(); // Esto recarga toda la página
                });
              } else {
                throw new Error('Respuesta inesperada del servidor');
              }
            })
            .catch((error) => {
    
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al agregar la mesa en el servidor.',
                confirmButtonColor: '#d33'
              });
            });
          }
        });
      }
    });
  },
  ListarMesas() {
  axios.get('/ListarMesas')
    .then((response) => {
      const mesas = response.data;

      if (mesas.length === 0) {
        this.Mesas = [];
        Swal.fire({
          icon: 'warning',
          title: 'Sin mesas',
          text: 'No hay mesas registradas actualmente.',
          confirmButtonText: 'Aceptar'
        });
      } else {
        this.Mesas = mesas;
      }
    })
    .catch((error) => {
    
      Swal.fire({
        icon: 'error',
        title: 'Error al cargar mesas',
        text: 'Hubo un problema al obtener las mesas.',
        confirmButtonText: 'Aceptar'
      });
    });
  },
  abrirModal(id) {
    axios.get("/DatosF", {
      params: {
        id: id
      }
    })
    .then(response => {
      this.datosMesa = response.data;

      const modal = new bootstrap.Modal(document.getElementById('modalMesa'));
      modal.show();
    })
    .catch(error => {
      if (error.response && error.response.data?.error === 'Mesa no encontrada') {
        Swal.fire({
          icon: 'info',
          title: 'Mesa disponible',
          text: 'Lo siento, esta mesa no tiene productos. Está disponible para usar.',
          confirmButtonText: 'Aceptar'
        });
      } else if (error.response && error.response.data?.error === 'Factura no encontrada') {
        Swal.fire({
          icon: 'warning',
          title: 'Factura no encontrada',
          text: 'Esta mesa está registrada pero no tiene factura activa.',
          confirmButtonText: 'Aceptar'
        });
      } else {
       
      }
    });
  },
    liberarMesa(Nombre) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Confirmas que el cliente ya ha pagado esta mesa?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
        // Segundo Swal para seleccionar tipo de pago
       Swal.fire({
        title: 'Selecciona el tipo de pago',
        input: 'radio',
        inputOptions: {
            efectivo: 'Efectivo',
            transferencia: 'Transferencia'
        },
        inputValidator: (value) => {
            if (!value) {
            return 'Debes seleccionar un tipo de pago';
            }
        },
        confirmButtonText: 'Confirmar'
        }).then((resultPago) => {
            if (resultPago.isConfirmed) {
            let tipoPago = resultPago.value;

            axios.post('/liberar-mesa', { Nombre: Nombre, tipoPago: tipoPago })
                .then(response => {
                let modalEl = document.getElementById('modalMesa');
                let modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
                this.ListarMesas();
                Swal.fire({
                    icon: 'success',
                    title: 'Mesa liberada',
                    text: `Pago registrado: ${tipoPago.toUpperCase()}`,
                    timer: 2000,
                    showConfirmButton: false,
                    timerProgressBar: true
                });
                })
                .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al liberar la mesa.'
                });
                });
            }
        });
        }
    });
    }






},
    mounted() {
     this.ListarMesas();
    },
  };
  </script>



<style scoped>


.mesas-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 80px;
}

.mesa-card {
  width: 100px;
  height: 100px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: white;
  text-align: center;
}

@media (max-width: 576px) {
  .mesa-card {
    width: 80px;
    height: 80px;
    font-size: 14px;
  }}

.mesa-card:hover {
  transform: scale(1.05);
}

.libre {
  background-color: #28a745;
}

.ocupada {
  background-color: #dc3545;
}

.reservada {
  background-color: #ffc107;
  color: #000;
}
#cuerpo{
    margin-top: -20px;
}
.agregar-btn {
  background-color: #353A3F;
  color: white;
  font-weight: bold;
  padding: 5px 10px;
  border: none;
  border-radius: 8px;
  transition: background-color 0.3s ease;
}

.agregar-btn:hover {
  background-color: #1f2326;
}

#Boton1{

  margin-right: 10px;
}
  
</style>
