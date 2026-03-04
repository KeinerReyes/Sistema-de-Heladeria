<template>
    <br>
    <div v-if="user.rol === 0" class="access-denied">
    <h1>Acceso Denegado</h1>
    <p>No tienes permisos para acceder a este módulo.</p>
    </div>
    <div v-else>
      <div id="todonav">
           <div id="ContenedorNav" >
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
    <div class="container-fluid mt-4">
      <div class="card shadow-lg">
        <div class="card-body">
          <form @submit.prevent="registrar" class="mt-4">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="codigo" class="text-dark">Código</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                    </div>
                    <input type="text" class="form-control form-control-lg custom-input" id="codigo" v-model="codigo" required autocomplete="off">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="categoria" class="text-dark">Categoría</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-tags"></i></span>
                    </div>
                    <select class="form-control form-control-lg custom-input" id="categoria" v-model="categoria" required>
                      <option value="" disabled>Selecciona una categoría</option>
                      <option v-for="categoria in categorias" :key="categoria.id_productos" :value="categoria.categoria">
                        {{ categoria.categoria }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="marca" class="text-dark">Marca</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-copyright"></i></span>
                    </div>
                    <input type="text" class="form-control form-control-lg custom-input" id="marca" v-model="marca"  required autocomplete="off">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
        <label for="talla" class="text-dark">Talla</label>
        <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-ruler"></i></span>
          </div>
          <input 
            type="text" 
            class="form-control form-control-lg custom-input" 
            id="talla" 
            v-model="talla" 
            @input="buscarTallas" 
            @focus="mostrarOpciones = true" 
            autocomplete="off" 
            placeholder="Escribe una talla"
            required
          >
          
          <select 
            v-if="mostrarOpciones && resultados.length >= 0" 
            class="form-control form-control-lg custom-select"
            @change="seleccionarTalla($event)" 
            size="10"
          >
            <option disabled value="">Debes seleccionar una de estas tallas</option>
            <option 
              v-for="(opcion, index) in resultados" 
              :key="index" 
              :value="opcion.talla"
            >
              {{ opcion.talla }}
            </option>
          </select>
        </div>
      </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="referencia" class="text-dark">Referencia</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    </div>
                    <input type="text" class="form-control form-control-lg custom-input" id="referencia" v-model="referencia"  required autocomplete="off">
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="color" class="text-dark">Color</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-palette"></i></span>
                    </div>
                    <input type="text" class="form-control form-control-lg custom-input" id="color" v-model="color"  required autocomplete="off">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="precio" class="text-dark">Precio</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                    </div>
                    <input type="number" class="form-control form-control-lg custom-input" id="precio" v-model="precio" required>
                  </div>
                </div>
              </div>
            </div>
            <div id="botones">
            <button type="submit" class="btn btn-dark btn-lg custom-button mt-4" >Registrar</button>
           
          </div>
          </form>
        </div>
      </div>
    </div>   
  </div>
  <div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="miModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content" >
        <div class="modal-header">
          <h5 class="modal-title" id="miModalLabel">Registrar Perfume</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="modal">
            <div class="mb-3 row align-items-center">
              <label for="codigo" class="col-sm-3 col-form-label">
                <i class="fas fa-barcode"></i> Código
              </label>
              <div class="col-sm-9">
                <input type="text" class="form-control" id="codigo" placeholder="Código del perfume" autocomplete="off" v-model="codigoLocion">
              </div>
            </div>
              <div class="mb-3 row align-items-center">
                <label for="categoria" class="col-sm-3 col-form-label">
                  <i class="fas fa-tags"></i> Categoría
                </label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="categoria" placeholder="Categoría" autocomplete="off" v-model="CategoriaLocion">
                </div>
              </div>

              <div class="mb-3 row align-items-center">
                <label for="mililitros" class="col-sm-3 col-form-label">
                  <i class="fas fa-wine-bottle"></i> Mililitros
                </label>
                <div class="col-sm-9">
                  <input type="number" class="form-control" id="mililitros" placeholder="Cantidad de Mililitros" autocomplete="off" v-model="MlLociones">
                </div>
              </div>

              <div class="mb-3 row align-items-center">
                <label for="precio" class="col-sm-3 col-form-label">
                  <i class="fas fa-dollar-sign"></i> Precio
                </label>
                <div class="col-sm-9">
                  <input type="number" class="form-control" id="precio" placeholder="Precio" autocomplete="off" v-model="PrecioLocion">
                </div>
              </div>
          </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" @click="RegistrarLociones">Guardar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalAgregarML" tabindex="-1" aria-labelledby="modalAgregarMLLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAgregarMLLabel">Ajuste de Mililitros</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="modal1">
 
            <div class="mb-3">
              <label for="cantidadML" class="form-label">Codigo</label>
              <select v-model="SelecioneCodigo" class="form-control" id="codigoLocion">
                <option disabled value="">Selecciona un código</option>
                <option v-for="codigo in Codigos" :key="codigo" :value="codigo">
                  {{ codigo }}
                </option>
              </select>
            </div>

            <div class="mb-3">
              <label for="cantidadML" class="form-label">Cantidad a Agregar (ml)</label>
              <input type="number" class="form-control" id="cantidadML" placeholder="Ingresa la cantidad en ml" v-model="NuevoML">
            </div>

            <div class="mb-3">
              <label for="cantidadML" class="form-label">Precio</label>
              <input type="number" class="form-control" id="cantidadML" placeholder="Ingresa Precio" v-model="PrecioNv">
            </div>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-primary" @click="AgrgarLociones">Agregar</button>
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
        codigo: '',
        codigoLocion:'',
        CategoriaLocion:'',
        MlLociones:'',
        PrecioLocion:'',
        user:{},
        categoria: '',
        marca: '',
        talla: '',
        mostrarOpciones: false,
        resultados: [],
        referencia: '',
        color: '',
        precio: '',
        categorias: [],
        Codigos:[],
        SelecioneCodigo:'',
        NuevoML:'',
        PrecioNv:'',
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
    registrar() {
        const formData = {
          codigo: this.codigo,
          categoria: this.categoria,
          marca: this.marca,
          talla: this.talla,
          referencia: this.referencia,
          color: this.color,
          precio: this.precio
        };
        axios.post('/Registrar', formData)
          .then(response => {
            Swal.fire({
              icon: 'success',
              title: 'Éxito',
              text: 'Producto registrado correctamente'
            });
            this.codigo = '';
            this.categoria = '';
            this.marca = '';
            this.talla = '';
            this.referencia = '';
            this.color = '';
            this.precio = '';

          })
          .catch(error => {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'No se pudo registrar el producto'
            });
          });
    },
    RegistrarLociones() {
      if (!this.codigoLocion || !this.CategoriaLocion || !this.MlLociones || !this.PrecioLocion) {
        Swal.fire({
          icon: 'warning',
          title: 'Campos incompletos',
          text: 'Todos los campos deben estar completos antes de registrar la loción.',
          confirmButtonText: 'Entendido'
        });
        return; 
      }
      axios.get('/RegistrarLociones', {
        params: {
          codigoLocion: this.codigoLocion,
          CategoriaLocion: this.CategoriaLocion,
          MlLociones: this.MlLociones,
          PrecioLocion: this.PrecioLocion,
        }
      })
      .then(response => {
      this.codigoLocion = '',
      this.CategoriaLocion = '',
      this.MlLociones = '',
      this.PrecioLocion = '',
      
        Swal.fire({
          icon: 'success',
          title: 'Registro exitoso',
          text: 'La loción se ha registrado correctamente.',
          confirmButtonText: 'Aceptar'
        });
      })
      .catch(error => {
        Swal.fire({
          icon: 'error',
          title: 'Error de registro',
          text: 'Hubo un problema al registrar la loción. Inténtalo nuevamente.',
          confirmButtonText: 'Aceptar'
        });
      });
    },
    AgrgarLociones() {
    if (!this.SelecioneCodigo || !this.NuevoML || !this.PrecioNv) {
      Swal.fire({
        icon: 'warning',
        title: 'Campos incompletos',
        text: 'Todos los campos deben estar completos antes de registrar la loción.',
        confirmButtonText: 'Entendido'
      });
      return; 
    }
    axios.get('/AgrgarLociones', {
      params: {
        SelecioneCodigo: this.SelecioneCodigo,
        NuevoML: this.NuevoML,
        PrecioNv: this.PrecioNv
      }
    })
    .then(response => {
      this.SelecioneCodigo = '',
      this.NuevoML = '',
      this.PrecioNv = '',
      Swal.fire({
        icon: 'success',
        title: 'Registro exitoso',
        text: 'La loción se ha registrado correctamente.',
        confirmButtonText: 'Aceptar'
      });
    })
    .catch(error => {
      Swal.fire({
        icon: 'error',
        title: 'Error de registro',
        text: 'Hubo un problema al registrar la loción. Inténtalo nuevamente.',
        confirmButtonText: 'Aceptar'
      });
    });
    },
    obtenerCategorias() {
        axios.get('/obtenerCategorias')
          .then(response => {
            this.categorias = response.data;
          })
          .catch(error => {
          });
    },
    buscarTallas() {
      if (this.talla.length >= 0) {
        axios.get('/buscar-tallas', {
          params: {
            query: this.talla
          }
        })
        .then(response => {
          this.resultados = response.data; // Guardamos los resultados filtrados
          this.mostrarOpciones = true;     // Mostramos el select con opciones
        })
        .catch(error => {
        });
      } else {
        this.resultados = [];  // Limpiamos resultados si el input está vacío
        this.mostrarOpciones = false; // Ocultamos el select
      }
    },
    // Función para seleccionar una talla desde el select
    seleccionarTalla(event) {
      const tallaSeleccionada = event.target.value;
      this.talla = tallaSeleccionada;
      this.mostrarOpciones = false; // Ocultar opciones después de seleccionar
    },
    SacarCodigos(){
      axios.get('/SacarCodigos', {
      })
      .then((response) => {
      this.Codigos = response.data;
      }).catch((error) => {
      });
    }
    },
    mounted() {
      this.obtenerCategorias();
      this.Rol();
      this.SacarCodigos();
    }
  };
  </script> 
  <style scoped>
.line-separator {
    border-top: 2px solid #000000; 
    width: 100%;
    margin: 0 auto; 
}
  .container-fluid {
    max-width: 90%;
    padding: 9px;
    border-radius: 10px;
    background-color:white; 
  }
  .card-body {
    padding-left: 30px;
    padding-right: 30px;
  }
  form{
    margin-top: -60px;
  }
  .custom-input {
    background-color:white; 
    border-radius: 5px;
  }
  .custom-input:focus {
    background-color:white; 
  }
  .custom-button {
    background-color: #000;

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
  margin-bottom: -20px;
  display: flex;
  justify-content: flex-start; /* Alinea el contenido a la izquierda */
  margin-left: 78px;
  box-sizing: border-box; /* Asegura que el padding se mantenga dentro del contenedor */
}
  .card {
    border-radius: 10px;
    overflow: hidden;
    border-radius: 10px;
    background-color: #ffffff;

  }
  .card-header {
    color: white;
    font-size: 1.25rem;
    font-weight: bold;
    padding-left: 0px;
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
  .custom-select {
  position: absolute;
  margin-top: 60px;
  z-index: 1000;
  width: 100%;
  max-height: 150px;
  overflow-y: auto;
  background-color: white;
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
  #registrarBtn{
    margin-left: 20px;
    background: #343a40;
    color: white;
  }
  #modal{
    height: auto;
    height: 250px;
  }
  #botones{
    display: flex;
  }
  #botonAgregar{
    margin-left: 20px;
  }
  #modal1{
    height: auto;
    height: 270px;
  }
  </style>