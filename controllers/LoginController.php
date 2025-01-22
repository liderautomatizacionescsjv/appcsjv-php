<?php

namespace Controllers;

use Classes\Email;
use MVC\Router;
use Model\Usuario;

class LoginController{
    public static function login(Router $router){

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);

                if($usuario){
                    // Verificar el password
                    if($usuario->comprobar($auth->password)){
                        // Autenticar el usuario
                        // session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;
                        $_SESSION['idpersonal'] = $usuario->usuarioid;



                        // Redireccionamiento

                        if($usuario->rol === "DOCENTE"){
                            $_SESSION['rol'] = $usuario->rol ?? '';
                            header('Location: /docentes');
                        }else{
                            header('Location: /');
                        }



                    };
                }else{
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }
        $alertas = Usuario::getAlertas();
        $router->render('/auth/login', [
            'alertas' => $alertas

        ]);


    }

    public static function logout(){
        $_SESSION = [];

        header('Location: /');
    }

    public static function olvide(Router $router){

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

            if(empty($alertas)){
                $usuario = Usuario::where('email', $auth->email);

                if($usuario && $usuario->confirmado === '1'){
                    // Generar un token
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Alerta de exito
                    Usuario::setAlerta('exito', 'Revisa tu email');

                }else{
                    Usuario::setAlerta('error', 'El usuario no existe o no está confirmado');
                    
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('/auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }
    public static function recuperar(Router $router){
        $alertas = [];
        $error = false;

        $token = s($_GET['token']);

        // Buscar usuario por su token

        $usuario = Usuario::where('token' , $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no Válido');
            $error = true;

        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Leer el nuevo password y guardarlo

            $password = new Usuario($_POST);

            $alertas = $password->validarPassword();
            if(empty($alertas)){
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                $resultado = $usuario->guardar();
                if($resultado){
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }


    public static function crear(Router $router) {
        $usuario = new Usuario();
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();
            
            // Si hay errores, no continuar
            if (!empty($alertas['error'])) {
                $router->render('/auth/crear-cuenta', [
                    'usuario' => $usuario,
                    'alertas' => $alertas
                ]);
                return;
            }
    
            // Validar si el email existe en la tabla 'personal' y obtener el nombre
            if ($usuario->emailExisteEnPersonal()) {
                // Verificar que el usuario no esté registrado en 'usuarios'
                $resultado = $usuario->existeUsuario();
        
                if ($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();
        
                    // Generar un Token único
                    $usuario->crearToken();
        
                    // Enviar el Email de Confirmación
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
        
                    // Guardar en la base de datos
                    $resultado = $usuario->guardar();
                    
                    if ($resultado) {
                        header('Location: /mensaje');
                    }
                }
            } else {
                $alertas = Usuario::getAlertas();
            }
        }
    
        $router->render('/auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }
    
    
    

    public static function mensaje(Router $router){

        $router->render('auth/mensaje');
    }

    public static function confirmar(Router $router){
        $alertas = [];

        $token = s($_GET['token']);

        $usuario = Usuario::where("token", $token);


        if(empty($usuario) || $usuario->token === ''){
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token no válido');

        }else{
            // Modificar a usuario confirmado

            $usuario->confirmado = "1";
            $usuario->token = '';

            $usuario->guardar();
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');

        }

        $alertas =  Usuario::getAlertas();

        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }
}