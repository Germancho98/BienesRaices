<?php

require_once '../Config/Conexion.php';

class Bien extends DataBase
{
    // Inicializacion de los atributos del modelo
    private $id;
    private $ciudad_id;
    private $tipo_id;
    private $direccion;
    private $telefono;
    private $codigo_postal;
    private $precio;

    
    //Metodos set y get
    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
 
    public function getCiudad_id()
    {
        return is_array($this->ciudad_id) ? trim($this->ciudad_id['id']) : trim($this->ciudad_id);
    }

    public function setCiudad_id($ciudadIdName, $search = false)
    {
        // Verificar si la variable search se encuentra en true
        // lo que significa que lo que se esta recibiendo en un id 
        // directamente por lo cual no se necesita ejecutar el metodo setterIds,
        // que se ejecuta cuando lo que se recibe es un nombre.
        if (!$search) {
            $this->ciudad_id = $this->setterIds($ciudadIdName, 'ciudades');;
        }else{
            $this->ciudad_id = $ciudadIdName;
        }

        return $this;
    }

    public function getTipo_id()
    {
        return is_array($this->tipo_id) ? trim($this->tipo_id['id']) : trim($this->tipo_id);
    }
 
    public function setTipo_id($tipoIdName, $search = false)
    {
        // Verificar si la variable search se encuentra en true
        // lo que significa que lo que se esta recibiendo en un id 
        // directamente por lo cual no se necesita ejecutar el metodo setterIds,
        // que se ejecuta cuando lo que se recibe es un nombre.
        if(!$search){
            $this->tipo_id = $this->setterIds($tipoIdName, 'tipos_casa');
        }else{
            $this->tipo_id = $tipoIdName;
        }

        return $this;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }
 
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }
 
    public function getCodigo_postal()
    {
        return $this->codigo_postal;
    }

    public function setCodigo_postal($codigo_postal)
    {
        $this->codigo_postal = $codigo_postal;

        return $this;
    }
 
    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    public function save(){

        // Sentencia sql con parametros protegidos
        $sql = 'INSERT INTO bienes (`id`, `ciudad_id`, `tipo_id`, `direccion`, `telefono`, `codigo_postal`, `precio`) 
                VALUES (:id, :ciudadId, :tipoId, :direccion, :telefono, :codigoPostal, :precio)';
        
        // conexion a la base de datos con PDO
        $db = self::connect();
        
        // Array utilizado para remplazar los parametros
        $data = [
            'id'           => $this->getId(),
            'ciudadId'     => $this->getCiudad_id(),
            'tipoId'       => $this->getTipo_id(),
            'direccion'    => $this->getDireccion(),
            'telefono'     => $this->getTelefono(),
            'codigoPostal' => $this->getCodigo_postal(),
            'precio'       => $this->getPrecio()
        ];

        // Preparacion y ejecucion de la sentencia
        $result = $db->prepare($sql)->execute($data);

        return $result;

    }


    /**
     * Funcion utilizada para settear los ids cuando el parametro que se tiene
     * es el nombre.**/
    private function setterIds($tipoName, $tableName){

        $sql = "SELECT id FROM $tableName WHERE nombre = '$tipoName'";

        $db = self::connect();
        
        $query = $db->query($sql);

        $id = $query->fetch();

        return $id;
    
    }

    /**
     * Funcion utilizada para validar si el registro
     * ya existe en la base de datos**/
    public function exist(){

        $id = $this->getId();
        
        $sql = "SELECT id FROM bienes WHERE id = $id";

        $db = self::connect();
        
        $validate = $db->query($sql)->fetch();
        
        return $validate || $validate != null ? true : false;
    }


    /**
     * Funcion para obtener de la base de datos
     * todos los registros que se han guardado con sus respectivas relaciones**/
    public function saved(){

        $sql = 'SELECT b.id, b.direccion, c.nombre as ciudad, b.codigo_postal as codigo, b.telefono, t.nombre as tipo, '. 
            ' b.precio FROM bienes as b, ciudades as c, tipos_casa as t '.   
            'WHERE b.ciudad_id = c.id and b.tipo_id = t.id';

        $db = self::connect();

        $query = $db->query($sql);

        $bienes = $query->fetchAll();

        return $bienes;

    }


    /**
     * Funcion utilizada para eliminar un registro de la base de datos**/
    public function deleted(){

        $sql = "DELETE FROM bienes WHERE id = :id";
        
        $db = self::connect();

        $data = [
            'id' => $this->getId(),
        ];

        $result = $db->prepare($sql)->execute($data);

        return $result;

    }


    /**
     * Funcion utilizada para filtrar, validar y devolver todos los registros
     * de bienes que coincidan con los datos de busqueda.**/
    public function filterReport(){

        $tipoId   = $this->getTipo_id();
        $ciudadId = $this->getCiudad_id();

        $sql = "SELECT b.*, c.nombre as ciudad, t.nombre as tipo FROM bienes as b, ciudades as c, tipos_casa as t WHERE ";
        $sql .= "b.tipo_id = t.id AND b.ciudad_id = c.id";

        if(!empty($tipoId) || !empty($ciudadId)){
            
            $sql .= ' AND ';
            
            if($tipoId && $ciudadId){
    
                $sql .= "t.id = $tipoId AND c.id = $ciudadId";
            
            }elseif($tipoId){
            
                $sql .= "t.id = $tipoId";
            
            }else{
            
                $sql .= "c.id = $ciudadId";
            
            }
        }


        $db = self::connect();

        $query = $db->prepare($sql);

        $query->execute();

        $arrayBienes = $query->fetchAll();

        return COUNT($arrayBienes) >= 1 ? $arrayBienes : false;

    }

    /**
     * Funcion utilizada para emitir los reportes**/
    public function emitReport($bienes){

        $filename = 'reporte-bienes-intelcost.csv';

        require_once '../Config/excel.php'; 

    }
}