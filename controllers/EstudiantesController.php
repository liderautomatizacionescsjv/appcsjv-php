<?php

namespace Controllers;

use Model\Estudiante;
use MVC\Router;

class EstudiantesController{
    public static function index(Router $router){

        $estudiantes = Estudiante::all();



        $router->render('asistencia/panel_docente', [
            'estudiantes' => $estudiantes
        ]);
    }
}