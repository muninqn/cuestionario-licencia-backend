<?php
require_once("../app/opcion/OpcionService.php");

class PreguntaService
{
    public function insertPregunta($params)
    {
        $sqlQuery = "INSERT INTO FDPyR_pregunta (modulo_id, tipo_pregunta_id, adjunto_id, pregunta, orden, puntaje) VALUES(?,?,?,?,?,?)";
        $bindParams = [
            $params['modulo_id'],
            $params['tipo_pregunta_id'],
            $params['adjunto_id'],
            $params['pregunta'],
            $params['orden'],
            $params['puntaje'],
        ];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlInsert($sqlQuery, $bindParams);
    }

    public function updatePregunta($params)
    {
        $bindParams = [];
        $sqlQuery = "UPDATE FDPyR_pregunta SET" ;
        if (isset($params['modulo_id'])) {
            $sqlQuery .= " modulo_id = ?,";
            array_push($bindParams,$params['modulo_id']);
        }
        if (isset($params['tipo_pregunta_id'])) {
            $sqlQuery .= " tipo_pregunta_id = ?,";
            array_push($bindParams,$params['tipo_pregunta_id']);
        }
        if (isset($params['adjunto_id'])) {
            $sqlQuery .= " adjunto_id = ?,";
            array_push($bindParams,$params['adjunto_id']);
        }
        if (isset($params['pregunta'])) {
            $sqlQuery .= " pregunta = ?,";
            array_push($bindParams,$params['pregunta']);
        }
        if (isset($params['orden'])) {
            $sqlQuery .= " orden = ?,";
            array_push($bindParams,$params['orden']);
        }
        if (isset($params['puntaje'])) {
            $sqlQuery .= " puntaje = ?,";
            array_push($bindParams,$params['puntaje']);
        }
        array_push($bindParams,$params['id_pregunta']);
        $sqlQuery .= " modified_at = CURRENT_TIMESTAMP WHERE id_pregunta=? AND deleted_at IS NULL";

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlUpdateDelete($sqlQuery, $bindParams);
    }
    public function deletePregunta($params)
    {
        $sqlQuery = "UPDATE FDPyR_pregunta SET deleted_at = CURRENT_TIMESTAMP WHERE id_pregunta=? AND deleted_at IS NULL";
        $bindParams = [
            $params['id_pregunta']
        ];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlUpdateDelete($sqlQuery, $bindParams);
    }

    public function listarPreguntasModulo($params)
    {
        $sqlQuery = "SELECT id_pregunta, modulo_id, pregunta, orden, puntaje, tipo_pregunta_id, tipo_pregunta, descripcion, adjunto_id, tipo_adjunto, path_adjunto, tipo_archivo FROM FDPyR_pregunta 
        LEFT JOIN FDPyR_tipo_pregunta ON FDPyR_pregunta.tipo_pregunta_id = FDPyR_tipo_pregunta.id_tipo_pregunta
        LEFT JOIN FDPyR_adjunto ON FDPyR_pregunta.adjunto_id = FDPyR_adjunto.id_adjunto
        WHERE modulo_id=? AND FDPyR_pregunta.deleted_at IS NULL";

        $bindParams = [$params['modulo_id']];

        $response = [];

        $database = new BaseDatos();
        $database->connect();

        if ($sqlStatement = odbc_prepare($database->getConn(), $sqlQuery)) {
            if ($pudoEjecutar = odbc_execute($sqlStatement, $bindParams)) {
                while ($unaTupla = odbc_fetch_array($sqlStatement)) {
                    // $unaTupla["Nombre"] = htmlspecialchars(iconv("iso-8859-1", "utf-8", $unaTupla["Nombre"]));
                    // $unaTupla["DomicilioReal"] = htmlspecialchars(iconv("iso-8859-1", "utf-8", $unaTupla["DomicilioReal"]));
                    // $unaTupla["DomicilioLegal"] = htmlspecialchars(iconv("iso-8859-1", "utf-8", $unaTupla["DomicilioLegal"]));
                    array_push($response, $unaTupla);
                    // $response = $unaTupla;
                }
            }
        };
        $objServiceOpciones = new OpcionService();
        foreach ($response as $key => $value) {
            $params['pregunta_id']=$value['id_pregunta'];
            $response[$key]['Opciones'] = $objServiceOpciones->listarOpcionesPregunta($params);
            // var_dump($response['Modulo'][$key]);
            
            // array_push($response['Modulo'][$key],$objServicePregunta->listarPreguntasModulo($params));
            // $response['Modulo'][$key]['Preguntas']=$objServicePregunta->listarPreguntasModulo($params);
        }
        return $response;
    }
}
