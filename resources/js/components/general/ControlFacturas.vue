<template>
<br>
<div class="d-flex align-items-center gap-2 p-3 rounded">
<button class="btn" style="background-color: #353A3F; color: white;" onclick="window.location.href='/Ganancias'">
  Ganancias
</button>

<select class="form-select w-auto" aria-label="Tipo de venta" v-model="BUsqueda" @change="ListarFacturas">
  <option value="">Seleccionar</option>
  <option value="domicilio">Domicilios</option>
  <option value="mesa">Mesas ocupadas</option>
</select>
</div>


<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card w-100 shadow rounded-4 border-0">
        <div class="card-header text-white rounded-top-4" style="background-color: #353A3F;">
          <h5 class="mb-0">Lista de productos</h5>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center border-0">
                <thead class="table-light border-0">
                    <tr class="border-0">
                    <th class="border-0">#</th>
                    <th class="border-0">N</th>
                    <th class="border-0">D</th>
                    <th class="border-0">T</th>
                    <th class="border-0">Dager</th>
                    <th class="border-0">Total</th>
                     <th class="border-0">Valor Domi</th>
                    <th class="border-0">Fecha</th>
                    <th class="border-0">Op</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="ProductoVendido in Facturas" :key="ProductoVendido.id" class="border-0">
                        <th class="border-0" scope="row">{{ ProductoVendido.id }}</th>
                        
                        <td class="border-0">{{ ProductoVendido.Nombre == 0 || !ProductoVendido.Nombre ? 'Vacío' : ProductoVendido.Nombre }}</td>
                        <td class="border-0">{{ ProductoVendido.DIrrecion == 0 || !ProductoVendido.DIrrecion ? 'Vacío' : ProductoVendido.DIrrecion }}</td>
                        <td class="border-0">{{ ProductoVendido.Telefono == 0 || !ProductoVendido.Telefono ? 'Vacío' : ProductoVendido.Telefono }}</td>
                        
                        <td class="border-0">
                       {{ (ProductoVendido.Nombre == 0 || !ProductoVendido.Nombre) ? 'Dager ' + ProductoVendido.Mesa : 'Domicilio' }}

                        </td>
                        
                        <td class="border-0">{{ formatearPesos(ProductoVendido.total) }}</td>
                       <td class="border-0">{{ formatearPesos(ProductoVendido.ValorDomi || 0) }}</td>

                        
                        <td class="border-0">{{ fechaRelativa(ProductoVendido.fecha) }}</td>
                        
                        <td style="display: flex; gap: 10px; justify-content: center; align-items: center; border: none;">
                        <!-- Ver -->
                        <span @click="ver(ProductoVendido.id)" style="cursor: pointer;" data-toggle="modal" data-target="#exampleModal">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" fill="black" viewBox="0 0 24 24">
                            <path d="M12 5c-7 0-11 7-11 7s4 7 11 7 11-7 11-7-4-7-11-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 5-5
                            5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
                            </svg>
                        </span>
                        <!-- Eliminar -->
                        <span @click="EliminarP(ProductoVendido.id)" style="cursor: pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" fill="orangered" viewBox="0 0 24 24">
                            <path d="M3 6h18v2H3V6zm2 3h14v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V9zm3 3v6h2v-6H8zm4 0v6h2v-6h-2z"/>
                            </svg>
                        </span>
                        </td>
                    </tr>
                    </tbody>

                </table>

          </div>
        </div>
      </div>
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

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog " role="document">
    <div class="modal-content">
      <div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">Detalles del producto</h5>

        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
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
        <div class="modal-footer d-flex justify-content-between w-100">
        <button type="button" class="btn btn-primary" @click="generarFactura(PorductosDes)">Imprimir</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalGanancias" tabindex="-1" aria-labelledby="modalGananciasLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow rounded">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="modalGananciasLabel">Filtrar por Fechas</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="fechaInicio" class="form-label">Fecha inicial</label>
          <input type="date" class="form-control" id="fechaInicio" v-model="Inicio">
        </div>
        <div class="mb-3">
          <label for="fechaFinal" class="form-label">Fecha final</label>
          <input type="date" class="form-control" id="fechaFinal" v-model="Final">
        </div>
        <div class="mb-3">
          <label for="tipoGanancia" class="form-label">Seleccionar tipo de ganancia</label>
          <select class="form-select" id="tipoGanancia" v-model="TipoGanancia">
            <option value="totales">Ganancias Totales</option>
            <option value="domicilio">Ganancias de Domicilio</option>
            <option value="mesa">Ganancias de Mesa</option>
          </select>
        </div>
      </div>

      <div class="modal-footer d-flex justify-content-between">
        <button type="button" class="btn btn-primary" @click="Ganancias()">Filtrar</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
</template>
  <script >
    import axios from 'axios';
    import Swal from 'sweetalert2';
    import html2pdf from 'html2pdf.js';
    import dayjs from 'dayjs'
    import relativeTime from 'dayjs/plugin/relativeTime'
    import 'dayjs/locale/es' 
    dayjs.extend(relativeTime);
    dayjs.locale('es')
    export default {
    data() {
        return {
        Facturas: [],
        TipoGanancia: 'totales',
        PorductosDes: [],
        BUsqueda: '',
        Inicio: [],
        Final:[], 
        factura:{},
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
    ListarFacturas(page = 1) {
    axios.get('/Facturas', {
        params: {
        page: page,
        BUsqueda: this.BUsqueda
        }
    })
    .then((response) => {
        const facturas = response.data.resultados;
        const paginacion = response.data.pagination;

        if (facturas.length === 0) {
        this.Facturas = [];
        this.pagination = paginacion; // aún si está vacía, se actualiza
        Swal.fire({
            icon: 'warning',
            title: 'Sin resultados',
            text: 'No se encontraron facturas.',
            confirmButtonText: 'Aceptar'
        });
        } else {
        this.Facturas = facturas;
        this.pagination = paginacion;
        }
    })
    .catch((error) => {
      
        Swal.fire({
        icon: 'error',
        title: 'Error al cargar facturas',
        text: 'Hubo un problema al obtener las facturas.',
        confirmButtonText: 'Aceptar'
        });
    });
    },
    formatearPesos(valor) {
        if (valor === null || valor === undefined) return '0';
        return '$ ' + Number(valor).toLocaleString('es-CO');
    },

    fechaRelativa(fecha) {
      return dayjs(fecha).fromNow()
    },
    cambiarPagina(page) {
    this.pagination.current_page = page;
    this.ListarFacturas(page); // ✅ pásale la página correcta
    },
    EliminarP(id){
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Este pedido será eliminado permanentemente.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
        }).then((result) => {
        if (result.isConfirmed) {
            axios.post("/eliminar",{
                id : id
            })
            .then(response => {
                Swal.fire('¡Eliminado!', 'El pedido ha sido eliminado.', 'success');
                this.ListarFacturas();
            })
            .catch(error => {
                Swal.fire('Error', 'Hubo un problema al eliminar el pedido.', 'error');
            });
        }
        });
    },
    ver(id) {
    axios.get("/Ver",
      {
      params: {
        id: id
      }
      }
    )
        .then(response => {
         this.factura = response.data;
         this.PorductosDes = this.factura.Descripcion;
       
        })
        .catch(error => {
           
        });
    },
    Ganancias() {
    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 10000, // 10 segundos
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      }
    });

    // Mostrar "cargando..."
    Toast.fire({
      icon: "info",
      title: "Cargando ganancias..."
    });

    // Enviar petición GET con Axios
    axios.get('/ganancias', {
      params: {
        inicio: this.Inicio,
        final: this.Final,
         tipo: this.TipoGanancia
      }
    })
    .then(response => {
      const total = response.data.total; // Asegúrate que en backend devuelves { total: ... }
      const numero = Number(total); // convertir a número

      Toast.fire({
        icon: "success",
        title: `Ganancia Total: $ ${numero.toLocaleString('es-CO')}`
      });

      this.Final = '';
      this.Inicio = '';

    })
    .catch(error => {
    
      Toast.fire({
        icon: "error",
        title: "Error al cargar ganancias"
      });
    });
    },
   generarFactura() {
  const datos = this.factura; // ya tienes toda la factura completa

  const productos = datos.Descripcion; // aquí están los productos decodificados
  const totalProductos = productos.reduce((sum, p) => sum + (p.cantidad * p.Precio), 0);
  const valorDomicilio = parseInt(datos.valorDomicilio || 0);
  const totalFinal = totalProductos + valorDomicilio;
  const nombreCliente = datos.Nombre != 0 ? datos.Nombre : '';
  const telefonoCliente = datos.Telefono != 0 ? datos.Telefono : '';
  const direccionCliente = datos.DIrrecion != 0 ? datos.DIrrecion : '';


  let tipoPedido = datos.Nombre == 0 ? 'Mesa' : 'Domicilio';
  const numeroFactura = datos.numeroFactura || 'N/A';

  const contenidoFactura = `
    <html>
      <head>
        <title>Factura</title>
        <style>
          @media print {
            @page { size: 58mm auto; margin: 0; }
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
          .line { border-top: 1px dashed #000; margin: 5px 0; }
          table { width: 100%; margin-top: 6px; font-size: 11px; text-align: left; }
          th, td { padding: 2px 0; }
          td:last-child, th:last-child { text-align: right; }
          .total { font-weight: bold; }
          .adicion { color: #e60000; }
          .observacion-prod {
            font-size: 11px;
            font-style: italic;
            margin-bottom: 4px;
            display: block;
            text-align: left;
          }
        </style>
      </head>
      <body>
        <h2><strong>Factura</strong></h2>
    
        <div><strong>${tipoPedido}</strong></div>
        <div>${nombreCliente}</div>
        <div>${telefonoCliente}</div>
        <div>${direccionCliente}</div>
        ${datos.observacion ? `<div><strong>Obs:</strong> ${datos.observacion}</div>` : ''}
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
            ${productos.map(p => `
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
                <tr><td colspan="3">
                  <span class="observacion-prod">👉 ${p.Observacion}</span>
                </td></tr>` : ''}
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

  const doc = iframe.contentWindow.document;
  doc.open();
  doc.write(contenidoFactura);
  doc.close();

  setTimeout(() => {
    iframe.contentWindow.focus();
    iframe.contentWindow.print();
    document.body.removeChild(iframe);
  }, 500);
}



    },
    mounted() {
    this.ListarFacturas();
    }
};
  </script>
    <style >
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

    .icon-btn {
    background: none;
    border: none;
    padding: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    }

    .icon-btn:hover {
    transform: scale(1.1);
    }



  }
    </style>