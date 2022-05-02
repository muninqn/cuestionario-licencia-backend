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
}
