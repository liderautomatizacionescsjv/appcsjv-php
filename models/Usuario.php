<?php

namespace Model;

use Classes\Email;

class Usuario extends ActiveRecord{
    // Base de datos
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'email', 'password', 'rol', 'confirmado', 'token', 'nombre', 'usuarioid'];
    protected static $primaryKey = 'id';
    public $id;
    public $email;
    public $password;
    public $rol;
    public $confirmado;
    public $token;
    public $nombre;
    public $usuarioid;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->rol = $args['rol'] ?? 0;
        $this->confirmado = $args['confirmado'] ?? 0;
        $this->token = $args['token'] ?? '';
        $this->nombre = $args['nombre'] ?? '';
        $this->usuarioid = $args['usuarioid'] ?? '';
    }


    // Mensajes de validación para la creación de una cuenta
    public function validarNuevaCuenta() {
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
    
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio';
        } elseif (strlen($this->password) < 10) {
            self::$alertas['error'][] = 'El password debe contener al menos 10 caracteres';
        } elseif (!preg_match('/[0-9]/', $this->password)) {
            self::$alertas['error'][] = 'El password debe contener al menos un número';
        } elseif (!preg_match('/[^a-zA-Z0-9]/', $this->password)) { 
            self::$alertas['error'][] = 'El password debe contener al menos un carácter especial (!@#$%^&* etc.)';
        }
    
        return self::$alertas;
    }


    public function validarEmail(){
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        
        return self::$alertas;

    }

    public function validarPassword(){
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password es Obligatorio';
        } elseif (strlen($this->password) < 10) {
            self::$alertas['error'][] = 'El password debe contener al menos 10 caracteres';
        } elseif (!preg_match('/[0-9]/', $this->password)) {
            self::$alertas['error'][] = 'El password debe contener al menos un número';
        } elseif (!preg_match('/[^a-zA-Z0-9]/', $this->password)) { 
            self::$alertas['error'][] = 'El password debe contener al menos un carácter especial (!@#$%^&* etc.)';
        }
    
        return self::$alertas;
    }
    

    // Revisa si el usuario ya existe
    public function existeUsuario(){
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";

        $resultado = self::$db->query($query);

        if($resultado->num_rows){
            self::$alertas['error'][] = 'El usuario ya está registrado';
        }

        return $resultado;

    }

    // Revisar si existe en la tabla de personal

    public function emailExisteEnPersonal() {
        $query = "SELECT nombre, rol, idpersonal FROM personal WHERE correo = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $resultado = $stmt->get_result();
        // debuguear($resultado);
        if($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $this->nombre = $fila['nombre']; // Asigna el nombre del usuario
            $this->rol = $fila['rol']; // Asigna el rol del usuario
            $this->usuarioid = $fila['idpersonal'];
            return true;
        } else {
            self::$alertas['error'][] = "El correo no está registrado en la base de datos del personal";
            return false;
        }
    }
    
    

    public function hashPassword(){
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(){
        $this->token = uniqid();

    }



    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = "El email es Obligatorio";
        }

        if(!$this->password){
            self::$alertas['error'][] = "El password es Obligatorio";
        }

        return self::$alertas;
    }

    public function comprobar($password){

        $resultado = password_verify($password, $this->password);

        if(!$resultado || !$this->confirmado){
            self::$alertas['error'][] = 'Contraseña Incorrecta o tu cuenta no ha sido confirmada';
        }else{
            return true;
        }

    }

}