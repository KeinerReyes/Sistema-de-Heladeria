
<template>
<br>
<!-- Contenedor con separación entre elementos -->
<div class="d-flex justify-content-start align-items-center mb-3" style="gap: 15px;">
  <!-- Botón Agregar -->
  <button type="button" 
          class="btn" 
          style="background-color: #353A3F; color: white;" 
          data-toggle="modal" 
          data-target="#exampleModal" 
          title="Agregar">
    Agregar Producto
  </button>
  <button type="button" 
          class="btn ms-auto" 
          style="background-color: #353A3F; color: white;" 
          data-bs-toggle="modal" 
          data-bs-target="#modalCategoria"
          title="Agregar">
    Agregar Categoria
  </button>
  <!-- Input Buscar -->
  <input type="text" 
         class="form-control" 
         placeholder="Buscar producto..." 
         v-model="busquedaProducto" 
         style="max-width: 250px;" />

  <!-- Botón solo con ícono -->
  <button class="btn" title="Buscar"  @click="ListarProductos" style="padding: 0px;">
    <i class="bi bi-search" style="font-size: 2rem; color: black;"></i>
  </button>



   
</div>



  <div class="card shadow rounded-4 border-0">
  

    <div class="card-header text-white rounded-top-4" style="background-color: #353A3F;">
      <h5 class="mb-0">Lista de productos</h5>
    </div>
    <div class="card-body p-4">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="text-align: center;">
          <thead class="table-light">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Nombre</th>
              <th scope="col">Precio</th>
              <th scope="col">Categoría</th>
              <th scope="col">Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ProductoVendido in ProductoVendidos" :key="ProductoVendido.id">
              <th scope="row">{{ProductoVendido.id}}</th>
              <td>{{ProductoVendido.Nombre}}</td>
              <td>{{ formatearPesos(ProductoVendido.Precio) }}</td>

              <td>{{ ProductoVendido.Categoria }}</td>
            <td style="display: flex; gap: 15px;">
            <button class="btn btn-danger" title="Eliminar"
                    @click="eliminarProducto(ProductoVendido.id)">
              <i class="bi bi-trash"></i>
            </button>

         <button class="btn btn-primary" title="Actualizar"
                  data-toggle="modal"
                  data-target="#modalActualizar"
                  @click="abrirModalActualizar(ProductoVendido)">
            <i class="bi bi-pencil"></i>
          </button>


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

<!-- Modal de registrar productos -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document"> <!-- modal centrado -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Registrar nuevo producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="Registrar_Productos">
        <div class="modal-body">
          <div class="form-group">
            <label for="productoNombre">Nombre</label>
            <input type="text" v-model="Nombre" class="form-control" id="productoNombre" placeholder="Ingrese nombre" autocomplete="off" required>
          </div>
        <div class="form-group">
          <label for="productoPrecio">Precio</label>
         <input
          type="text"
          class="form-control"
          id="productoPrecio"
          v-model="precioFormateado"
          placeholder="Ingrese precio"
          autocomplete="off"
        />


        </div>
          <div class="form-group">
            <label for="productoCategoria">Categoría</label>
            <select class="form-control" id="productoCategoria" required v-model="Categoria">
              <option value="" disabled selected>Seleccione categoría</option>
              <option v-for="cat in CategoriasC" :key="cat.id" :value="cat.Nombre">
                {{ cat.Nombre }}
              </option>
            </select>

          </div>
        </div>
        <div class="modal-footer">
         <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-guardar" >Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

 <!-- Modal de actualizar producto -->
<div class="modal fade" id="modalActualizar" tabindex="-1" role="dialog" aria-labelledby="modalActualizarLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document"> <!-- modal centrado -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Registrar nuevo producto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form @submit.prevent="Actualizar_Producto">
        <div class="modal-body">
          <div class="form-group">
            <label for="productoNombre">Nombre</label>
            <input type="text"  v-model="productoActual.Nombre" class="form-control" id="productoNombre" placeholder="Ingrese nombre" autocomplete="off" required>
          </div>
        <div class="form-group">
         
        <div class="form-group">
        <label>Precio</label>
        <input
          type="text"
          class="form-control"
          :value="formatearPesos(productoActual.Precio)"
          @input="formatearPrecio($event); productoActual.Precio = precio"
          required
        />
      </div>



        </div>
        <div class="form-group">
          <label for="productoCategoria">Categoría</label>
          <select class="form-control" id="productoCategoria" required v-model="productoActual.Categoria">
            <option value="" disabled selected>Seleccione categoría</option>
            <option v-for="cat in CategoriasC" :key="cat.id" :value="cat.Nombre">
              {{ cat.Nombre }}
            </option>
          </select>
        </div>

        </div>
        <div class="modal-footer">
         <button type="button" class="btn btn-cerrar" data-dismiss="modal">Cerrar</button>
         <button type="submit" class="btn btn-guardar">Actualizar producto</button>

        </div>
      </form>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      
      <!-- Header -->
      <div class="modal-header">
        <h5 class="modal-title" id="modalCategoriaLabel" >Nueva Categoría</h5>
      
      </div>

      <!-- Body -->
      <div class="modal-body text-center">
        <input type="text" class="form-control" placeholder="Nombre de la categoría" v-model="NombreCategoria" required>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" @click="GuardarCategoria">Guardar</button>
      </div>

    </div>
  </div>
</div>

</template>
  
  <script>
  import axios from 'axios';

  
  export default {
    data() {
      return {
      precio: '',
      Nombre: '',
      Categoria: '',
      NombreA: '',
      PrecioA: '',
      CategoriaA: '',
      ProductoVendidos: [],
      CategoriasC: [],
      busquedaProducto: '',
      NombreCategoria: '',
       productoActual: {
          id: null,
          Nombre: '',
          Precio: '',
          Categoria: ''
        },
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
    mounted() {
    this.ListarProductos();
    this.ListarCategorias();
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
    precioFormateado: {
      get() {
        if (!this.precio) return '';
        return '$ ' + Number(this.precio).toLocaleString('es-CO');
      },
      set(value) {
        const soloNumeros = value.replace(/\D/g, '');
        this.precio = soloNumeros;
      }
    }
  
  },
    methods: {
  abrirModalActualizar(producto) {
   
    this.productoActual = { ...producto };
  },
  formatearPrecio(event) {
      const soloNumeros = event.target.value.replace(/\D/g, '');
      this.precio = soloNumeros;
  },
   formatearPesos(valor) {
    if (!valor) return '';
    return '$ ' + Number(valor).toLocaleString('es-CO');
  },
  Registrar_Productos() {
   const soloNumeros = /^\d+$/;

    if (!soloNumeros.test(this.precio)) {
      Swal.fire({
        icon: 'warning',  
        title: 'Precio inválido',
        text: 'El precio debe contener solo números.',
        confirmButtonText: 'Aceptar'
      });
      return; // detener ejecución si el precio es inválido
    }
  
    axios.post('/RegistrarProductos', {
      Nombre: this.Nombre,
      precio: this.precio,
      Categoria: this.Categoria,
    })
    .then((response) => {
      Swal.fire({
        icon: 'success',
        title: 'Producto registrado',
        text: 'El producto se ha registrado exitosamente.',
        timer: 500,
        showConfirmButton: false
      });


      setTimeout(() => {
        location.reload();
      }, 3000);
    })
    .catch((error) => {
      Swal.fire({
        icon: 'error',
        title: 'Error al registrar',
        text: 'No se pudo registrar el producto. Inténtalo nuevamente.',
        confirmButtonText: 'Cerrar'
      });
   
    });
  },
  ListarProductos(page = 1) {
    axios.get('/ListarProductos', {
      params: {
        buscar: this.busquedaProducto?.trim() || '',
        page: page
      }
    })
    .then((response) => {
      const productos = response.data.resultados;
      const paginacion = response.data.pagination;

      if (productos.length === 0) {
        this.productos = [];
        this.pagination = paginacion; // se actualiza aunque esté todo en 0
        Swal.fire({
          icon: 'warning',
          title: 'Sin resultados',
          text: 'Lo siento, no se encontró ningún producto con ese nombre.',
          confirmButtonText: 'Aceptar'
        });
      } else {
        this.ProductoVendidos = productos;
        this.pagination = paginacion;
      }
    })
    .catch((error) => {
     
      Swal.fire({
        icon: 'error',
        title: 'Error al cargar productos',
        text: 'Hubo un problema al obtener los productos.',
        confirmButtonText: 'Aceptar'
      });
    });
  },
  Actualizar_Producto() {
    axios.post('/ActualizarProducto', {
        id: this.productoActual.id,
        Nombre: this.productoActual.Nombre,
        Precio: this.productoActual.Precio,
        Categoria: this.productoActual.Categoria
    })
    .then(response => {
        Swal.fire({
        icon: 'success',
        title: 'Producto actualizado',
        timer: 2000,
        showConfirmButton: false
        });

        // Cierra el modal
        $('#modalActualizar').modal('hide');

        // Refresca la lista sin recargar toda la página
        setTimeout(() => {
        location.reload();
      }, 1000);
    })
    .catch(error => {
        Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo actualizar el producto'
        });
    });
  },
  eliminarProducto(id) {
    Swal.fire({
      title: '¿Estás seguro?',
      text: "¡No podrás revertir esto!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminarlo'
    }).then((result) => {
      if (result.isConfirmed) {
        axios.delete(`/EliminarProducto/${id}`)
          .then(response => {
            Swal.fire({
              icon: 'success',
              title: 'Producto eliminado',
              timer: 2000,
              showConfirmButton: false
            });
            this.ListarProductos(); // recarga la lista
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
  cambiarPagina(page) {
    this.pagination.current_page = page;
    this.ListarProductos(page); // ✅ pásale la página correcta
  },
  GuardarCategoria(){
        this.ListarCategorias();
      axios.post('/GuardarCategoria', {
      NombreCategoria: this.NombreCategoria

      })
      .then(response => {
          Swal.fire({
            icon: 'success',
            title: 'Categoría guardada',
            timer: 2000,
            showConfirmButton: false
          });
          this.NombreCategoria = ""; // limpiar input
      })
      .catch(error => {
          console.error(error);
          Swal.fire({
            icon: 'error',
            title: 'Error al guardar',
            text: 'Intenta de nuevo'
          });
      });
  },
  ListarCategorias(){
   axios.get('/ListarCategorias')
    .then(response => {
      this.CategoriasC = response.data; 
    })
    .catch(error => {
      console.error(error);
    });
  }
  }
  }
    
  </script>
  <style>
  tbody th, tbody td {
    text-align: center;
    vertical-align: middle;
  }
  tbody td:last-child {
    display: flex !important; 
    justify-content: center;
    gap: 15px;
    vertical-align: middle;
  }
.btn-guardar {
    background-color: #353A3F;
    color: white;
    border: none;
  }
  .btn-guardar:hover {
    background-color: #2a2d30; 
    color: white;
  }

  .btn-cerrar {
    background-color: #dc3545; 
    color: white;
    border: none;
  }
  .btn-cerrar:hover {
    background-color: #b02a37; 
    color: white;
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