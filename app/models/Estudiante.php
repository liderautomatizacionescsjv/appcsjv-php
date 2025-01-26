<?php

namespace Model;

class Estudiante extends ActiveRecord{
    protected static $tabla = 'estudiantes';
    protected static $columnasDB = ['codigo', 'documento', 'nombre', 'fechaNacimiento', 'grupoid', 'estado', 'observacion', 'alerta', 'fecha', 'email'];



    public $codigo;
    public $documento;
    public $nombre;
    public $fechaNacimiento;
    public $grupoid;
    public $estado;
    public $observacion;
    public $alerta;
    public $fecha;
    public $email;


    public function __construct($args = [])
    {
        $this-> codigo= $args['codigo'] ?? null;
        $this-> documento= $args['documento'] ?? null;
        $this-> nombre= $args['nombre'] ?? null;
        $this-> fechaNacimiento= $args['fechaNacimiento'] ?? null;
        $this-> grupoid= $args['grupoid'] ?? null;
        $this-> estado =  $args['estado'] ?? null;
        $this-> observacion =  $args['observacion'] ?? null;
        $this-> alerta =  $args['alerta'] ?? null;
        $this-> fecha =  $args['fecha'] ?? null;
        $this-> email =  $args['email'] ?? null;

    }
}