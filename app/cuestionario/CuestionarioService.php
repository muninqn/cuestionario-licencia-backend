<?php
class CuestionarioService
{
    public function insertCuestionario($params)
    {
        $sqlQuery = "INSERT INTO FDPyR_cuestionario (adjunto_id, proyecto, categoria, titulo, descripcion) VALUES(?,?,?,?,?)";
        $bindParams = [
            $params['adjunto_id'],
            $params['proyecto'],
            $params['categoria'],
            $params['titulo'],
            $params['descripcion'],
        ];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlInsert($sqlQuery, $bindParams);
    }

    public function updateCuestionario($params)
    {
        $sqlQuery = "UPDATE FDPyR_cuestionario SET adjunto_id = ?, proyecto = ?,categoria=?,titulo=?, descripcion=?, modified_at = CURRENT_TIMESTAMP WHERE id_cuestionario=? AND deleted_at IS NULL";
        $bindParams = [
            $params['adjunto_id'],
            $params['proyecto'],
            $params['categoria'],
            $params['titulo'],
            $params['descripcion'],
            $params['id_cuestionario']
        ];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlUpdateDelete($sqlQuery, $bindParams);
    }
    public function deleteCuestionario($params)
    {
        $sqlQuery = "UPDATE FDPyR_cuestionario SET deleted_at = CURRENT_TIMESTAMP WHERE id_cuestionario=? AND deleted_at IS NULL";
        $bindParams = [
            $params['id_cuestionario']
        ];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlUpdateDelete($sqlQuery, $bindParams);
    }
}
