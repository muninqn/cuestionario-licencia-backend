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
        $sqlQuery = "UPDATE FDPyR_modulo SET cuestionario_id=?, adjunto_id=?, orden=?, titulo=?, descripcion=?, puntaje=? modified_at = CURRENT_TIMESTAMP WHERE id_modulo=? AND deleted_at IS NULL";
        $bindParams = [
            $params['cuestionario_id'],
            $params['adjunto_id'],
            $params['orden'],
            $params['titulo'],
            $params['descripcion'],
            $params['puntaje'],
            $params['id_modulo']
        ];

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
