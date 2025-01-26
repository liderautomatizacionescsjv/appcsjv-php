<?php
namespace Model;

date_default_timezone_set('America/Bogota'); // Ajusta según el país


#[\AllowDynamicProperties]
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];
    protected static $primaryKey = ''; // Por defecto, 'id'


    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($database) {
        self::$db = $database;
    }

    public static function setAlerta($tipo, $mensaje) {
        static::$alertas[$tipo][] = $mensaje;
    }

    // Validación
    public static function getAlertas() {
        return static::$alertas;
    }

    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    // Consulta SQL para crear un objeto en Memoria
    public static function consultarSQL($query) {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;
    }

    // Crea el objeto en memoria que es igual al de la BD
    protected static function crearObjeto($registro) {
        $objeto = new static;

        foreach($registro as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }

        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    // Sanitizar los datos antes de guardarlos en la BD
    public function sanitizarAtributos() {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            // Si el valor es NULL, lo convertimos en 'NULL' (string) para SQL
            if (is_null($value)) {
                $sanitizado[$key] = 'NULL';
            } else {
                $sanitizado[$key] = self::$db->escape_string($value);
            }
        }
        return $sanitizado;
    }
    

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }

    // Registros - CRUD
    public function guardar() {
        $primaryKey = static::$primaryKey ?? 'id'; // Valor predeterminado: 'id'

        $resultado = '';
        if(!is_null($this->$primaryKey)) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }

    // Todos los registros
    public static function all() {
        $query = "SELECT * FROM " . static::$tabla;
        $resultado = self::consultarSQL($query);
        return $resultado;
    }

    // Busca un registro por su id
    public static function find($id) {
        // Usa la clave primaria definida dinámicamente
        $primaryKey = static::$primaryKey ?? 'id'; // Usa 'id' como predeterminado si no está definido
    
        $query = "SELECT * FROM " . static::$tabla . " WHERE $primaryKey = '" . self::$db->escape_string($id) . "'";
        $resultado = self::consultarSQL($query);
        return array_shift($resultado);
    }
    

     // Busca un registro por su where
     public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE $columna = '$valor'";
        // debuguear($query);
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

     // Busca varios registros por su where
     public static function where_multiple($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla  ." WHERE $columna = '$valor'";
        // debuguear($query);
        $resultado = self::consultarSQL($query);
        return $resultado  ;
    }

    // JOINS
    public static function join($joins = []) {
        $columnasTablaPrincipal = static::$tabla . ".*";
        $joinsSQL = "";
    
        foreach ($joins as $join) {
            $joinsSQL .= " JOIN {$join['tabla']} ON " . static::$tabla . ".{$join['columnaFK']} = {$join['tabla']}.{$join['columnaPK']}";
            foreach ($join['columnas'] as $alias => $columna) {
                $columnasTablaPrincipal .= ", {$join['tabla']}.$columna AS $alias";
            }
        }
    
        $query = "SELECT $columnasTablaPrincipal FROM " . static::$tabla . $joinsSQL;
        $resultado = self::$db->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    // JOIN CONDICIONADO
    public function joinWithCondition($joins = [], $whereCondition = '') {
        $columnasTablaPrincipal = static::$tabla . ".*";
        $joinsSQL = "";
    
        foreach ($joins as $join) {
            $joinsSQL .= " JOIN {$join['tabla']} ON " . static::$tabla . ".{$join['columnaFK']} = {$join['tabla']}.{$join['columnaPK']}";
            foreach ($join['columnas'] as $alias => $columna) {
                $columnasTablaPrincipal .= ", {$join['tabla']}.$columna AS $alias";
            }
        }
    
        $query = "SELECT $columnasTablaPrincipal FROM " . static::$tabla . $joinsSQL;
    
        // Agregar condición WHERE si está definida
        if (!empty($whereCondition)) {
            $query .= " WHERE $whereCondition";
        }
    
        // Ejecutar consulta
        $resultado = self::$db->query($query);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }
    
    
    
    

    // Obtener Registros con cierta cantidad
    public static function get($limite) {
        $query = "SELECT * FROM " . static::$tabla . " LIMIT {$limite}";
        $resultado = self::consultarSQL($query);
        return array_shift( $resultado ) ;
    }

    // crea un nuevo registro
    public function crear() {
        // Sanitizar los datos
        $atributos = $this->sanitizarAtributos();

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= ") VALUES ('"; 
        $query .= join("', '", array_values($atributos));
        $query .= " ') ";
        // debuguear($query);
        // Resultado de la consulta
        $resultado = self::$db->query($query);
        return [
           'resultado' =>  $resultado,
           'id' => self::$db->insert_id
        ];
    }

    // Actualizar el registro
    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->atributos(); // NO sanitizamos aquí para preservar los NULLs
    
        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value) {
            if ($value === null) {
                $valores[] = "{$key}=NULL"; // ✅ Guardar NULL correctamente
            } else {
                $valores[] = "{$key}='" . self::$db->escape_string($value) . "'";
            }
        }
    
        // Consulta SQL
        $query = "UPDATE " . static::$tabla ." SET ";
        $query .= join(', ', $valores);
        $query .= " WHERE " . static::$primaryKey . " = '" . self::$db->escape_string($this->{static::$primaryKey}) . "' ";
        $query .= " LIMIT 1 ";
    
        // Actualizar BD
        $resultado = self::$db->query($query);
        return $resultado;
    }

    

    // Eliminar un Registro por su ID
    public function eliminar() {
    // Usa la clave primaria dinámica
    $primaryKey = static::$primaryKey ?? 'id';

    if (!$this->$primaryKey) {
        return false; // Evitar eliminar sin clave primaria
    }

    $query = "DELETE FROM " . static::$tabla . " WHERE $primaryKey = '" . self::$db->escape_string($this->$primaryKey) . "' LIMIT 1";
    $resultado = self::$db->query($query);
    return $resultado;
}

    

}