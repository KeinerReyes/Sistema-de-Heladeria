<template>
    <div class="container">
      <div class="card search-card">
        <div class="card-body">
          <div class="search-container">
            <input
              type="text"
              placeholder="Buscar por Nombre"
              class="search-input"
              v-model="buscar"
              @keyup.enter="listar" 

            />
  
            <div class="notification-icon-container">
              <button @click="abrirModalCumpleaneros" class="notification-button">
                <i class="fas fa-bell"></i>
                <span v-if="cumpleaneros.length > 0" class="notification-count">
                  {{ cumpleaneros.length }}
                </span>
              </button>
            </div>
  
            <div class="boton-gmail-container">
              <button class="btn-gmail" @click="abrirModal">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
                  <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm1.5 0L8 8.5 14.5 4H1.5zM1 5.182l6.5 4.5L1 14.318V5.182zM15 14.318l-6.5-4.5L15 5.182v9.136z"/>
                </svg>
                Enviar Correo
              </button>
            </div>
          </div>
        </div>
      </div>
  
      <!-- Modal de Cumpleañeros -->
      <div class="modal fade" id="modalCumpleaneros" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content shadow-lg border-0" id="modal2">
            <div class="modal-header bg-white text-white">
              <h5 class="modal-title" id="modalCumpleanerosLabel">Clientes Cumpleañeros</h5>
              <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p v-if="cumpleaneros.length === 0" class="text-center">No hay clientes cumpliendo años hoy.</p>
              <ul class="list-group" v-else>
                <li class="list-group-item d-flex justify-content-between align-items-center" v-for="cliente in cumpleaneros" :key="cliente.id_cliente">
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      v-model="seleccionados"
                      :value="cliente.id_cliente"
                      @change="actualizarCorreos(cliente.gmail)"
                    />
                    <label class="form-check-label" :for="'cliente_' + cliente.id_cliente">
                      <strong>{{ cliente.nombre }}</strong><br>
                      <small>{{ cliente.gmail }}</small>
                    </label>
                  </div>
                  <span class="badge bg-secondary">{{ cliente.fecha_nacimiento }}</span>
                </li>
              </ul>
            </div>
  
          </div>
        </div>
      </div>
  
      <!-- Modal para Enviar Correo -->
      <div class="modal fade" id="modalEnviarCorreo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalEnviarCorreoLabel">Enviar Correo</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="cerrarModal"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="enviarCorreo">
                <div class="form-group">
                  <label for="de">De</label>
                  <input type="email" class="form-control" id="de" v-model="correo.de" required>
                </div>
  
                <div class="form-group">
              <label for="destinatarios">Destinatarios Seleccionados</label>
              <ul class="list-group mb-3">
                <li v-for="email in correosSeleccionados" :key="email"  class="list-group-item">
                  {{ email }}
                </li>
              </ul>
            </div>
                
                <div class="form-group">
                  <label for="cuerpo">Redactar Mensaje</label>
                  <textarea class="form-control" id="cuerpo" rows="4" v-model="correo.cuerpo" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Enviar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
  
      <div class="table-wrapper">
      <table class="data-table">
        <thead>
          <tr>
            <th>
              <input
                type="checkbox"
                v-model="seleccionar"
                @change="SeleccionarTodos"
              />
            </th>
            <th>Nombre</th>
            <th>Gmail</th>
            <th>Teléfono</th>
            <th>Documento</th>
            <th>Fecha de Nacimiento</th>
            <th>Crédito</th>
            <th>Estado</th>
            <th>Opciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cliente in clientes" :key="cliente.id_cliente">
            <td>
              <input
                type="checkbox"
                v-model="seleccionados"
                :value="cliente.id_cliente"
                @change="actualizarCorreos(cliente.gmail)"
              />
            </td>
            <td>{{ cliente.nombre }}</td>
            <td>{{ cliente.gmail }}</td>
            <td>{{ cliente.telefono }}</td>
            <td>{{ cliente.documento }}</td>
            <td>{{ cliente.fecha_nacimiento }}</td>
            <td>
              <span v-if="cliente.deuda > 0 ">
                <i class="fas fa-check" style="color: green;"></i> 
              </span>
              <span v-else>
                <i class="fas fa-times" style="color: red;"></i>
              </span>
            </td>
            <td v-if="cliente.mpValores.includes(4)">
                <button 
                    type="button" 
                    class="btn btn-dark" 
                    data-toggle="modal" 
                    data-target="#productosModal"
                    @click="listarProductosApartados(cliente.documento)"
                >
                Producto apartado
                </button>
            </td>
            <td v-else></td>


            <td>
              <button class="btn btn-icon btn-update" @click="abrirModalActualizar(cliente)">
                <i class="fas fa-pencil-alt"></i>
              </button>
              <button class="btn btn-icon btn-delete" @click="eliminarCliente(cliente.id_cliente)">
                <i class="fas fa-trash"></i>
              </button>
              <button class="btn btn-icon btn-update" data-toggle="modal" data-target="#exampleModalCenter" @click="DatosClientes(cliente.id_cliente)">
                <i class="fas fa-dollar-sign"></i>
              </button>
              <button class="btn btn-icon btn-update" data-bs-toggle="modal" data-bs-target="#miModal" @click="SacarDetallesAbono(cliente.documento)">
                <i class="fas fa-table table-icon"></i>
              </button>

            </td>
          </tr>
        </tbody>
      </table>

      </div>
  
      <!-- Modal para actualizar cliente -->
      <div class="modal fade" id="modalActualizar" tabindex="-1" aria-labelledby="modalActualizarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalActualizarLabel">Actualizar Cliente</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="Actualizar(clienteSeleccionado.id_cliente)">
                <div class="mb-3">
                  <label for="nombre" class="form-label">Nombre</label>
                  <input type="text" class="form-control" v-model="clienteSeleccionado.nombre" id="nombre" required />
                </div>
                <div class="mb-3">
                  <label for="gmail" class="form-label">Gmail</label>
                  <input type="email" class="form-control" v-model="clienteSeleccionado.gmail" id="gmail" required />
                </div>
                <div class="mb-3">
                  <label for="telefono" class="form-label">Teléfono</label>
                  <input type="text" class="form-control" v-model="clienteSeleccionado.telefono" id="telefono" required />
                </div>
                <div class="mb-3">
                  <label for="documento" class="form-label">Documento</label>
                  <input type="text" class="form-control" v-model="clienteSeleccionado.documento" id="documento" required />
                </div>
                <div class="mb-3">
                  <label for="documento" class="form-label">Fecha de Nacimiento</label>
                  <input type="text" class="form-control" v-model="clienteSeleccionado.fecha_nacimiento" id="fecha_nacimiento" required />
                </div>
                <button type="submit" class="btn btn-primary">Actualizar</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Formulario de Pago</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
            <div class="modal-body" >
            <p><strong>Nombre del Cliente:</strong>  {{NombreAbono}}</p>
          <p><strong>Estado de Pago:</strong> Debe una suma de {{(formatCurrency(suma_debe))}}</p>
                <div class="input-group mb-3"  id="inputabonar"  >
                  <span class="input-group-text">$</span>
                  <input type="text"  v-model="inputabonar1" class="form-control" aria-label="Amount (to the nearest dollar)" >
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="completo"  class="btn btn-secondary" @click="AbonoTotal(documentoAbono)" >PAGO COMPLETO</button>
            <button type="button" class="btn btn-primary" @click="abonocliente(documentoAbono)" >Abonar</button>
          </div>
        </div>
      </div>
  </div>
      <div class="modal fade" id="productosModal" tabindex="-1" role="dialog" aria-labelledby="productosModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 80%; width: auto;">
            <div class="modal-content" style="background-color: white;">
                <div class="modal-header">
                    <h5 class="modal-title" id="productosModalLabel">Apartado del Cliente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-container" style="display: flex; justify-content: center; overflow-x: auto;">
                        <table class="table table-borderless" style="background-color: transparent; width: 90%; table-layout: fixed;"> 
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Categoría</th>
                                    <th>Marca</th>
                                    <th>Talla</th>
                                    <th>Referencia</th>
                                    <th>Color</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                              <tr v-for=" item  in ProductosApartados" :key="item.codigo">
                                    <td>{{ item.codigo }}</td>
                                    <td>{{ item.categoria }}</td>
                                    <td>{{ item.marca }}</td>
                                    <td>{{ item.talla }}</td>
                                    <td>{{ item.referencia }}</td>
                                    <td>{{ item.color }}</td>
                                    <td>{{ formatCurrency(item.precio) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="footer">
                  <div class="total-card" id="totalCard">
                    <h3>Total a Pagar: {{ formatCurrency(TotalDelApartado)}}</h3>
                  </div>

                  <div class="abono-container" id="abonoContainer">
                    <input type="number" id="abono" v-model="AbonoApartado" placeholder="Ingresa el abono" />
                  </div>

                  <div class="button-container" id="buttonContainer">
                    <button  type="button" class="btn btn-secondary" id="completo" @click="PagarCompletoApartado(documento)">Pagar Completo</button>
                    <button type="button" class="btn btn-dark" id="abonoButton" @click="AbonarProductoApartado(documento)">Abono</button>
                  </div>
                </div>
            </div>
        </div>
    </div>
<!-- Modal -->
<div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content"  id="modalReportes">
      <div class="modal-header">
        <h5 class="modal-title" id="miModalLabel">Detalles De Abonos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-borderless" style="background-color: transparent; table-layout: fixed;"> 
          <thead>
              <tr>
                  <th>Nombre</th>
                  <th>Documento</th>
                  <th>Abonos</th>
                  <th>Pago Un</th>
                  <th>Fecha</th>
               
              </tr>
          </thead>
          <tbody>
            <tr v-for=" item  in Detalles" :key="item.codigo">
                  <td>{{ item.nombre }}</td>
                  <td>{{ item.documento }}</td>
                  <td>{{ formatCurrency(item.total_d) }}</td>
                  <td>
                      <span v-if="item.mp == '1'" :style="{ color: 'black' }">Pago en efectivo</span>
                      <span v-else-if="item.mp == '2'" :style="{ color: 'black' }">Pago en transferencia</span>
                      <span v-else-if="item.mp == '3'" :style="{ color: 'black' }">Credito</span>
                      <span v-else-if="item.mp == '4'" :style="{ color: 'black' }">Credito - con apartado</span>
                      <span v-else-if="item.mp == '5'" :style="{ color: 'black' }">Credito - con apartado</span>
                      <span v-else-if="item.mp == '6'" :style="{ color: 'black' }">Factura - Devolución</span>
                      <span v-else :style="{ color: 'gray' }">Método de pago no disponible</span>
                        </td>
                  <td>{{ formatFecha(item.fecha_de_creacion) }}</td>
              </tr>
          </tbody>
      </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
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
        clientes: [],
        cumpleaneros: [],
        buscar: '',
        seleccionados: [],
        seleccionar: false,
        clienteSeleccionado: {},
        correosSeleccionados: [], 
        inputabonar1:'',
        NombreAbono:'',
        listarCreditos:[],
        documento:'',
        ProductosApartados: [],
        TotalDelApartado: 0,
        suma_debe:'',
        AbonoApartado: 0,
        documentoAbono:'',
        Detalles: [],
        correo: {
          de: 'kairosdmsanmartin@gmail.com',
          correosSeleccionados: [], 
          cuerpo: '',
        },
      };
    },
  
    methods: {
      listar() {
    axios
      .get('/listar', {
        params: {
          ClienteBusqueda: this.buscar
        },
      })
      .then((response) => {
        this.clientes = response.data;
      })
      .catch((error) => {
        console.error('Ocurrió un error al listar los clientes:', error);
      });
  },
  formatFecha(fecha) {
      const date = new Date(fecha);
      const options = {
        day: 'numeric',
        month: 'long',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
      };

      // Formatear la fecha de manera más simple
      return new Intl.DateTimeFormat('es-ES', options).format(date).replace(',', ' a las');
    },

      PagarCompletoApartado(documento) {
        Swal.fire({
          title: '¿Estás seguro de completar el pago?',
          text: "Esta acción confirmará que la persona pagará todo lo que tiene apartado. ¿Deseas continuar?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, pagar todo',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            // Si el usuario confirma, realiza la solicitud
            axios.get('/PagarCompletoApartado', {
              params: {
                documento: documento,
              }
            })
            .then((response) => {
              // Puedes manejar la respuesta aquí, por ejemplo:
              Swal.fire(
                'Pago completado',
                'El pago completo del producto apartado ha sido realizado exitosamente.',
                'success'
              );
            })
            .catch((error) => {
              Swal.fire(
                'Error',
                'Hubo un problema al realizar el pago.',
                'error'
              );
            });
          }
        });
      },
      formatCurrency(value) {
      return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(value);
    },
    SacarDetallesAbono(documento){
      console.log(documento);
      axios
      .get('/SacarDetallesAbono', {
        params: {
          documento: documento
        },
      })
      .then((response) => {
        this.Detalles = response.data;
      })
      .catch((error) => {
       
      });
    },
    AbonarProductoApartado(documento) {
      const debeActual = parseFloat(this.TotalDelApartado); // Convert debeActual to a number
      const Abono = parseFloat(this.AbonoApartado); // Convert Abono to a number
      const debeActualFormatted = this.formatCurrency(debeActual);

      if (Abono > debeActual) {
          Swal.fire({
              icon: 'error',
              title: 'Error',
              text: `No puedes abonar más de lo que debes (${debeActualFormatted}).`,
          });
          return; // Stop execution
      }

      Swal.fire({
        title: '¿Estás seguro de realizar un abono?',
        text: `Vas a realizar un abono de ${this.formatCurrency(this.AbonoApartado)}. ¿Deseas continuar?`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, abonar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          // Realiza la solicitud si el usuario confirma
          axios.get('/AbonarApartado', {
            params: {
              documento: documento,
              Abono: this.AbonoApartado
            }
          })
          .then((response) => {
            // Mensaje de éxito
            Swal.fire(
              'Abono realizado',
              `Se ha realizado un abono de ${this.formatCurrency(this.AbonoApartado)} exitosamente.`,
              'success'
            );
           this.AbonoApartado = '';
          })
          .catch((error) => {
            Swal.fire(
              'Error',
              'Hubo un problema al realizar el abono.',
              'error'
            );
          });
        }
      });
    },

      listarProductosApartados(documento) {
        axios.get('/listarProductosApartados', {
        params: {
          documento: documento,
          }
        })
          .then((response) => {
            this.ProductosApartados = response.data.productos;
            this.TotalDelApartado = response.data.total;
            this.documento = response.data.documento;

          })
          .catch((error) => {

          });
      },
      listarCredito() {
        axios
          .get('/listarCredito')
          .then((response) => {
            this.listarCreditos = response.data;
          })
          .catch((error) => {

          });
      },
      formatCurrency(value) {
            return new Intl.NumberFormat('es-CO', { 
                style: 'currency', 
                currency: 'COP', 
                minimumFractionDigits: 0, 
                maximumFractionDigits: 0 
            }).format(value);
      },
      DatosClientes(id_cliente) {
    axios.get('/datosClientesAbono', {
        params: {
            id_cliente: id_cliente
        }
       })
      .then(response => {
          this.NombreAbono = response.data.nombre;
          this.suma_debe = response.data.suma_debe;
          this.documentoAbono = response.data.documento;

      })
      .catch(error => {
          console.error(error);
      });
      },
      abonocliente(documentoAbono) {
      const debeActual = parseFloat(this.suma_debe); // Convert debeActual to a number
      const Abono = parseFloat(this.inputabonar1); // Convert Abono to a number
      console.log(Abono);
      console.log(debeActual);

    const debeActualFormatted = this.formatCurrency(debeActual);

    if (Abono > debeActual) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: `No puedes abonar más de lo que debes (${debeActualFormatted}).`,
        });
        return; // Stop execution
    }

    // Confirmation dialog
    Swal.fire({
        title: '¿Estás seguro?',
        text: `Vas a abonar ${this.formatCurrency(Abono)} para el cliente con el documento ${documentoAbono}.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, confirmar abono'
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with the axios request if the user confirms
            axios.get('/Abono', {
                params: {
                    documentoAbono: documentoAbono,
                    Abono: this.inputabonar1
                }
            })
            .then(response => {
                // Mostrar mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.data.success,
                });
                this.inputabonar1 = ''; // Limpiar el campo de entrada
            })
            .catch(error => {
                // Manejar errores
                if (error.response && error.response.data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response.data.error,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la solicitud.',
                    });
                }
                console.error(error);
            });
        }
    });
      },
      AbonoTotal(documentoAbono) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Estás seguro de que el cliente va a pagar todo lo que debe?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, estoy seguro',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.get('/AbonoTotal', {
                params: {
                    documentoAbono: documentoAbono,
                }
            })
            .then(response => {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: 'Abono exitoso para el cliente. ¡Gracias!',
                });
            })
            .catch(error => {
                if (error.response && error.response.data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.response.data.error,
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la solicitud.',
                    });
                }
                console.error(error);
            });
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Cancelado',
                text: 'La operación ha sido cancelada.',
            });
        }
    });
      },

      obtenerCumpleaneros() {
        axios.get('/cumple')
          .then(response => {
            this.cumpleaneros = response.data;
          })
          .catch(error => {

          });
      },
  

  
  
  
      SeleccionarTodos() {
        if (this.seleccionar) {
          this.seleccionados = this.clientes.map((cliente) => cliente.id_cliente);
        } else {
          this.seleccionados = [];
        }
        this.actualizarCorreos();
      },
  
      abrirModal() {
        this.actualizarCorreos(); 
        $('#modalEnviarCorreo').modal('show');
      },
  
      cerrarModal() {
        $('#modalEnviarCorreo').modal('hide');
        this.correo.para = [];
        this.seleccionados = [];
      },
  
      enviarCorreo() {
          if (this.correosSeleccionados.length === 0) {  // Validamos si hay destinatarios seleccionados
            Swal.fire({
              icon: 'warning',
              title: 'Atención',
              text: 'Por favor, selecciona al menos un cliente para enviar el correo.',
            });
            return;  // Detenemos el proceso si no hay destinatarios
          }

          // Si hay destinatarios, procedemos con el envío
          axios.post('/enviar-correo', {
            mensaje: this.correo.cuerpo,
            asunto: this.correo.asunto,
            destinatarios: this.correosSeleccionados, 
          })
          .then(response => {
            Swal.fire({
              icon: 'success',
              title: 'Correo enviado',
              text: 'El correo fue enviado con éxito.',
            });
            this.cerrarModal();
          })
          .catch(error => {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Hubo un problema al enviar el correo.',
            });
          });
        },

  
      actualizarCorreos() {
        this.correosSeleccionados = this.clientes
          .filter(cliente => this.seleccionados.includes(cliente.id_cliente))
          .map(cliente => cliente.gmail);
        this.correo.para = this.correosSeleccionados;
      },
  
      abrirModalActualizar(cliente) {
        this.clienteSeleccionado = { ...cliente };
        $('#modalActualizar').modal('show'); 
      },
  
      abrirModalCumpleaneros() {
        if (this.cumpleaneros.length > 0) {
          const modal = new bootstrap.Modal(document.getElementById('modalCumpleaneros'));
          modal.show();
        } else {
          Swal.fire('Sin Cumpleaños', 'No hay clientes cumpliendo años hoy.', 'info');
        }
      },
  
  
      Actualizar(id_cliente) {
        axios.put(`/actualizarCliente/${id_cliente}`, this.clienteSeleccionado)
              .then((response) => {
                  Swal.fire('Actualizado!', 'Cliente actualizado con éxito!', 'success').then(() => {
                      this.listar(); 
                      $('#modalActualizar').modal('hide');
                      location.reload(); 
                  });
              })
              .catch((error) => {

                  Swal.fire('Error!', 'Error al actualizar el cliente!', 'error');
              });
      },
  
  
      eliminarCliente(id_cliente) {
        Swal.fire({
          title: '¿Estás seguro?',
          text: "No podrás revertir esto!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, eliminar!'
        }).then((result) => {
          if (result.isConfirmed) {
            axios
              .delete(`/eliminarCliente/${id_cliente}`)
              .then((response) => {
                Swal.fire('Eliminado!', 'Cliente eliminado con éxito!', 'success');
                this.listar(); 
              })
              .catch((error) => {

                Swal.fire('Error!', 'Error al eliminar el cliente!', 'error');
              });
          }
        });
      },
    },
  
    mounted() {
      this.listar();
      this.SacarDetallesAbono();
      this.obtenerCumpleaneros();
      this.DatosClientes();
      this.listarCredito();
      this.listarProductosApartados();
    },
  };
  </script>
  
  
  <style scoped>
  
  .modal-content {
      border-radius: 1rem; /* Bordes redondeados */
  }
  
  .modal-header {
      border-bottom: none; /* Sin borde en el header */
  }
  
  .list-group-item {
      transition: background-color 0.3s;
      border: none; /* Efecto de transición */
  }
  .list-group {
    max-height: 500px; 
    overflow-y: auto;  

  }
  
  .list-group-item:hover {
      background-color: #ffffff; /* Color de fondo al pasar el ratón */
  }
  
  .modal-footer {
      border-top: none; /* Sin borde en el footer */
  }
  
  .btn-primary {
      background-color: #007bff; /* Color del botón */
      border: none; /* Sin borde */
  }
  
  .btn-primary:hover {
      background-color: #0056b3; /* Color del botón al pasar el ratón */
  }
  
  .notification-icon-container {
    position: relative; 
    margin-right: -700px; 

  }
  
  .notification-button {
    background: transparent; 
    border: none; 
    cursor: pointer; 
    font-size: 28px;
    position: relative;
  }
  
  .notification-button:hover {
    color: #0099ff; /* Cambia el color al pasar el ratón */
  }
  
  .notification-count {
    position: absolute;
    top: -5px;
    right: -10px;
    background-color: red; /* Color de fondo del contador */
    color: white; /* Color del texto del contador */
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 12px;
  }
  
  .btn-gmail {
    background-color: #f44336; /* Color de fondo del botón */
    color: white; /* Color del texto del botón */
    border: none; /* Sin borde */
    padding: 10px 15px; /* Espaciado interno */
    border-radius: 5px; /* Bordes redondeados */
    cursor: pointer; /* Cambia el cursor al pasar el ratón */
  }
  
  .btn-gmail:hover {
    background-color: #d32f2f; /* Color más oscuro al pasar el ratón */
  }
  
  
  .custom-input {
    border-radius: 0.25rem; /* Ajusta el radio de borde */
  }
  
  .input-group-text {
    background-color: #f8f9fa; /* Color de fondo para el contenedor del icono */
    border: 1px solid #ced4da; /* Borde del icono */
  }
  .modal-content {
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  }
  
  .modal-header {
    background-color: #f7f7f7;
    border-bottom: 2px solid #ffffff; /* Color de Gmail */
  }
  
  .modal-title {
    font-weight: bold;
    color: #333;
  }
  
  .close {
    color: #0c0c0c; /* Color de Gmail */
  }
  
  .modal-body {
    padding: 20px;
  }
  
  .form-group label {
    font-weight: bold;
    color: #000000;
  }
  
  .form-control {
    border-radius: 4px;
    transition: border-color 0.3s;
  }
  
  .form-control:focus {
    border-color: #c13526; /* Color más oscuro al enfocar */
    box-shadow: 0 0 5px rgba(219, 68, 55, 0.5);
  }
  
  .btn-primary {
    background-color: #db4437; /* Color de Gmail */
    border: none;
    transition: background-color 0.3s;
  }
  
  .btn-primary:hover {
    background-color: #c13526; /* Color más oscuro al pasar el mouse */
  }
  
  .boton-gmail-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 10px 0; /* Ajusta el margen según sea necesario */
  }
  
  .btn-gmail {
    background-color: #db4437; /* Color rojo de Gmail */
    color: white;
    border: none;
    border-radius: 4px;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s; /* Transiciones suaves */
  }
  
  .btn-gmail svg {
    margin-right: 8px; /* Espaciado entre el icono y el texto */
  }
  
  .btn-gmail:hover {
    background-color: #c13526; /* Color más oscuro al pasar el mouse */
    transform: scale(1.05); /* Efecto de escala al pasar el mouse */
  }
  
  .btn-gmail:focus {
    outline: none; /* Elimina el contorno por defecto */
  }
  
  .table {
    color: #333;
  }
  .table thead {
    background-color: #ffffff;
    color: #fff;
  }
  .table tbody tr:hover {
    background-color: #dbdbdb;
  }
  .modal-header {
    background-color: #ffffff;
    color: #fff;
  }
  .modal-footer {
    background-color: #f9f9f9;
  }
  
  .container {
    width: 100%;
    max-width: 1500px;
    margin: 0 auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  
  .card {
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
  }
  
  .card-body {
    padding: 20px;
  }
  
  .search-card {
    border: 1px solid #e0e0e0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .search-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .search-input {
    width: 250px;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    background-color: #f9f9f9;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
  }
  
  
  .table-wrapper {
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #fff;
    max-height: 900px; 
    overflow-x: auto; 
  }
  
  .data-table {
    width: 100%;
    border-collapse: collapse;
  }
  
  .data-table th,
  .data-table td {
    padding: 14px;
    text-align: center;
    border-bottom: 1px solid #e0e0e0;
    color: #000;
  }
  
  .data-table th {
    background-color: #f5f5f5;
    font-weight: bold;
  }
  
  .data-table tr:nth-child(even) {
    background-color: #f9f9f9;
  }
  
  .data-table tr:hover {
    background-color: #f5f5f5;
  }
  
  .modal-content {
    border-radius: 10px;
    background-color: #f8f9fa;
  
  }

  #modal2{
    margin-top: -300px;
  }
  
  .modal-header {
    border-bottom: 1px solid #ddd;
  }
  
  .modal-title {
    color: #333;
    font-weight: bold;
  }
  
  .modal-body {
    padding: 20px;
    background: white;
  }
  
  .modal-footer {
    border-top: 1px solid #ddd;
  }
  
  .btn-secondary {
    background-color: #6c757d;
    border: none;
  }
  
  .btn-primary {
    background-color: #333;
    border: none;
  }
  th, td {
    text-align: center;
}
 /* Footer Styling */
#totalCard {
  background-color: grey; 
  color: white;
  padding: 10px;
  border-radius: 4px;
  width: 300px;
  text-align: center;
}

#totalCard h3 {
  margin: 0;
  font-size: 18px;
}

#totalCard span {
  font-size: 22px;
  font-weight: bold;
}

/* Abono Container and Input Styling */
#abonoContainer input {
  padding: 10px;
  font-size: 16px;
  width: 250px;
  border-radius: 8px;
  margin-left: -340px;
}

/* Button Container Styling */
#buttonContainer {
  display: flex;
  gap: 15px;
}

/* Common Button Styling */
button {                       
  padding: 10px 20px;
  font-size: 16px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  background-color: white;
  border-radius: 8px;
}
#modalReportes {
  border: 1px solid red;
    width: 800px; /* Ajusta el ancho según lo que necesites, por ejemplo, 80% del ancho de la ventana */
    max-width: 1200px; /* Establece un límite máximo de ancho */
    height: auto; /* Permite que la altura se ajuste automáticamente */
    max-height: 900px; /* Limita la altura máxima a 900px */
    overflow-y: auto; /* Habilita el scroll vertical si el contenido excede la altura máxima */
    margin-left: -25%;
    background-color: white; /* Opcional: fondo blanco */
    padding: 20px; /* Opcional: agrega un poco de espacio interior */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Opcional: agrega sombra */
    border-radius: 8px; 
}


  </style>