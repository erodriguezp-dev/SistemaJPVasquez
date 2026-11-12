# Sistema de Gestión de Inventario y Pedidos
### JP Vásquez Servicios Generales E.I.R.L.

Sistema web integral desarrollado para optimizar la trazabilidad, el control de inventario en tiempo real y el ciclo de vida de pedidos en **JP Vásquez Servicios Generales E.I.R.L.**, abarcando sus tres líneas de actividad:
1. **Fabricación de Muebles:** Diseño, fabricación y laqueado de muebles personalizados en melamina y madera.
2. **Estructuras Metálicas:** Fabricación de productos metálicos de uso estructural y carpintería metálica pesada.
3. **Comercio:** Venta de artículos de ferretería y materiales de construcción.

---

## Tecnologías Utilizadas

* **Backend:** PHP 8.2+ / 8.3 / 8.4 (Parcheado ante CVE-2026-109618 / CVE-2026-109619) & Laravel 12
* **Frontend:** Blade Templates, Bootstrap 5, Bootstrap Icons, Chart.js & Vite
* **Base de Datos:** MySQL 8.0 / MariaDB con transacciones ACID y bloqueo pesimista (`lockForUpdate`)
* **Reportes:** Maatwebsite Excel 3.1 & DomPDF 3.1
* **Seguridad:** Control de Acceso Basado en Roles (RBAC), Middleware de Protección, Rate Limiting (Throttle) y pantalla 403 personalizada

---

## Módulos del Sistema

* **Dashboard Analítico:** Indicadores clave (KPIs), gráficas de pedidos por estado, evolución temporal, distribución de insumos más consumidos y balance de movimientos de almacén (Entradas vs Salidas).
* **Gestión de Inventario & Kardex:** Control de stock físico y lógico en tiempo real, alertas preventivas por stock bajo o agotado, y registro automático de entradas, salidas y ajustes de almacén.
* **Ciclo de Vida de Pedidos:** Trazabilidad completa con historial de transiciones:
  `Recibido` ➔ `En Proceso` ➔ `Terminado` ➔ `Entregado` ➔ `Confirmado`.
* **Auditoría & Telemetría:** Historial de reportes generados con medición exacta de tiempos de respuesta en milisegundos.
* **Control de Usuarios:** Administración de personal, asignación de roles y activación/desactivación de accesos.

---

## Roles de Usuario y Matriz de Acceso

| Rol | Alcance Operativo |
| :--- | :--- |
| **Administrador** | Acceso total al sistema, auditoría, gestión de usuarios, catálogo, movimientos y reportes. |
| **Gerente de Proyecto** | Seguimiento y avance operativo de pedidos, consulta de inventario y generación de reportes. |
| **Contador** | Liquidación de pedidos, balances contables y exportación de reportes gerenciales en PDF/Excel. |
| **Colaborador (Taller/Almacén)** | Registro de movimientos de almacén, verificación de kardex y recepción de pedidos. |

---

## Instalación y Ejecución Local

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/erodriguezp-dev/SistemaJPVasquez.git
   cd SistemaJPVasquez
   ```

2. **Instalar dependencias:**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Configurar el entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configurar credenciales de base de datos en `.env`.*

4. **Ejecutar migraciones y datos de prueba reales:**
   ```bash
   php artisan migrate --seed
   ```

5. **Iniciar el servidor local:**
   ```bash
   php artisan serve
   ```
