<template>
<br>
<div id="cabeza">
    <div id="Cuerpo"> 
        <div class="Busqueda">
            <div class="input-group mb-6">
                <div class="input-group-prepend">
                    <span class="input-group-text" id="re">
                         <button style="border: none; background: none;"  >
                        <i class="fas fa-search" ></i>
                        </button>
                    </span>
                </div>
                  <input v-if="busqueda == 0"  type="text" class="form-control" id="buscar1111"  @keyup.enter="AgregarProducto"  placeholder="Buscar..." aria-label="Buscar" v-model="BuscarCodigo" aria-describedby="search-icon" autocomplete="off">
                  <input v-if="busqueda == 1" type="text" class="form-control" id="buscar1111"  @keyup.enter="AgregarProductoLocion"  placeholder="Busca por código de loción" aria-label="Buscar" v-model="BuscarCodigoPerfumen" aria-describedby="search-icon" autocomplete="off">
            </div>    
            <div class="ActivarCodigo">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="activarCodigo" @click="busqueda = busqueda === 0 ? 1 : 0">
                    <label class="form-check-label" for="activarCodigo">Lociones</label>
                </div>
            </div>
        </div>

        <div id="BotonBusqueda">
            <button class="btn-enviar" @click="AgregarProducto" v-if="busqueda == 0">
                <i class="fas fa-tshirt"></i> Agregar a la factura
            </button>
            <button class="btn-enviar" @click="AgregarProductoLocion" v-if="busqueda == 1">
                <i class="fas fa-tshirt"></i> Agregar a la factura
            </button>
        </div>
        <br>
        <div class="table-responsive mailbox-messages">
              <div style="overflow-x: auto;">
                <table  class="table table-hover table-striped" id="factura-para-imprimir">
                    <thead>
                        <tr>
                        <th>Codigo</th>
                        <th>Categoria</th>
                        <th>Marca</th>
                        <th>Talla</th>
                        <th>Referencia</th>
                        <th>Precio</th>
                        <th  class="eliminar-th">Fecha De Compra</th>
                        <th class="eliminar-th">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                         <tr v-for="Producto in AgregarPruducto" :key="Producto.id_productos">
                            <td class="col-1 text-center">{{ Producto.codigo }}</td>
                            <td class="col-1">{{ Producto.categoria }}</td>
                            <td class="col-2">{{ Producto.marca }}</td>
                            <td class="col-1">{{ Producto.talla }}</td>
                            <td class="col-1">{{ Producto.referencia }}</td>
                            <td class="col-2">{{ formatPrecio(Producto.precio) }}</td>
                            <td class="col-2 text-center eliminar-td" >{{ obtenerFechaEnFormato() }}</td>
                           <td class="col-1 text-center eliminar-td">
                                <button class="btn btn-danger" @click="EliminarProducto(Producto.codigo)">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    
                        <div class="total-row" id="TotalDefacturatabla" v-if="MostrasTotalF === 1">
                            <tr>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111">Descuento</td>
                            <td class="tabla1112" v-if="descuentoPorcentaje !== null && descuentoPorcentaje > 0">
                                {{ formatCurrency(descuentoPorcentaje) }}
                            </td>
                            <td class="tabla1112" v-else-if="descuentoValor !== null && descuentoValor > 0">
                                {{ formatCurrency(descuentoValor) }}
                            </td>
                            <td class="tabla1112" v-else>
                                $0
                            </td>


                            </tr>
                            <tr>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111"></td>
                            <td class="tabla111">Total</td>
                            <td class="tabla1112">{{ formatCurrency(total) }}</td>
                            </tr>

                        </div>
                        
                    </tbody>
                </table>
               
            </div>
         </div>
         <hr>
        <div id="piedepagina">
           <div id="BotonBusquedaEliminar">
            <button class="btn btn-danger" @click="VaciarLista">
                <i class="fas fa-trash"></i> Cancelar Factura
            </button>
            <button class="btn btn-dark"  @click="Modal2">
                <i class="fas fa-percent"></i> Descuento
            </button>
            <div class="btn-group">
                <select class="form-select btn btn-dark" style="width: auto;" v-model="mp">
                    <option value="" disabled selected>Métodos de pago</option>
                    <option value="1">Pago en efectivo</option>
                    <option value="2">Pago en transferencia</option>
                    <option value="3">Crédito</option>
                    <option value="4">Apartado</option>
                    <option value="6">Devolución</option>
                </select>
            </div>
            <input v-if="mp == 3 || mp == 4" type="number" id="abono" class="form-control" v-model="abono" placeholder="Ingrese abono">
            <input v-if="mp == 6 " type="number" id="abono" class="form-control" v-model="devolucion" placeholder="Ingrese excedente">
        </div>
         

        <div id="TotalF">
            <div id="moverTotal">
            <p>Total <span class="amount">{{ formatCurrency(total)  }}</span></p>
            </div>
        </div>

        </div> 
    </div>

    <div id="cuerpo2">
      <div id="CuerpoClinetes">
         <h4 class="titulo"> Datos de Venta <i class="fas fa-shopping-cart"></i></h4>
         <hr class="hr">
        <div id="ClientesC">
          <h6 style="font-weight: bold; font-size: 1.1rem;" class="Clientes2424">Cliente</h6>
            <div class="input-icon-container">
                <input type="text" id="inpudClientes" readonly :value="getNombre()"> 
               <button class="btn-icon" data-toggle="modal" data-target="#exampleModal">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <br>
            <div id="containesDeClientes">
                <div class="client-info">
                    <h6 class="Clientes2424">Comprobante</h6>
                    <input type="text" id="inpudClientes1" readonly value="Ticket">
                </div>
                <div class="client-info">
                    <h6 class="Clientes2424">Orden de servicio</h6>
                    <input type="text" id="inpudClientes2"  v-model="orden" readonly>
                </div>
            </div>
         </div>
      </div>

      <div id="Calcular">
         <h4 class="titulo">Realiza venta <i class="fas fa-cart-plus"></i> </h4>
         <hr class="hr">
          <div id="ClientesC">
            <div class="input-icon-container" id="TotalDeVentas">
               <input type="text" id="inpudClientes" :value="formatCurrency(total)" readonly style="text-align: center; font-weight: bold;" />
            </div>
            <br>
           <div id="containesDeClientes">
                <div class="client-info11">
                    <h6 class="Clientes24241">Cantidad</h6>
                    <input type="text" id="InpudDeVentas" v-model="Cantidad" autocomplete="off">
                </div>
                <div class="client-info">
                    <h6 class="Clientes2424122">Cambio</h6>
                    <input type="text" id="InpudDeVentas1" readonly   :value="formatCurrency(TOTALDEPAGO)"   >
                </div>
            </div>

            <div class="button-container">
                  <button class="btn-blue" @click="SacarCambio">
                    <i class="fas fa-check"></i> Calcular
                </button>
                <button class="btn-green" @click="Fctura">
                    <i class="fas fa-check"></i> Realiza factura
                </button>
              
            </div>

         </div>
      </div>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" id="MODAL1">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn btn-primary d-flex align-items-center" style="font-size: 1rem; background-color: #1f2124; border-color: #1f2124;" @click="openModal">
                    <i class="fas fa-user-plus" style="margin-right: 8px;"></i> Agregar Cliente
                </button>
               <button type="button" class="btn btn-successd-flex align-items-center" style="font-size: 1rem;" @click="openModal2" v-if="Modal === 1">
                   <i class="fas fa-search" style="color: black; font-size: 1.6rem; margin-right: 10px;"></i>
                </button>
                 <div class="input-group"  id="InpuDeBusqueda" v-if="Modal === 0" >
                    <input type="text" class="form-control"  placeholder="Buscar cliente por documento" aria-label="Buscar cliente por documento" v-model="BusquedaDocumentoC"  />
                <button  @click="MostraUsuarios" id="Bontondebusqueda" style="background: none; ">
                    <span class="input-group-text1">
                        <i class="fas fa-search"></i>
                    </span>
                </button>   
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           <div class="modal-body">

            <div v-if="Modal === 0" class="modal-container">
              <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">Op</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Documento</th>
                        <th scope="col">Telefono</th>
                        <th scope="col">Gmail</th>
                        <th scope="col">Fecha de naciemiento</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">    
                        <tr  v-for="Usuario in Usuarios" :key="Usuario.id_cliente">
                        <th scope="row">
                            <button class="btn-invoice" @click="RalizarFactura(Usuario.documento)">
                                <i class="fas fa-file-invoice"></i>
                            </button>
                        </th>
                        <td>{{Usuario.nombre}}</td>
                        <td>{{Usuario.documento}}</td>
                        <td>{{Usuario.telefono}}</td>
                        <td>{{Usuario.gmail}}</td>
                        <td>{{formatDate(Usuario.fecha_de_creacion)}}</td>
                        </tr>
                    </tbody>
                    </table>
                     <div v-if="noClienteFound" class="alert alert-warning mt-3 custom-alert">
                      No se encontró el documento del cliente.
                    </div>
            </div>

            <div v-if="Modal === 1" id="conten">
             <form @submit.prevent="submitForm">
                <div>
                    <label for="nombre">Nombre del Cliente</label>
                    <input type="text" id="nombre" v-model="nombre"  placeholder="Ingresa el nombre del cliente"  autocomplete="off" />
                </div>

                <div>
                    <label for="telefono">Número de Teléfono</label>
                    <input type="tel" id="telefono" v-model="telefono"  placeholder="Ingresa el número de teléfono"  autocomplete="off" />
                </div>

                <div>
                    <label for="gmail">Gmail</label>
                    <input type="email" id="gmail" v-model="gmail"  placeholder="Ingresa el Gmail"  autocomplete="off" />
                </div>

                <div>
                    <label for="fechaNacimiento">Fecha de Nacimiento</label>
                    <input type="date" id="fechaNacimiento" v-model="fechaNacimiento"    autocomplete="off"/>
                </div>

                <div>
                    <label for="documento">Documento</label>
                    <input type="text" id="documento" v-model="documento"  placeholder="Ingresa el documento"  autocomplete="off"/>
                </div>

               <button type="submit" class="btn-success" @click="GuardarDatosClientes">Guardar y Generar Factura</button>
            </form>
  
         </div>

            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<!-- Modal -->
<!-- Modal -->
<div class="modal fade" id="descuentoModal" tabindex="-1" aria-labelledby="descuentoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm"> <!-- Puedes usar modal-sm para un modal pequeño -->
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title d-flex align-items-center" id="descuentoModalLabel">
          <i class="fas fa-percent me-2"></i> Descuento
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
    <!-- Descuento por porcentaje -->
            <div class="input-container d-flex align-items-center"  id="c1">
                <div class="input-group" id="ii">
                <i class="fas fa-percentage"></i>
                <input type="text" class="custom-input" id="input1"  placeholder="Ingrese porcentaje" v-model="descuento"/>
            </div>
            </div>
            <!-- Descuento personalizado -->
            <div class="input-container d-flex align-items-center" id="c2">
                <div class="input-group" id="i">
                    <i class="fas fa-tag fa-2x"></i> 
                    <input type="text" class="custom-input" id="input2" placeholder="Ingrese descuento" v-model="descuentoper" />
                </div>
            </div>

        </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-dark" @click="RealizarDescuento">Realizar Descuento</button>
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
    Modal: 0,
    devolucion: 0,
    BuscarCodigoPerfumen: '',
    busqueda: 0,
    orden: 0,
    estado: 0,
    MostrasTotalF: 0,
    BuscarCodigo: '',
    Productos: [],
    AgregarPruducto: [],
    total:'',
    Cantidad: 0, 
    TOTALDEPAGO: 0,
    nombre: '',
    telefono: '',
    gmail: '',
    fechaNacimiento: '',
    documento: '',
    Usuarios:[],
    BusquedaDocumentoC:'',
    DatosClientes:[],
    DatosClientesNuevos:[],
    noClienteFound: false,
    totalFormatted: 0,
    EstadoConSIste: 0,
    descuento: '',
    descuentoper: '',
    mlsRestar: 0,
    preciorestar: 0,
    descuentoPorcentaje: null, 
    descuentoValor: null,
    mp:'',
    abono: 0,
    };
  },
methods: {
    AgregarProductoLocion(){

    },
    formatCurrency(amount) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0, // Sin decimales
                maximumFractionDigits: 0  // Sin decimales
            }).format(amount);
    },
    Modal2() {
        const modalElement = document.getElementById('descuentoModal');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    },
    openModal() {
        this.Modal = 1;
    },
    openModal2(){
        this.Modal = 0;
    },
    BuscarCodigoRopa() {
    return axios.get('/BusquedaF', {
        params: {
        codigo: this.BuscarCodigo
        }
    })
    .then(response => {
        if (Array.isArray(response.data)) {
        return response.data;
        } else if (response.data && Object.keys(response.data).length === 0) {
        return []; 
        } else {
        return [response.data];
        }
    })
    .catch(error => {
        throw error;
    });
    },
    BuscarCodigoLocion() {
    return axios.get('/BuscarCodigoLocion', {
        params: {
            Perfume: this.BuscarCodigoPerfumen
        }
    })
    .then(response => {
        if (Array.isArray(response.data)) {
        return response.data;
        } else if (response.data && Object.keys(response.data).length === 0) {
        return []; 
        } else {
        return [response.data];
        }
    })
    .catch(error => {
        throw error;
    });
    },
    async AgregarProductoLocion() {
    if (this.BuscarCodigoPerfumen) {
        try {
            const productosEncontrados = await this.BuscarCodigoLocion();
            console.log(productosEncontrados);

            if (!productosEncontrados || productosEncontrados.length === 0 || productosEncontrados == "" ) {
                Swal.fire({
                    icon: 'error',
                    title: 'Producto no encontrado',
                    text: `No se encontró ningún producto con el código ${this.BuscarCodigo}.`,
                });
            } else {
                for (const producto of productosEncontrados) {
                    const productoYaEnLista = this.AgregarPruducto.some(p => p.codigo === producto.codigo);

                    if (productoYaEnLista) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Producto ya en la lista',
                            text: `El producto con código ${producto.codigo} ya está en la lista.`,
                        });
                    } else {
                        // Mostrar confirmación con opciones de precio y talla editables
                        const result = await Swal.fire({
                            title: 'Agregar producto',
                            icon: 'question',
                            html: `
                                <ul style="text-align: left;">
                                    <li><strong>Código:</strong> ${producto.codigo}</li>
                                    <li><strong>Nombre:</strong> ${producto.categoria}</li>
                                    <li><strong>Precio:</strong> $${producto.precio}</li>
                                    <li><strong>mls:</strong> ${producto.talla}</li>
                                </ul>
                                <div style="display: flex; flex-direction: column; gap: 8px;">
                                    <label>Nueva cantidad en mililitros </label>
                                    <input id="talla" v-model="mlsRestar" class="swal2-input" type="text" placeholder="Nueva cantidad en mililitros" value="${producto.talla}" style="width: 60%;" autocomplete="off">
                                    <label>Nuevo precio</label>
                                    <input id="precio" v-model="preciorestar" class="swal2-input" type="text" placeholder="Precio" value="${producto.precio}" style="width: 60%;" autocomplete="off">
                                </div>
                            `,
                            showCancelButton: true,
                            cancelButtonText: 'Cancelar',
                            confirmButtonText: 'Confirmar',
                            preConfirm: () => {
                                const precio = document.getElementById('precio').value;
                                const talla = document.getElementById('talla').value;
                                
                                if (precio && isNaN(precio)) {
                                    Swal.showValidationMessage('Por favor ingresa un precio válido');
                                } else if (precio && parseFloat(precio) > producto.precio) {
                                    Swal.showValidationMessage('El nuevo precio no puede ser mayor al precio actual');
                                } else if (talla && parseFloat(talla) > producto.talla) {
                                    Swal.showValidationMessage('La cantidad en mililitros no puede ser mayor a la cantidad actual');
                                } else {
                                    
                                    this.mlsRestar = talla ? parseFloat(talla) : this.mlsRestar;
                                    this.preciorestar = precio ? parseFloat(precio) : this.preciorestar;

                                    return {
                                        precio: precio ? parseFloat(precio) : this.preciorestar,
                                        talla: talla ? parseFloat(talla) : this.mlsRestar
                                    };
                                }
                            }
                        });

                        if (result.isConfirmed) {
                            const { precio, talla } = result.value;
                            const productoConPrecioTalla = { ...producto, precio, talla };
                            this.AgregarPruducto.push(productoConPrecioTalla);
                            this.SacarTotal();
                        }
                    }
                }
            }
        } catch (error) {
            console.error(error);
        }
    }
    },
    async AgregarProducto() {
    if (this.BuscarCodigo) {
        try {
        const productosEncontrados = await this.BuscarCodigoRopa();
        
        if (productosEncontrados.length === 0) {
            Swal.fire({
            icon: 'error',
            title: 'Producto no encontrado',
            text: `No se encontró ningún producto con el código ${this.BuscarCodigo}.`,
            });
        } else {
            productosEncontrados.forEach(producto => {
            const productoYaEnLista = this.AgregarPruducto.some(p => p.codigo === producto.codigo);
            
            if (productoYaEnLista) {
                Swal.fire({
                icon: 'info',
                title: 'Producto ya en la lista',
                text: `El producto con código ${producto.codigo} ya está en la lista.`,
                });
            } else {
                this.AgregarPruducto.push(producto);
            }
            });
         
            this.SacarTotal();
        }
        } catch (error) {
      
        }
    }
    },
    obtenerFechaEnFormato() {
        const ahora = new Date();
        const opciones = { day: 'numeric', month: 'long' };
        const formatoFecha = new Intl.DateTimeFormat('es-ES', opciones).format(ahora);
        return formatoFecha;
    },
    EliminarProducto(codigo) {

        const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-danger btn-margin',
            cancelButton: 'btn btn-secondary btn-margin'
        },
        buttonsStyling: false
        });


        swalWithBootstrapButtons.fire({
        title: 'Eliminar Producto',
        text: 'Esta acción eliminará permanentemente el producto seleccionado. ¿Deseas continuar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
        }).then((result) => {
        if (result.isConfirmed) {
        const index = this.AgregarPruducto.findIndex(producto => producto.codigo === codigo);
        
        if (index !== -1) {
            this.AgregarPruducto.splice(index, 1);
            
            swalWithBootstrapButtons.fire({
            title: 'Eliminación Exitosa',
            text: 'El producto seleccionado ha sido eliminado correctamente.',
            icon: 'success'
            });
            this.SacarTotal();

        } else {
            swalWithBootstrapButtons.fire({
            title: 'Producto no encontrado',
            text: 'No se encontró el producto con el código proporcionado en la lista.',
            icon: 'error'
            });
        }
        }
    });
    },
    VaciarLista() {
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
            confirmButton: 'btn btn-danger btn-margin',
            cancelButton: 'btn btn-secondary btn-margin'
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: 'Vaciar Lista de Productos',
            text: 'Esta acción eliminará todos los productos de la lista. ¿Deseas continuar?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Vaciar Lista',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
            this.AgregarPruducto = [];
            this.SacarTotal();
            swalWithBootstrapButtons.fire({
                title: 'Lista Vacía',
                text: 'Todos los productos han sido eliminados de la lista.',
                icon: 'success'
            });

         
            }
        });
    },
    SacarTotal() {
                if (this.AgregarPruducto.length === 0) {
                    this.total = 0;
                    this.totalFormatted = new Intl.NumberFormat('es-CO', { 
                        style: 'currency', 
                        currency: 'COP', 
                        minimumFractionDigits: 0, 
                        maximumFractionDigits: 0  
                    }).format(0);

                } else {
                    console.log( this.AgregarPruducto);
                    const totalSinFormato = this.AgregarPruducto.reduce((acumulador, producto) => {

                    
                        const nuevoAcumulador = acumulador + (producto.precio || 0);

                        return nuevoAcumulador;
                    }, 0);
                    this.total = totalSinFormato;
                    this.totalFormatted = new Intl.NumberFormat('es-CO', { 
                        style: 'currency', 
                        currency: 'COP', 
                        minimumFractionDigits: 0, 
                        maximumFractionDigits: 0  
                    }).format(totalSinFormato);

              
                }
    },
    formatPrecio(precio) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(precio);
    },
    formatCurrency(value) {
            return new Intl.NumberFormat('es-CO', { 
                style: 'currency', 
                currency: 'COP', 
                minimumFractionDigits: 0, 
                maximumFractionDigits: 0 
            }).format(value);
    },
    SacarCambio() {
            const cantidad = parseFloat(this.Cantidad);
            const total = parseFloat(this.total);
            this.TOTALDEPAGO = cantidad - total;
             if (this.TOTALDEPAGO > 0) {
                this.estado = 1; 
            } else {
                this.estado = 0; 
            }
    },
    GuardarDatosClientes() {
    axios.post('GuardasClientes', {
        'nombre': this.nombre,
        'telefono': this.telefono,
        'gmail': this.gmail,
        'fechaNacimiento': this.fechaNacimiento,
        'documento': this.documento
    })
    
    .then(response => {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'Servicio guardado correctamente.'
        });
        this.RealizaFacturaCreacionClientes();
        this.nombre = '';
        this.telefono = '';
        this.gmail = '';
        this.fechaNacimiento = '';
        this.documento = '';
    })
    .catch(error => {

        const errors = error.response.data.errors;
        if (errors) {
            if (errors.nombre) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo Nombre',
                });
            }
            if (errors.telefono) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo Teléfono',
                });
            }
            if (errors.documento) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo Documento',
                });
            }
            if (errors.fechaNacimiento) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo Fecha de Nacimiento',
                });
            }
            if (errors.gmail) {
                Swal.fire({
                    icon: 'error',
                    title: 'Campo Gmail',
                });
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al guardar el servicio.'
            });
        }
    });
    },
    MostraUsuarios() {
        axios.get('/ListraUsuarios', {
        params: {
            BusquedaDocumentoC: this.BusquedaDocumentoC
        }
        })
        .then((response) => {
        this.Usuarios = response.data;
        
        if (this.Usuarios.length > 0) {
            this.noClienteFound = false; 
        } else {
            Swal.fire({
            icon: 'error',
            title: 'Cliente no encontrado',
            text: `No se encontró un cliente con el documento ${this.BusquedaDocumentoC}.`,
            confirmButtonText: 'Aceptar'
            });
            this.noClienteFound = true; 
        }
        })
        .catch((error) => {
   
        });
    },
    RalizarFactura(documento) {
        let me = this;
        axios.get('/RalizarFacturaBusqueda', {
            params: {
                documento: documento
            }
        })
        .then(function (response) {
            me.DatosClientes = response.data;
            
        })
        .catch(function (error) {
        
        });
    },
    RealizaFacturaCreacionClientes(){
     let me = this;
      axios.get('/RalizarFacturaBusqueda', {
            params: {
                documento: this.documento
            }
        })
        .then(function (response) {
            me.DatosClientes = response.data;
            
        })
        .catch(function (error) {
       
        });
    },
    getNombre() {
    
    return this.DatosClientes.length > 0 ? this.DatosClientes[0].nombre : '';
    },
    Fctura() {
    // Verificación de productos
    if (!this.AgregarPruducto || this.AgregarPruducto.length === 0) {
        Swal.fire({
            title: "Error",
            text: "Debe agregar al menos un producto.",
            icon: "error"
        });
        return;
    }
    if (!this.mp) { // Ensure you're checking the correct property
            Swal.fire({
                title: "Error",
                text: "Debe seleccionar un método de pago.",
                icon: "error"
            });
            return; // Exit the method if validation fails
        }
    
    // Verificación de datos del cliente
    if (!this.DatosClientes || Object.keys(this.DatosClientes).length === 0) {
        Swal.fire({
            title: "Error",
            text: "Debe proporcionar los datos del cliente.",
            icon: "error"
        });
        return;
    }

    // Verificación del total
    if (this.total < 0) {
    Swal.fire({
        title: "Error",
        text: "El total debe ser mayor o igual a cero.",
        icon: "error"
    });
    return;
}


    
    // Mostrar alerta de éxito
    Swal.fire({
        title: "Factura Guardada",
        html: `
        <p>La factura se ha guardado correctamente. <br>
        ¿Desea vaciar los datos?</p>
        <div class="checkbox-container">
            <input type="checkbox" id="creditoCheckbox" class="large-checkbox" /> 
            <label for="creditoCheckbox">Usar crédito disponible con Sistecrédito</label>
        </div>
        <style>
            .checkbox-container {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 10px;
                margin-top: 10px;
            }

            .large-checkbox {
                width: 20px;
                height: 20px;
            }

            label {
                font-size: 16px;
            }
        </style>
        `,
        icon: "success",
        showCancelButton: true,
        confirmButtonText: 'Confirmar Factura Exitosa',
        cancelButtonText: 'Cancelar Factura'
        }).then((result) => {
            if (result.isConfirmed) {
                const usarCredito = document.getElementById('creditoCheckbox').checked ? 1 : 0;
                console.log(this.mlsRestar,this.preciorestar);
            axios.post('/Factura', {
                DatosClientes: this.DatosClientes,
                devolucion: this.devolucion,
                mlsRestar: this.mlsRestar,
                preciorestar: this.preciorestar,
                AgregarPruducto: this.AgregarPruducto,
                Total: this.total,
                descuentoPorcentaje: this.descuentoPorcentaje, 
                descuentoValor: this.descuentoValor,
                mp: this.mp,
                abono: this.abono,
                EstadoConSIste: usarCredito // Asegúrate de que `EstadoConSIste` esté definido
            })
            .then((response) => {
                this.Usuarios = response.data;
                this.ImprimirFactura(); 

                window.onafterprint = function() {
                    window.location.reload(); // Recargar la página si se imprimió correctamente
                };
            })
            .catch((error) => {
           
                Swal.fire({
                    title: "Error al Guardar",
                    text: "Hubo un problema al guardar la factura. Inténtelo de nuevo más tarde.",
                    icon: "error"
                });
            });
        }
    });
    },
    ImprimirFactura() {
    this.MostrasTotalF = 1;

    const documentos = this.DatosClientes.map(cliente => cliente.documento);
    const cliente = this.DatosClientes.find(c => documentos.includes(c.documento));
    const Orden = this.orden;

    if (!cliente) {
    
        return;
    }

    setTimeout(() => {
        const factura = document.getElementById('factura-para-imprimir');
        const copiaFactura = factura.cloneNode(true);
        copiaFactura.querySelectorAll('.eliminar-th, .eliminar-td').forEach(el => el.remove());

        const printWindow = document.createElement('iframe');
        printWindow.style.position = 'absolute';
        printWindow.style.width = '0px';
        printWindow.style.height = '0px';
        printWindow.style.border = 'none';
        document.body.appendChild(printWindow);

        const doc = printWindow.contentWindow.document;
        doc.open();
        const fechaEmision = new Date();
            const opcionesFecha = { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric', 
            timeZone: 'America/Bogota' 
            };
            const fechaFormateada = new Intl.DateTimeFormat('es-CO', opcionesFecha).format(fechaEmision);

        doc.write(`
            <html>
            <head>
                <style>
                    body { 
                        width: 100%; 
                        font-family: Arial, sans-serif; 
                        margin: auto; 
                        padding: 0; 
                    }
                    .header {
                        margin: -15 auto; 
                        width: 80%; 
                        text-align: center; 
                        margin-bottom: 10px; 
                        padding: 10px; 
                        background-color: #f4f4f4; 
                    }
                    .header p {
                        margin: 2px 0; 
                        font-size: 12px; 
                        color: #333; 
                        font-weight: bold; 
                    }
                    .header p strong {
                        color: #555; 
                    }
                    table {
                        width: 100%; 
                        border-collapse: collapse; 
                        font-size: 10px; 
                        margin: 0 auto; 
                    }
                    th, td {
                        padding: 4px; 
                        text-align: center; 
                    }
                    table th {
                        background-color: #f2f2f2; 
                    }
                    .footer {
                        text-align: center; 
                        margin-top: 10px; 
                        font-size: 12px; 
                        color: #777; 
                    }
                    .footer p { 
                        margin: 2px 0; 
                    }
                </style>
            </head>
            <body>
                <div class="header">
                <br>
                    <p><strong>Nombre:</strong> ${cliente.nombre}</p>
                    <p><strong>Gmail:</strong> ${cliente.gmail}</p>
                    <p><strong>Documento:</strong> ${cliente.documento}</p>
                    <p><strong>Teléfono:</strong> ${cliente.telefono}</p>
                    <p><strong>Orden de servicio:</strong> ${Orden}</p>
                </div>
                ${copiaFactura.outerHTML}
                <div class="footer">
                    <p>Kairos Dm</p>
                    <p>Dirección: Carrera 9 # 1505 Barrio Buenos Aires</p>
                    <p>Teléfono: 3156716475</p>
                    <p>NIT: 1049023620-5</p>
                    <p>Email: kairosdmsanmartin@gmail.com</p>
                    <p>Fecha de emisión de la factura: ${fechaFormateada}</p>
                </div>
            </body>
            </html>
        `);
        doc.close();
        printWindow.contentWindow.focus();
        printWindow.contentWindow.print();
        document.body.removeChild(printWindow);
    }, 1000);
    },
    RealizarDescuento() {
        const isDescuentoFilled = this.descuento.trim() !== '';
        const isDescuentoPerFilled = this.descuentoper.trim() !== '';

        if (isDescuentoFilled && isDescuentoPerFilled) {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: 'Solo uno de los campos debe estar lleno, no ambos.',
                confirmButtonText: 'Aceptar'
            });
            return; // Salir de la función si ambos campos están llenos
        }

        const isDescuentoValid = /^\d*\.?\d+$/.test(this.descuento);
        const isDescuentoPerValid = /^\d*\.?\d+$/.test(this.descuentoper);

        if ((!isDescuentoFilled && !isDescuentoPerFilled) || (!isDescuentoValid && !isDescuentoPerValid)) {
            Swal.fire({
                icon: 'warning',
                title: '¡Atención!',
                text: 'Uno de los campos debe estar lleno y contener solo números.',
                confirmButtonText: 'Aceptar'
            });
            return; // Salir de la función si las validaciones no se cumplen
        }

        // Mostrar alerta de confirmación
        Swal.fire({
            title: 'Aplicar Descuento',
            text: '¿Está seguro de que desea aplicar el descuento?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                if (this.descuento) {
                    let descuento = parseFloat(this.descuento);
                    let descuentoDividido = descuento / 100;
                    let totalneto = this.total * descuentoDividido;
                    this.total = this.total - totalneto;

                    this.descuentoPorcentaje = totalneto; 
                    this.descuentoValor = null; 

                    Swal.fire({
                        title: 'Descuento Aplicado',
                        text: `Se le ha aplicado un descuento del ${descuento}%. El precio final es: ${this.formatCurrency(this.total)}`,
                        icon: 'info',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        // Vaciar los campos de entrada
                        this.descuento = '';
                        this.descuentoper = '';
                    });
                } else if (this.descuentoper) {
                    let descuento = parseFloat(this.descuentoper);
                    this.total = this.total - descuento;

                    // Almacenar el descuento aplicado
                    this.descuentoValor = descuento; // Almacenar el valor del descuento
                    this.descuentoPorcentaje = null; // Si es un descuento fijo, porcentaje es nulo

                    Swal.fire({
                        title: 'Descuento Aplicado',
                        text: `Se le ha aplicado un descuento de ${this.formatCurrency(descuento)}. El precio final es: ${this.formatCurrency(this.total)}`,
                        icon: 'info',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        // Vaciar los campos de entrada
                        this.descuento = '';
                        this.descuentoper = '';
                    });
                }
            }
        });
    },
    SacarOrdenServicio(){
    return axios.get('/SacarOrdenServicio', {
    })
    .then(response => { 
        this.orden = response.data;
     
    })
    .catch(error => {
        throw error;
    });
    },
    formatDate(date) {
            if (!date || isNaN(new Date(date).getTime())) {
         
                return 'Invalid date';
            }

            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
    },
    },
    mounted() {
        this.BuscarCodigoRopa();
        this.SacarTotal();
        this.SacarCambio();
        this.MostraUsuarios();
        this.RalizarFactura();
        this.RealizaFacturaCreacionClientes();
        this.SacarOrdenServicio();
        this.BuscarCodigoLocion();
    }
};
</script>
<style >
@media print {
    body {
        width: 58mm;
        margin: 0;
        padding: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px; 
    }

    th, td {
        border: 1px solid black;
        padding: 4px;
        text-align: left;
    }
    .total-row {
        display: table-row; 
        font-weight: bold; 
        text-align: right; 
        padding: 10px; 
        background-color: #f8f9fa; 
        border-top: 2px solid #000; 
        font-size: 16px; 
        border: 2px solid red; 
        width: 100%; 
        box-sizing: border-box; 
    }
    .total-row td {
        border: none; 
    }

}

*{
    margin: 0px;
    padding: 0px;
}
#cabeza{
     height: 100%;
     width: 100%;
     display: flex;
}
#Cuerpo{
     width: 72%;
     height: 90%;
     border: 1px solid rgb(179, 179, 179);
     border-radius: 40px;
     margin-right: 30px;
     background: white;
     padding: 10px;
}
#cuerpo2{
    width: 30%;
    height: 90%;
}
#CuerpoClinetes{
    height: 35%;
    border-radius: 30px;
    border: 1px solid rgb(179, 179, 179);
    background: white;
}
#Calcular{
    height: 40%;
    margin-top: 70px;
    border: 1px solid rgb(179, 179, 179);
    border-radius: 30px;
    background: white;
}
#pieDePagina{
    border-top: 2px solid #dee2e6;
    width: 100%;
    height: 170px;
    margin-top: 620px;
    padding:10px 20px;
    display: flex;
}
#text1{
      width: 350px;
    font-size: 18px;
    color: #343a40;
    padding:10px 20px;
     margin: 0;
    font-family: 'Roboto', sans-serif;

}
#Telefonos{
    font-family: 'Roboto', sans-serif;
    padding-top: 15px;
    width: 350px;
}
#Telefonos i {
    margin-right: 5px;
    font-size: 20px;
}
.fa-envelope {
    font-size: 18px;
    margin-right: 10px;
}
#FormularioFooter{
    width: 700px;
    margin-left: 190px;

}
#botonDeProblema{
    border: none;
     margin-left: 80px;
    background-color:white;
    padding: 10px; 
    border-radius: 5px; 
    width: 70%;
}
#botonFormulario{
    width: 90px;
    margin-left: 500px;
    margin-top: -50px;
}
.Busqueda {
    width: 70%;
    height: 50px;
    margin-top: 20px;
    margin-left: 20px;
    display: flex;
    gap: 20px;
}
#search-icon i {
    font-size: 20px; 
    border-radius: 20px;
    
}
.input-group-text {
    padding: 10px 15px; 
}
.form-control {
    font-size: 18px; 
    height: 50px;
    padding: 15px 10px; 
    border-radius: 20px;
}
.ActivarCodigo{
     display: flex;
     width: 240px;
     margin-right: -300px;
     padding: 10px;
}
#CuerpoClientes {
    border: 1px solid #ddd; 
    padding: 15px;
    background-color: #f9f9f9; 
}

#CuerpoClientes h5 {
    margin-bottom: 10px;
}

#CuerpoClientes hr {
    border: 1px solid #000; 
    margin-top: 10px; 
    margin-bottom: 10px; 
}
#BotonBusqueda{
    width: 390px;
    height: 60px;
    margin-left: 20px;
    margin-top: 30px;
    
}
.titulo{
    margin-left: 17px;
    margin-top: 13px;
    height: 40px;
    width: 310px;
    font-weight: bold;
    display: flex;
    font-size: 18px;
}
.titulo i {
    margin-left: 5px;
}
.hr{
    margin-top:-15px ;
}
.btn-enviar {
    background-color: #1f2124; 
    color: white;
    border: none; 
    border-radius: 5px; 
    padding: 10px 20px; 
    font-size: 16px; 
    font-weight: bold; 
    cursor: pointer;
    display: flex;
    align-items: center; 
    gap: 10px; 
}
.btn-enviar:hover {
    background-color: #1f2124; 
}
.btn-enviar i {
    font-size: 20px; 
}
  table {
        width: 100%;
        border-collapse: collapse;
    }
    th td {
        text-align: center; 
        background-color: #f4f4f4; 
        padding: 10px; 
    }
     .table-responsive {
        border: 1px solid rgb(179, 179, 179);
        height: 400px; 
        overflow-y: auto; 
        overflow-x: auto; 
        border-radius: 10px;
    }
#piedepagina{
    display: flex;
    margin-top: -18px;
    height: 100px;
}
#BotonBusquedaEliminar{
    color: white;
    width: 900px; 
    border-radius: 5px; 
    font-size: 22px; 
    font-weight: bold; 
    cursor: pointer;
    display: flex;
    align-items: center; 
    gap: 10px; 
}
#TotalF {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    border-radius: 5px;
    width: 300px;
    height: 74px;
    padding-right: 30px;
}
#TotalF p {
    margin: 0;
    font-size: 22px; 
    font-weight: bold;
}
.amount {
    font-weight: bold;
    font-size: 18px; 
}
#ClientesC{
    margin-left: 10px;
}
.input-icon-container {
    display: flex;
    align-items: center;
}
.input-icon-container i {
    color:#1f2124;
    font-size: 26px;
}
#inpudClientes{
    width: 270px;
    height: 38px;
    border-radius: 20px;
}
.input-icon-container input {
    margin-right: 10px;
    background-color: #d3d3d3; 
    border: 1px solid white; 
    padding: 0.5rem; 
    font-family: Arial, sans-serif; 
    padding-left: 10px;
}
.Clientes2424{
    padding-left: 10px;
}
#containesDeClientes{
    height: 90px;
}
#containesDeClientes {
    display: flex; 
    gap: 20px; 
}
.client-info {
    display: flex;
    flex-direction: column; 
    align-items: flex-start; 
}
.Clientes2424 {
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 8px; 
}
#inpudClientes1{
    background-color: #d3d3d3; 
    border: 1px solid #ccc; 
    font-weight: bold;
    padding: 0.5rem; 
    color: #333;
    font-size: 1rem; 
    font-family: Arial, sans-serif; 
    width: 100px;
    margin-left: 10px;
    border-radius: 10px;
    text-align: center;
}
 #inpudClientes2{
    background-color: #d3d3d3; 
    border: 1px solid #ccc; 
    padding: 0.5rem; 
    color: #333;
    font-size: 1rem; 
    font-family: Arial, sans-serif; 
    width: 150px;
    margin-left: 10px;
    border-radius: 10px;
    font-weight: bold;
    text-align: center;
 }.btn-icon {
    background: none; 
    border: none; 
    color:#1f2124;
    font-size: 1.5rem; 
    cursor: pointer; 
    padding: 0; 
    display: flex; 
    align-items: center; 
    justify-content: center;
}
.btn-icon:hover {
    color: #1f2124; 
}
#exampleModal .modal-dialog {
    max-width: 950px; 
    margin: auto; 
    margin-top: 30px;
}
#exampleModal .modal-content {
    width: 100%;
}
#InpuDeBusqueda{
    width: 570px;
    margin-left: 75px;
}
.Clientes24241{
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 8px; 
    width: 100px;
    text-align: center;
    margin-left: 10px;
}
#InpudDeVentas{
    background-color: #d3d3d3; 
    border: 1px solid #ccc; 
    font-weight: bold;
    padding: 0.5rem; 
    color: #333;
    font-size: 1rem; 
    font-family: Arial, sans-serif; 
    width: 100px;
    margin-left: 10px;
    border-radius: 10px;
    text-align: center;
}
#InpudDeVentas1{
    background-color: #d3d3d3; 
    border: 1px solid #ccc; 
    padding: 0.5rem; 
    color: #333;
    font-size: 1rem; 
    font-family: Arial, sans-serif; 
    width: 140px;
    border-radius: 10px;
    font-weight: bold;
    text-align: center; 
}
.Clientes2424122{
    font-weight: bold;
    font-size: 1.1rem;
    margin-bottom: 8px; 
    width: 140px;
    text-align: center;
}
#TotalDeVentas{
    text-align: center;
    padding: 5px;
    margin-bottom: -7px;
    padding-left: 20px;
}
.client-info11{
    margin-left: 20px;
}
form {
  background-color: #f9f9f9;
  padding: 20px;
  border-radius: 15px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  max-width: 500px;
  margin: 0 auto;

}
form div {
  margin-bottom: 15px;
}
label {
  display: block;
  font-weight: 600;
  margin-bottom: 8px;
  color: #333;
}
input[type="text"],
input[type="tel"],
input[type="email"],
input[type="date"] {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 10px;
  font-size: 16px;
  color: #333;
  box-sizing: border-box;
  transition: border-color 0.3s;
}
input[type="text"]:focus,
input[type="tel"]:focus,
input[type="email"]:focus,
input[type="date"]:focus {
  outline: none;
}
.btn-success {
  display: inline-block;
  width: 100%;
  padding: 12px 20px;
  font-size: 16px;
  font-weight: 600;
  color: #fff;
 background-color:#1f2124; 
  border: none;
  border-radius: 10px;
  cursor: pointer;
  transition: background-color 0.3s;
}
.btn-success:hover {
 background-color: #1f2124; 
}
.btn-invoice {
  background-color: #1f2124; 
  color: #fff;
  border: none;
  border-radius: 4px;
  padding: 8px 12px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.3s;
}
.btn-invoice:hover {
  background-color: #1f2124; 
}
.btn-invoice i {
  font-size: 16px;
}
table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  text-align: center;
  vertical-align: middle;
  padding: 8px;
}
th {
  background-color:#1f2124; 
  color: #fff;
}
.modal-container {
  max-height: 70vh; 
  overflow-y: auto; 
  text-align: center;
  width: 1000px;
}
.btn-margin {
  margin: 0 1rem; 
}
.button-container {
    display: flex;
    justify-content: space-between; 
    align-items: center;
    padding: 10px; 
    margin-top: -10px;
}
.btn-blue {
    color: white;
    border: none;
    padding-left: 8px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    width: 100px;
    height: 40px;
    font-weight: bold;
    font-size: 1rem;
    margin-bottom: 8px; 
    text-align: center;
    margin-left: 19.3px;
}
.btn-green {
    color: white;
    border: none;
    border-radius: 8px;
    padding-left: 8px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    text-align: center;
    width: 146px;
    height: 40px;
    margin-bottom: 8px; 
    font-weight: bold;
    margin-right: 19.3px;
}
.btn-blue {
    background-color:#1f2124;
}
.btn-green {
    background-color: #28a745;
}
.btn-blue i, .btn-green i {
    margin-right: 4px;
}
#Bontondebusqueda{
    width: 50px;
    border-top-right-radius: 10px ;
    border-bottom-right-radius: 10px;
    border: none;
}
.input-group-text1 {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  font-size: 24px;

}
.custom-alert {
  border: 1px solid #f39c12; 
  background-color: #fef9e7; 
  color: #e67e22; 
  padding: 15px; 
  border-radius: 5px; 
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
  margin-top: 10px; 
  max-width: 50%; 
  text-align: center;
  margin-left: 25%;
}
.total-row {
    font-weight: bold;            
    text-align: right;           
    padding: 10px;               
    background-color: white;    
    border-top: 2px solid #000;   
    font-size: 16px;              
}
.total-cell {
    border: 2px solid red;        
    width: 100%;                  
    box-sizing: border-box;       
    text-align: right;            
}
.tabla111{
    border: none;
}
.modal-dialog.modal-sm {
    max-width: 600px; 
    display: flex;
    padding: 20px;
}
    .modal-body i {
        font-size: 20px; 
    }
    .label-text {
        font-size: 14px; 
    }
    .input-small {
        max-width: 100px; 
    }
    .modal-body .d-flex {
        align-items: center; 
    }
    .modal-body{
        display: flex;
    }
    #c1{
        width: 50%;
    }
    #c2{
        width: 50%;
    }
    #input1 {
        width: 210px;
        border-radius: 10px;      
    }
    #input2 {
        width: 210px;
        border-radius: 10px;      

    }
    .input-container {
        margin-bottom: 1rem; 
    }
    .d-flex {
        display: flex;
        align-items: center; 
    }
    #i{
        width: 320px;
    }
    #ii{
        width: 320px;
    }
    .input-group {
        display: flex;
        align-items: center; 
        gap: 0.5rem; 
    }
    .input-group i {
        font-size: 28px; 
    }
    #re{
        margin-right: -5.9px;
    }
    #conten{
        width: 800px;
        margin: 0 auto;
        margin-top: 0px;
    }
    #abono{
        width: 200px;
        height: 40px;
    }


</style>