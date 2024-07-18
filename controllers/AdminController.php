<?php 

    namespace Controllers;

use Model\AdminCita;
use MVC\Router;

    class AdminController {
        public static function index(Router $router) {

            isSession();

            isAdmin();

            $fecha = $_GET['fecha'] ?? date('Y-m-d');

            $fechas = explode( '-', $fecha);

            if (!checkdate($fechas[1], $fechas[2], $fechas[0])) {
                header('Location: /404');
            }

            // Consultar base de datos
            $consulta ="SELECT c.id, c.hora, CONCAT(u.nombre, ' ', u.apellido) cliente, ";
            $consulta .=    "u.email, u.telefono, s.nombre servicio, s.precio "; 
            $consulta .=    "FROM citas c "; 
            $consulta .=    "LEFT JOIN usuarios u ";
            $consulta .=    "    ON  u.id = c.usuarioId ";
            $consulta .=    "LEFT JOIN citasServicios cs ";
            $consulta .=    "    ON cs.citaId = c.id ";
            $consulta .=    "LEFT JOIN servicios s ";
            $consulta .=    "    on s.id = cs.servicioId ";
            $consulta .=    "WHERE c.fecha  = '{$fecha}'";

            $citas = AdminCita::SQL($consulta);
            
            $router -> render('admin/index', [
                'nombre' => $_SESSION['nombre'],
                'citas' => $citas,
                'fecha' => $fecha
            ]);
        }
    }