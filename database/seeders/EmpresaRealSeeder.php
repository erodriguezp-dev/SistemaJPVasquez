<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\DetallePedido;
use App\Models\HistorialEstadoPedido;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\ReporteGenerado;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmpresaRealSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ReporteGenerado::truncate();
        HistorialEstadoPedido::truncate();
        DetallePedido::truncate();
        MovimientoInventario::truncate();
        Pedido::truncate();
        Producto::truncate();
        Categoria::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Usuarios Oficiales de la Empresa
        $uAdmin = User::create([
            'name' => 'Juan Pablo Vásquez (Gerente General)',
            'usuario' => 'administrador_jpvasquez',
            'email' => 'gerencia@jpvasquez.com',
            'password' => Hash::make('J4VASQ76#'),
            'rol' => 'administrador',
            'activo' => true,
        ]);

        $uGerente = User::create([
            'name' => 'Ing. Ricardo Saldaña (Jefe de Proyectos)',
            'usuario' => 'ing_proyectos_jp',
            'email' => 'proyectos@jpvasquez.com',
            'password' => Hash::make('Proy2026!#JpV'),
            'rol' => 'gerente_proyecto',
            'activo' => true,
        ]);

        $uContador = User::create([
            'name' => 'CPC. Patricia Benavides (Contabilidad)',
            'usuario' => 'contabilidad_jp',
            'email' => 'contabilidad@jpvasquez.com',
            'password' => Hash::make('Cont4_JpV#26!'),
            'rol' => 'contador',
            'activo' => true,
        ]);

        $uColab = User::create([
            'name' => 'Manuel Quispe Huamán (Jefe de Taller)',
            'usuario' => 'taller_almacen',
            'email' => 'taller@jpvasquez.com',
            'password' => Hash::make('T4ller_M4nuel!'),
            'rol' => 'colaborador',
            'activo' => true,
        ]);

        // 2. Categorías según las 3 actividades de JP Vásquez
        $cats = [
            'melamina' => Categoria::create(['nombre' => 'Melamina y Tableros', 'descripcion' => 'Tableros de melamina, MDF, enchapes y cantos para fabricación de muebles']),
            'metales' => Categoria::create(['nombre' => 'Perfiles y Estructuras Metálicas', 'descripcion' => 'Tubos de fierro, ángulos, platinas, vigas y perfiles para estructuras metálicas']),
            'pinturas' => Categoria::create(['nombre' => 'Pinturas y Acabados Industriales', 'descripcion' => 'Esmaltes anticorrosivos, lacas piroxilina, thinner acrílico y selladores']),
            'herrajes' => Categoria::create(['nombre' => 'Herrajes y Accesorios para Muebles', 'descripcion' => 'Correderas telescópicas, bisagras de cazoleta, tiradores y patas']),
            'ferreteria' => Categoria::create(['nombre' => 'Ferretería y Fijaciones', 'descripcion' => 'Tornillos drywall, pernos, electrodos para soldadura y discos de corte']),
            'construccion' => Categoria::create(['nombre' => 'Materiales de Construcción', 'descripcion' => 'Cemento, arena, yeso y fierro corrugado para obras']),
        ];

        // 3. Catálogo de Productos y Existencias Iniciales
        $prodsData = [
            // Mueblería
            ['cod' => 'MEL-BLA-18', 'nom' => 'Tablero Melamina Blanco 18mm 2.14x2.44m Pelíkano', 'cat' => 'melamina', 'und' => 'Plancha', 'stock' => 28, 'min' => 8, 'prec' => 125.00],
            ['cod' => 'MEL-ROB-18', 'nom' => 'Tablero Melamina Roble Santana 18mm Novopan', 'cat' => 'melamina', 'und' => 'Plancha', 'stock' => 2, 'min' => 5, 'prec' => 148.50], // ALERTA BAJO
            ['cod' => 'MDF-055-03', 'nom' => 'Tablero MDF Trupan 3mm 2.44x1.52m para Fondos', 'cat' => 'melamina', 'und' => 'Plancha', 'stock' => 35, 'min' => 10, 'prec' => 38.00],
            ['cod' => 'CAN-PVC-02', 'nom' => 'Tapacanto Grueso PVC 22x2mm Blanco (Rollo 100m)', 'cat' => 'melamina', 'und' => 'Rollo', 'stock' => 12, 'min' => 4, 'prec' => 85.00],
            // Herrajes
            ['cod' => 'HER-COR-45', 'nom' => 'Corredera Telescópica Pesada 45cm Zincada Ducasse', 'cat' => 'herrajes', 'und' => 'Par', 'stock' => 80, 'min' => 20, 'prec' => 16.50],
            ['cod' => 'HER-BIS-35', 'nom' => 'Bisagra Cangrejo Cazoleta 35mm Cierre Suave', 'cat' => 'herrajes', 'und' => 'Par', 'stock' => 140, 'min' => 30, 'prec' => 7.80],
            ['cod' => 'HER-TIR-16', 'nom' => 'Tirador Tubo de Acero Inoxidable 160mm para Mueble', 'cat' => 'herrajes', 'und' => 'Unidad', 'stock' => 95, 'min' => 25, 'prec' => 9.20],
            // Estructuras Metálicas
            ['cod' => 'MET-TUB-2X1', 'nom' => 'Tubo Fierro Rectangular LAC 2x1 pulg x 2.0mm x 6m', 'cat' => 'metales', 'und' => 'Barra', 'stock' => 42, 'min' => 12, 'prec' => 46.50],
            ['cod' => 'MET-TUB-2X2', 'nom' => 'Tubo Fierro Cuadrado Estructural 2x2 pulg x 2.0mm x 6m', 'cat' => 'metales', 'und' => 'Barra', 'stock' => 30, 'min' => 10, 'prec' => 62.00],
            ['cod' => 'MET-ANG-112', 'nom' => 'Ángulo Estructural de Fierro 1 1/2 x 1 1/2 x 3/16 pulg', 'cat' => 'metales', 'und' => 'Barra', 'stock' => 25, 'min' => 8, 'prec' => 54.00],
            ['cod' => 'MET-PLA-1X3', 'nom' => 'Platina de Fierro 1 pulg x 3/16 pulg x 6m', 'cat' => 'metales', 'und' => 'Barra', 'stock' => 50, 'min' => 15, 'prec' => 22.00],
            ['cod' => 'MET-PLN-108', 'nom' => 'Plancha de Fierro Estructural LAC 1/8 pulg 1.20x2.40m', 'cat' => 'metales', 'und' => 'Plancha', 'stock' => 15, 'min' => 5, 'prec' => 185.00],
            // Soldadura y Fijaciones
            ['cod' => 'ELE-CEL-6011', 'nom' => 'Electrodo para Soldar Cellocord E6011 1/8 pulg (Caja 20kg)', 'cat' => 'ferreteria', 'und' => 'Caja', 'stock' => 2, 'min' => 6, 'prec' => 195.00], // ALERTA BAJO
            ['cod' => 'ELE-SUP-7018', 'nom' => 'Electrodo Supercito E7018 1/8 pulg Siderperú (Caja 20kg)', 'cat' => 'ferreteria', 'und' => 'Caja', 'stock' => 8, 'min' => 4, 'prec' => 230.00],
            ['cod' => 'DIS-COR-45', 'nom' => 'Disco de Corte para Metal 4 1/2 pulg Norton Quantum', 'cat' => 'ferreteria', 'und' => 'Unidad', 'stock' => 110, 'min' => 30, 'prec' => 4.50],
            ['cod' => 'TOR-AUT-158', 'nom' => 'Tornillo Autoperforante Drywall 6x1 5/8 pulg (Caja 1000u)', 'cat' => 'ferreteria', 'und' => 'Caja', 'stock' => 18, 'min' => 5, 'prec' => 32.00],
            ['cod' => 'PER-HEX-122', 'nom' => 'Perno Hexagonal Grado 5 zincado 1/2 x 2 pulg con Tuerca', 'cat' => 'ferreteria', 'und' => 'Ciento', 'stock' => 14, 'min' => 5, 'prec' => 68.00],
            // Pinturas y Acabados
            ['cod' => 'PIN-ANT-GRI', 'nom' => 'Pintura Esmalte Anticorrosivo Gris Vencedor (Galón)', 'cat' => 'pinturas', 'und' => 'Galón', 'stock' => 22, 'min' => 8, 'prec' => 58.00],
            ['cod' => 'PIN-ESM-NEG', 'nom' => 'Pintura Esmalte Sintético Negro Brillante Fast (Galón)', 'cat' => 'pinturas', 'und' => 'Galón', 'stock' => 16, 'min' => 6, 'prec' => 64.00],
            ['cod' => 'PIN-THI-ACR', 'nom' => 'Thinner Acrílico Extrapuro para Soplete (Galón)', 'cat' => 'pinturas', 'und' => 'Galón', 'stock' => 25, 'min' => 10, 'prec' => 35.00],
            ['cod' => 'LAC-POL-TRA', 'nom' => 'Laca Barniz Poliuretano Transparente Tekno (Galón)', 'cat' => 'pinturas', 'und' => 'Galón', 'stock' => 10, 'min' => 4, 'prec' => 92.00],
            // Materiales de Construcción
            ['cod' => 'MAT-CEM-SOL', 'nom' => 'Cemento Portland Tipo I Sol (Bolsa 42.5kg)', 'cat' => 'construccion', 'und' => 'Bolsa', 'stock' => 85, 'min' => 25, 'prec' => 28.50],
            ['cod' => 'MAT-FIE-12', 'nom' => 'Fierro Corrugado Construcción 1/2 pulg x 9m Aceros Arequipa', 'cat' => 'construccion', 'und' => 'Varilla', 'stock' => 65, 'min' => 20, 'prec' => 43.80],
            ['cod' => 'MAT-YES-CER', 'nom' => 'Yeso Cerámico Fino de Construcción (Bolsa 25kg)', 'cat' => 'construccion', 'und' => 'Bolsa', 'stock' => 0, 'min' => 10, 'prec' => 14.00], // AGOTADO
        ];

        $prods = [];
        $fechaInicioOperaciones = Carbon::now('America/Lima')->subDays(50)->startOfDay();

        foreach ($prodsData as $p) {
            $nuevoProd = Producto::create([
                'codigo' => $p['cod'],
                'nombre' => $p['nom'],
                'categoria_id' => $cats[$p['cat']]->id,
                'unidad_medida' => $p['und'],
                'stock_actual' => $p['stock'],
                'stock_minimo' => $p['min'],
                'precio_referencial' => $p['prec'],
                'activo' => true,
            ]);

            $prods[$p['cod']] = $nuevoProd;

            // Registro en Kardex de apertura
            MovimientoInventario::create([
                'producto_id' => $nuevoProd->id,
                'tipo' => 'entrada',
                'cantidad' => $p['stock'] + 10, // Simular entrada previa
                'stock_anterior' => 0,
                'stock_resultante' => $p['stock'] + 10,
                'motivo' => 'Inventario Inicial de Apertura de Almacén',
                'pedido_id' => null,
                'user_id' => $uAdmin->id,
                'fecha_movimiento' => $fechaInicioOperaciones,
            ]);
        }

        // 4. Registro de Pedidos Reales de las 3 actividades
        $pedidosDef = [
            // MUEBLES
            ['tipo' => 'mueble', 'cli' => 'Arq. Gonzalo Rivas Estudio', 'tel' => '984123567', 'dir' => 'Av. Javier Prado Este 2450, San Borja', 'est' => 'confirmado', 'dias' => 45, 'obs' => 'Repostero alto y bajo en melamina Pelíkano con tiradores de acero.', 'lines' => [
                ['p' => 'MEL-BLA-18', 'desc' => 'Tableros de Melamina Blanca para estructura repostero', 'cant' => 4, 'pu' => 145.00],
                ['p' => 'HER-COR-45', 'desc' => 'Correderas telescópicas para cajones de cocina', 'cant' => 6, 'pu' => 20.00],
                ['p' => 'HER-TIR-16', 'desc' => 'Tiradores de acero satinado', 'cant' => 12, 'pu' => 12.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Dra. Vanessa Carrión Gómez', 'tel' => '992456781', 'dir' => 'Calle Los Nogales 320, San Isidro', 'est' => 'confirmado', 'dias' => 40, 'obs' => 'Closet empotrado de 3 cuerpos con zapatera móvil.', 'lines' => [
                ['p' => 'MEL-BLA-18', 'desc' => 'Cuerpo y divisiones de closet en melamina 18mm', 'cant' => 5, 'pu' => 145.00],
                ['p' => 'CAN-PVC-02', 'desc' => 'Tapacanto grueso 2mm pegado en taller', 'cant' => 1, 'pu' => 95.00],
                ['p' => null, 'desc' => 'Servicio de diseño y armado a medida', 'cant' => 1, 'pu' => 650.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Inmobiliaria Los Parques S.A.C.', 'tel' => '987345612', 'dir' => 'Av. Benavides 1890, Miraflores', 'est' => 'entregado', 'dias' => 32, 'obs' => 'Escritorio gerencial en L con soporte metálico y cajonera.', 'lines' => [
                ['p' => 'MEL-ROB-18', 'desc' => 'Tablero superior en melamina Roble Santana', 'cant' => 2, 'pu' => 165.00],
                ['p' => 'MET-TUB-2X1', 'desc' => 'Estructura soporte metálica pintada al horno', 'cant' => 3, 'pu' => 55.00],
                ['p' => 'HER-COR-45', 'desc' => 'Juego de rieles telescópicos para gavetero', 'cant' => 3, 'pu' => 20.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Lic. Fernando Tuesta Ruiz', 'tel' => '981654329', 'dir' => 'Jr. Huiracocha 1420, Jesús María', 'est' => 'entregado', 'dias' => 28, 'obs' => 'Mueble de entretenimiento para TV 65 pulg con repisas flotantes.', 'lines' => [
                ['p' => 'MEL-BLA-18', 'desc' => 'Panel flotante y repisas en melamina 18mm', 'cant' => 3, 'pu' => 145.00],
                ['p' => 'HER-BIS-35', 'desc' => 'Bisagras cierre suave para puertas inferiores', 'cant' => 4, 'pu' => 10.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Consultorio Dental Sonrisas Bellas', 'tel' => '994567890', 'dir' => 'Av. La Marina 890, Pueblo Libre', 'est' => 'terminado', 'dias' => 18, 'obs' => 'Mostrador de recepción curvo con zócalo de aluminio.', 'lines' => [
                ['p' => 'MEL-BLA-18', 'desc' => 'Estructura interior de melamina blanca', 'cant' => 3, 'pu' => 145.00],
                ['p' => null, 'desc' => 'Enchape especial y luces LED perimétricas', 'cant' => 1, 'pu' => 820.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Sra. Elena Morales De la Cruz', 'tel' => '993214567', 'dir' => 'Urb. La Encalada Mz. B Lt. 14, Surco', 'est' => 'en_proceso', 'dias' => 12, 'obs' => 'Juego de dormitorio: Cama King con veladores empotrados.', 'lines' => [
                ['p' => 'MEL-ROB-18', 'desc' => 'Cabecera y bastidor en melamina Roble', 'cant' => 3, 'pu' => 165.00],
                ['p' => 'HER-COR-45', 'desc' => 'Rieles para cajoneras de veladores', 'cant' => 4, 'pu' => 20.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Restaurante Fuego & Sabor', 'tel' => '986543219', 'dir' => 'Av. Universitaria 1200, San Miguel', 'est' => 'en_proceso', 'dias' => 7, 'obs' => 'Fabricación de 8 mesas de madera con base de fierro fundido.', 'lines' => [
                ['p' => 'MET-TUB-2X2', 'desc' => 'Pedestales y bases de fierro estructural', 'cant' => 4, 'pu' => 75.00],
                ['p' => null, 'desc' => 'Tableros de pino alistonado laqueados', 'cant' => 8, 'pu' => 120.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Ing. Mateo Cárdenas Silva', 'tel' => '991827364', 'dir' => 'Av. Arequipa 3420, Lince', 'est' => 'recibido', 'dias' => 2, 'obs' => 'Estantería modular de oficina de 2.00x1.20m.', 'lines' => [
                ['p' => 'MEL-BLA-18', 'desc' => 'Melamina blanca 18mm para baldas y costados', 'cant' => 4, 'pu' => 145.00],
            ]],

            // ESTRUCTURAS METÁLICAS
            ['tipo' => 'mueble', 'cli' => 'Almacenes Industriales del Centro S.A.', 'tel' => '982736451', 'dir' => 'Av. Argentina 4500, Callao', 'est' => 'confirmado', 'dias' => 48, 'obs' => 'Fabricación e instalación de 4 tijerales parabólicos de 12m.', 'lines' => [
                ['p' => 'MET-TUB-2X2', 'desc' => 'Tubos cuadrados estructurales para arcos de tijeral', 'cant' => 10, 'pu' => 72.00],
                ['p' => 'MET-ANG-112', 'desc' => 'Ángulos de celosía interior para tijeral', 'cant' => 12, 'pu' => 62.00],
                ['p' => 'ELE-SUP-7018', 'desc' => 'Electrodos especiales para soldadura estructural', 'cant' => 2, 'pu' => 245.00],
                ['p' => 'PIN-ANT-GRI', 'desc' => 'Pintura anticorrosiva base epóxica gris', 'cant' => 5, 'pu' => 65.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Condominio Los Cipreses', 'tel' => '995647382', 'dir' => 'Av. El Sol 450, Chorrillos', 'est' => 'confirmado', 'dias' => 38, 'obs' => 'Portón levadizo metálico automático 4.00 x 2.40m con puerta peatonal.', 'lines' => [
                ['p' => 'MET-TUB-2X1', 'desc' => 'Bastidor perimétrico y refuerzos de portón', 'cant' => 8, 'pu' => 54.00],
                ['p' => 'MET-PLN-108', 'desc' => 'Planchas LAC acanaladas para paños', 'cant' => 4, 'pu' => 210.00],
                ['p' => 'PIN-ESM-NEG', 'desc' => 'Pintura esmalte negro brillante secado rápido', 'cant' => 3, 'pu' => 72.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Empresa de Transportes Rápidos S.R.L.', 'tel' => '983647581', 'dir' => 'Av. Faucett 1200, Callao', 'est' => 'entregado', 'dias' => 26, 'obs' => 'Fabricación de rejas perimétricas de seguridad 20 metros lineales.', 'lines' => [
                ['p' => 'MET-TUB-2X1', 'desc' => 'Postes y travesaños de reja', 'cant' => 6, 'pu' => 54.00],
                ['p' => 'MET-PLA-1X3', 'desc' => 'Platinas verticales decorativas con puntas de lanza', 'cant' => 15, 'pu' => 28.00],
                ['p' => 'PIN-ANT-GRI', 'desc' => 'Acabado anticorrosivo de alta resistencia', 'cant' => 4, 'pu' => 65.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Taller Mecánico Hermanos Castro', 'tel' => '994837261', 'dir' => 'Av. Canadá 1420, La Victoria', 'est' => 'terminado', 'dias' => 16, 'obs' => 'Mesa de trabajo pesada para soldadura y torno con plancha de 1/8.', 'lines' => [
                ['p' => 'MET-TUB-2X2', 'desc' => 'Patas y amarres de fierro estructural pesado', 'cant' => 4, 'pu' => 72.00],
                ['p' => 'MET-PLN-108', 'desc' => 'Cubierta superior de plancha de fierro 1/8', 'cant' => 1, 'pu' => 210.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Textiles Andinos S.A.C.', 'tel' => '981928374', 'dir' => 'Carretera Central Km 11, Ate', 'est' => 'en_proceso', 'dias' => 9, 'obs' => 'Estructura metálica para mezanine de almacenamiento 6x4m.', 'lines' => [
                ['p' => 'MET-ANG-112', 'desc' => 'Vigas y columnas de ángulo reforzado', 'cant' => 8, 'pu' => 62.00],
                ['p' => 'ELE-SUP-7018', 'desc' => 'Soldadura estructural certificada 7018', 'cant' => 1, 'pu' => 245.00],
            ]],
            ['tipo' => 'mueble', 'cli' => 'Colegio San Gabriel', 'tel' => '992837465', 'dir' => 'Av. Brasil 2200, Magdalena', 'est' => 'recibido', 'dias' => 3, 'obs' => 'Barandas metálicas de seguridad para escaleras de 3 pisos.', 'lines' => [
                ['p' => 'MET-TUB-2X1', 'desc' => 'Pasamanos y parantes de tubo rectangular', 'cant' => 7, 'pu' => 54.00],
                ['p' => 'MET-PLA-1X3', 'desc' => 'Platinas de amarre y anclajes a concreto', 'cant' => 10, 'pu' => 28.00],
            ]],

            // COMERCIO Y FERRETERÍA
            ['tipo' => 'ferreteria', 'cli' => 'Constructora Horizonte Urbano S.A.', 'tel' => '984736251', 'dir' => 'Av. República de Panamá 3500, Surquillo', 'est' => 'confirmado', 'dias' => 42, 'obs' => 'Venta minorista de cemento y fierro para obra residencial.', 'lines' => [
                ['p' => 'MAT-CEM-SOL', 'desc' => 'Bolsas de Cemento Sol Tipo I', 'cant' => 40, 'pu' => 29.50],
                ['p' => 'MAT-FIE-12', 'desc' => 'Varillas de Fierro Corrugado 1/2 pulg Aceros Arequipa', 'cant' => 25, 'pu' => 45.00],
            ]],
            ['tipo' => 'ferreteria', 'cli' => 'Maestro Contratista Julián Huanca', 'tel' => '993847561', 'dir' => 'Jr. Huanta 780, Cercado de Lima', 'est' => 'confirmado', 'dias' => 35, 'obs' => 'Artículos de soldadura y corte para taller de herrería.', 'lines' => [
                ['p' => 'ELE-CEL-6011', 'desc' => 'Electrodos Cellocord 1/8 caja x 20kg', 'cant' => 2, 'pu' => 210.00],
                ['p' => 'DIS-COR-45', 'desc' => 'Discos de corte para metal 4 1/2 Norton', 'cant' => 30, 'pu' => 5.00],
                ['p' => 'PIN-ANT-GRI', 'desc' => 'Galones de anticorrosivo gris', 'cant' => 4, 'pu' => 62.00],
            ]],
            ['tipo' => 'ferreteria', 'cli' => 'Ferretería El Tornillo Feliz', 'tel' => '981726354', 'dir' => 'Av. Canta Callao 890, San Martín de Porres', 'est' => 'entregado', 'dias' => 24, 'obs' => 'Lote de tornillería y herrajes al por mayor.', 'lines' => [
                ['p' => 'TOR-AUT-158', 'desc' => 'Cajas de tornillo drywall 6x1 5/8 pulg (1000u)', 'cant' => 6, 'pu' => 35.00],
                ['p' => 'HER-BIS-35', 'desc' => 'Bisagras de cazoleta con freno', 'cant' => 40, 'pu' => 8.50],
                ['p' => 'HER-COR-45', 'desc' => 'Pares de correderas telescópicas pesadas', 'cant' => 15, 'pu' => 18.00],
            ]],
            ['tipo' => 'ferreteria', 'cli' => 'Carpintería El Roble de Jesús', 'tel' => '994738291', 'dir' => 'Av. Pachacútec 1800, Villa María del Triunfo', 'est' => 'entregado', 'dias' => 20, 'obs' => 'Suministro de melaminas y tapacantos.', 'lines' => [
                ['p' => 'MEL-BLA-18', 'desc' => 'Planchas de Melamina Blanca 18mm Pelíkano', 'cant' => 6, 'pu' => 135.00],
                ['p' => 'CAN-PVC-02', 'desc' => 'Rollo de tapacanto blanco 22x2mm', 'cant' => 2, 'pu' => 90.00],
            ]],
            ['tipo' => 'ferreteria', 'cli' => 'Ing. Laura Valdivia (Residente de Obra)', 'tel' => '982635471', 'dir' => 'Av. Primavera 1100, Monterrico', 'est' => 'terminado', 'dias' => 14, 'obs' => 'Cemento y fierro para vaciado de losa aligerada.', 'lines' => [
                ['p' => 'MAT-CEM-SOL', 'desc' => 'Cemento Sol Tipo I para vaciado', 'cant' => 25, 'pu' => 29.50],
                ['p' => 'MAT-FIE-12', 'desc' => 'Fierro corrugado 1/2 pulg', 'cant' => 15, 'pu' => 45.00],
            ]],
            ['tipo' => 'ferreteria', 'cli' => 'Servicios Metalmecánicos San Roque', 'tel' => '993847261', 'dir' => 'Av. Separadora Industrial 800, Ate', 'est' => 'en_proceso', 'dias' => 8, 'obs' => 'Perfiles tubulares y pintura para estructuras.', 'lines' => [
                ['p' => 'MET-TUB-2X1', 'desc' => 'Tubos rectangulares de fierro 2x1 pulg', 'cant' => 6, 'pu' => 50.00],
                ['p' => 'PIN-ESM-NEG', 'desc' => 'Esmalte sintético negro brillante', 'cant' => 4, 'pu' => 68.00],
                ['p' => 'PIN-THI-ACR', 'desc' => 'Thinner acrílico para pistola', 'cant' => 4, 'pu' => 38.00],
            ]],
            ['tipo' => 'ferreteria', 'cli' => 'Sr. Roberto Benítez Castro', 'tel' => '981928375', 'dir' => 'Calle Las Águilas 120, Surquillo', 'est' => 'recibido', 'dias' => 1, 'obs' => 'Compra de pintura, laca y brochas para mantenimiento.', 'lines' => [
                ['p' => 'PIN-ESM-NEG', 'desc' => 'Galón de esmalte negro brillante', 'cant' => 2, 'pu' => 68.00],
                ['p' => 'LAC-POL-TRA', 'desc' => 'Galón de laca poliuretano para madera', 'cant' => 2, 'pu' => 98.00],
                ['p' => 'PIN-THI-ACR', 'desc' => 'Thinner acrílico', 'cant' => 2, 'pu' => 38.00],
            ]],

            // REPARACIONES Y MANTENIMIENTO
            ['tipo' => 'reparacion', 'cli' => 'Club Social y Deportivo Rinconada', 'tel' => '984837261', 'dir' => 'Av. Manuel Valle 560, La Molina', 'est' => 'confirmado', 'dias' => 44, 'obs' => 'Mantenimiento integral y soldadura de portones de acceso vehicular.', 'lines' => [
                ['p' => 'MET-ANG-112', 'desc' => 'Refuerzos en ángulos de bisagras y quicios', 'cant' => 3, 'pu' => 60.00],
                ['p' => 'PIN-ANT-GRI', 'desc' => 'Pintado anticorrosivo de 2 portones dobles', 'cant' => 4, 'pu' => 65.00],
                ['p' => null, 'desc' => 'Mano de obra especializada de soldadura y calibración', 'cant' => 1, 'pu' => 750.00],
            ]],
            ['tipo' => 'reparacion', 'cli' => 'Dra. Pilar Albarracín S.', 'tel' => '992837461', 'dir' => 'Av. Pezet 890, San Isidro', 'est' => 'confirmado', 'dias' => 36, 'obs' => 'Restauración y laqueado en poliuretano de juego de comedor de caoba.', 'lines' => [
                ['p' => 'LAC-POL-TRA', 'desc' => 'Laca poliuretano de alto brillo', 'cant' => 2, 'pu' => 98.00],
                ['p' => 'PIN-THI-ACR', 'desc' => 'Solvente y desengrasante para madera', 'cant' => 3, 'pu' => 38.00],
                ['p' => null, 'desc' => 'Servicio de raspado, masillado y re-laqueado al horno', 'cant' => 1, 'pu' => 880.00],
            ]],
            ['tipo' => 'reparacion', 'cli' => 'Industrias Gráficas del Perú E.I.R.L.', 'tel' => '987463521', 'dir' => 'Jr. Zorritos 420, Breña', 'est' => 'entregado', 'dias' => 22, 'obs' => 'Reparación de estanterías metálicas colapsadas en depósito.', 'lines' => [
                ['p' => 'MET-ANG-112', 'desc' => 'Puntales y diagonales de fierro ángulo 1 1/2', 'cant' => 4, 'pu' => 60.00],
                ['p' => 'ELE-CEL-6011', 'desc' => 'Soldadura eléctrica en campo', 'cant' => 1, 'pu' => 210.00],
                ['p' => null, 'desc' => 'Nivelación y fijación con pernos de expansión a piso', 'cant' => 1, 'pu' => 450.00],
            ]],
            ['tipo' => 'reparacion', 'cli' => 'Sra. Cecilia Barrenechea', 'tel' => '994837192', 'dir' => 'Av. Dos de Mayo 1420, San Isidro', 'est' => 'terminado', 'dias' => 13, 'obs' => 'Acondicionamiento y cambio de rieles en reposteros de cocina.', 'lines' => [
                ['p' => 'HER-COR-45', 'desc' => 'Cambio de 8 pares de correderas telescópicas vencidas', 'cant' => 8, 'pu' => 20.00],
                ['p' => 'HER-BIS-35', 'desc' => 'Cambio de 12 bisagras con freno desalineadas', 'cant' => 6, 'pu' => 10.00],
                ['p' => null, 'desc' => 'Servicio técnico a domicilio de nivelación y ajuste', 'cant' => 1, 'pu' => 280.00],
            ]],
            ['tipo' => 'reparacion', 'cli' => 'Edificio Residencial Parque Castilla', 'tel' => '981726359', 'dir' => 'Calle Manuel Fuentes 340, San Isidro', 'est' => 'en_proceso', 'dias' => 6, 'obs' => 'Mantenimiento y repintado de rejas de perímetro y puertas de cochera.', 'lines' => [
                ['p' => 'PIN-ANT-GRI', 'desc' => 'Base anticorrosiva para metal', 'cant' => 3, 'pu' => 65.00],
                ['p' => 'PIN-ESM-NEG', 'desc' => 'Esmalte negro brillante para acabado final', 'cant' => 4, 'pu' => 68.00],
                ['p' => null, 'desc' => 'Lijado mecánico y pintado con soplete', 'cant' => 1, 'pu' => 620.00],
            ]],
            ['tipo' => 'reparacion', 'cli' => 'Oficinas Corporativas Torre Real', 'tel' => '993847562', 'dir' => 'Av. Rivera Navarrete 500, San Isidro', 'est' => 'recibido', 'dias' => 1, 'obs' => 'Reparación de 6 módulos de oficina: cajones trabados y bisagras sueltas.', 'lines' => [
                ['p' => 'HER-COR-45', 'desc' => 'Rieles telescópicos de repuesto', 'cant' => 6, 'pu' => 20.00],
                ['p' => 'HER-TIR-16', 'desc' => 'Tiradores de reemplazo', 'cant' => 6, 'pu' => 12.00],
                ['p' => null, 'desc' => 'Mano de obra de carpintería técnica en oficina', 'cant' => 1, 'pu' => 320.00],
            ]],
        ];

        $secuencia = ['recibido', 'en_proceso', 'terminado', 'entregado', 'confirmado'];
        $num = 1;

        foreach ($pedidosDef as $pdef) {
            $fechaPed = Carbon::now('America/Lima')->subDays($pdef['dias'])->addHours(rand(8, 17))->addMinutes(rand(0, 59));
            $codPed = sprintf("PED-2026-%04d", $num++);

            $pedido = Pedido::create([
                'codigo' => $codPed,
                'tipo' => $pdef['tipo'],
                'cliente_nombre' => $pdef['cli'],
                'cliente_telefono' => $pdef['tel'],
                'cliente_direccion' => $pdef['dir'],
                'estado' => $pdef['est'],
                'fecha_pedido' => $fechaPed,
                'fecha_entrega' => $fechaPed->copy()->addDays(rand(3, 10))->format('Y-m-d'),
                'observaciones' => $pdef['obs'],
                'user_id' => ($num % 2 == 0) ? $uGerente->id : $uColab->id,
            ]);

            // Crear detalles y registrar movimientos si corresponde
            $idxMeta = array_search($pdef['est'], $secuencia);

            foreach ($pdef['lines'] as $line) {
                $prodObj = !empty($line['p']) ? ($prods[$line['p']] ?? null) : null;
                $prodId = $prodObj ? $prodObj->id : null;

                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $prodId,
                    'descripcion' => $line['desc'],
                    'cantidad' => $line['cant'],
                    'precio_unitario' => $line['pu'],
                ]);

                // Si el pedido ya llegó a entregado o confirmado, se registró la salida en Kardex
                if ($prodObj && $idxMeta >= 3) {
                    $stockAnt = (float)$prodObj->stock_actual;
                    $cantSalida = (float)$line['cant'];
                    $stockRes = max(0, $stockAnt - $cantSalida);
                    $prodObj->stock_actual = $stockRes;
                    $prodObj->save();

                    MovimientoInventario::create([
                        'producto_id' => $prodObj->id,
                        'tipo' => 'salida',
                        'cantidad' => $cantSalida,
                        'stock_anterior' => $stockAnt,
                        'stock_resultante' => $stockRes,
                        'motivo' => "Despacho por Pedido {$codPed}",
                        'pedido_id' => $pedido->id,
                        'user_id' => $uColab->id,
                        'fecha_movimiento' => $fechaPed->copy()->addDays(rand(1, 3)),
                    ]);
                }
            }

            // Historial de cambios de estado cronológicos
            $fechaTrans = $fechaPed->copy();
            for ($i = 0; $i <= $idxMeta; $i++) {
                $est = $secuencia[$i];
                HistorialEstadoPedido::create([
                    'pedido_id' => $pedido->id,
                    'estado_anterior' => ($i == 0) ? null : $secuencia[$i - 1],
                    'estado_nuevo' => $est,
                    'user_id' => ($i == 0) ? $pedido->user_id : $uAdmin->id,
                    'fecha_cambio' => $fechaTrans->copy(),
                    'observacion' => ($i == 0) ? 'Registro inicial del pedido' : "Avance operativo a {$est}",
                ]);
                $fechaTrans->addDays(rand(1, 2))->addHours(rand(1, 4));
            }
        }

        // 5. Un par de reportes previos para alimentar el historial de telemetría (RF17)
        ReporteGenerado::create([
            'tipo' => 'inventario',
            'filtros' => ['tipo' => 'inventario', 'estado_stock' => 'bajo'],
            'formato' => 'pdf',
            'hora_inicio' => Carbon::now('America/Lima')->subDays(2)->addHours(10),
            'hora_fin' => Carbon::now('America/Lima')->subDays(2)->addHours(10)->addMilliseconds(820),
            'duracion_segundos' => 0.82,
            'user_id' => $uContador->id,
        ]);

        ReporteGenerado::create([
            'tipo' => 'pedidos',
            'filtros' => ['tipo' => 'pedidos', 'tipo_pedido' => 'mueble'],
            'formato' => 'excel',
            'hora_inicio' => Carbon::now('America/Lima')->subDays(1)->addHours(15),
            'hora_fin' => Carbon::now('America/Lima')->subDays(1)->addHours(15)->addMilliseconds(640),
            'duracion_segundos' => 0.64,
            'user_id' => $uAdmin->id,
        ]);
    }
}
