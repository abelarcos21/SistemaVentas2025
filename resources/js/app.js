import './bootstrap';

//Modificacion para integrar vuejs3 al proyecto
import './bootstrap';
import { createApp } from 'vue';

// Importar componentes
import ProductosTable from './components/ProductosTable.vue';

// Crear la aplicación Vue
const app = createApp({});

// Registrar componentes globalmente
app.component('productos-table', ProductosTable);

// Montar la aplicación si existe el elemento
const productosApp = document.getElementById('productos-app');
if (productosApp) {
    app.mount('#productos-app');
}
