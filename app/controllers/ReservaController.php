<?php

namespace Controllers;

use Model\Carros;
use MVC\Router;
use Model\Reserva;

class ReservaController {
    public static function index(Router $router) {
        // $reservas = Reserva::all(); // Obtiene todas las reservas de la base de datos

        $router->render('reservas/crear_reserva', [
            // 'reservas' => $reservas
        ]);
    }

    public static function misReservas(Router $router) {
        $router->render('reservas/mis_reservas');
    }
    

    public static function crear(Router $router) {
        $alertas = [];
        $reserva = new Reserva();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reserva->sincronizar($_POST);
            $alertas = $reserva->validarReserva();

            if (empty($alertas)) {
                $resultado = $reserva->guardar();
                if ($resultado) {
                    header('Location: /reservas');
                }
            }
        }

        $router->render('reservas/crear', [
            'alertas' => $alertas,
            'reserva' => $reserva
        ]);
    }

    public static function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            if ($id) {
                $reserva = Reserva::find($id);
                if ($reserva) {
                    $reserva->eliminar();
                    header('Location: /reservas');
                }
            }
        }
    }

    public static function obtenerCarros() {
        $sede = $_GET['sede'] ?? '';

        if (!$sede) {
            echo json_encode([]);
            return;
        }

        $carros = Carros::where('sede', $sede);
        echo json_encode($carros);
    }
}
