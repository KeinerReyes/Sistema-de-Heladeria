import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';



export default defineConfig({
    plugins: [
        laravel({
            input: [
                
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/Agregar.js',
                
                'resources/js/Clientes.js',
                'resources/js/ControlC.js',
                'resources/js/ControlFacturas.js',
                'resources/js/ControlInventario.js',
                'resources/js/DomiCasa.js',
                'resources/js/FacturaCasa.js',
                'resources/js/FacturaKairo.js',
                'resources/js/Ganacias.js', // revisa si está bien escrito
                'resources/js/ganacia.js',  // veo que tienes 2, ojo aquí
                'resources/js/Inventario.js',
               
                'resources/js/PedidosCasa.js',
                'resources/js/Perfil.js',
                'resources/js/PerfilCasa.js',
                'resources/js/ProductosCasa.js',
                'resources/js/RegistrarProducto.js',
                'resources/js/stock.js',
                'resources/js/Ventas.js'
            

            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});