<?php

/**
 * Clase helper encargado de crear mayor abstraccion
 * en el codigo, con distintos metodos utiles que pueden
 * ser accedidos direcctamente en el controlador
 */
class Helper extends Database
{
    /**
     * Funcion encargada de verificar si un dato
     * ya existe en la base de datos
     *
     * @param string $tableName
     * @param string $id
     * @return bool
     */
    public static function exist(string $tableName, $id){
        $validate = false; 

        if($id != null){
            $sql = "SELECT id FROM $tableName WHERE id = $id";
    
            $db = self::connect();
            
            $validate = $db->query($sql)->fetch();
        }

        
        return $validate || $validate != null ? true : false;
    }
}