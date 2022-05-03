<?php
require_once("../app/base/BaseController.php");
require_once("../app/opcion/OpcionService.php");

class OpcionController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function procesarRespuesta($method, $params)
    {
        try {
            if ($this->getRequestMethod() == "GET" || $this->getRequestMethod() == "DELETE") {
                $response = $this->{$method}();
            } else {
                $response = $this->{$method}($params);
            }
        } catch (Error $e) {
            $response = crearRespuestaSolicitud(404, "error", $e->getMessage());
            //$response['headers'] = ['HTTP/1.1 404 Not Found'];
        }
        return $response;
    }
    private function almacenarOpcion($params)
    {
        if ($this->getPerfilUsuario() == 3) {
            if ($this->getRequestMethod() == "POST") {
                $objService = new OpcionService;
                $resultSet = $objService->insertOpcion($params);

                // $resultSet = $this->obtenerDatosTabla($params["idTramite"], "autorizacion");
                if ($resultSet != null) {
                    $response = crearRespuestaSolicitud(200, "OK", "La opcion se registro correctamente.", $resultSet);
                } else {
                    $response = crearRespuestaSolicitud(400, "error", "No se pudo registrar la opcion.");
                }
                $response['headers'] = ['HTTP/1.1 200 OK'];
            } else {
                $response = crearRespuestaSolicitud(400, "error", "Metodo HTTP equivocado.");
            }
        } else {
            $response = crearRespuestaSolicitud(401, "error", "No tiene permisos para ejecutar la consulta.");
            $response['headers'] = ['HTTP/1.1 401 Unauthorized'];
        }
        return $response;
    }
    private function actualizarOpcion($params)
    {
        if ($this->getPerfilUsuario() == 3) {
            if ($this->getRequestMethod() == "PUT") {
                if (isset($params['id_opcion'])) {

                    //obtenemos el nombre del campo donde vamos a registrar el valor
                    $objService = new OpcionService();
                    $affectedRows = $objService->updateOpcion($params);
                    if ($affectedRows > 0) {
                        $response = crearRespuestaSolicitud(200, "OK", "Se modifico la opcion.", ["affectedRows" => $affectedRows]);
                    } else {
                        $response = crearRespuestaSolicitud(400, "error", "Ocurrió un error al intentar modificar la opcion.");
                    }
                } else {
                    $response = crearRespuestaSolicitud(400, "error", "Ocurrió un error al intentar eliminar el cuestionario.");
                }
            } else {
                //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
                $response = crearRespuestaSolicitud(400, "error", "Metodo HTTP equivocado");
            }
        } else {
            $response = crearRespuestaSolicitud(401, "error", "No tiene permisos para ejecutar la consulta.");
            $response['headers'] = ['HTTP/1.1 401 Unauthorized'];
        }
        return $response;
    }
    private function eliminarOpcion($params)
    {
        if ($this->getPerfilUsuario() == 3) {
            if ($this->getRequestMethod() == "PUT") {
                if (isset($params['id_opcion'])) {
                    //obtenemos el nombre del campo donde vamos a registrar el valor
                    $objService = new OpcionService();
                    $affectedRows = $objService->deleteOpcion($params);
                    if ($affectedRows > 0) {
                        $response = crearRespuestaSolicitud(200, "OK", "Se elimino la opcion.", ["affectedRows" => $affectedRows]);
                    } else {
                        $response = crearRespuestaSolicitud(400, "error", "Ocurrió un error al intentar eliminar la opcion.");
                    }
                } else {
                    $response = crearRespuestaSolicitud(400, "error", "Ocurrió un error al intentar eliminar la opcion.");
                }
            } else {
                //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
                $response = crearRespuestaSolicitud(400, "error", "Metodo HTTP equivocado");
            }
        } else {
            $response = crearRespuestaSolicitud(401, "error", "No tiene permisos para ejecutar la consulta.");
            $response['headers'] = ['HTTP/1.1 401 Unauthorized'];
        }
        return $response;
    }

    private function listarOpcionesPregunta($params)
    {
        if ($this->getRequestMethod() == "POST") {
            // var_dump($this->getPerfilUsuario());
            if ($this->getPerfilUsuario() == 3) {
                if (isset($params['pregunta_id'])) {

                    $objService = new OpcionService;
                    $listadoTramites = $objService->listarOpcionesPregunta($params);

                    if (count($listadoTramites) > 0) {
                        $response = crearRespuestaSolicitud(200, "OK", "Datos recuperados exitosamente.", $listadoTramites);
                    } else {
                        $response = crearRespuestaSolicitud(200, "OK", "No se encontraron tramites para listar");
                    }
                    $response['headers'] = ['HTTP/1.1 200 OK'];
                } else {
                    $response = crearRespuestaSolicitud(400, "error", "Falto especificar algun parametro.");
                }
            } else {
                $response = crearRespuestaSolicitud(401, "error", "No tiene permisos para ejecutar la consulta.");
                //$response['headers'] = ['HTTP/1.1 401 Unauthorized'];
            }
        } else {
            $response = crearRespuestaSolicitud(400, "error", "Metodo HTTP equivocado");
            //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
        }
        return $response;
    }
}
