<?php

require_once '../Config/Conexion.php';

class Ciudad extends DataBase
{
    private $id;
    private $ciudad;

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of ciudad
     */ 
    public function getCiudad()
    {
        return $this->ciudad;
    }

    /**
     * Set the value of ciudad
     *
     * @return  self
     */ 
    public function setCiudad($ciudad)
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    public static function all(){

        $sql = 'SELECT * FROM ciudades ORDER BY id DESC';

		$db = self::connect();

		$query = $db->query($sql);

		$ciudades = $query->fetchAll();

		return $ciudades;
    
    }

    public function exist(){

        $id = $this->getId();
        
        $sql = "SELECT id FROM ciudades WHERE id = $id";

        $db = self::connect();
        
        $validate = $db->query($sql)->fetch();
        
        return $validate || $validate != null ? true : false;
    }
}