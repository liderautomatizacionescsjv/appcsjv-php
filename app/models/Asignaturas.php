<?php

namespace Model;

class Asignaturas extends ActiveRecord{
    protected static $tabla = 'asignaturas';
    protected static $columnasDB = ['idasignatura', 'nombre'];

    public $idasignatura;
    public $nombre;
 
    public function __construct($args = [])
    {
        $this-> idasignatura= $args['idasignatura'] ?? null;
        $this-> nombre= $args['nombre'] ?? null;
    
    }
}