<?php


// require_once("../app/elecciones/EleccionController.php");
// require_once("../app/tipo/TipoController.php");

require_once("../app/files/FilesController.php");

// require_once("../app/usuario/UsuarioController.php");

require_once("../app/tipoPregunta/TipoPreguntaController.php");
require_once("../app/cuestionario/CuestionarioController.php");
require_once("../app/modulo/ModuloController.php");
require_once("../app/opcion/OpcionController.php");
require_once("../app/pregunta/PreguntaController.php");
require_once("../app/respuesta/RespuestaController.php");

function getArrayNotFound($message)
{
    $response['headers'] = [
        "HTTP/1.1 404 Not Found",
    ];
    $response['body'] = [
        'code' => 404,
        'status' => "error",
        'message' => $message,
    ];
    return $response;
}

function retornarRespuestaSolicitud($response)
{
    if (isset($response['headers'])) {
        foreach ($response['headers'] as $unHeader) {
            header($unHeader);
        }
    }
    echo json_encode($response['body']);
    exit();
}

function repararChars($unCampito)
{
    return htmlspecialchars(iconv("iso-8859-1", "utf-8", $unCampito));
}

function formatearFechaAceptadaPorLaCuarentona($unaFechaConBarritas)
{
    return date("Y-m-d H:i:s", strtotime($unaFechaConBarritas));
}

function crearRespuestaSolicitud($code, $status, $message, $data = null)
{
    $response['body'] = [
        "code" => $code,
        "status" => $status,
        "message" => $message,
        "data" => $data
    ];

    return $response;
}

function obtenerController($controllerName)
{
    switch (strtolower($controllerName)) {
        case 'cuestionario': //controlador de tramites
            $controller = new CuestionarioController();
            break;

        case 'modulo': //controlador de usuarios
            $controller = new ModuloController();
            break;

        case 'opcion': //controlador de elecciones
            $controller = new OpcionController();
            break;

        case 'pregunta': //controlador de turnos
            $controller = new PreguntaController();
            break;

        case 'respuesta': //controlador de autorizaciones
            $controller = new RespuestaController();
            break;

        case 'tipoPregunta': //controlador de vehiculo
            $controller = new TipoPreguntaController();
            break;

        default: //no se encontro controlador
            $controller = null;
            break;
    }

    return $controller;
}

/* Funcion que recibe el nombre del archivo, el id de la solicitud y el tipo por si corresponde a una categoria/paso del proyecto
y retorna el camino al archivo para ser almacenado en la base de datos */
function getDireccionArchivoAdjunto($nombreProyecto, $nombreArchivo, $idSolicitud, $pasoLicencia = null)
{
    $filePath = null;

    if (PATH_FILE_LOCAL) {
        $target_path_local = $pasoLicencia != null
            ? "../../../projects_files/" . $nombreProyecto . "/" . $idSolicitud . "/" . $pasoLicencia . "/"
            : "../../../projects_files/" . $nombreProyecto . "/nodeberiapasar/" . $idSolicitud . "/";
    } else {
        PROD ? $serverFolder = "produccion" : $serverFolder = "replica";
        $target_path_local = $pasoLicencia != null
            ? "../../../../../../../../../DataServer/" . $serverFolder . "/projects_files/" . $nombreProyecto . "/" . $idSolicitud . "/" . $pasoLicencia . "/"
            : "../../../../../../../../../DataServer/" . $serverFolder . "/projects_files/" . $nombreProyecto . "/nodeberiapasar/" . $idSolicitud . "/";
    }

    if (!file_exists($target_path_local)) {
        mkdir($target_path_local, 0755, true);
    };

    if ($nombreArchivo != null) {
        $filePath = $target_path_local . $nombreArchivo;
    }

    return $filePath;
}

/* Funcion que recibe el nombre del archivo, un array con extensiones permitidas y verifica que la extensión del archivo se condiga con alguna de las permitidas */
function verificarExtensionValida($nombreArchivo, $arrayExtensionesPermitidas = ['jpg', 'jpeg', 'png', 'bmp', 'pdf'])
{
    $regexExtensionesPermitidas = "/(?).(" . implode("|", $arrayExtensionesPermitidas) . ")$/i";
    return preg_match($regexExtensionesPermitidas, $nombreArchivo);
}

/* Funcion que recibe el nombre del archivo y retorna la extension del archivo precedida por un punto */
function obtenerExtensionArchivo($fileType)
{
    if (str_contains($fileType, "image/")) {
        $extension = ".jpg";
    } elseif (str_contains($fileType, "application/pdf")) {
        $extension = ".pdf";
    } else {
        $extension = null;
    }
    return $extension;
}

/* El formato de la fecha se pone año dia mes para que lo acepte la consulta en la db, despues lo muestra bien */
function formatearFechaCenat($unaFecha)
{
    return str_replace(": ", ":", date('Y-m-d h:i:s', strtotime($unaFecha)));
}

function verEstructura($e, $die = false)
{
    echo "<pre>";
    print_r($e);
    echo "</pre>";
    if ($die) die();
}

function buscarPorEstado($unEstado)
{   $sqlQuery="";
    if (isset($unEstado)) {
        switch ($unEstado) {
            case 'completado':
                # code...
                $sqlQuery .= " AND licencia_tramite.id_estado = 9 GROUP BY wapPersonas.Nombre, wapPersonas.Documento, licencia_tramite.id_tramite, licencia_tipo.tipo, licencia_estado.descripcion";
                break;
            case 'incompleto':
                # code...
                $sqlQuery .= " AND licencia_tramite.id_estado <> 9 AND licencia_tramite.id_estado <> 10 AND licencia_tramite.id_estado <> 11 GROUP BY wapPersonas.Nombre, wapPersonas.Documento, licencia_tramite.id_tramite, licencia_tipo.tipo, licencia_estado.descripcion";
                break;
            case 'aprobado':
                # code...
                $sqlQuery .= " AND licencia_tramite.id_estado = 10 GROUP BY wapPersonas.Nombre, wapPersonas.Documento, licencia_tramite.id_tramite, licencia_tipo.tipo, licencia_estado.descripcion";
                break;
            case 'rechazado':
                # code...
                $sqlQuery .= " AND licencia_tramite.id_estado = 11 GROUP BY wapPersonas.Nombre, wapPersonas.Documento, licencia_tramite.id_tramite, licencia_tipo.tipo, licencia_estado.descripcion";
                break;

            default:
                # code...
                $sqlQuery .= " GROUP BY wapPersonas.Nombre, wapPersonas.Documento, licencia_tramite.id_tramite, licencia_tipo.tipo, licencia_estado.descripcion";
                break;
        }
    } else {
        $sqlQuery .= " GROUP BY wapPersonas.Nombre, wapPersonas.Documento, licencia_tramite.id_tramite, licencia_tipo.tipo, licencia_estado.descripcion";
    }

    return $sqlQuery;
}
