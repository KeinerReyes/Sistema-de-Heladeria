<template>
    <div id="cabeza">
        <div id="Cuerpo">
            <br>
            <div id="nav">
                <div class="card">
                    <ul class="nav nav-pills nav-fill">
                        <li class="nav-item">
                            <a class="nav-link bg-dark" href="/ControlInventario" style="cursor: pointer;">
                                <i class="fas fa-file-invoice"></i>
                                Control de Facturas
                            </a>
                        </li>
                        <li class="nav-item" id="nav-item">
                            <a href="/stock" class="btn  position-relative">
                                Stock 
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    {{stock}}
                                </span>
                            </a>
                        </li>

                        <li class="nav-item" id="nav-item1">
                            <a href="/ControlCantidad" style="color: black; text-decoration: none;">
                              <button class="btn ">Control de cantidad</button>
                            </a>
                        </li>
                        <li class="nav-item" id="nav-item2">
                            <a href="/ganacias" style="color: black; text-decoration: none;">
                              <button class="btn ">Ganancias</button>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card" id="bsuqueda">
                    <div class="search-container">
                        <input type="text" class="search-input" v-model="orden_servicioB"  placeholder="Buscar por número de factura">
                        <button class="search-button" @click="buscar">
                        <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

            </div>
          </div>
          <div id="table">
            <table class="table table-sm">
                <thead>
                <tr>
                    <th scope="col">Orden de servicio</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Documento</th>
                    <th scope="col">Telefono</th>
                    <th scope="col">Total</th>
                    <th scope="col">Sistecrédito</th>
                    <th scope="col">Métodos de pago</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Opciones</th>
                </tr>
                </thead>
                <tbody class="table-group-divider">
                <tr  v-for="DatosCliente in DatosClientes" :key="DatosCliente.orden_servicio">
                    <th>{{ DatosCliente.orden_servicio }}</th>
                    <th>{{ DatosCliente.nombre}}</th>
                    <td>{{ DatosCliente.documento }}</td>
                    <td>{{ DatosCliente.telefono  }}</td>
                    <td>{{ formatCurrency(DatosCliente.totalF) }}</td>
                    <td>
                        <span v-if="DatosCliente.estado === 1" id="icon-class1">
                        <i class="fas fa-check"></i> 
                        </span>
                        <span v-else id="icon-class2">
                        <i class="fas fa-times"></i> 
                        </span>
                    </td>
                    <td>
                            <span v-if="DatosCliente.mp == '1'" :style="{ color: 'black' }">Pago en efectivo</span>
                            <span v-else-if="DatosCliente.mp == '2'" :style="{ color: 'black' }">Pago en transferencia</span>
                            <span v-else-if="DatosCliente.mp == '3'" :style="{ color: 'black' }">Credito</span>
                            <span v-else-if="DatosCliente.mp == '4'" :style="{ color: 'black' }">Credito - con apartado</span>
                            <span v-else-if="DatosCliente.mp == '5'" :style="{ color: 'black' }">Credito - con apartado</span>
                            <span v-else-if="DatosCliente.mp == '6'" :style="{ color: 'black' }">Factura - Devolución</span>
                            <span v-else :style="{ color: 'gray' }">Método de pago no disponible</span>
                        </td>



                    <td>{{formatDate(DatosCliente.fecha_de_creacion) }}</td>
                    <td>
                    <button class="btn btn-danger" title="Eliminar" @click="EliminarFactura(DatosCliente.orden_servicio);">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button type="button" class="btn btn-dark" title="Ver" data-bs-toggle="modal" data-bs-target="#myModal" @click="SacarInfotmacionClientes(DatosCliente.documento,DatosCliente.orden_servicio)">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-primary" title="Actualizar" data-bs-toggle="modal" data-bs-target="#mySecondModal" @click="DevolucionProductos(DatosCliente.orden_servicio)">
                        <i class="fas fa-sync-alt"></i>
                    </button>

                    </td>
                </tr>
                </tbody>
            </table>
                <div v-if="noClienteFound" class="alert alert-warning mt-3 custom-alert">
                      No se encontró el documento del cliente.
                </div>
            <br>
            <nav>
                    <ul class="pagination">
                        <li class="page-item" v-if="pagination.current_page > 1">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1)">Ant</a>
                        </li>
                        <li class="page-item" v-for="page in pagesNumber" :key="page" :class="{ 'active': page === isActived }">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(page)" v-text="page"></a>
                        </li>
                        <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1)">Sig</a>
                        </li>
                    </ul>
                </nav>

        </div>
        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Detalles Completos de la Factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="printableArea">
                    <div class="custom-modal-body" ref="modalContent"  >
                        <!-- Header Section -->
                        <div class="header" v-for="DatosClientes in DatosClientesFactura" :key="DatosClientes.id_cliente">
                            <img src="img/logo.png" alt="Logo" id="img">
                            <p><strong>Nombre:</strong> {{ DatosClientes.nombre }}</p>
                            <p><strong>Gmail:</strong> {{ DatosClientes.gmail }}</p>
                            <p><strong>Documento:</strong> {{ DatosClientes.documento }} </p>
                            <p><strong>Teléfono:</strong> {{ DatosClientes.telefono }} </p>
                            <p><strong>Orden de servicio</strong> {{ orden}} </p>
                        </div>

                        <!-- Table Section -->
                        <div class="table-responsive mailbox-messages">
                            <div style="overflow-x: auto;">
                                <table class="table table-hover table-striped" id="factura-para-imprimir">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Categoría</th>
                                            <th>Marca</th>
                                            <th>Talla</th>
                                            <th>Referencia</th>
                                            <th class="eliminar-td">Color</th>
                                            <th>Precio</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="Producto in DatosFactura" :key="Producto.id_productos">
                                            <td class="col-1 text-center">{{ Producto.codigo }}</td>
                                            <td class="col-1" >{{ Producto.categoria }}</td>
                                            <td class="col-2">{{ Producto.marca }}</td>
                                            <td class="col-1">{{ Producto.talla }}</td>
                                            <td class="col-1">{{ Producto.referencia }}</td>
                                            <td class="col-1  eliminar-td">{{ Producto.color }}</td>
                                            <td class="col-2">{{ formatCurrency(Producto.precio) }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="8" id="tdcentro" class="text-right"> TOTAL {{ formatCurrency(TotalF)}}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- Footer Section -->
                        <div class="footer">
                            <p>Kairos Dm</p>
                            <p>Dirección: Carrera 9 # 1505 Barrio Buenos Aires</p>
                            <p>Teléfono: 3156716475</p>
                            <p>NIT:1049023620-5</p>
                            <p>Email: Kairos@kairosstore.com</p>
                            <p>Fecha de emisión de la factura: {{ fechaEmision }}</p>

                            <!-- Action Buttons -->
                             <hr>
                             <div class="text-end mt-3" ref="buttonsContainer">
                                <button  @click="generatePDF" class="btn btn-danger ms-2">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </button>
                                <button type="button" id="printButton" class="btn btn-secondary ms-2" @click="ImprimirFactura">
                                    <i class="fas fa-print"></i> Imprimir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

         <!-- Segundo Modal -->
         <div class="modal fade" id="mySecondModal" tabindex="-1" aria-labelledby="mySecondModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl"> <!-- Cambié a modal-xl para mayor tamaño -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mySecondModalLabel">Gestión de Devoluciones</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        
        <div class="modal-body">
            <div class="table-responsive mailbox-messages">
                    <div style="overflow-x: auto;">
                        <table class="table table-hover table-striped" id="factura-para-imprimir">
                            <thead>
                                <tr>
                                    <th></th>
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
                                <tr v-for="Producto in DatosFactura2" :key="Producto.id_productos">
                                    <td class="col-1">
                                        <input type="checkbox" :id="'checkbox' + Producto.id_productos" class="custom-checkbox" @change="toggleProductSelection(Producto.id_productos)">
                                    </td>
                                    <td class="col-1 text-center eliminar-td">{{ Producto.codigo }}</td>
                                    <td class="col-1">{{ Producto.categoria }}</td>
                                    <td class="col-1">{{ Producto.marca }}</td>
                                    <td class="col-1">{{ Producto.talla }}</td>
                                    <td class="col-1">{{ Producto.referencia }}</td>
                                    <td class="col-1">{{ Producto.color }}</td>
                                    <td class="col-1">{{ formatCurrency(Producto.precio) }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div v-if="DatosFactura2.length == 0" class="alert alert-warning mt-3 custom-alert">
                          Esta factura no tiene ningún producto.
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer con botón -->
            <div class="modal-footer">
                <button type="button" id="deleteButton" class="btn btn-danger" @click="DevolerInventario">
                    <i class="fas fa-undo-alt"></i> Devolver productos al inventario
                </button>
            </div>
        </div>
    </div>

        </div>
     </div>
    </template>
  <script >
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import html2pdf from 'html2pdf.js';
    export default {
    data() {
        return {
            DatosClientes:[],
            DatosClientesFactura: [],
            DatosFactura: [],
            orden_servicioB: '',
            orden: 0,
            stock: 0,
            DatosFactura2: [],
            selectedProductIds: [],
            devolerInventario:'',
            descuento: '',
            DatosFactura2: false,
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
    }

        },
    methods: {
        SacrDatos(resetPage = false) {
        if (resetPage) {
            this.pagination.current_page = 1;
        }

        axios.get('/SacarInformacion', {
            params: {
                orden_servicio: this.orden_servicioB, 
                page: this.pagination.current_page
            }
        })
        .then((response) => {
            let respuesta = response.data;
            this.DatosClientes = respuesta.resultados;
            this.pagination = respuesta.pagination;

            if (this.DatosClientes.length > 0) {
                this.noClienteFound = false; 
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Cliente no encontrado',
                    text: `No se encontró una factura con el número ${this.orden_servicioB}.`,
                    confirmButtonText: 'Aceptar'
                });
                this.noClienteFound = true; 
            }
        })
        .catch((error) => {
       
        });
        },
        cambiarPagina(page) {
            this.pagination.current_page = page;
            this.SacrDatos(); 
        },
        buscar() {
            this.SacrDatos(true); // Resetea la página a la primera cuando haces una nueva búsqueda
        },
        obtenerFechaActual() {
        const hoy = new Date();
        const dia = hoy.getDate().toString().padStart(2, "0"); // Obtener el día con dos dígitos
        const mes = (hoy.getMonth() + 1).toString().padStart(2, "0"); // Obtener el mes con dos dígitos (el mes es 0-indexado)
        const anio = hoy.getFullYear(); // Obtener el año completo
        this.fechaEmision = `${dia}/${mes}/${anio}`; // Formatear la fecha
        },
        formatDate(date) {
            if (!date || isNaN(new Date(date).getTime())) {
                return 'Invalid date';
            }

            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(date).toLocaleDateString(undefined, options);
        },
        formatCurrency(amount) { /// Funcion sacar preico colombiano $$$
            const formatter = new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,  
            });
            return formatter.format(amount);
        },
        async EliminarFactura(orden_servicio) { //// Eliminar Factura Y guadar en facturas eliminadas
        // Muestra el mensaje de confirmación
        const { value: confirm } = await Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar!',
            cancelButtonText: 'Cancelar'
        });

        // Si el usuario confirma, muestra un input para el motivo de eliminación
        if (confirm) {
            const { value: motivo } = await Swal.fire({
                title: 'Motivo de eliminación',
                input: 'text',
                inputLabel: '¿Por qué estás eliminando esta factura?',
                inputPlaceholder: 'Ingresa el motivo aquí...',
                inputValidator: (value) => {
                    if (!value) {
                        return 'El motivo es obligatorio para eliminar la factura';
                    }
                },
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Enviar',
                cancelButtonText: 'Cancelar'
            });

            // Si el usuario proporciona un motivo, procede con la eliminación
            if (motivo) {
                try {
                    // Enviar la solicitud al backend para eliminar la factura
                    const response = await axios.delete('/EliminarFactura', {
                        params: { orden_servicio: orden_servicio, Problema: motivo }
                    });

                    // Verificar si la eliminación fue exitosa
                    if (response.status === 200) {
                        Swal.fire({
                            title: 'Eliminada',
                            text: 'La factura se ha eliminado correctamente.',
                            icon: 'success'
                        }).then(() => {
                            window.location.reload();
                        });

                    } else {
                        Swal.fire('Error', 'Hubo un problema al eliminar la factura.', 'error');
                    }
                } catch (error) {
                    // Manejar errores en la solicitud
                    Swal.fire('Error', 'Hubo un problema al eliminar la factura.', 'error');
               
                }
            } else {
                Swal.fire('No se ha eliminado', 'No se proporcionó un motivo de eliminación', 'info');
            }
        } else {
            Swal.fire('Cancelado', 'La eliminación de la factura ha sido cancelada', 'info');
        }
        },
        SacarInfotmacionClientes(documento, orden_servicio) {   /// Sacar datos perosnles de la perosna 
        axios.get('SacarInformacioC', {
        params: {
            documento: documento,
            orden_servicio: orden_servicio
        }
        })
        .then((response) => {
             
                this.DatosClientesFactura = response.data.cliente;
                this.DatosFactura = response.data.descripcion;
                this.TotalF = response.data.TotalF;
                this.orden = response.data.orden;
                this.descuento = response.data.descuento;

        })
        .catch((error) => {
          
        });
        },
        DevolucionProductos(orden_servicio){    //// devolucion de los productos por x razon !!
        axios.get('DevolucionProductos', {
        params: {
            orden_servicio: orden_servicio, 
        }
        })
        .then((response) => {
         this.DatosFactura2 = response.data.descripcion;
         this.devolerInventario = response.data.orden_servicio;
        })
        .catch((error) => { 
        });
        },
        toggleProductSelection(productId) { /// escoger los productos que se van a devolver
            const index = this.selectedProductIds.indexOf(productId);
            if (index > -1) {
                this.selectedProductIds.splice(index, 1);
            } else {
                this.selectedProductIds.push(productId);
            }
        },
        DevolerInventario() {
        if (this.selectedProductIds.length === 0) {
            Swal.fire(
                'Advertencia!',
                'Debe seleccionar al menos un producto para eliminar.',
                'warning'
            );
            return;
        }
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Esta acción eliminará los productos seleccionados y los devolverá al inventario.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.get('DevolerInventario', {
                    params: {
                        ids_productos: this.selectedProductIds,
                        orden_servicio: this.devolerInventario,
                    }
                })
                .then((response) => {
                    Swal.fire(
                        'Eliminado!',
                        'Los productos han sido eliminados y devueltos al inventario.',
                        'success'
                    );
                })
                .catch((error) => {
                    Swal.fire(
                        'Error!',
                        'Hubo un problema al eliminar los productos.',
                        'error'
                    );
                });
            }
    });
        },
        generatePDF() {/// Sacar pdf de la factura
     
      const buttons = this.$refs.buttonsContainer;
      if (buttons) {
        buttons.style.display = 'none'; 
      }

      const element = this.$refs.modalContent;
      const opt = {
        margin: 1,
        filename: 'factura.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
      };

      html2pdf().from(element).set(opt).save().then(() => {
        if (buttons) {
          buttons.style.display = 'block'; 
        }
      }).catch((error) => {
    
        if (buttons) {
          buttons.style.display = 'block';
        }
      });
        },
       ImprimirFactura() {  /// imprrimir la factra y el resto de funciones las realiza
    const documentos = this.DatosClientesFactura.map(cliente => cliente.documento);
    const cliente = this.DatosClientesFactura.find(c => documentos.includes(c.documento));
    const orden =  this.orden;

    if (!cliente) {
        alert('No se encontró información del cliente.');
        return;
    }

    const factura = document.getElementById('factura-para-imprimir');
    const copiaFactura = factura.cloneNode(true);
    
    copiaFactura.querySelectorAll('.eliminar-th, .eliminar-td').forEach(el => el.remove());

    const printWindow = document.createElement('iframe');
    printWindow.style.position = 'absolute';
    printWindow.style.width = '0';
    printWindow.style.height = '0';
    printWindow.style.border = 'none';
    document.body.appendChild(printWindow);

    const doc = printWindow.contentWindow.document;
    const fechaEmision = new Date(); // Obtener la fecha actual
    const opcionesFecha = { day: '2-digit', month: '2-digit', year: 'numeric' };
    const fechaFormateada = fechaEmision.toLocaleDateString('es-CO', opcionesFecha); // Formato: DD/MM/YYYY
    doc.open();
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
                .header { text-align: center; margin-bottom: 10px; padding: 10px; background-color: #f4f4f4; }
                .header img { width: 60px; height: 60px; border-radius: 50%; }
                .header p { margin: 2px 0; font-size: 12px; font-weight: bold; }
                table { width: 100%; border-collapse: collapse; font-size: 10px; }
                th, td { padding: 4px; text-align: center; }
                table th { background-color: #f2f2f2; }
                .footer { text-align: center; font-size: 10px; color: #777; }
                .footer p { margin: 2px 0; }
                #tdcentro { text-align: right; padding: 8px; }
            </style>
        </head>
        <body>
            <div class="header">
                <br>
                <p><strong>Nombre:</strong> ${cliente.nombre}</p>
                <p><strong>Gmail:</strong> ${cliente.gmail}</p>
                <p><strong>Documento:</strong> ${cliente.documento}</p>
                <p><strong>Teléfono:</strong> ${cliente.telefono}</p>
                <p><strong>Orden de servicio:</strong> ${orden}</p>
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
        },
        Mostrarsctok(){  // mosytras scto cantidad
            axios.get('Mostrarsctok', {
            })
            .then((response) => {
            this.stock = response.data;
            })
            .catch((error) => { 
            });
         }
        },
    mounted() {
    this.obtenerFechaActual();
    this.SacrDatos();
    this.Mostrarsctok();
    this.SacarInfotmacionClientes();
    }
};
  </script>
    <style >
    @media print {
    body * {
        visibility: hidden;
    }
    #printableArea, #printableArea * {
        visibility: visible;
    }
    #printableArea {
        position: absolute;
        left: 0;
        top: 0;
    }
    .text-end {
        display: none; /* Hides the button container when printing */
    }
    }

    *{
        margin: 0px;
        padding: 0px;
    }
    .hidden-in-pdf {
    display: none !important;
    }
    .custom-modal-body {
        width: 58mm;
        margin: 0 auto;
        padding: 0;
    }
    .modal-dialog {
        margin: 2% auto; 
    }
    #cabeza{
         height: 100%;
         width: 100%;
    }
    #Cuerpo{
         width: 100%;
    }
    #text1{
        width: 350px;
        font-size: 18px;
        color: #343a40;
        padding:10px 20px;
        margin: 0;
        font-family: 'Roboto', sans-serif;
    }
    .card{
        width: 50%;
        padding: 5px;
    }
    #menu{
        border: 2px solid red;
        width: 100%;
        height: 100%;
    }
    .table {
        width: 100%; 
        margin: 0 auto;
    }
    .table th,
    .table td {
        text-align: center; 
        border: none;
    }
    .table td {
        vertical-align: middle; 
        border: none;
    }
    .btn {
        border: none;
        text-decoration: none;
        background: none; 
        cursor: pointer; 
        padding: 5px 10px; 
        margin: 0 5px; 
        font-size: 1.2rem; 
    }
    .btn-danger {
        color: red; 
    }
    .btn-dark {
        color: black; 
    }
    .btn-primary {
        color: blue; 
    }
    .btn:hover {
        opacity: 0.8; 
    }
    #nav{
        display: flex;

    }
    #bsuqueda{
        width: 25%;
        margin-left: auto;
        margin-right: 0;
    }
    #bsuqueda {
        width: 30%; 
        margin-left: auto;
        margin-right: 0;
    }
    .search-container {
    display: flex;
    align-items: center;
    }
    .search-input {
    flex: 1; 
    padding: 10px;
    border-radius: 4px;
    font-size: 16px;
    outline: none; 
    border: none;
    }
    .search-input:focus {
    border-color: #000000;
    box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
    }
    .search-button {
    border: 1px solid transparent; 
    padding: 10px 15px;
    margin-left: 5px;
    border-radius: 4px;
    background-color: transparent; 
    color: #000000; 
    cursor: pointer;
    display: flex;
    align-items: center;
    }
    .search-button i {
    font-size: 24px;
    }
    .search-button:hover i {
    color: blcak; 
    }
    .pagination {
        display: flex;
        justify-content: center;
        padding-left: 0;
        list-style: none;
        border-radius: 0.25rem;
    }

    .page-item {
        margin: 0 0.25rem;
    }

    .page-link {
        color: #fff;
        background-color: rgba(55, 58, 63, 1);
        border: 1px solid rgba(55, 58, 63, 1);
        padding: 0.5rem 0.75rem;
        text-decoration: none;
        border-radius: 0.25rem;
        transition: background-color 0.3s, color 0.3s;
    }

    .page-link:hover {
        background-color: rgba(45, 48, 53, 1);
        color: #fff;
    }

    .page-item.active .page-link {
        color: #fff;
        background-color: rgba(35, 38, 43, 1);
        border-color: rgba(35, 38, 43, 1);
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: rgba(55, 58, 63, 1);
        border-color: rgba(55, 58, 63, 1);
    }
    .custom-alert {
    border: 1px solid #f39c12; 
    color: #e67e22; 
    padding: 15px; 
    border-radius: 5px; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
    margin-top: 10px; 
    max-width: 50%; 
    text-align: center;
    margin-left: 25%;
    }
    .custom-modal-body {
    width: 100%;
    font-family: Arial, sans-serif;
    margin: auto;
    padding: 0;
    }
    .custom-modal-body .header {
        margin: 0 auto;
        width: 60%;
        text-align: center;
        margin-bottom: 10px;
        padding: 10px;
        background-color: white;
    }
    .custom-modal-body .header img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        margin-bottom: 5px;
    }
    .custom-modal-body .header p {
        margin: 2px 0;
        font-size: 12px;
        color: #333;
        font-weight: bold;
    }
    .custom-modal-body .header p strong {
        color: #555;
    }
    .custom-modal-body table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
        margin: 0 auto;
    }
    .custom-modal-body th, .custom-modal-body td {
        padding: 4px;
        text-align: center;
    }
    .custom-modal-body table th {
        background-color: #f2f2f2;
    }
    .custom-modal-body .footer {
        text-align: center;
        margin-top: 10px;
        font-size: 12px;
        color: #777;
    }
    .custom-modal-body .footer p {
        margin: 2px 0;
    }
    #icon-class1{
        color: green;
    }
    #icon-class2{
        color: red;
    }
    #tdcentro{
        padding-right: 35px;
    }
    .custom-checkbox {
    width: 20px;
    height: 20px;
    accent-color: #007bff; 
    }
    #pdfButton {
        background-color: #dc3545; 
        border-color: #dc3545; 
        color: #ffffff;
    }
    #pdfButton:hover {
        background-color: #c82333; 
        border-color: #bd2130; 
        color: #ffffff; 
    }
    #pdfButton i {
        color: #ffffff; 
    }
    #printButton {
        background-color: #6c757d; 
        border-color: #6c757d; 
        color: #ffffff; 
    }
    #printButton:hover {
        background-color: #5a6268; 
        border-color: #545b62; 
        color: #ffffff;
    }
    #printButton i {
        color: #ffffff; 
    }
    #deleteButton {
        background-color: #dc3545; 
        border-color: #dc3545; 
        color: #ffffff;
        width: 400px;
        text-align: center;
        margin-left: 730px;
        border-radius: 10px;
    }
    #deleteButton:hover {
        background-color: #c82333; 
        border-color: #bd2130; 
        color: #ffffff; 
    }
    #deleteButton i {
        color: #ffffff; 
    }
    #img{
        width: 150px;
        height: 150px;
        margin-bottom: -17px;
        margin-top: -50px;
    }
    #nav-item {
    width: 60px;
    margin-left: 10px;
    text-align: center;
    padding: 0;
    text-decoration: none;
    }
    #nav-item:hover {
        border-bottom: 2px solid black; 
        cursor: pointer;
        text-decoration: none;
        color: black;
    }
    #nav-item1:hover {
        border-bottom: 2px solid black; 
        cursor: pointer;
        text-decoration: none;
    }
    #nav-item2:hover {
        border-bottom: 2px solid black; 
        cursor: pointer;
        text-decoration: none;
    }
    </style>