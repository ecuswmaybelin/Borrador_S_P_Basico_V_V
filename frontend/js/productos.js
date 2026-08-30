/**
 * PRODUCTOS.JS - Logica del modulo de productos
 */

var todosLosProductos = [];
var todasLasCategorias = [];

document.addEventListener('DOMContentLoaded', function() {
    cargarProductos();
});

async function cargarProductos() {
    var data = await fetchData('../backend/productos/listar.php');
    if (data && Array.isArray(data)) {
        todosLosProductos = data;
        renderizarProductos(data);
        cargarCategorias(data);
    } else if (data && data.error) {
        document.getElementById('listaProductos').innerHTML =
            '<tr><td colspan="7" class="text-center">Error: ' + data.error + '</td></tr>';
    } else {
        document.getElementById('listaProductos').innerHTML =
            '<tr><td colspan="7" class="text-center">No se pudieron cargar los productos</td></tr>';
    }
}

function cargarCategorias(productos) {
    var cats = {};
    productos.forEach(function(p) {
        if (p.categoria_id && p.categoria_nombre) {
            cats[p.categoria_id] = p.categoria_nombre;
        }
    });
    todasLasCategorias = Object.entries(cats);

    var selectFiltro = document.getElementById('filtroCategoria');
    if (selectFiltro) {
        var html = '<option value="">Todas las categorias</option>';
        todasLasCategorias.forEach(function(c) {
            html += '<option value="' + c[0] + '">' + c[1] + '</option>';
        });
        selectFiltro.innerHTML = html;
    }

    var selectModal = document.getElementById('productoCategoria');
    if (selectModal) {
        var html2 = '<option value="">Sin categoria</option>';
        todasLasCategorias.forEach(function(c) {
            html2 += '<option value="' + c[0] + '">' + c[1] + '</option>';
        });
        selectModal.innerHTML = html2;
    }
}

function renderizarProductos(productos) {
    var tbody = document.getElementById('listaProductos');
    if (!tbody) return;

    if (productos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No se encontraron productos</td></tr>';
        return;
    }

    var html = '';
    productos.forEach(function(p) {
        var nombreLimpio = escapeHtml(p.nombre);
        var descLimpio = p.descripcion ? escapeHtml(p.descripcion) : '';
        var catLimpio = p.categoria_nombre ? escapeHtml(p.categoria_nombre) : 'Sin categoria';
        var esBajo = p.stock <= p.stock_minimo;

        html += '<tr>';
        html += '<td>#' + p.id + '</td>';
        html += '<td><strong>' + nombreLimpio + '</strong>';
        if (descLimpio) html += '<br><small style="color: var(--color-texto-claro);">' + descLimpio + '</small>';
        html += '</td>';
        html += '<td>' + catLimpio + '</td>';
        html += '<td><strong>S/. ' + parseFloat(p.precio).toFixed(2) + '</strong></td>';
        html += '<td>';
        html += '<span class="' + (esBajo ? 'text-danger' : '') + '" style="font-weight: 600;">' + p.stock + '</span>';
        if (esBajo) html += '<br><small class="text-danger">Stock bajo</small>';
        html += '</td>';
        html += '<td><span class="badge badge-success">Activo</span></td>';
        html += '<td>';
        html += '<button class="btn btn-warning btn-sm" onclick="editarProducto(' + p.id + ')" title="Editar">&#9998;</button> ';
        html += '<button class="btn btn-danger btn-sm" onclick="eliminarProducto(' + p.id + ', \'' + nombreLimpio.replace(/'/g, "\\'") + '\')" title="Eliminar">&#128465;</button>';
        html += '</td>';
        html += '</tr>';
    });

    tbody.innerHTML = html;
}

function buscarProductos(texto) {
    var filtered = todosLosProductos.filter(function(p) {
        return p.nombre.toLowerCase().indexOf(texto.toLowerCase()) !== -1 ||
               (p.descripcion && p.descripcion.toLowerCase().indexOf(texto.toLowerCase()) !== -1);
    });
    renderizarProductos(filtered);
}

function filtrarPorCategoria(categoriaId) {
    if (!categoriaId) {
        renderizarProductos(todosLosProductos);
        return;
    }
    var filtered = todosLosProductos.filter(function(p) {
        return p.categoria_id == categoriaId;
    });
    renderizarProductos(filtered);
}

function abrirModalCrear() {
    document.getElementById('modalProductoTitulo').textContent = 'Nuevo Producto';
    document.getElementById('formProducto').reset();
    document.getElementById('productoId').value = '';
    document.getElementById('productoStockMinimo').value = '5';
    abrirModal('modalProducto');
}

function editarProducto(id) {
    var producto = null;
    for (var i = 0; i < todosLosProductos.length; i++) {
        if (todosLosProductos[i].id == id) {
            producto = todosLosProductos[i];
            break;
        }
    }
    if (!producto) return;

    document.getElementById('modalProductoTitulo').textContent = 'Editar Producto';
    document.getElementById('productoId').value = producto.id;
    document.getElementById('productoNombre').value = producto.nombre;
    document.getElementById('productoDescripcion').value = producto.descripcion || '';
    document.getElementById('productoPrecio').value = producto.precio;
    document.getElementById('productoStock').value = producto.stock;
    document.getElementById('productoStockMinimo').value = producto.stock_minimo;
    document.getElementById('productoCategoria').value = producto.categoria_id || '';

    abrirModal('modalProducto');
}

async function guardarProducto() {
    var id = document.getElementById('productoId').value;
    var nombre = document.getElementById('productoNombre').value.trim();
    var descripcion = document.getElementById('productoDescripcion').value.trim();
    var precio = document.getElementById('productoPrecio').value;
    var stock = document.getElementById('productoStock').value;
    var stock_minimo = document.getElementById('productoStockMinimo').value;
    var categoria_id = document.getElementById('productoCategoria').value;

    if (!nombre) {
        showToast('El nombre es obligatorio', 'error');
        return;
    }
    if (!precio || parseFloat(precio) < 0) {
        showToast('Ingrese un precio valido', 'error');
        return;
    }
    if (stock === '' || parseInt(stock) < 0) {
        showToast('Ingrese un stock valido', 'error');
        return;
    }

    var formData = new FormData();
    formData.append('nombre', nombre);
    formData.append('descripcion', descripcion);
    formData.append('precio', precio);
    formData.append('stock', stock);
    formData.append('stock_minimo', stock_minimo);
    formData.append('categoria_id', categoria_id);

    var url;
    if (id) {
        formData.append('id', id);
        url = '../backend/productos/editar.php';
    } else {
        url = '../backend/productos/crear.php';
    }

    var data = await fetchData(url, { method: 'POST', body: formData });

    if (data && data.success) {
        showToast(data.message, 'success');
        cerrarModal('modalProducto');
        cargarProductos();
    } else {
        showToast(data ? data.message : 'Error del servidor', 'error');
    }
}

function eliminarProducto(id, nombre) {
    document.getElementById('idProductoEliminar').value = id;
    document.getElementById('nombreProductoEliminar').textContent = nombre;
    abrirModal('modalEliminar');
}

async function confirmarEliminar() {
    var id = document.getElementById('idProductoEliminar').value;
    var formData = new FormData();
    formData.append('id', id);

    var data = await fetchData('../backend/productos/eliminar.php', {
        method: 'POST',
        body: formData
    });

    if (data && data.success) {
        showToast(data.message, 'success');
        cerrarModal('modalEliminar');
        cargarProductos();
    } else {
        showToast(data ? data.message : 'Error del servidor', 'error');
    }
}

function escapeHtml(text) {
    if (!text) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}
