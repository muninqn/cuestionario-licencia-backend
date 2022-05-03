<?php
class OpcionService
{
    public function insertOpcion($params)
    {
        $sqlQuery = "INSERT INTO FDPyR_opcion (pregunta_id, adjunto_id, opcion, orden, puntaje, opcion_correcta) VALUES(?,?,?,?,?,?)";
        $bindParams = [
            $params['pregunta_id'],
            $params['adjunto_id'],
            $params['opcion'],
            $params['orden'],
            $params['puntaje'],
            $params['opcion_correcta'],
        ];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlInsert($sqlQuery, $bindParams);
    }

    public function updateOpcion($params)
    {

        $bindParams = [];
        $sqlQuery = "UPDATE FDPyR_opcion SET" ;
        if (isset($params['pregunta_id'])) {
            $sqlQuery .= " pregunta_id = ?,";
            array_push($bindParams,$params['pregunta_id']);
        }
        if (isset($params['adjunto_id'])) {
            $sqlQuery .= " adjunto_id = ?,";
            array_push($bindParams,$params['adjunto_id']);
        }
        if (isset($params['opcion'])) {
            $sqlQuery .= " opcion = ?,";
            array_push($bindParams,$params['opcion']);
        }
        if (isset($params['orden'])) {
            $sqlQuery .= " orden = ?,";
            array_push($bindParams,$params['orden']);
        }
        if (isset($params['puntaje'])) {
            $sqlQuery .= " puntaje = ?,";
            array_push($bindParams,$params['puntaje']);
        }
        if (isset($params['opcion_correcta'])) {
            $sqlQuery .= " opcion_correcta = ?,";
            array_push($bindParams,$params['opcion_correcta']);
        }
        array_push($bindParams,$params['id_opcion']);
        $sqlQuery .= " modified_at = CURRENT_TIMESTAMP WHERE id_opcion=? AND deleted_at IS NULL";

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlUpdateDelete($sqlQuery, $bindParams);
    }
    public function deleteOpcion($params)
    {
        $sqlQuery = "UPDATE FDPyR_opcion SET deleted_at = CURRENT_TIMESTAMP WHERE id_opcion=? AND deleted_at IS NULL";
        $bindParams = [
            $params['id_opcion']
        ];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlUpdateDelete($sqlQuery, $bindParams);
    }

    public function listarOpcionesPregunta($params)
    {
        $sqlQuery = "SELECT id_opcion, pregunta_id, opcion, orden, puntaje, opcion_correcta, adjunto_id, tipo_adjunto, path_adjunto, tipo_archivo FROM FDPyR_opcion 
        LEFT JOIN FDPyR_adjunto ON FDPyR_opcion.adjunto_id = FDPyR_adjunto.id_adjunto
        WHERE pregunta_id=? AND FDPyR_opcion.deleted_at IS NULL";

        $bindParams = [$params['pregunta_id']];

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
