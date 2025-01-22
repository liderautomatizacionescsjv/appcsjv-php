<?php

namespace Controllers;

use Model\Asistencia;
use Model\Estudiante;
use MVC\Router;

class ControlAsistencia {

    public static function docentes(Router $router){
        
        $router->render('/asistencia/panel_docente');
    }

    public static function reservas(Router $router){
        $router->render(('reservas/crear_reserva'));
    }
    
}
