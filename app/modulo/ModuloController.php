<?php
require_once("../app/base/BaseController.php");
require_once("../app/modulo/ModuloService.php");

class ModuloController extends BaseController
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
    private function almacenarModulo($params)
    {
        if ($this->getPerfilUsuario() == 3) {
            if ($this->getRequestMethod() == "POST") {
                $objService = new ModuloService;
                $resultSet = $objService->insertModulo($params);

                // $resultSet = $this->obtenerDatosTabla($params["idTramite"], "autorizacion");
                if ($resultSet != null) {
                    $response = crearRespuestaSolicitud(200, "OK", "El Cuestionario se registro correctamente.", $resultSet);
                } else {
                    $response = crearRespuestaSolicitud(400, "error", "No se pudo registrar el cuestionario.");
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
    private function actualizarModulo($params)
    {
        if ($this->getPerfilUsuario() == 3) {
            if ($this->getRequestMethod() == "PUT") {
                if (isset($params['id_modulo'])) {
                    //obtenemos el nombre del campo donde vamos a registrar el valor
                    $objService = new ModuloService();
                    $affectedRows = $objService->updateModulo($params);
                    if ($affectedRows > 0) {
                        $response = crearRespuestaSolicitud(200, "OK", "Se modifico el cuestionario.", ["affectedRows" => $affectedRows]);
                    } else {
                        $response = crearRespuestaSolicitud(400, "error", "Ocurrió un error al intentar modificar el cuestionario.");
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
    private function eliminarModulo($params)
    {
        if ($this->getPerfilUsuario() == 3) {
            if ($this->getRequestMethod() == "PUT") {
                if (isset($params['id_modulo'])) {
                    //obtenemos el nombre del campo donde vamos a registrar el valor
                    $objService = new ModuloService();
                    $affectedRows = $objService->deleteModulo($params);
                    if ($affectedRows > 0) {
                        $response = crearRespuestaSolicitud(200, "OK", "Se elimino el cuestionario.", ["affectedRows" => $affectedRows]);
                    } else {
                        $response = crearRespuestaSolicitud(400, "error", "Ocurrió un error al intentar eliminar el cuestionario.");
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
}
