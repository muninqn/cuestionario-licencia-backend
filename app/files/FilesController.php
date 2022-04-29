<?php

//
require_once("../app/files/FilesService.php");


class FilesController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function procesarRespuesta($method, $params)
    {
        try {
            if ($this->getRequestMethod() == "DELETE") {
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

    private function subirArchivo($params)
    {
        if ($this->getRequestMethod() == "POST") {

            if (isset($_FILES['archivo'])) {
                if (isset($_FILES['archivo']['mime'])) {
                    $fileExtension = $_FILES['archivo']['mime'];
                } elseif (isset($_FILES['archivo']['type'])) {
                    $fileExtension = $_FILES['archivo']['type'];
                } else {
                    $fileExtension = null;
                }
                if (verificarExtensionValida($fileExtension)) {
                    if ($this->getIdTramite() != null) {
                        if ((isset($params['ID']) && $params['ID'] != null && $params['ID'] != '') &&
                            (isset($params['descripcionArchivo']) && $params['descripcionArchivo'] != null && $params['descripcionArchivo'] != '')
                        ) {
                            $objService = new FilesService();
                            $controller = $objService->obtenerControllerSubirArchivo($params['ID']);

                            if ($controller != null) {
                                $nombreArchivo = "licencia_" . $this->getIdTramite() . "_" . $params['descripcionArchivo'] . obtenerExtensionArchivo($fileExtension);
                                //$nombreArchivo = "licencia_" . $this->getIdTramite() . "_" . $params['descripcionArchivo'];
                                $filePathLicencia = getDireccionArchivoAdjunto("licencia", $nombreArchivo, $this->getIdTramite(), strtolower($params['ID']));

                                //echo $nombreArchivo;
                                if ($objService->subirArchivoServidor($_FILES['archivo']['tmp_name'], $fileExtension, $_FILES['archivo']['size'], $filePathLicencia)) {
                                    //porque hay que validar la boleta del cenat ):
                                    if ($params['ID'] == "cenat") {
                                        //creamos la URI del archivo subido
                                        $cenatURI = str_replace("/", "\\\\", substr($filePathLicencia, 45));
                                        //echo $cenatURI;

                                        //llamamos al metodo que verifica la validez del cenat
                                        $cenatService = new CenatService;
                                        $analisisBoleta = $cenatService->verificarValidezCenat($cenatURI);
                                        //echo "jaje";
                                        //print_r($analisisBoleta);

                                        if ($analisisBoleta['error'] == null && $analisisBoleta['value']['boletaDePago'] != 0 && $analisisBoleta['value']['dni'] != 0) {
                                            $datosBoletaCenat = $analisisBoleta['value'];
                                            $datosBoletaCenat['descripcionArchivo'] = $params['descripcionArchivo'];
                                            $datosBoletaCenat['fechaEmision'] = formatearFechaCenat($datosBoletaCenat['fechaEmision']);
                                            $datosBoletaCenat['fechaVencimiento'] = formatearFechaCenat($datosBoletaCenat['fechaVencimiento']);

                                            $pudoSetearPath = $controller->actualizarPathArchivo($this->getIdTramite(), $datosBoletaCenat, $filePathLicencia);
                                        } else {
                                            if (unlink($filePathLicencia)) {
                                                //$response = crearRespuestaSolicitud(400, "error", ($analisisBoleta['error'] != null && $analisisBoleta['error'] != '') ? $analisisBoleta['error'] : "El archivo subido no es una boleta cenat valida.");
                                                $response = crearRespuestaSolicitud(400, "error", "El archivo subido no es una boleta cenat valida");
                                            } else {
                                                $response = crearRespuestaSolicitud(500, "error", "La boleta no es válida y ocurrio un error al borrar el archivo temporal.");
                                            }
                                            $controller->actualizarPathArchivo($this->getIdTramite(), ["descripcionArchivo" => "boleta_cenat"], "");
                                        }
                                    } else {
                                        //actualizar registro desde el controller
                                        $pudoSetearPath = $controller->actualizarPathArchivo($this->getIdTramite(), $params['descripcionArchivo'], $filePathLicencia);
                                    }

                                    if (isset($pudoSetearPath['body']['status']) && $pudoSetearPath['body']['status'] == "OK") {
                                        if (isset($pudoSetearPath['body']['data']['affectedRows'])) {
                                            $response = crearRespuestaSolicitud(
                                                200,
                                                "OK",
                                                "El archivo supuestamente se subió.",
                                                [
                                                    "nombreCampo" => $pudoSetearPath['body']['data']['nombreCampo'],
                                                    "fileUrl" => $filePathLicencia,
                                                    "paso" => $pudoSetearPath['body']['data']['paso']
                                                ]
                                            );
                                        } else {
                                            $response = crearRespuestaSolicitud(
                                                200,
                                                "OK",
                                                "El archivo supuestamente se subió.",
                                                [
                                                    $params['ID'] => $pudoSetearPath['body']['data'][$params['ID']],
                                                    "fileUrl" => $filePathLicencia,
                                                    "nombreCampo" => $pudoSetearPath['body']['data']['nombreCampo'],
                                                    "paso" => $pudoSetearPath['body']['data']['paso']
                                                ]
                                            );
                                        }

                                        //$response['headers'] = ['HTTP/1.1 200 OK'];
                                    } else {
                                        //No se pudo guardar el path del archivo en la tabla
                                        $response = crearRespuestaSolicitud(500, "error", $response['body']['message']);
                                        //$response['headers'] = ['HTTP/1.1 500 Internal Server Error'];
                                    }
                                } else {
                                    //No se pudo copiar el archivo en la carpeta indicada
                                    $response = crearRespuestaSolicitud(500, "error", "Ocurrio un error al intentar guardar el archivo.");
                                    //$response['headers'] = ['HTTP/1.1 500 Internal Server Error'];
                                }
                            } else {
                                //El controlador indicado no existe
                                $response = crearRespuestaSolicitud(400, "error", "Ocurrio un error al intentar guardar el archivo.");
                                //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
                            }
                        } else {
                            //No se recibieron los parámetros esperados en el body de la peticion
                            $response = crearRespuestaSolicitud(400, "error", "No se recibieron los parámetros esperados.");
                            //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
                        }
                    } else {
                        //El usuario indicado con el sessionkey no tiene un tramite activo
                        $response = crearRespuestaSolicitud(400, "error", "No existe registro de tramite activo para el usuario especificado.");
                        //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
                    }
                } else {
                    //El archivo subido tiene una extension que no esta permitida
                    $response = crearRespuestaSolicitud(400, "error", "El archivo subido tiene una extension no permitida.");
                    //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
                }
            } else {
                //no se encontro el archivo en $_FILES
                $response = crearRespuestaSolicitud(400, "error", "No se adjunto ningun archivo.");
                //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
            }
        } else {
            //No se utilizo el metodo HTTP correcto
            $response = crearRespuestaSolicitud(400, "error", "Metodo HTTP equivocado");
            //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
        }
        return $response;
    }

    private function obtenerArchivo($params)
    {
        if ($this->getRequestMethod() == "GET") {

            if ($this->getIdTramite() != null) {
                if ((isset($params['ID']) && $params['ID'] != null && $params['ID'] != '') &&
                    (isset($params['descripcionArchivo']) && $params['descripcionArchivo'] != null && $params['descripcionArchivo'] != '')
                ) {
                    $objService = new FilesService();
                    $service = $objService->obtenerServiceGetArchivo($params['ID']);

                    if ($service != null) {
                        $filePath = $service->obtenerUrlArchivo($this->getIdTramite(), $params['descripcionArchivo']);

                        if ($filePath != null || $filePath != "") {


                            //print_r($filePath);
                            // Procedemos a generar el nombre del archivo sacando los guiones bajos
                            $nombreArchivo = str_replace('_', ' ', $params['descripcionArchivo']);

                            // Obtenemos la extension del archivo a partir de la ruta del mismo
                            $path = $filePath['url'];
                            $fileExtension = pathinfo($path, PATHINFO_EXTENSION);

                            // Definimos el tipo de archivo
                            if ($fileExtension == "pdf") {
                                $fileMimeType = "application/" . $fileExtension;
                            } else {
                                $fileMimeType = "image/" . $fileExtension;
                            }

                            // Obtenemos el archivo y lo convertimos a base64
                            $fileData = file_get_contents($path);
                            $base64File = "data:$fileMimeType;base64," . base64_encode($fileData);

                            // Retornamos la respuesta adecuada, especificando el nombre del archivo, su tipo y el archivo en base64
                            $response = crearRespuestaSolicitud(
                                200,
                                "OK",
                                "Archivo recuperado satisfactoriamente.",
                                [
                                    "fileName" => $nombreArchivo,
                                    "fileType" => $fileMimeType,
                                    "base64File" => $base64File
                                ]
                            );
                            $response['headers'] = ['HTTP/1.1 200 OK'];
                            $response['headers'] = ['Content-type: application/json'];
                        } else {
                            $response = crearRespuestaSolicitud(400, "error", "No hay registros del archivo.");
                        }
                    } else {
                        // El controlador indicado no existe
                        $response = crearRespuestaSolicitud(400, "error", "El modulo solicitado no existe.");
                        //$response['headers'] = ['HTTP/1.1 400 Internal Server Error'];
                    }
                } else {
                    //No se recibieron los parámetros esperados en el body de la peticion
                    $response = crearRespuestaSolicitud(400, "error", "No se recibieron los parámetros esperados.");
                    //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
                }
            } else {
                //El usuario indicado con el sessionkey no tiene un tramite activo
                $response = crearRespuestaSolicitud(400, "error", "No existe registro de tramite activo para el usuario especificado.");
                //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
            }
        } else {
            //No se utilizo el metodo HTTP correcto
            $response = crearRespuestaSolicitud(400, "error", "Metodo HTTP equivocado");
            //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
        }
        return $response;
    }

    private function obtenerArchivoAdministrador($params)
    {
        if ($this->getRequestMethod() == "GET") {
            if ($this->getPerfilUsuario() == 3) {
                if ((isset($params['ID']) && $params['ID'] != null && $params['ID'] != '') &&
                    (isset($params['idTramite']) && $params['idTramite'] != null && $params['idTramite'] != '') &&
                    (isset($params['descripcionArchivo']) && $params['descripcionArchivo'] != null && $params['descripcionArchivo'] != '')
                ) {
                    $objService = new FilesService();
                    $service = $objService->obtenerServiceGetArchivo($params['ID']);

                    if ($service != null) {
                        $filePath = $service->obtenerUrlArchivo($params['idTramite'], $params['descripcionArchivo']);
                        //print_r($filePath);
                        // Procedemos a generar el nombre del archivo sacando los guiones bajos
                        $nombreArchivo = str_replace('_', ' ', $params['descripcionArchivo']);

                        // Obtenemos la extension del archivo a partir de la ruta del mismo
                        $path = $filePath['url'];
                        $fileExtension = pathinfo($path, PATHINFO_EXTENSION);

                        // Definimos el tipo de archivo
                        if ($fileExtension == "pdf") {
                            $fileMimeType = "application/" . $fileExtension;
                        } else {
                            $fileMimeType = "image/" . $fileExtension;
                        }

                        // Obtenemos el archivo y lo convertimos a base64
                        $fileData = file_get_contents($path);
                        $base64File = "data:$fileMimeType;base64," . base64_encode($fileData);

                        // Retornamos la respuesta adecuada, especificando el nombre del archivo, su tipo y el archivo en base64
                        $response = crearRespuestaSolicitud(
                            200,
                            "OK",
                            "Archivo recuperado satisfactoriamente.",
                            [
                                "fileName" => $nombreArchivo,
                                "fileType" => $fileMimeType,
                                "base64File" => $base64File
                            ]
                        );
                        $response['headers'] = ['HTTP/1.1 200 OK'];
                        $response['headers'] = ['Content-type: application/json'];
                    } else {
                        // El controlador indicado no existe
                        $response = crearRespuestaSolicitud(400, "error", "El modulo solicitado no existe.");
                        //$response['headers'] = ['HTTP/1.1 400 Internal Server Error'];
                    }
                } else {
                    //No se recibieron los parámetros esperados en el body de la peticion
                    $response = crearRespuestaSolicitud(400, "error", "No se recibieron los parámetros esperados.");
                    //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
                }
            } else {
                //El usuario indicado con el sessionkey no tiene un tramite activo
                $response = crearRespuestaSolicitud(401, "error", "No tiene permisos para ejecutar la consulta.");
                //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
            }
        } else {
            //No se utilizo el metodo HTTP correcto
            $response = crearRespuestaSolicitud(400, "error", "Metodo HTTP equivocado");
            //$response['headers'] = ['HTTP/1.1 400 Bad Request'];
        }
        return $response;
    }
}
