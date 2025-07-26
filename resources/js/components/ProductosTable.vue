<template>
  <div>
    <!-- Loading state -->
    <div v-if="loading" class="text-center">
      <div class="spinner-border" role="status">
        <span class="sr-only">Cargando...</span>
      </div>
    </div>

    <!-- Error state -->
    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>

    <!-- Products table -->
    <div v-if="!loading && !error" class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="bg-gradient-info">
          <tr>
            <th>Nro</th>
            <th class="no-exportar">Imagen</th>
            <th>Codigo de Barras</th>
            <th>Nombre</th>
            <th>Categoria</th>
            <th>Marca</th>
            <th>Descripción</th>
            <th>Proveedor</th>
            <th>Stock</th>
            <th>Precio Venta</th>
            <th>Precio Compra</th>
            <th>Fecha Registro</th>
            <th>Activo</th>
            <th class="no-exportar">Comprar</th>
            <th class="no-exportar">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="producto in productos"
            :key="producto.id"
            :class="{ 'table-warning': producto.cantidad == 0 }"
          >
            <td>{{ producto.id }}</td>

            <!-- Imagen -->
            <td>
              <a
                href="#"
                @click.prevent="mostrarImagenModal(producto)"
                data-bs-toggle="modal"
                :data-bs-target="`#modalImagen${producto.id}`"
              >
                <img
                  :src="getImagenUrl(producto)"
                  width="50"
                  height="50"
                  class="img-thumbnail rounded shadow"
                  style="object-fit: cover;"
                  :alt="`Imagen de ${producto.nombre}`"
                >
              </a>

              <!-- Modal de imagen -->
              <div
                class="modal fade"
                :id="`modalImagen${producto.id}`"
                tabindex="-1"
                :aria-labelledby="`modalLabel${producto.id}`"
                aria-hidden="true"
              >
                <div class="modal-dialog modal-dialog-centered modal-lg">
                  <div class="modal-content bg-white">
                    <div class="modal-header bg-gradient-info">
                      <h5 class="modal-title" :id="`modalLabel${producto.id}`">
                        Imagen de {{ producto.nombre }}
                      </h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center">
                      <img :src="getImagenUrl(producto)" class="img-fluid rounded shadow" :alt="`Imagen de ${producto.nombre}`">
                    </div>
                  </div>
                </div>
              </div>
            </td>

            <!-- Código -->
            <td><code>{{ producto.codigo }}</code></td>

            <!-- Nombre -->
            <td>{{ producto.nombre }}</td>

            <!-- Categoría -->
            <td><span class="badge bg-primary">{{ producto.nombre_categoria }}</span></td>

            <!-- Marca -->
            <td>{{ producto.nombre_marca }}</td>

            <!-- Descripción -->
            <td>{{ producto.descripcion }}</td>

            <!-- Proveedor -->
            <td>{{ producto.nombre_proveedor }}</td>

            <!-- Stock -->
            <td>
              <span v-if="producto.cantidad == 0" class="badge bg-warning">Sin stock</span>
              <span v-else class="badge bg-success">{{ producto.cantidad }}</span>
            </td>

            <!-- Precio Venta -->
            <td class="text-primary">
              <strong v-if="producto.precio_venta">
                {{ producto.monedas?.codigo || 'Sin codigo' }} ${{ formatPrice(producto.precio_venta) }}
              </strong>
              <span v-else class="text-muted">No definido</span>
            </td>

            <!-- Precio Compra -->
            <td class="text-primary">
              <strong v-if="producto.precio_compra">
                {{ producto.monedas?.codigo || 'Sin codigo' }} ${{ formatPrice(producto.precio_compra) }}
              </strong>
              <span v-else class="text-muted">No definido</span>
            </td>

            <!-- Fecha -->
            <td>{{ formatDate(producto.created_at) }}</td>

            <!-- Switch Activo -->
            <td>
                <label class="custom-switch-container">
                    <input
                    type="checkbox"
                    :checked="producto.activo"
                    @change="toggleEstado(producto)"
                    :disabled="updatingStatus[producto.id]"
                    >
                    <span class="custom-switch-slider"></span>
                </label>
            </td>


            <!-- Botón Comprar -->
            <td>
              <div class="d-flex">
                <a
                  v-if="producto.cantidad == 0"
                  :href="getCompraUrl(producto.id)"
                  class="btn btn-success btn-sm me-1 d-flex align-items-center"
                >
                  <i class="fas fa-shopping-cart me-1"></i> 1.ª Compra
                </a>
                <a
                  v-else
                  :href="getCompraUrl(producto.id)"
                  class="btn btn-primary btn-sm me-1 d-flex align-items-center"
                >
                  <i class="fas fa-plus me-1"></i> Reabastecer
                </a>
              </div>
            </td>

            <!-- Acciones -->
            <td>
              <div class="d-flex">
                <a
                  :href="getEditUrl(producto.id)"
                  class="btn btn-info btn-sm me-1 d-flex align-items-center"
                >
                  <i class="fas fa-edit me-1"></i> Editar
                </a>
                <a
                  :href="getShowUrl(producto.id)"
                  class="btn btn-danger btn-sm me-1 d-flex align-items-center"
                >
                  <i class="fas fa-trash-alt me-1"></i> Eliminar
                </a>
              </div>
            </td>
          </tr>

          <!-- Empty state -->
          <tr v-if="productos.length === 0 && !loading">
            <td colspan="15" class="text-center py-4">
              <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
              <p class="text-muted">No hay productos registrados</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Refresh button -->
    <div class="text-center mt-3">
      <button @click="cargarProductos" class="btn btn-outline-primary" :disabled="loading">
        <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
        Actualizar lista
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ProductosTable',
  data() {
    return {
      productos: [],
      loading: true,
      error: null,
      updatingStatus: {} //Inicializado como objeto vacío Para controlar el estado de actualización de cada producto
    }
  },
  mounted() {
    this.cargarProductos();
  },
  methods: {
    async cargarProductos() {
      try {
        this.loading = true;
        this.error = null;

        const response = await fetch('/api/productos', {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
          }
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.success) {
          this.productos = data.data;
        } else {
          this.error = 'Error al cargar productos';
        }
      } catch (error) {
        console.error('Error:', error);
        this.error = 'Error de conexión. Verifica tu conexión a internet.';
      } finally {
        this.loading = false;
      }
    },

    async toggleEstado(producto) {

        console.log('Toggle iniciado:', producto.id, producto.activo);

        // Prevenir múltiples actualizaciones simultáneas
        if (this.updatingStatus[producto.id]) {
            return;
        }

        // En Vue 3, simplemente asigna (NO uses $set)
        this.updatingStatus[producto.id] = true;

        const valorActual = producto.activo;
        const nuevoValor = !valorActual;

        try {
            const response = await fetch(`/api/productos/${producto.id}/toggle-estado`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.getCsrfToken() // si usas CSRF en modo sesión
            },
            body: JSON.stringify({
                activo: nuevoValor,  // Usar el nuevo valor calculado
               /*  credentials: 'include' // importante si usas cookies/sanctum */
            })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if(data.success) {

                //Solo aquí cambiamos el valor en la UI
                producto.activo = nuevoValor;

                Swal.fire({
                    icon: 'success',
                    title: 'Actualizado',
                    text: `El estado del producto fue cambiado correctamente.`,
                    timer: 1800,
                    showConfirmButton: false
                });

                // (opcional) vuelve a cargar la lista desde la BD para estar 100% seguro
                //await this.cargarProductos();

            }else{

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar el estado del producto.',
                    confirmButtonColor: '#d33'
                });

            }
        } catch (error) {
            console.error('Error:', error);
            // Revertir el cambio si falló
            producto.activo = !nuevoValor;

            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'No se pudo conectar con el servidor.',
                confirmButtonColor: '#d33'
            });
        } finally {

            // En Vue 3, simplemente asigna false (NO uses $set)
            this.updatingStatus[producto.id] = false;
        }
    },

    getImagenUrl(producto) {
      if (producto.imagen_producto) {
        return `/storage/${producto.imagen_producto}`;
      }
      return '/images/placeholder-caja.png';
    },

    formatPrice(price) {
      return Number(price).toLocaleString('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    },

    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
      });
    },

    mostrarImagenModal(producto) {
      console.log('Mostrando imagen de:', producto.nombre);
    },

    getCompraUrl(productoId) {
      return `/compra/create/${productoId}`;
    },

    getEditUrl(productoId) {
      return `/producto/${productoId}/edit`;
    },

    getShowUrl(productoId) {
      return `/producto/${productoId}`;
    },

    getCsrfToken() {
      const metaTag = document.querySelector('meta[name="csrf-token"]');
      return metaTag ? metaTag.getAttribute('content') : '';
    }
  }
}
</script>

<style scoped>
.table th {
  font-size: 0.875rem;
  font-weight: 600;
}

.badge {
  font-size: 0.75rem;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
}

.custom-switch-container {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.custom-switch-container input {
    opacity: 0;
    width: 0;
    height: 0;
}

.custom-switch-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.custom-switch-slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .custom-switch-slider {
    background-color: #28a745;
}

input:checked + .custom-switch-slider:before {
    transform: translateX(26px);
}

input:disabled + .custom-switch-slider {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
