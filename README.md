# Panaderia "Aqui Nadie Se Rinde"

Sistema de gestion de panaderia desarrollado en PHP con PostgreSQL.
INGRESAR AL SISTEMA https://panaderia-app-hz1s.onrender.com

## Funcionalidades

- **Dashboard** - Resumen de ventas, productos y alertas de stock bajo
- **Productos** - CRUD completo de productos con categorias
- **Ventas** - Registro de ventas con carrito de compras
- **Inventario** - Control de movimientos de entrada, salida y ajuste
- **Reportes** - Consulta de ventas por rango de fechas
- **Usuarios** - Gestion de usuarios con roles (admin/vendedor)
- **Login** - Autenticacion con sesiones

## Tecnologias

- **Backend:** PHP 8.2
- **Base de datos:** PostgreSQL 15
- **Frontend:** HTML5, CSS3, JavaScript
- **Contenedor:** Docker

## Requisitos

- [Docker Desktop para Windows](https://www.docker.com/products/docker-desktop/)
- Navegador web

## Instalacion

### Opcion 1: Docker (Recomendado)

1. Clonar o descargar el proyecto
2. Abrir una terminal en la carpeta del proyecto
3. Ejecutar:
   ```bash
   docker-compose up -d
   ```
4. Abrir el navegador en `http://localhost:8080/instalar.php`
5. Seguir las instrucciones en pantalla
6. **Eliminar** `instalar.php` despues de instalar por seguridad

### Opcion 2: Importar base de datos manualmente

1. Abrir pgAdmin o la linea de comandos de PostgreSQL
2. Crear una base de datos llamada `panaderia`
3. Ejecutar el archivo `database/panaderia.sql` para crear las tablas e insertar datos
4. Ejecutar `docker-compose up -d`

### Comandos utiles

```bash
# Iniciar el sistema
docker-compose up -d

# Detener el sistema
docker-compose down

# Ver logs
docker-compose logs -f

# Reconstruir contenedor
docker-compose up -d --build
```

## Credenciales de prueba

| Usuario | Contrasena | Rol |
|---------|------------|-----|
| angelo | Juniorapp123 | Admin |
| maria | 123456 | Vendedor |
| carlos | 123456 | Vendedor |

## Estructura del proyecto

```
0010_Panaderia_Aqui_Nadie_Se_Rinde/
├── backend/          # Logica del servidor (PHP)
│   ├── auth/         # Login, logout, verificacion de sesion
│   ├── config/       # Conexion a base de datos
│   ├── inventario/   # CRUD de inventario
│   ├── productos/    # CRUD de productos
│   ├── reportes/     # Generacion de reportes
│   ├── usuarios/     # CRUD de usuarios
│   └── ventas/       # Registro y consulta de ventas
├── database/         # Archivo SQL de la base de datos
├── frontend/         # Recursos del cliente (CSS, JS, imagenes)
├── includes/         # Componentes reutilizables (header, sidebar, footer)
├── pages/            # Paginas principales del sistema
├── instalar.php      # Instalador automatico
├── Dockerfile        # Configuracion del contenedor PHP
└── docker-compose.yml # Orquestacion de contenedores
```

## Nota de seguridad

Despues de instalar el sistema, eliminar el archivo `instalar.php` para evitar que alguien reinstale la base de datos.
