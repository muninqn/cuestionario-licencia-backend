<?php
class RespuestaService
{
    public function insertRespuesta($params)
    {
        $sqlQuery = "INSERT INTO FDPyR_respuesta (usuario_id, pregunta_id, adjunto_id, opcion_seleccionada_id, respuesta, puntaje) VALUES(?,?,?,?,?,?)";
        $bindParams = [
            $params['usuario_id'],
            $params['pregunta_id'],
            $params['adjunto_id'],
            $params['opcion_seleccionada_id'],
            $params['respuesta'],
            $params['puntaje'],
        ];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlInsert($sqlQuery, $bindParams);
    }

    public function updateRespuesta($params)
    {
        $bindParams = [];
        $sqlQuery = "UPDATE FDPyR_respuesta SET" ;
        if (isset($params['usuario_id'])) {
            $sqlQuery .= " usuario_id = ?,";
            array_push($bindParams,$params['usuario_id']);
        }
        if (isset($params['pregunta_id'])) {
            $sqlQuery .= " pregunta_id = ?,";
            array_push($bindParams,$params['pregunta_id']);
        }
        if (isset($params['adjunto_id'])) {
            $sqlQuery .= " adjunto_id = ?,";
            array_push($bindParams,$params['adjunto_id']);
        }
        if (isset($params['opcion_seleccionada_id'])) {
            $sqlQuery .= " opcion_seleccionada_id = ?,";
            array_push($bindParams,$params['opcion_seleccionada_id']);
        }
        if (isset($params['respuesta'])) {
            $sqlQuery .= " respuesta = ?,";
            array_push($bindParams,$params['respuesta']);
        }
        if (isset($params['puntaje'])) {
            $sqlQuery .= " puntaje = ?,";
            array_push($bindParams,$params['puntaje']);
        }
        array_push($bindParams,$params['id_respuesta']);
        $sqlQuery .= " modified_at = CURRENT_TIMESTAMP WHERE id_respuesta=? AND deleted_at IS NULL";

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlUpdateDelete($sqlQuery, $bindParams);
    }
    public function deleteRespuesta($params)
    {
        $sqlQuery = "UPDATE FDPyR_respuesta SET deleted_at = CURRENT_TIMESTAMP WHERE id_respuesta=? AND deleted_at IS NULL";
        $bindParams = [
            $params['id_respuesta']
        ];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlUpdateDelete($sqlQuery, $bindParams);
    }

    public function listarRespuestaUsuario($params)
    {
        $sqlQuery = "SELECT * FROM FDPyR_respuesta 
        LEFT JOIN FDPyR_adjunto ON FDPyR_respuesta.adjunto_id = FDPyR_adjunto.id_adjunto
        WHERE usuario_id=? AND FDPyR_respuesta.deleted_at IS NULL";

        $bindParams = [$params['usuario_id']];

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
        return $response;
    }
}
