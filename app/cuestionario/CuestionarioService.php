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
        $bindParams = [];
        $sqlQuery = "UPDATE FDPyR_cuestionario SET" ;
        if (isset($params['adjunto_id'])) {
            $sqlQuery .= " adjunto_id = ?,";
            array_push($bindParams,$params['adjunto_id']);
        }
        if (isset($params['proyecto'])) {
            $sqlQuery .= " proyecto = ?,";
            array_push($bindParams,$params['proyecto']);
        }
        if (isset($params['categoria'])) {
            $sqlQuery .= " categoria = ?,";
            array_push($bindParams,$params['categoria']);
        }
        if (isset($params['titulo'])) {
            $sqlQuery .= " titulo = ?,";
            array_push($bindParams,$params['titulo']);
        }
        if (isset($params['descripcion'])) {
            $sqlQuery .= " descripcion = ?,";
            array_push($bindParams,$params['descripcion']);
        }
        array_push($bindParams,$params['id_cuestionario']);
        $sqlQuery .= " modified_at = CURRENT_TIMESTAMP WHERE id_cuestionario=? AND deleted_at IS NULL";
       
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
