
<template>
  <div id="todo">
  <br>
    <div class="container mt-4">
      <div id="ContenedorNav">
        <div class="card" id="card">
            <nav class="navbar navbar-expand-lg navbar-light mb-4 w-100">
                <div class="container">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                        <ul class="navbar-nav">
                          <li class="nav-item active">
                              <a class="nav-link nav-button" href="Inventario" id="inventario-button">
                                  <i class="fas fa-box me-2"></i> Inventario
                              </a>
                          </li> 

                            <li class="nav-item active">
                                <a class="nav-link nav-button" href="/RegistrarProductos">
                                  Registrar Producto
                                </a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link nav-button" href="Agregar">
                                   Agregar Productos-Almacén
                                </a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link nav-button" href="/Ventas">
                                  Ventas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-button" href="#" @click="exportToExcel">
                                    Exportar Excel
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link nav-button" href="#" @click="triggerFileInput">
                                   Importar Excel
                                </a>
                                <input type="file" class="form-control-file d-none" name="file" id="fileUpload" @change="handleFileUpload" />
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
      </div>


  
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="card-header text-black">
            <h3 class="mb-0" style="color: black;">Catálogo Completo de Productos de Almacén</h3>
          </div>
          <div v-if="message" :class="messageClass" class="alert" role="alert">
            {{ message }}
          </div>
          <div class="table-responsive" id="table">
            <table id="tableinv" class="table table-sm">
              <thead class="thead-dark">
                  <tr>
                      <th class="text-center">Código</th>
                      <th class="text-center">Categoría</th>
                      <th class="text-center">Marca</th>
                      <th class="text-center">Talla</th>
                      <th class="text-center">Referencia</th>
                      <th class="text-center">Color</th>
                      <th class="text-center">Precio</th>
                      <th class="text-center">Fecha de entrada</th>
                      <th class="text-center">Opciones</th>
                  </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  
    <div class="modal fade" id="updateProductModal" tabindex="-1" role="dialog" aria-labelledby="updateProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="updateProductModalLabel1">Actualizar Producto</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="fas fa-times"></i></span>
        </button>

        </div>
        <div class="modal-body">
          <form @submit.prevent="Actualizar">
            <div class="form-group">
              <label for="productCategory">Categoría</label>
              <input type="text" v-model="currentProduct.categoria" class="form-control" id="productCategory" required>
            </div>
            <div class="form-group">
              <label for="productBrand">Marca</label>
              <input type="text" v-model="currentProduct.marca" class="form-control" id="productBrand" required>
            </div>
            <div class="form-group">
              <label for="productSize">Talla</label>
              <input type="text" v-model="currentProduct.talla" class="form-control" id="productSize" required>
            </div>
            <div class="form-group">
              <label for="productReference">Referencia</label>
              <input type="text" v-model="currentProduct.referencia" class="form-control" id="productReference" required>
            </div>
            <div class="form-group">
              <label for="productColor">Color</label>
              <input type="text" v-model="currentProduct.color" class="form-control" id="productColor" required>
            </div>
            <div class="form-group">
              <label for="productPrice">Precio</label>
              <input type="number" v-model="currentProduct.precio" class="form-control" id="productPrice" required>
            </div>
            <button type="submit" class="btn btn-dark">Actualizar</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
   <!-- Modal para mostrar el código QR -->
   <div class="modal fade static" id="qrModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="qrModalLabel">Código QR</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="cerrarModal">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <canvas id="qrCanvas" width="400" height="400"></canvas>
          </div>

          <div class="modal-footer">
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import { formatDistanceToNow } from 'date-fns';
  import { es } from 'date-fns/locale';
  import Swal from 'sweetalert2';
  import * as XLSX from 'xlsx';
  import QRCode from 'qrcode';
  
  export default {
    data() {
      return {
        inventario: [],
        message: '',
        messageClass: '',
        currentProduct: {},
      };
    },
    mounted() {
      this.listarinv();
    },
    methods: {
      listarinv() {
        axios.get('/listarinv')
          .then(response => {
            this.inventario = response.data;
            this.$nextTick(() => {
              if ($.fn.DataTable.isDataTable('#tableinv')) {
                $('#tableinv').DataTable().clear().destroy();
              }
              $('#tableinv').DataTable({
                data: this.inventario,
                columns: [
                  { data: 'codigo' },
                  { data: 'categoria' },
                  { data: 'marca' },
                  { data: 'talla' },
                  { data: 'referencia' },
                  { data: 'color' },
                  { data: 'precio', render: data => {

                      return this.formatCurrency(data);
                    }
                  },
                  { data: 'fecha_de_creacion', render: data => {

                      return this.formatFecha(data);
                    }
                  },
                  {
                    data: null,
                    defaultContent: `
                      <div class="btn-container">
                        <button class="btn btn-icon btn-update">
                          <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button class="btn btn-icon btn-delete">
                          <i class="fas fa-trash"></i>
                        </button>
                    <button class="btn btn-icon btn-qr">
                      <i class="fas fa-qrcode"></i>
                    </button>
                      </div>
                    `,

                    orderable: false
                  }
                ],
                language: {
                  sProcessing: "Procesando...",
                  sLengthMenu: "Mostrar _MENU_ registros",
                  sZeroRecords: "No se encontraron resultados",
                  sEmptyTable: "Ningún dato disponible en esta tabla",
                  sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                  sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
                  sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
                  sSearch: "Buscar:",
                  sLoadingRecords: "Cargando...",
                  oPaginate: {
                    sFirst: "Primero",
                    sLast: "Último",
                    sNext: "Siguiente",
                    sPrevious: "Anterior"
                  },
                  oAria: {
                    sSortAscending: ": Activar para ordenar la columna de manera ascendente",
                    sSortDescending: ": Activar para ordenar la columna de manera descendente"
                  }
                },
                drawCallback: () => {
                  this.bindButtonEvents();
                }
              });
            });
          })
          .catch(error => {
            this.message = 'Error al obtener los datos';
            this.messageClass = 'alert-danger';
          });
      },
      formatCurrency(amount) { /// Funcion sacar preico colombiano $$$
            const formatter = new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,  
            });
            return formatter.format(amount);
      },
      bindButtonEvents() {
        $('#tableinv').off('click', '.btn-update').on('click', '.btn-update', (e) => {
          const row = $(e.currentTarget).closest('tr');
          const data = $('#tableinv').DataTable().row(row).data();
          this.currentProduct = { ...data };
          $('#updateProductModal').modal('show');
        });
  
        $('#tableinv').off('click', '.btn-delete').on('click', '.btn-delete', (e) => {
          const row = $(e.currentTarget).closest('tr');
          const data = $('#tableinv').DataTable().row(row).data();
          this.Eliminar(data.id_productos);
        });
        
      $('#tableinv').off('click', '.btn-qr').on('click', '.btn-qr', (e) => {
        const row = $(e.currentTarget).closest('tr');
        const data = $('#tableinv').DataTable().row(row).data();
        this.generarQRCode(data.codigo);
      });
  
      },
      Eliminar(id_productos) {
        Swal.fire({
          title: '¿Estás seguro?',
          text: "No podrás revertir esto",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, eliminarlo',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) {
            axios.delete(`/Eliminar/${id_productos}`)
              .then(response => {
                Swal.fire({
                  icon: 'success',
                  title: 'Eliminado',
                  text: 'El producto ha sido eliminado correctamente'
                });
                this.listarinv();
              })
              .catch(error => {

                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'No se pudo eliminar el producto'
                });
              });
          }
        });
      },
      formatFecha(fecha) {
          if (!fecha) {
              return 'Fecha no disponible';
          }
          const parsedDate = new Date(fecha);
          if (isNaN(parsedDate.getTime())) {
              return 'Fecha no válida';
          }
          return parsedDate.toLocaleDateString('es-CO'); // Format as DD/MM/YYYY
      },
      triggerFileInput() {
        document.getElementById('fileUpload').click();
      },
      handleFileUpload(event) {
        const file = event.target.files[0];
        if (file) {
          const formData = new FormData();
          formData.append('file', file);
         
          axios.post('/exel', formData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          })
          .then(response => {
            Swal.fire({
            title: "¡Importación Exitosa!",
            text: "El archivo se importó correctamente.",
            icon: "success",
            confirmButtonText: "OK", 
            backdrop: true 
           });
            this.listarinv(); 
          })
          .catch(error => {

            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error al importar el archivo',
              confirmButtonText: 'Aceptar'
            });
          });
        }
      },
      exportToExcel() { 
        const ws = XLSX.utils.json_to_sheet(this.inventario, {
          header: ['codigo', 'categoria', 'marca', 'talla', 'referencia', 'color', 'precio', 'fecha_de_creacion']
        });
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Inventario');
        XLSX.writeFile(wb, 'inventario.xlsx');
      },
      openUpdateModal(product) {
        this.currentProduct = { ...product };
        $('#updateProductModal').modal('show');
      },
      Actualizar() {
        this.currentProduct.codigo = String(this.currentProduct.codigo);
        
        axios.put(`/Actualizar/${this.currentProduct.id_productos}`, this.currentProduct)
          .then(response => {
            Swal.fire({
                title: "¡Actualización Exitosa!",
                text: "El producto se actualizó correctamente.",
                icon: "success",
                confirmButtonText: "OK"
            });
            $('#updateProductModal').modal('hide');
            this.listarinv(); 
          })
          .catch(error => {
       
            if (error.response && error.response.data.errors) {
              this.message = Object.values(error.response.data.errors).flat().join('<br>');
              this.messageClass = 'alert-danger';
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al actualizar el producto',
                confirmButtonText: 'Aceptar'
              });
            }
          });
      },
      cerrarModal() {
    $('#qrModal').modal('hide');
  },
      generarQRCode(codigo) {
      const codigoString = String(codigo);

      const canvas = document.getElementById('qrCanvas');
      if (canvas) {
        QRCode.toCanvas(canvas, codigoString, { width: 300 }, function (error) {
          if (error) {
            console.error('Error al generar el código QR:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error al generar el código QR'
            });
          } else {
            $('#qrModal').modal('show');
          }
        });
      } else {
        console.error('No se encontró el elemento canvas para generar el código QR.');
      }
    }
    }
  };
  </script>
  <style>
  #todo{
    height: auto;
  }
  .modal-content {
    border-radius: 10px;
  }
  .modal-header {
    background-color: #333;
    color: white;
  }
  .modal-title {
    font-size: 1.25rem;
    font-weight: bold;
  }
  .modal-body {
    background-color:white;
  }
  .form-control {
    border-radius: 5px;
  }
  .form-group label {
    font-weight: bold;
  }
  .btn-primary {
    background-color: #333;
    border-color: #333;
  }
  .btn-primary:hover {
    background-color: #555;
    border-color: #555;
  }
  .close {
    color: white;
  }
  .navbar {
    background: none; 
    box-shadow: none; 
  }
  .navbar-nav {
    gap: 20px; 
  }
  .navbar-nav .nav-link {
    color: black; 
    transition: color 0.3s, border-bottom 0.3s; 
    border-bottom: 2px solid transparent; 
  }
  .navbar-nav .nav-link:hover {
    color: black; 
    border-bottom: 2px solid black; 
  }
  .navbar-toggler {
    border-color: black; 
  }
  .container {
    max-width: 100%;
  }
  .card {
    border-radius: 10px;
    overflow: hidden;
  }
  .card-header {
    background-color: #ffffff;
    color: white;
    font-size: 1.25rem;
    font-weight: bold;
    padding-left: 0px;
  }
  .table-responsive {
    margin-top: 20px;
  }
  .table thead th {
    background-color: #a7bacc;
    color: white;
    text-align: center;
    vertical-align: middle;
  }
  .table td {
    text-align: center;
    vertical-align: middle;
  }
  .button-group {
    display: flex;
    flex-direction: column;
    align-items: start;
  }
  #fileUpload {
    display: none; 
  }
  button {
    align-self: start;
  }
  button i {
    margin-right: 5px;
  }
  .alert {
    margin-bottom: 20px;
  }
  .btn-update {
      background-color: black; 
      border: none;
      padding: 8px;
      border-radius: 5px;
      cursor: pointer;
      text-align: center;
  }
  .btn-update i {
      color: white; 
      font-size: 18px;
      text-align: center;
    
  }
  .btn-delete {
      background-color: red; 
      border: none;
      padding: 8px;
      border-radius: 5px;
      cursor: pointer;
      text-align: center;
  }
  .btn-delete i {
      color: white; 
      font-size: 18px;
      text-align: center; 
  }
  .btn-update:hover {
      background-color: #333; 
  }
  .btn-delete:hover {
      background-color: darkred; 
  }
  .btn-container {
    display: flex;
    gap: 10px; 
    justify-content: center; 
    align-items: center; 
  }
  #card {
  padding: 10px;
  border-radius: 16px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
  margin-top: -15px;
  padding-left: 0px;
  height: 80px;
  width: auto; /* Permite que el ancho del card se ajuste automáticamente */
  max-width: 1000px; /* Fija un tamaño máximo si es necesario */
  font-size: 16px;
  justify-content: flex-start; 
  display: flex; /* Mantiene flexbox para el contenido dentro del card */
}

#ContenedorNav {
  width: 100%; /* Ocupa todo el ancho disponible */
  max-width: 1200px; /* Limita el tamaño máximo del contenedor */
  height: 80px;
  margin-bottom: 4px;
  display: flex;
  justify-content: flex-start; /* Alinea el contenido a la izquierda */

  box-sizing: border-box; /* Asegura que el padding se mantenga dentro del contenedor */
}



  #inventario-button {
    background-color: black; 
    color: white; 
    width: 120px;
    border-radius: 8px;
    transition: background-color 0.3s; 
  }
  #inventario-button:hover {
        background-color: #333; 
  }
  .table {
        width: 100%; 
        margin: 0 auto;
  }
  /* Estilo del modal de QR */
  #qrModal .modal-dialog {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* Modal ocupa toda la altura de la ventana */
    max-width: 400px; /* Ajusta el ancho máximo del modal */
    width: 100%; /* Ocupa el 90% del ancho de la pantalla */
  }
  #qrModal .modal-content {
    background-color: white;      /* Fondo completamente blanco */
    border-radius: 10px;          /* Bordes redondeados */
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); /* Sombra suave */
  }
  #qrModal .modal-header {
    border-bottom: none;          /* Sin borde inferior */
    background-color: white;      /* Fondo blanco */
    color: black;                 /* Texto negro */
    border-radius: 10px 10px 0 0; /* Bordes superiores redondeados */
  }
  #qrModal .modal-title {
    font-weight: bold;
    font-size: 18px;
    color: black;                 /* Texto negro */
  }
  #qrModal .modal-body {
    padding: -50px;                /* Menor espaciado interno */
    display: flex;
    justify-content: center;       /* Centrar el contenido (el canvas) */
    align-items: center;
    flex-direction: column;        /* Alineación vertical */

  }
  #qrCanvas {
    padding: -70px;                /* Espaciado interno */
    background-color: white;      /* Fondo blanco */
    width: 100%;                  /* Ajusta el tamaño del QR para ocupar todo el ancho */
    height: 600px;                /* Ajusta el tamaño del QR para ser más largo */

  }
  #qrModal .modal-footer {
    border-top: none;             /* Sin borde superior */
    display: flex;
    justify-content: flex-end;    /* Botones alineados a la derecha */
    padding: 10px 20px;
    background-color: white;      /* Fondo blanco */
  }
  #qrModal .modal-footer .btn {
    background-color: white;      /* Botón blanco */
    color: black;                 /* Texto negro */
    border-radius: 5px;           /* Botones ligeramente redondeados */
  }
  #qrModal .modal-footer .btn:hover {
    background-color: #f0f0f0;    /* Color del botón al pasar el mouse */
  }
  #qrModal .close {
    color: black;                 /* Color de la X */
    font-size: 24px;             /* Tamaño de la X */
  }

  </style>