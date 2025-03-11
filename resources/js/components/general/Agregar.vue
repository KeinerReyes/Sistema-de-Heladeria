<template>
  <br>
    <div v-if="user.rol === 0" class="access-denied">
    <h1>Acceso Denegado</h1>
    <p>No tienes permisos para acceder a este módulo.</p>
    </div>
    <div v-else> 
    <div id="todonav" >
           <div id="ContenedorNav">
            <div class="card" id="card">
              <nav class="navbar navbar-expand-lg navbar-light mb-4 w-100">
                  <div id="container">
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
                                  <a class="nav-link nav-button" href="RegistrarProductos">
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
                          </ul>
                      </div>
                  </div>
              </nav>
          </div>
        </div>
        </div>
        <div class="container">
              <div class="card search-card">
                  <div class="card-body">
                      <div class="search-container">
                          <input
                              type="text"
                              placeholder="Buscar en la tabla..."
                              class="search-input"
                              v-model="buscar"
                              @input="buscarReferencia"
                          />
                          <div class="cantidad-productos">
                              <div class="cantidad-icon-container">
                                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-database" viewBox="0 0 16 16">
                                      <path d="M4.318 2.687C5.234 2.271 6.536 2 8 2s2.766.27 3.682.687C12.644 3.125 13 3.627 13 4c0 .374-.356.875-1.318 1.313C10.766 5.729 9.464 6 8 6s-2.766-.27-3.682-.687C3.356 4.875 3 4.373 3 4c0-.374.356-.875 1.318-1.313M13 5.698V7c0 .374-.356.875-1.318 1.313C10.766 8.729 9.464 9 8 9s-2.766-.27-3.682-.687C3.356 7.875 3 7.373 3 7V5.698c.271.202.58.378.904.525C4.978 6.711 6.427 7 8 7s3.022-.289 4.096-.777A5 5 0 0 0 13 5.698M14 4c0-1.007-.875-1.755-1.904-2.223C11.022 1.289 9.573 1 8 1s-3.022.289-4.096.777C2.875 2.245 2 2.993 2 4v9c0 1.007.875 1.755 1.904 2.223C4.978 15.71 6.427 16 8 16s3.022-.289 4.096-.777C13.125 14.755 14 14.007 14 13zm-1 4.698V10c0 .374-.356.875-1.318 1.313C10.766 11.729 9.464 12 8 12s-2.766-.27-3.682-.687C3.356 10.875 3 10.373 3 10V8.698c.271.202.58.378.904.525C4.978 9.71 6.427 10 8 10s3.022-.289 4.096-.777A5 5 0 0 0 13 8.698m0 3V13c0 .374-.356.875-1.318 1.313C10.766 14.729 9.464 15 8 15s-2.766-.27-3.682-.687C3.356 13.875 3 13.373 3 13v-1.302c.271.202.58.378.904.525C4.978 12.71 6.427 13 8 13s3.022-.289 4.096-.777c.324-.147.633-.323.904-.525"/>
                                  </svg>
                              </div>
                              <span class="cantidad-text">
                                  Cantidad: {{ cantidadProducto }}
                              </span>
                          </div>
                      </div>
                  </div>
              </div>

      <!-- Modal -->
      <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" >
          <div class="modal-content" >
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">Agregar Cantidad de Productos</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label for="cantidadProducto" class="form-label">Cantidad</label>
                <input
                  type="number"
                  class="form-control"
                  id="cantidadProducto"
                  placeholder="Ingresa la cantidad"
                  v-model="cantidadAgregar"
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-primary" @click="agregarCantidad">Agregar</button>
            </div>
          </div>
        </div>
      </div>
  

      <div class="modal fade" id="addedProductsModal" tabindex="-1" aria-labelledby="addedProductsLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addedProductsLabel">Productos Recientemente Añadidos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div  id="table">
          <table  class="table">
            <thead id="tableth">
              <tr>
                <th>Código</th>
                <th>Categoría</th>
                <th>Referencia</th>
                <th>Talla</th>
                <th>Color</th>
                <th>Marca</th>
                <th>Precio</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="producto in productosRecientes" :key="producto.codigo">
                <td>{{ producto.codigo }}</td>
                <td>{{ producto.categoria }}</td>
                <td>{{ producto.referencia }}</td>
                <td id="talla" style="text-align: center;">
                  <input type="text" v-model="producto.talla" style="width: 90px; text-align: center;" />
                </td>
                <td>{{ producto.color }}</td>
                <td>{{ producto.marca }}</td>
                <td>{{ producto.precio }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" @click="guardarEdiciones">Guardar</button>
      </div>
    </div>
  </div>
</div>
  
      <div class="table-wrapper">
        <table class="data-table">
          <thead>
            <tr>
              <th>Categoría</th>
              <th>Referencia</th>
              <th>Talla</th>
              <th>Color</th>
              <th>Marca</th>
              <th>Precio</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="buscar in refe" :key="buscar.id_productos">
              <td>{{ buscar.categoria }}</td>
              <td>{{ buscar.referencia }}</td>
              <td>{{ buscar.talla }}</td>
              <td>{{ buscar.color }}</td>
              <td>{{ buscar.marca }}</td>
              <td>{{ formatCurrency(buscar.precio) }}</td>
              <td>
                <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#staticBackdrop"  @click="AgregarProductos(buscar)">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-folder-plus" viewBox="0 0 16 16">
                    <path d="m.5 3 .04.87a2 2 0 0 0-.342 1.311l.637 7A2 2 0 0 0 2.826 14H9v-1H2.826a1 1 0 0 1-.995-.91l-.637-7A1 1 0 0 1 2.19 4h11.62a1 1 0 0 1 .996 1.09L14.54 8h1.005l.256-2.819A2 2 0 0 0 13.81 3H9.828a2 2 0 0 1-1.414-.586l-.828-.828A2 2 0 0 0 6.172 1H2.5a2 2 0 0 0-2 2m5.672-1a1 1 0 0 1 .707.293L7.586 3H2.19q-.362.002-.683.12L1.5 2.98a1 1 0 0 1 1-.98z"/>
                    <path d="M13.5 9a.5.5 0 0 1 .5.5V11h1.5a.5.5 0 1 1 0 1H14v1.5a.5.5 0 1 1-1 0V12h-1.5a.5.5 0 0 1 0-1H13V9.5a.5.5 0 0 1 .5-.5"/>
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
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
        buscar: '', 
        user:{},
        refe: [], 
        cantidadProducto: null,
        cantidadAgregar: 1,
        productoActual: null,
        productosRecientes: []
      };
    },
    methods: {
      Rol() {/// sacr roles para ahcer validacion
        axios.get('/Rol', {
        })
        .then((response) => {
        this.user = response.data;
        }).catch((error) => {
          });
        },
      buscarReferencia() {
        axios
          .get('/obtenerproductoreferencia', {
            params: { referencia: this.buscar },
          })
          .then((response) => {
            this.refe = [response.data];
            this.obtenerCantidadProducto(this.buscar);
          });
      },
  
      obtenerCantidadProducto(referencia) {
        axios
          .get(`/cantidadProducto?referencia=${referencia}`)
          .then((response) => {
            this.cantidadProducto = response.data.cantidad;
          })
          .catch((error) => {
          });
      },
  
      AgregarProductos(producto) {
        this.productoActual = producto;
      },
      formatCurrency(amount) {  // Format Colombian currency
            const formatter = new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0,  
            });
            return formatter.format(amount);
        },
  
          agregarCantidad() {
        if (this.cantidadAgregar <= 0 || !this.productoActual) {
          Swal.fire('Error', 'Ingrese una cantidad válida y seleccione un producto.', 'error');
          return;
        }
  
        axios
          .post('/agregarproductos', {
            producto: this.productoActual,
            cantidad: this.cantidadAgregar,
          })
          .then((response) => {
            if (response.status === 200 ) {
              Swal.fire('Éxito', 'Productos agregados exitosamente.', 'success'); 
              this.cantidadAgregar = 1;        
              $('#staticBackdrop').modal('hide');
              this.obtenerCantidadProducto(this.productoActual.referencia);
              this.productosRecientes = response.data.productos;
  
              $('#addedProductsModal').modal('show');
            } else {
              Swal.fire('Error', 'No se pudieron agregar los productos. Intente nuevamente.', 'error');
            }
          })
          .catch((error) => {
            Swal.fire('Error', 'No se pudo agregar los productos.', 'error');
          });
      },
      handleCloseAndRefresh() {
        $('#addedProductsModal').modal('hide');
        window.location.reload();
    },
      actualizarTalla(producto) {
        axios
          .post(`/actualizar-talla/${producto.codigo}`, {
            talla: producto.talla,
          })
          .then((response) => {
          })
          .catch((error) => {
            console.error('Error al actualizar la talla:', error);
            Swal.fire('Error', 'No se pudo actualizar la talla.', 'error');
          });
      },

    guardarEdiciones() {
      const promises = this.productosRecientes.map(producto => {
        return this.actualizarTalla(producto);
      });
      Promise.all(promises)
        .then(() => {
          window.location.reload();
        })
        .catch(error => {
          console.error('Error al actualizar las tallas:', error);
          Swal.fire('Error', 'No se pudieron actualizar todas las tallas.', 'error');
        });
    },
  
    },
    mounted() {
      this.Rol();
    }
  };
  </script>
  
  <style scoped>
  
  /* Estilos para el modal de productos añadidos */
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
    background-color: white;
    color: #fff;
  }
  .modal-footer {
    background-color: #f9f9f9;
  }
  
  .container {
    width: 100%;
    max-width: 1200px;
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
  
  .cantidad-productos {
    display: flex;
    align-items: center;
    gap: 12px;
    background-color: #ffffff; /* Fondo blanco para un look limpio */
    padding: 12px 16px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08); /* Sombras más marcadas */
    border: 1px solid #e0e0e0; /* Borde sutil */
    transition: box-shadow 0.3s ease; /* Transición suave para efectos de hover */
  }
  
  .cantidad-productos:hover {
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15), 0 2px 5px rgba(0, 0, 0, 0.1); /* Efecto de hover más pronunciado */
  }
  
  .cantidad-icon-container {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px; /* Tamaño del contenedor del icono */
    height: 20px; /* Tamaño del contenedor del icono */
    background-color: #f5f5f5; /* Fondo claro para el contenedor del icono */
    border-radius: 50%; /* Forma circular para el fondo del icono */
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); /* Sombra interna para el icono */
  }
  
  .cantidad-icon {
    font-size: 20px; /* Tamaño del icono */
    color: #333; /* Color del icono */
  }
  
  .cantidad-text {
    color: #333;
    font-size: 16px;
    font-weight: 500;
    display: flex;
    align-items: center;
  }
  
  .cantidad-text strong {
    color: #000;
    font-weight: 600;
    margin-left: 5px;
  }
  
  .table-wrapper {
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    background-color: #fff;

  }
  
  .data-table {
    width: 100%;
    border-collapse: collapse;
  }
  
  .data-table th,
  .data-table td {
    padding: 14px;
    text-align: left;
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
    background-color:white;
  }
  
  .modal-header {
    border-bottom: 1px solid #ddd;
  }
  
  .modal-title {
    color: #333;
    font-weight: bold;
  }
  
  .modal-body {
    padding: 10px;
    max-height: 500px; 
    overflow-y: auto; 
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
  #modal{
    margin-top: -200px;
  }
  .table {
          width: 100%; 
          margin: 0 auto;
    }
    #table{
        text-align: center;
        font-size: 28px;
        border-collapse: collapse;
        width: 100%;
        height: 68%;
    }
    .table, .table th, .table td {
    border: none; 
    }
    .table-group-divider td {
    text-align: center; 
    }
    #tableth{
    text-align: center;
    border: none;
    background-color: white;
    }
    #todonav{
        margin-bottom: 10px;
        margin-top: -10px;
    }
    #card {
    padding: 10px;
    border-radius: 16px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    padding-left: 10px;
    height: 80px;
    max-width: 1000px; 
    font-size: 16px;
    justify-content: flex-start; 
    display: flex; 
  }
    #ContenedorNav {
      width: 100%; 
      max-width: 1200px;
      height: 80px;
      margin-bottom: -10px;
      display: flex;
      margin-left: 60px;
      justify-content: flex-start; 
      box-sizing: border-box;
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
    .card-header {
      background-color: #ffffff;
      color: white;
      font-size: 1.25rem;
      font-weight: bold;
      padding-left: 0px;
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
    #container{
      display: flex;
      gap: 10px; 
      justify-content: center; 
      align-items: center; 
    }
    .access-denied {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background-color: #ffffff; 
    color: #333333; 
    border: 1px solid #e0e0e0; 
    border-radius: 12px; 
    padding: 30px;
    margin: 40px auto;
    max-width: 450px; 
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1); 
    transition: box-shadow 0.3s, transform 0.3s; 
  }
  .access-denied:hover {
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2); 
    transform: translateY(-2px); 
  }
  .access-denied h1 {
    font-size: 26px; 
    font-family: 'Arial', sans-serif; 
    margin-bottom: 15px; 
  }
  .access-denied p {
    font-size: 18px; 
    margin-bottom: 25px; 
    text-align: center;
  }
  .access-denied button {
    background-color: #007bff;
    color: white; 
    border: none; 
    border-radius: 6px; 
    padding: 12px 24px; 
    cursor: pointer; 
    font-size: 16px;
    transition: background-color 0.3s, transform 0.3s; 
  }
  .access-denied button:hover {
    background-color: #0056b3; 
    transform: translateY(-1px);
  }
    

  </style>