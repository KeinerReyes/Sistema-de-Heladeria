import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';



export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/FacturaKairo.js',
                'resources/js/Inventario.js',
                'resources/js/Clientes.js',
                'resources/js/ControlInventario.js',
                'resources/js/Agregar.js',
                'resources/js/ControlC.js',
                'resources/js/ganacia.js',
                'resources/js/Perfil.js',
                'resources/js/RegistrarProducto.js',
                'resources/js/Ventas.js',
                'resources/js/stock.js'
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