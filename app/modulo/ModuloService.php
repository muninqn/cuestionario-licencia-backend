<?php
class ModuloService
{
    public function insertModulo($params)
    {
        $sqlQuery = "INSERT INTO FDPyR_modulo (cuestionario_id, adjunto_id, orden, titulo, descripcion, puntaje) VALUES(?,?,?,?,?,?)";
        $bindParams = [
            $params['cuestionario_id'],
            $params['adjunto_id'],
            $params['orden'],
            $params['titulo'],
            $params['descripcion'],
            $params['puntaje'],
        ];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlInsert($sqlQuery, $bindParams);
    }

    public function updateModulo($params)
    {
        $bindParams = [];
        $sqlQuery = "UPDATE FDPyR_modulo SET" ;
        if (isset($params['cuestionario_id'])) {
            $sqlQuery .= " cuestionario_id = ?,";
            array_push($bindParams,$params['cuestionario_id']);
        }
        if (isset($params['adjunto_id'])) {
            $sqlQuery .= " adjunto_id = ?,";
            array_push($bindParams,$params['adjunto_id']);
        }
        if (isset($params['orden'])) {
            $sqlQuery .= " orden = ?,";
            array_push($bindParams,$params['orden']);
        }
        if (isset($params['titulo'])) {
            $sqlQuery .= " titulo = ?,";
            array_push($bindParams,$params['titulo']);
        }
        if (isset($params['descripcion'])) {
            $sqlQuery .= " descripcion = ?,";
            array_push($bindParams,$params['descripcion']);
        }
        if (isset($params['puntaje'])) {
            $sqlQuery .= " puntaje = ?,";
            array_push($bindParams,$params['puntaje']);
        }
        array_push($bindParams,$params['id_modulo']);
        $sqlQuery .= " modified_at = CURRENT_TIMESTAMP WHERE id_modulo=? AND deleted_at IS NULL";

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlUpdateDelete($sqlQuery, $bindParams);
    }
    public function deleteModulo($params)
    {
        $sqlQuery = "UPDATE FDPyR_modulo SET deleted_at = CURRENT_TIMESTAMP WHERE id_modulo=? AND deleted_at IS NULL";
        $bindParams = [
            $params['id_modulo']
        ];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlUpdateDelete($sqlQuery, $bindParams);
    }
}
