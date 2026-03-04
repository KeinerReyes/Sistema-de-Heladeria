<template>
<br>

<div class="container">
  <div class="card shadow rounded-4 border-0 p-4">
    <div class="row">
      <!-- Columna 1 -->
     <div class="col-12 col-md-6">

        <br>
      <div class="table-responsive-sm">
       <table class="table text-center align-middle table-bordered text-white">
            <thead style="background-color: #343a40; color: white;">
                <tr>
                <th style="border: 1px solid white;">#</th>
                   <th style="border: 1px solid white;">Cantidad</th>
                <th style="border: 1px solid white;">Nombre</th>
                <th style="border: 1px solid white;">Precio</th>
               
                </tr>
            </thead>
          <tbody>
            <tr v-for="(producto, index) in productosOrdenados" :key="producto.id">
                <!-- Cantidad editable con botones -->
                <td style="width: 100px; border: 1px solid white;">
                    <div class="d-flex justify-content-center align-items-center gap-2">
                    <button 
                        class="btn btn-sm" 
                        @click="decrementarCantidad(index)"
                        :disabled="producto.cantidad <= 1"
                        title="Disminuir cantidad"
                        style="background-color: white; color: black; padding: 0 12px; font-weight: bold; font-size: 1.5rem; border-radius: 4px; line-height: 1;"
                    >-</button>
                    <button 
                        class="btn btn-sm" 
                        @click="incrementarCantidad(index)"
                        title="Aumentar cantidad"
                        style="background-color: white; color: black; padding: 0 12px; font-weight: bold; font-size: 1.5rem; border-radius: 4px; line-height: 1;"
                    >+</button>
                    </div>
                </td>

               <td style="border: 1px solid white;"><span 
                    style="background-color: white; color: black; padding: 0 10px; font-weight: bold; font-size: 1.3rem; border-radius: 4px;"
                    >{{ producto.cantidad }}</span>
                </td>
                <!-- Nombre -->
                <td style="border: 1px solid white;">{{ producto.Nombre }}</td>

                <!-- Precio total -->
                <td style="border: 1px solid white;">{{ formatoPrecio(producto.Precio * producto.cantidad) }}</td>
                
                </tr>
                <!-- Si no hay productos -->
                <tr v-if="productosOrdenados.length === 0">
                <td colspan="3" style="border: 1px solid white; color: gray; text-align: center; vertical-align: middle; height: 100px;">
                    No hay productos seleccionados
                </td>
                </tr>
            </tbody>
        </table>
        </div>
        <br>
        <br>
     <tr v-if="productosOrdenados.length > 0">
        <td colspan="3" style="border-top: 2px solid white; text-align: left; font-weight: bold; font-size: 1.3rem;">
          Total
        </td>
        <td style="border-top: 2px solid white; text-align: right; font-weight: bold; font-size: 1.3rem;">
          {{ formatoPrecio(totalGeneral) }}
        </td>
    </tr>

    </div>

      <!-- Columna 2 -->
        <div class="col-12 col-md-6">
        <div class="mb-3">
         <div class="input-group mb-3">
        <select
          class="form-select"
          v-model="Busqueda"
          @change="ListarProductos"
        >
            <option value="" >Seleccione categoría</option>
        <option v-for="cat in CategoriasC" :key="cat.id" :value="cat.Nombre">
        {{ cat.Nombre }}
        </option>

        </select>
       
        <button class="btn btn-dark" type="button" @click="ListarProductos">
          <i class="bi bi-search"></i>
        </button>
      </div>

        </div>

        <div class="table-responsive" style="max-height: 440px; overflow-y: auto;">
        <table class="table text-center align-middle" style="border: 1px solid white; color: white;">
            <thead style="background-color: #343a40; color: white; position: sticky; top: 0; z-index: 1;">
                <tr>
                <th style="border: 1px solid white;"></th>
                <th style="border: 1px solid white;">Nombre</th>
                <th style="border: 1px solid white;">Precio</th>
                </tr>
            </thead>
            <tbody>
                <tr 
                v-for="Producto in Productos" 
                :key="Producto.id"
                @click="seleccionarProducto(Producto)" 
                style="cursor: pointer;"
                >
                <td style="border: 1px solid white;">
                    <input 
                    type="checkbox" 
                    :checked="estaSeleccionado(Producto.id)" 
                    style="transform: scale(1.5); cursor: pointer;" 
                    @click.stop="seleccionarProducto(Producto)"
                    >
                </td>
                <td style="border: 1px solid white;">{{ Producto.Nombre }}</td>
                <td style="border: 1px solid white;">{{ formatoPrecio(Producto.Precio) }}</td>
                </tr>
            </tbody>
            </table>
        </div>
    </div>
    </div>
  </div>
</div>
<div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px; padding: 10px;">
  <input
    type="text"
    placeholder="Observación"
    v-model="Observacion"
    style="flex: 1 1 200px; padding: 7px; border-radius: 10px; border: 1px solid #ccc; min-width: 150px; box-sizing: border-box;"
  />


  <button
      style="
          background-color: #353A3F;
          color: white;
          padding: 8px 16px;
          border: none;
          border-radius: 6px;
          font-size: 0.95rem;
          font-weight: 500;
          transition: background-color 0.3s ease;
      "
      onmouseover="this.style.backgroundColor='#2c3136'"
      onmouseout="this.style.backgroundColor='#353A3F'"
      @click="preguntarDomicilio()"
  >
      Imprimir
  </button>


</div>








</template>
<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {

  data() {
    return {
      
    Productos:[],
    Busqueda: '',
    productosSeleccionados: [],
    Observacion: '',
    nombreCliente: '',
    direccionCliente: '',
    telefonoCliente: '',
    valorDomicilio:'',
    total: 0,
    numeroMesa: '',
    MesasDisponibles: [],
    CategoriasC: [],
     categoriaSeleccionada: "",
  
    };
  },
  watch: {
  Busqueda(newVal) {
    this.ListarProductos();
  }
},
computed: {
    productosOrdenados() {
      // Aquí retornamos el array de seleccionados tal cual, para manejar la ordenación real
      return this.productosSeleccionados;
    },
     totalGeneral() {
    return this.productosOrdenados.reduce((total, producto) => {
      return total + producto.Precio * producto.cantidad;
    }, 0);
  }
  },
methods: {
  
  ListarProductos() {
  axios.get('/ListarP', {
      params: {
      Busqueda: this.Busqueda?.trim() || ''
      }
  })
  .then((response) => {
      this.Productos = response.data; 
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
  formatoPrecio(valor) {
  return '$ ' + Number(valor).toLocaleString('es-CO');
  },
    seleccionarProducto(producto) {
  const index = this.productosSeleccionados.findIndex(p => p.id === producto.id);

  if (index === -1) {
      // Copiamos el producto y le agregamos cantidad: 1
      this.productosSeleccionados.push({ ...producto, cantidad: 1 });
  } else {
      this.productosSeleccionados.splice(index, 1);
  }
  },
  estaSeleccionado(id) {
    return this.productosSeleccionados.some(p => p.id === id);
  },
  incrementarCantidad(index) {
    this.productosOrdenados[index].cantidad++;
  },
  decrementarCantidad(index) {
    if (this.productosOrdenados[index].cantidad > 1) {
      this.productosOrdenados[index].cantidad--;
    }
  },
  preguntarDomicilio() {
    const self = this;

  

     Swal.fire({
      title: '¿El servicio es de domicilio?',
      icon: 'question',
      showCancelButton: true,
      showConfirmButton: true,
      showDenyButton: true,

      confirmButtonText: 'Sí',
      denyButtonText: 'Cancelar pedido',
      cancelButtonText: 'No (Pager)',

      // Quita reverseButtons para que "Cancelar pedido" no esté al final
      allowOutsideClick: false,
      allowEscapeKey: false
    }).then((result) => {
       if (result.isDenied) {
          return self.CancelarPedido();
        }

    if (result.isConfirmed) {
      Swal.fire({
        title: 'Datos del cliente',
        html: `
        <input id="nombreCliente" class="swal2-input" placeholder="Nombre del cliente" autocomplete="off">
        <input id="direccionCliente" class="swal2-input" placeholder="Dirección" autocomplete="off">
        <input id="telefonoCliente" class="swal2-input" placeholder="Número telefónico" autocomplete="off">
        <input id="domi" class="swal2-input" placeholder="Valor del domi" inputmode="numeric" autocomplete="off">

                `,
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar pedido',
        allowOutsideClick: false,
        allowEscapeKey: false,

        didOpen: () => {
          const domiInput = document.getElementById('domi');
          domiInput.addEventListener('input', () => {
            let valor = domiInput.value.replace(/\D/g, ''); // quitar todo lo que no sea número
            if (valor) {
              valor = new Intl.NumberFormat('es-CO').format(Number(valor));
              domiInput.value = `$${valor}`;
            } else {
              domiInput.value = '';
            }
          });
        },

        preConfirm: () => {
          const nombre = document.getElementById('nombreCliente').value.trim();
          const direccion = document.getElementById('direccionCliente').value.trim();
          const telefono = document.getElementById('telefonoCliente').value.trim();
          const domi = document.getElementById('domi').value.trim();

          if (!nombre || !direccion || !telefono || !domi) {
            Swal.showValidationMessage('Todos los campos son obligatorios');
            return false;
          }

          if (!/^[0-9]{10}$/.test(telefono)) {
            Swal.showValidationMessage('El número telefónico debe tener 10 dígitos');
            return false;
          }

          // Obtener solo el número limpio del valor del domicilio (sin $ ni puntos)
          const valorDomi = parseInt(domi.replace(/[$.,\s]/g, ''));

          if (isNaN(valorDomi) || valorDomi <= 0) {
            Swal.showValidationMessage('El valor del domicilio debe ser un número válido');
            return false;
          }

          return { nombre, direccion, telefono, valorDomi };
        }
      }).then((resultadoDomicilio) => {
        if (resultadoDomicilio.isDismissed) {
          return self.CancelarPedido();
        }

        if (resultadoDomicilio.isConfirmed && resultadoDomicilio.value) {
          const datos = resultadoDomicilio.value;
          self.nombreCliente = datos.nombre;
          self.direccionCliente = datos.direccion;
          self.telefonoCliente = datos.telefono;
          self.valorDomicilio = datos.valorDomi;
          self.numeroMesa = null;

          Swal.fire('Guardado', 'Los datos del cliente fueron registrados.', 'success')
            .then(() => self.EnviarProductos())
             self.generarFactura();
        }
      });
    }
 else {
axios.get('/MesasDisponibles').then(response => {
  const mesasDisponibles = response.data.mesas_activas;
  const mesasAgregar = response.data.mesas_agregar;

  const opcionesActivas = mesasDisponibles.map(mesa => 
    `<option value="${mesa.Nombre}">Mesa ${mesa.Nombre}</option>`
  ).join('');

  const opcionesAgregar = mesasAgregar.map(mesa => 
    `<option value="${mesa.Nombre}">Mesa ${mesa.Nombre}</option>`
  ).join('');

  Swal.fire({
    title: 'Seleccionar una Pager',
    html: `
      <label style="text-align:left; display:block;">Pager activas:</label>
      <select id="selectActiva" class="swal2-input">
        <option value="">-- Seleccione Pager activa --</option>
        ${opcionesActivas}
      </select>

      <label style="margin-top:15px; text-align:left; display:block;">Pager para agregar:</label>
      <select id="selectAgregar" class="swal2-input">
        <option value="">-- Seleccione Pager a agregar --</option>
        ${opcionesAgregar}
      </select>
    `,
    showCancelButton: true,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar pedido',
    allowOutsideClick: false,
    allowEscapeKey: false,
    preConfirm: () => {
      const activa = document.getElementById('selectActiva').value;
      const agregar = document.getElementById('selectAgregar').value;

      if ((activa && agregar) || (!activa && !agregar)) {
        Swal.showValidationMessage('Debe seleccionar solo una mesa, activa o para agregar.');
        return false;
      }

      return { activa, agregar };
    }
  }).then((resultado) => {
    if (resultado.isDismissed) {
      return self.CancelarPedido();
    }

    if (resultado.isConfirmed) {
      const { activa, agregar } = resultado.value;

      // Limpiar campos anteriores
      self.nombreCliente = '';
      self.direccionCliente = '';
      self.telefonoCliente = '';

      if (activa) {
        self.numeroMesa = activa;
        self.mesaAgregar = null;
        Swal.fire('Pager registrada', `Pager activa: ${activa}`, 'success')
          .then(() => {
          
            self.EnviarProductos();
             self.generarFactura();
          });
      } else {
        self.numeroMesa = null;
        self.mesaAgregar = agregar;
        Swal.fire('Pager registrada', `Pager agregada: ${agregar}`, 'success')
          .then(() => {
          
            self.EnviarProductos();
             self.generarFactura();
          });
      }
    }
  });

}).catch(error => {
          
            Swal.fire('Error', 'No se pudieron cargar las mesas disponibles.', 'error');
          });
        }
      });
    },

 CancelarPedido() {
    // Lógica cuando el usuario decide cancelar el pedido.
    // Por ejemplo, limpiar todo y mostrar un mensaje.
    this.nombreCliente = '';
    this.direccionCliente = '';
    this.telefonoCliente = '';
    this.numeroMesa = null;
    // Si hay un arreglo de productos seleccionados, vacíalo:
    // this.productosSeleccionados = [];
    Swal.fire('Pedido cancelado', 'Su pedido ha sido cancelado.', 'info');
  },
  SacarMesas() {
    axios.get('/MesasDisponibles')
      .then(response => {
        this.MesasDisponibles = response.data
      
      })
      .catch(error => {
       
      })
  },
  EnviarProductos() {
  
  if (this.productosSeleccionados.length === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Sin productos',
      text: 'Debe seleccionar al menos un producto.',
    });
    return;
  }
      const total = this.totalGeneral;
     axios.post('/FacturasImpri', {
        nombre: this.nombreCliente,
        total: total,
        direccion: this.direccionCliente,
        telefono: this.telefonoCliente,
        observacion: this.Observacion || '',
        numeroMesa: this.numeroMesa,
        AgregarMesa: this.mesaAgregar,
        productos: this.productosSeleccionados,
        valorDomicilio : this.valorDomicilio
      })

    
      .then((response) => {
        Swal.fire({
          icon: 'success',
          title: 'Pedido enviado',
          text: 'El pedido fue enviado correctamente.'
        });

        // Opcional: limpiar después de enviar
        this.productosSeleccionados = [];
        this.nombreCliente = '';
        this.direccionCliente = '';
        this.telefonoCliente = '';
        this.Observacion = '';
        this.valorDomicilio = '';
      })
      .catch((error) => {
     
        Swal.fire({
          icon: 'error',
          title: 'Error al enviar',
          text: 'Ocurrió un problema al enviar el pedido.'
        });
      });
  },
  generarFactura() {
    const totalProductos = this.productosSeleccionados.reduce((sum, p) => sum + (p.cantidad * p.Precio), 0);
    const valorDomicilio = parseInt(this.valorDomicilio || 0);
    const totalFinal = totalProductos + valorDomicilio;
    const numeroFactura = this.getNumeroFacturaDelDia();

    let tipoPedido = 'Domicilio';
    if (this.numeroMesa) {
      tipoPedido = `Pager ${this.numeroMesa}`;
    } else if (this.mesaAgregar) {
      tipoPedido = `Pager (Nueva): ${this.mesaAgregar}`;
    }

    const contenidoFactura = `
      <html>
        <head>
          <title>Factura</title>
          <style>
            @media print {
              @page {
                size: 58mm auto;
                margin: 0;
              }
              body {
              width: 182px;
              font-family: monospace;
              font-size: 11px;
              text-align: center;
              padding: 5px;
            }

            }

            body {
              width: 182px;
              font-family: monospace;
              font-size: 11px;
              text-align: center;
              padding: 5px;
            }

            h2, h3 {
              margin: 4px 0;
            }

            .line {
              border-top: 1px dashed #000;
              margin: 5px 0;
            }

            table {
              width: 100%;
              margin-top: 6px;
              font-size: 11px;
              text-align: left;
            }

            th, td {
              padding: 2px 0;
            }

            td:last-child, th:last-child {
              text-align: right;
            }

            .total {
              font-weight: bold;
            }

            .adicion {
              color: #e60000;
            }

            .observacion-prod {
              font-size: 11px;
              font-style: italic;
              margin-bottom: 4px;
              display: block;
              text-align: left;
            }

            .footer {
              margin-top: 6px;
              font-size: 10px;
            }
          </style>
        </head>
        <body>
          <h2><strong>Factura</strong></h2>
          <h3>Orden #${numeroFactura}</h3>
          <div><strong>${tipoPedido}</strong></div>
          <div>${this.nombreCliente || ''}</div>
          <div>${this.telefonoCliente || ''}</div>
          <div>${this.direccionCliente || ''}</div>
          ${this.Observacion ? `<div><strong>Obs:</strong> ${this.Observacion}</div>` : ''}
          <div class="line"></div>

          <table>
            <thead>
              <tr>
                <th>Cant</th>
                <th>Producto</th>
                <th>Precio</th>
              </tr>
            </thead>
            <tbody>
              ${this.productosSeleccionados.map(p => `
                <tr class="${p.Categoria === 'Adiciones' ? 'adicion' : ''}">
                  <td>${p.cantidad}</td>
                  <td>${p.Nombre}</td>
                  <td>${p.Precio.toLocaleString('es-CO', {
                    style: 'currency',
                    currency: 'COP',
                    minimumFractionDigits: 0
                  })}</td>
                </tr>
                ${p.Observacion ? `
                  <tr><td colspan="3"><span class="observacion-prod">👉 ${p.Observacion}</span></td></tr>
                ` : ''}
              `).join('')}
            </tbody>
          </table>

          <div class="line"></div>

          <table>
            <tr>
              <td>Subtotal:</td>
              <td style="text-align: right;">${totalProductos.toLocaleString('es-CO', {
                style: 'currency', currency: 'COP', minimumFractionDigits: 0
              })}</td>
            </tr>
            <tr>
              <td>Domicilio:</td>
              <td style="text-align: right;">${valorDomicilio.toLocaleString('es-CO', {
                style: 'currency', currency: 'COP', minimumFractionDigits: 0
              })}</td>
            </tr>
            <tr class="total">
              <td>Total:</td>
              <td style="text-align: right;">${totalFinal.toLocaleString('es-CO', {
                style: 'currency', currency: 'COP', minimumFractionDigits: 0
              })}</td>
            </tr>
          </table>
        </body>
      </html>
    `;

    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    document.body.appendChild(iframe);

   const ventana = window.open('', '_blank');
    ventana.document.write(contenidoFactura);
    ventana.document.close();

  ventana.focus();
    setTimeout(() => {
      ventana.print();

      // Espera un poco más antes de cerrar para que la impresión se dispare bien
      setTimeout(() => {
        ventana.close();
      }, 800);

    }, 300);


  },
  getNumeroFacturaDelDia() {
    const ahora = new Date();
    if (ahora.getHours() < 5) {
      ahora.setDate(ahora.getDate() - 1);
    }
    const fechaClave = `facturas_${ahora.getFullYear()}-${(ahora.getMonth() + 1).toString().padStart(2, '0')}-${ahora.getDate().toString().padStart(2, '0')}`;
    let numero = parseInt(localStorage.getItem(fechaClave) || "0");
    numero++; // sumamos 1
    localStorage.setItem(fechaClave, numero); // actualizamos
    return numero;
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

    },
    mounted() {
    this.ListarCategorias();
    this.ListarProductos();
    this.SacarMesas();
    this.productosOrdenados.forEach(p => {
       if (!p.cantidad || p.cantidad < 1) p.cantidad = 1;
      });
    }
};
</script>
<style >
body {
  background-color: #f8f9fa;
}

.sidebar-left {
  background-color: #353A3F;
  color: white;
  min-height: 100vh;
  padding: 1rem;
}

.sidebar-right {
  background-color: #e9ecef;
  min-height: 100vh;
  padding: 1rem;
}

.main-content {
  padding: 2rem;
}

.floating-btn {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #353A3F;
  color: white;
  border: none;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  font-weight: bold;
  font-size: 1.2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  z-index: 999;
}

.floating-btn:hover {
  background-color: #2E3338;
}
.col-6 {
    background-color: white;
    height: auto;
    max-height:1000px ;
  }

</style>