<template>
  <br>                             
  <div  id="CuerpoC">
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">

  <!-- Fechas y búsqueda a la izquierda -->
  <div class="d-flex flex-wrap align-items-center">
    <!-- Fecha inicio -->
    <input 
      type="date" 
      class="form-control form-control-sm fecha-input me-2 mb-2"
      style="width: auto;"
      v-model="fechaInicio"
    >

    <!-- Fecha final -->
    <input 
      type="date" 
      class="form-control form-control-sm fecha-input me-2 mb-2"
      style="width: auto;"
      v-model="fechaFinal"
    >

    <!-- Botón de búsqueda -->
    <button 
      class="btn btn-sm mb-2 d-flex align-items-center btn-buscar"
      @click="ListarDatos()"
    >
      <i class="bi bi-search me-1"> Buscar</i>
    </button>
  </div>

  <!-- Botones a la derecha -->
  <div class="d-flex flex-wrap">

    <button 
      id="pdf"
      class="btn btn-danger me-2 mb-2"
      @click="generarPDF"
    >
      <i class="fas fa-file-pdf me-1"></i> PDF
    </button>

    <!-- Botón de Control de Inventario -->
    <a href="/FacturasControl" class="btn mb-2 text-white" style="background-color: #353A3F;">
    Control de Inventario
    </a>

  </div>

</div>

    <!-- Tabla -->
  <div class="tabla-contenedor table-responsive">
    <table class="table table-borderless text-center w-100">
      <thead class="table-dark">
        <tr>
          
          <th>Nombre</th>
          <th>Cantidad</th>
          <th>Total</th>
           <th>Mp</th>
          <th>Fecha de compra</th>
        </tr>
      </thead>
      <tbody>
        <!-- Si hay compras -->
        <tr v-for="(compra, index) in compras" :key="index">
        
          <td class="texto-ajustado">{{ compra.Nombre }}</td>
        <td class="texto-ajustado">{{ compra.Cantidad }}</td>
         <td>
        {{ compra.PrecioTotal.toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0, maximumFractionDigits: 0 }) }}
        </td>
        <td class="texto-ajustado">{{ compra.Mp }}</td>



          <td>{{ formatearFecha(compra.Fecha) }}</td>
        </tr>

        <!-- Mensaje si no hay compras -->
        <tr v-if="compras.length === 0">
          <td colspan="4" class="text-center text-danger py-4 fw-semibold" style="font-size: 1rem;">
            ⚠️ No hay productos registrados en el rango de fechas seleccionado.
          </td>
        </tr>
      </tbody>
    </table>
  </div>


  </div>
  <br>
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
  <div 
    class="modal fade" 
    id="modalCompra" 
    tabindex="-1" 
    aria-labelledby="modalCompraLabel" 
    aria-hidden="true"
  >
   <div class="modal-dialog modal-dialog-centered" style="max-width: 500px; width: 90%;">
  <div class="modal-content">
    <div class="modal-header" style="background-color: #353A3F;">
      <h5 class="modal-title text-white" id="modalCompraLabel">Nueva Compra</h5>
      
    </div>
    <div class="modal-body">
      <form>
        <div class="mb-3">
          <label class="form-label">Descripción</label>
          <input type="text" class="form-control"  v-model="descripcion">
        </div>
        <div class="mb-3">
          <label class="form-label">Precio</label>
          <input type="number" class="form-control" v-model="precio">
        </div>
      </form>
    </div>
   <div class="modal-footer">
    <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal">
      Cerrar
    </button>
    <button type="button" class="btn text-white" style="background-color: #353A3F;" @click="GuardarP">
      Guardar
    </button>
  </div>

</div>
</div>
  </div>
</template>

<script>
  import jsPDF from 'jspdf';
  import 'jspdf-autotable';
  export default {
  data() {
    return {
    descripcion: '',
    precio: '',
    compras: [],
    ComprasSin: [],
    fechaInicio: '',
    fechaFinal: '',
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
  methods:{
  formatearFecha(fecha) {
    if (!fecha) return '';
    const opciones = { day: 'numeric', month: 'long', year: 'numeric' };
    return new Date(fecha).toLocaleDateString('es-ES', opciones);
  },
  formatearCOP(valor) {
      if (valor == null || isNaN(valor)) return '';
      return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0
      }).format(valor);
  },
  ListarDatos(page = 1) {
    axios.get(`/DatosVentas`, {
      params: {
        page: page,
        fechaInicio: this.fechaInicio,
        fechaFinal: this.fechaFinal
      }
    })
    .then(response => {
      this.compras = response.data.resultados;
      this.ComprasSin = response.data.resultadosSin;
      this.pagination = response.data.pagination;
    })
    .catch(error => {
      console.error('Error al obtener los datos:', error);
    });
  },
  generarPDF() {
    if (!this.ComprasSin.length) {
      alert("No hay datos para exportar a PDF");
      return;
    }

    const doc = new jsPDF();

    // ==== CABECERA ====
    doc.setFontSize(16);
    doc.text("Reporte de Compras", 14, 15);
    doc.setFontSize(10);
    doc.text(`Fecha de generación: ${new Date().toLocaleDateString("es-CO")}`, 14, 22);

    // ==== TABLA PRINCIPAL ====
    const columnas = ['ID', 'Nombre', 'Total', 'Cantidad','Mp', 'Fecha'];
    const cuerpo = this.ComprasSin.map(item => [
      item.id,
      item.Nombre,
      `$${Number(item.PrecioTotal).toLocaleString("es-CO")}`,
      item.Cantidad,
        item.Mp,
      new Date(item.Fecha).toLocaleDateString("es-CO", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
        hour12: true
      })
    ]);

    doc.autoTable({
      head: [columnas],
      body: cuerpo,
      startY: 28
    });

    // ==== CALCULOS (AGRUPADO POR NOMBRE) ====
    const resumenPorNombre = {};
    let totalGeneral = 0;

    this.ComprasSin.forEach(item => {
      const nombre = String(item.Nombre).trim(); // agrupamos por nombre
      const precio = parseFloat(item.PrecioUnitario.toString().replace(/[^0-9.]/g, "")) || 0;
      const cantidad = parseInt(item.Cantidad) || 0;
      const subtotal = precio * cantidad;

      if (!resumenPorNombre[nombre]) {
        resumenPorNombre[nombre] = { cantidad: 0, total: 0 };
      }

      resumenPorNombre[nombre].cantidad += cantidad;
      resumenPorNombre[nombre].total += subtotal;

      totalGeneral += subtotal;
    });

    // Convertir objeto a array para la tabla resumen
    const resumenArray = Object.keys(resumenPorNombre).map(nombre => [
      nombre,
      resumenPorNombre[nombre].cantidad,
      `$${resumenPorNombre[nombre].total.toLocaleString("es-CO")}`
    ]);

    // ==== RESUMEN POR PRODUCTO ====
    doc.autoTable({
      head: [['Producto', 'Cantidad Total', 'Total $']],
      body: resumenArray,
      startY: doc.lastAutoTable.finalY + 10
    });

    // ==== RANGO DE FECHAS Y TOTAL GENERAL ====
    let fechaInicio = this.fechaInicio || "N/A";
    let fechaFinal = this.fechaFinal || "N/A";

    doc.setFontSize(12);
    doc.text(
      `Rango de fechas: ${fechaInicio} → ${fechaFinal}`,
      14,
      doc.lastAutoTable.finalY + 15
    );

    doc.setFontSize(14);
    doc.text(
      `TOTAL GENERAL: $${totalGeneral.toLocaleString("es-CO")}`,
      14,
      doc.lastAutoTable.finalY + 25
    );

    // ==== GUARDAR ====
    doc.save(`compras_${new Date().toISOString().split('T')[0]}.pdf`);
  },
  cambiarPagina(page) {
    this.pagination.current_page = page;
    this.ListarDatos(page);
  },


  },

  
mounted() {
  this.ListarDatos();
}

};
</script>

<style scoped>
/* Contenedor con bordes redondeados y sombra */
.tabla-contenedor {
  background-color: white;
  border-radius: 12px;
  box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.08);
}

/* Texto largo ajustado */
.texto-ajustado {
  white-space: normal;
  word-break: break-word;
  max-width: 300px;
}

/* Ajustes para móviles */
@media (max-width: 576px) {
  .table td, .table th {
    font-size: 14px;
    padding: 8px;
  }
  .texto-ajustado {
    max-width: 150px;
  }


}
  #pdf{
    margin-left: 20px;
  }/* Estilo moderno para inputs de fecha */
  .fecha-input {
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 4px 10px;
    font-size: 1.1rem;
    background-color: #f8f9fa;
    transition: all 0.2s ease-in-out;
    margin-left: 10px;
    margin-right: 20px;
  }

  .fecha-input:focus {
    border-color: #353A3F;
    background-color: white;
    box-shadow: 0 0 5px rgba(53, 58, 63, 0.5);
    outline: none;
  }

  .fecha-input::-webkit-calendar-picker-indicator {
    cursor: pointer;
    filter: invert(40%) sepia(10%) saturate(500%) hue-rotate(180deg);
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
  .btn-buscar {
  background-color: #353A3F;
  color: white;
  border: none;
  }

  .btn-buscar:hover {
    background-color: #2a2e32; /* un poco más oscuro al pasar el mouse */
  }

</style>
