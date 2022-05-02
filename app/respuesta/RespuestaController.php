<?php
require_once("../app/base/BaseController.php");
require_once("../app/respuesta/RespuestaService.php");

class RespuestaController extends BaseController
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
    private function almacenarRespuesta($params)
    {
        if ($this->getPerfilUsuario() == 3) {
            if ($this->getRequestMethod() == "POST") {
                $objService = new RespuestaService;
                $resultSet = $objService->insertRespuesta($params);

                // $resultSet = $this->obtenerDatosTabla($params["idTramite"], "autorizacion");
                if ($resultSet != null) {
                    $response = crearRespuestaSolicitud(200, "OK", "La respuesta se registro correctamente.", $resultSet);
                } else {
                    $response = crearRespuestaSolicitud(400, "error", "No se pudo registrar la respuesta.");
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
    private function actualizarRespuesta($params)
    {
        if ($this->getPerfilUsuario() == 3) {
            if ($this->getRequestMethod() == "PUT") {
                if (isset($params['id_respuesta'])) {
                    //obtenemos el nombre del campo donde vamos a registrar el valor
                    $objService = new RespuestaService();
                    $affectedRows = $objService->updateRespuesta($params);
                    if ($affectedRows > 0) {
                        $response = crearRespuestaSolicitud(200, "OK", "Se modifico la respuesta.", ["affectedRows" => $affectedRows]);
                    } else {
                        $response = crearRespuestaSolicitud(400, "error", "Ocurrió un error al intentar modificar la respuesta.");
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
    private function eliminarRespuesta($params)
    {
        if ($this->getPerfilUsuario() == 3) {
            if ($this->getRequestMethod() == "PUT") {
                if (isset($params['id_respuesta'])) {
                    //obtenemos el nombre del campo donde vamos a registrar el valor
                    $objService = new RespuestaService();
                    $affectedRows = $objService->deleteRespuesta($params);
                    if ($affectedRows > 0) {
                        $response = crearRespuestaSolicitud(200, "OK", "Se elimino la respuesta.", ["affectedRows" => $affectedRows]);
                    } else {
                        $response = crearRespuestaSolicitud(400, "error", "Ocurrió un error al intentar eliminar la respuesta.");
                    }
                } else {
                    $response = crearRespuestaSolicitud(400, "error", "Ocurrió un error al intentar eliminar la respuesta.");
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
}
