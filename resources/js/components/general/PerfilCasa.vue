<template>
  <div>
    <br>
  <div class="container-fluid py-3">
    <div class="row justify-content-center g-3">
      <!-- Productos Vendidos Hoy -->
      <div class="col-md-4 col-12">
        <div class="small-box text-white" data-bs-toggle="modal" data-bs-target="#exampleModal" style="cursor: pointer; background-color: #353A3F;">
          <div class="inner">
            <h2>{{ ProductosVendidosHoy }}</h2>
            <p>Productos Vendidos Hoy</p>
          </div>
        </div>
      </div>

      <!-- Ganancia Diaria -->
      <div class="col-md-4 col-12">
        <div class="small-box text-white"  style="background-color: #353A3F;">
          <div class="inner">
            <h2>{{ formatCurrency(ganancias_diarias) }}</h2>
            <p>Ganancia Diaria</p>
          </div>
          <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="white" class="bi bi-currency-dollar" viewBox="0 0 16 16">
              <path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73z"/>
            </svg>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-12">
        <div class="small-box text-white"  style="background-color: #353A3F;">
          <div class="inner">
            <h2>{{ formatCurrency(GananciasDomiDiarias) }}</h2>
            <p>Ganancias Diarias de Domicilio</p>
          </div>
          <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="white" class="bi bi-truck" viewBox="0 0 16 16">
              <path d="M0 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v3h1a1 1 0 0 1 .8.4l2 2.667a1 1 0 0 1 .2.6v3.334a1 1 0 0 1-1 1h-.5a2.5 2.5 0 0 1-5 0H6a2.5 2.5 0 0 1-5 0H1a1 1 0 0 1-1-1V1zm11 0H1v10h.5a2.5 2.5 0 0 1 5 0H11V1zm1 4.5V11h.5a2.5 2.5 0 0 1 5 0H15v-2.667L13.333 5.5H12zM4.5 13a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm8 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
            </svg>
          </div>
        </div>
      </div>

    </div>
    
      <div class="card card-dark">
        <div class="card-header">
          <h3 class="card-title">Análisis Mensual de Ganancias</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
            <button type="button" class="btn btn-tool" data-card-widget="remove">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        <div class="card-body">
          <div class="chart">
            <canvas id="barChart" style="min-height: 300px; height: 280px; max-height: 250px; max-width: 100%;"></canvas>
          </div>
        </div>
      </div>


<div id="contenedor">
    <div class="col-md-12">
      <div class="card">
          <div class="card-header">
              <div class="d-flex justify-content-between align-items-center w-100">
                  <div class="d-flex align-items-center">
                    <h3 class="card-title mb-0">Resumen de Ventas por Producto</h3>
                  </div>
                  <div class="card-tools">
                  </div>
              </div>
          </div>
              <div class="card-body p-0">
                <div id="table">
                 <div style="max-height:340px; overflow-y: auto; border-radius: 8px; background-color: white;">
                  <table class="table table-hover text-center" style="border-collapse: collapse; width: 100%;">
                    <thead id="tableth" style="position: sticky; top: 0; z-index: 1; background-color: white; border-bottom: 2px solid #f0f0f0;">
                      <tr>
                        <th scope="col" style="border: none; text-align: center;">Nombre</th>
                        <th scope="col" style="border: none; text-align: center;">Cantidad</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="Producto in Resumen" :key="Producto.id" style="border: none;">
                        <td style="border: none; text-align: center;">{{ Producto.nombre }}</td>
                        <td style="border: none; text-align: center;">{{ Producto.cantidad }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

              
                <div 
                    v-if="Resumen.length === 0"  class="alert alert-warning mt-3" style="width: 100%; max-width: 600px; margin: 0 auto; padding: 15px; text-align: center; margin-bottom: 10px;">
                    No se han registrado productos vendidos hasta el momento.
                </div>
          </div>
        
                </div>
      
              </div>
    
            </div>
            
</div>
</div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable" style="max-height: 70vh;max-width: 80vh; margin-top: 5vh; margin-bottom: 5vh;">
    <div class="modal-content" style="max-height: 100%; overflow: hidden;">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Detalles de Productos Vendidos Hoy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
        <div class="table-responsive">
          <table class="table">
            <thead id="tableth">
              <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre</th>
                <th scope="col">Cantidad</th>
                <th scope="col">Precio</th>
              </tr>
            </thead>
            <tbody class="table-group-divider">
              <tr v-for="(Producto, index) in detallesProductos" :key="Producto.id">
                <td>{{ index + 1 }}</td>
                <td>{{ Producto.nombre }}</td>
                <td>{{ Producto.cantidad }}</td>
                <td>{{ formatCurrency(Producto.total_compra) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-if="detallesProductos.length === 0" class="alert alert-warning mt-3 custom-alert">
          No se ha realizado ninguna compra.
        </div>
      </div>
    </div>
  </div>
</div>

</template>
<script>

import axios from 'axios';
import Swal from 'sweetalert2';
import Chart from 'chart.js/auto';

export default {
data() {
    return {
    user:{},
    total_productos_vendidos: '',
    ganancias_diarias: '',
    detallesProductos:[],
    cantidad_productos_vendidos_hoy: '',
    cantidad_clientes:'',
    VnetasDelDia: [],
    MejorProducto: [],
    PeorProducto: [],
    MejoresCliente: [],
    Resumen: [],
    GananciasDomiDiarias: [],
    barChartData: {
    labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
    datasets: [
      {
        label: 'Ganancias Del  Mes',
        backgroundColor: 'rgba(55, 58, 63, 1)',
        borderColor: 'rgba(55, 58, 63, 1)',
        borderWidth: 1,
        data: []
      },

      ]
      },
      barChartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            stacked: true
          },
          y: {
            stacked: true
          }
        }
      }
    };
},
methods: {
  Nav(){
    axios.get('/NavCasa', {
        })
    .then((response) => {
      this.GananciasDomiDiarias = response.data.GananciasDomiDiarias;
      this.ganancias_diarias = response.data.ganancias_diarias;
      this.ProductosVendidosHoy = response.data.ProductosVendidosHoy;
      this.detallesProductos = response.data.detallesProductos;
      }).catch((error) => {
      });
  },
  formatCurrency(amount) {
            return new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 0, // Sin decimales
                maximumFractionDigits: 0  // Sin decimales
            }).format(amount);
  },
  fetchData() {
      axios.get('/getMonthlyDataCasa')
        .then(response => {
          this.barChartData.datasets[0].data = response.data.total_mensual;
          this.renderChart();
        })
        .catch(error => {
        });
  },
  renderChart() {
    var barChartCanvas = document.getElementById('barChart').getContext('2d');
    new Chart(barChartCanvas, {
      type: 'bar',
      data: this.barChartData,
      options: this.barChartOptions
    });
  },
 
  SacarResumen(){
    axios.get('/SacarResumenCasa', {
      })
      .then((response) => {
      this.Resumen = response.data;
      }).catch((error) => {
      }); 
  }
},
mounted() {
  this.Nav();
  this.fetchData();
  
  this.SacarResumen();

}
};
</script>
<style>
  *{
    height: auto;
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
  /* CSS para personalizar el modal */

 
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
  }
  .custom-alert {
    border: 1px solid #f39c12; 
    color: #e67e22; 
    padding: 15px; 
    border-radius: 5px; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
    margin-top: 10px; 
    max-width: 70%; 
    text-align: center;
    margin-left: 15%;
  }



</style>