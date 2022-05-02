<?php
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
}
