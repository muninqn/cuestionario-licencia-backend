<?php
class TipoPreguntaService
{
    public function insertTipoPregunta($params)
    {
        $sqlQuery = "INSERT INTO FDPyR_tipo_pregunta (tipo_pregunta, descripcion) VALUES(?,?)";
        $bindParams = [$params['tipo_pregunta'], $params['descripcion']];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlInsert($sqlQuery, $bindParams);
    }

    public function updateTipoPregunta($params)
    {
        $sqlQuery = "UPDATE licencia_cenat SET tipo_pregunta = ?, descripcion = ?, modified_at = CURRENT_TIMESTAMP WHERE id_tipo_pregunta=? AND deleted_at IS NULL";
        $bindParams = [$params['tipo_pregunta'],$params['descripcion'],$params['id_tipo_pregunta']];

        $database = new BaseDatos;
        $database->connect();
        return $database->ejecutarSqlUpdateDelete($sqlQuery, $bindParams);
    }
}
