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
        $sqlQuery = "UPDATE FDPyR_modulo SET";
        if (isset($params['cuestionario_id'])) {
            $sqlQuery .= " cuestionario_id = ?,";
            array_push($bindParams, $params['cuestionario_id']);
        }
        if (isset($params['adjunto_id'])) {
            $sqlQuery .= " adjunto_id = ?,";
            array_push($bindParams, $params['adjunto_id']);
        }
        if (isset($params['orden'])) {
            $sqlQuery .= " orden = ?,";
            array_push($bindParams, $params['orden']);
        }
        if (isset($params['titulo'])) {
            $sqlQuery .= " titulo = ?,";
            array_push($bindParams, $params['titulo']);
        }
        if (isset($params['descripcion'])) {
            $sqlQuery .= " descripcion = ?,";
            array_push($bindParams, $params['descripcion']);
        }
        if (isset($params['puntaje'])) {
            $sqlQuery .= " puntaje = ?,";
            array_push($bindParams, $params['puntaje']);
        }
        array_push($bindParams, $params['id_modulo']);
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

    public function listarModuloCuestionario($params)
    {
        $sqlQuery = "SELECT id_modulo, cuestionario_id, titulo, descripcion, orden, puntaje, adjunto_id, tipo_adjunto, path_adjunto, tipo_archivo FROM FDPyR_modulo 
        LEFT JOIN FDPyR_adjunto ON FDPyR_modulo.adjunto_id = FDPyR_adjunto.id_adjunto
        WHERE cuestionario_id=? AND FDPyR_modulo.deleted_at IS NULL";

        $bindParams = [$params['id_cuestionario']];

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
