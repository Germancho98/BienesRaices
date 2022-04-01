<?php

// Importacion de todos las clases utilizadas y requeridas
// para la ejecucion de los metodos del Controller.
require_once '../Config/Conexion.php';
require_once '../Models/bien.php';
require_once '../Controller/Validador.php';
require_once '../Models/tipo.php';
require_once '../Models/ciudad.php';


class BienController
{
    /**
     * Funcion encargada de obtener todos los datos de los filtros
     * 'tipo' y 'ciudad'.
     *
     * @return json
     */
    public static function getFilterData(){
        $tipos = Tipo::all();
        $ciudades = Ciudad::all();

        $return = [
            'status'   => true,
            'tipos'    => $tipos,
            'ciudades' => $ciudades   
        ];

        echo json_encode($return);
        return;
    }

    /**
     * Metodo utilizado para almacenar en la base de datos
     * los atributos del bien seleccionado en el front.
     *
     * @return json
     */
    public static function save(){

        // Verificar si se recibio la data
        if (isset($_POST['data'])) {

            $data = $_POST['data'];
            
            // Crear una nueva instancia de Bien
            $bien = new Bien;

            // Settear el dato Id
            $bien->setId($data['Id']);

            // Validar si existe en la base de datos
            // y retornar una respuesta en case de existir
            if(Helper::exist('bienes', $bien->getId())){
                $response = [
                    'status' => false,
                    'msg' => 'El Bien ya esta guardado!',
                    'title' => 'Ya lo guardaste!!',
                    'color' => 'warning'
                ];
            }else{

                // Settear todos los datos del model Bien
                // para luego almacenarlos en la base de datos
                $bien->setDireccion($data['Direccion']);
                $bien->setTelefono($data['Telefono']);
                $bien->setPrecio($data['Precio']);
                $bien->setCodigo_postal($data['Codigo_Postal']);
                $bien->setCiudad_id($data['Ciudad']);
                $bien->setTipo_id($data['Tipo']);
                
                // Almacenar el bien en la base de datos
                if($bien->save())
                {
                    // Retornar respuesta en caso de exito
                    $response = [
                        'status' => true,
                        'msg' => 'El Bien se ha guardado con exito.',
                        'title' => 'Guardado!!',
                        'color' => 'success'
                    ];
                }else{
                    // Retornar respuesta en caso de fallo
                    $response = [
                        'status' => false,
                        'msg' => 'Ha ocurrido un error al guardar el Bien.',
                        'title' => 'Ups, algo salio mal :(',
                        'color' => 'error'
                    ];
                }
            
            }

        }else{

            // Retornar respuesta en caso de no haber recibido los datos
            // correctamente
            $response = [
                'status' => false,
                'msg' => 'No se han recibido los datos correctamente.',
                'title' => 'Ups! Hay un problema con los datos.',
                'color' => 'error'
            ];

        }


        echo json_encode($response);
        return;
    }


    /**
     * funcion encargada de obtener todos los datos de los bienes almacenados
     * en la base de datos
     *
     * @return json
     */
    public static function mySavedData(){

        // Inicializar una nueva instancia del modelo 'Bien'
        $bienes = new Bien;

        // Ejecutar el metod 'saved' que retorna los bienes almacenados
        $bienes = $bienes->saved();
        
        // Retornar una respuesta con los bienes
        $response = [ 
            'status' => true,
            'bienes' => $bienes
        ];

        echo json_encode($response);
        return;
    }


    /**
     * Funcion encargada de eliminar de la base de datos un bien
     * seleccionado.
     *
     * @return json
     */
    public static function delete(){

        // Verificar si se ha recibido el dato id
        if(isset($_POST['id']) && !empty($_POST['id'])){

            // Crear una nueva instancia de 'Bien'
            $bien = new Bien;

            // Setear el id 
            $bien->setId($_POST['id']);

            // Validar si existe en la base de datos;
            if($bien->exist()){

                // En caso de existir, eliminarlo
                $bien->deleted();

                // Retornar una respuesta
                $response = [
                    'status' => true,
                    'msg'   => 'El bien ha sido eliminado correctamente',
                    'title' => 'Eliminado',
                    'color' => 'success'
                ];
            
            }else{
                
                // En caso de no existir retornar una respuesta
                // con los detalles.
                $response = [
                    'status' => false,
                    'msg'   => 'El bien que intentas eliminar no esta registrado en la base de datos.',
                    'title' => 'Ups!',
                    'color' => 'warning'
                ];

            }

        }else{

            // Retornar una respuesta en caso de no recibir los datos correctamente
            $response = [
                'status' => false,
                'msg' => 'No se han recibido los datos correctamente.',
                'title' => 'Ups! Hay un problema con los datos.',
                'color' => 'error'
            ];

        }

        echo json_encode($response);
        return;

    }


    /**
     * Funcion utilizada para filtrar y emitir el reporte
     * en formato csv
     *
     * @return text/view
     */
    public static function filterReport(){
        
        // Crear una nueva instancia de Bien
        $bien = new Bien;
        
        // Verificar si se recibio algun parametro del filtro por GET
        if(@!empty($_REQUEST['tipo_id']) || @!empty($_REQUEST['ciudad_id'])){

            // En caso de recibirse alguno de los parametros limpiarlos con trim() en 
            // caso de ser recibidos y asignarlos a una variable
            $tipo_id   = !empty($_REQUEST['tipo_id']) ? trim($_REQUEST['tipo_id']) : null;
            $ciudad_id = !empty($_REQUEST['ciudad_id']) ? trim($_REQUEST['ciudad_id']) : null;

            // Verificar si existen alguno de los 2 o los 2
            if(Helper::exist('tipos_casa', $tipo_id) || 
                Helper::exist('ciudades', $ciudad_id)){
                
                // Settear los ids en el bien    
                $bien->setTipo_id($tipo_id, true);
                $bien->setCiudad_id($ciudad_id, true);
                
            }else{

                // Responder en texto plano en caso de no recibir los datos correctos
                $responseText = 'Algunos de los valores enviados son incorrectos.';

            }


        }

        // Ejecutar, asignar y verificar si el resultado de la busqueda
        // es mayor o igual a uno para poder emitir un reporte.
        if($bienes = $bien->filterReport()){

            // En caso de haber al menos un registro emitir un reporte en csv
            $bien->emitReport($bienes);

        }else{

            // En caso de no haber mas de un reporte o uno, retornar una respuesta
            // en texto plano
            $responseText = 'No se encontraron bienes que coincidan o no ha guardado ningun bien'; 

        }

        echo isset($responseText) ? $responseText : '';
        return;

    }

}


// Algoritmo que ejecuta el metodo solicitado, que sea recibido por POST o por GET
// y que exista en el BienController, para luego ejecutarlo.
if (isset($_REQUEST['option']) &&  method_exists(new BienController, $_REQUEST['option'])) {

    // Almacenar el metodo recibido por el parametro 'option'
    $method = $_REQUEST['option'];

    // Ejecutar el metodo solicitado
    BienController::$method();

}else{
    // Responder en texto plano en caso de que el metodo no exista
    echo 'El metodo no existe';
}