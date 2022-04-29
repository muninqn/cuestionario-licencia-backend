<?php
require_once("../app/base/BaseController.php");
require_once("../app/tipoPregunta/TipoPreguntaService.php");

class TipoPreguntaController extends BaseController
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
    private function almacenarTipoPregunta($params)
    {
        if ($this->getRequestMethod() == "POST") {
            // if ($this->getPerfilUsuario() == 3) {
            $tipoPreguntaService = new TipoPreguntaService;
            $resultSet = $tipoPreguntaService->insertTipoPregunta($params);

            // $resultSet = $this->obtenerDatosTabla($params["idTramite"], "autorizacion");
            if ($resultSet != null) {
                $response = crearRespuestaSolicitud(200, "OK", "El tipo de pregunta se registro correctamente.", $resultSet);
            } else {
                $response = crearRespuestaSolicitud(400, "error", "No se pudo registrar el tipo de pregunta.");
            }
            $response['headers'] = ['HTTP/1.1 200 OK'];
            // } else {
            //     $response = crearRespuestaSolicitud(401, "error", "No tiene permisos para ejecutar la consulta.");
            //     $response['headers'] = ['HTTP/1.1 401 Unauthorized'];
            // }
        } else {
            $response = crearRespuestaSolicitud(400, "error", "Metodo HTTP equivocado.");
        }
        return $response;
    }
    private function actualizarTipoPregunta($params)
    {
        if ($this->getRequestMethod() == "PUT") {

            //obtenemos el nombre del campo donde vamos a registrar el valor
            $objService = new TipoPreguntaService();
            $affectedRows = $objService->updateTipoPregunta($params);
            if ($affectedRows > 0) {
                $response = crearRespuestaSolicitud(200, "OK", "Se modifico el tipo de pregunta.", ["affectedRows" => $affectedRows]);
            } else {
                $response = crearRespuestaSolicitud(400, "error", "Ocurrió un error al intentar modificar el tipo de pregunta.");
            }
        } else {
            //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
            $response = crearRespuestaSolicitud(400, "error", "Metodo HTTP equivocado");
        }
        return $response;
    }
}
