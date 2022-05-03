<?php
require_once("../app/modulo/ModuloService.php");
require_once("../app/pregunta/PreguntaService.php");
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
        $sqlQuery = "UPDATE FDPyR_cuestionario SET";
        if (isset($params['adjunto_id'])) {
            $sqlQuery .= " adjunto_id = ?,";
            array_push($bindParams, $params['adjunto_id']);
        }
        if (isset($params['proyecto'])) {
            $sqlQuery .= " proyecto = ?,";
            array_push($bindParams, $params['proyecto']);
        }
        if (isset($params['categoria'])) {
            $sqlQuery .= " categoria = ?,";
            array_push($bindParams, $params['categoria']);
        }
        if (isset($params['titulo'])) {
            $sqlQuery .= " titulo = ?,";
            array_push($bindParams, $params['titulo']);
        }
        if (isset($params['descripcion'])) {
            $sqlQuery .= " descripcion = ?,";
            array_push($bindParams, $params['descripcion']);
        }
        array_push($bindParams, $params['id_cuestionario']);
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

    public function listarDatosCuestionario($params)
    {
        $sqlQuery = "SELECT id_cuestionario, proyecto, categoria, titulo, descripcion, adjunto_id, tipo_adjunto, path_adjunto, tipo_archivo FROM FDPyR_cuestionario 
        LEFT JOIN FDPyR_adjunto ON FDPyR_cuestionario.adjunto_id = FDPyR_adjunto.id_adjunto
        WHERE id_cuestionario=? AND FDPyR_cuestionario.deleted_at IS NULL";

        $bindParams = [$params['id_cuestionario']];

        // $response = [];

        $database = new BaseDatos();
        $database->connect();

        if ($sqlStatement = odbc_prepare($database->getConn(), $sqlQuery)) {
            if ($pudoEjecutar = odbc_execute($sqlStatement, $bindParams)) {
                while ($unaTupla = odbc_fetch_array($sqlStatement)) {
                    // $unaTupla["Nombre"] = htmlspecialchars(iconv("iso-8859-1", "utf-8", $unaTupla["Nombre"]));
                    // $unaTupla["DomicilioReal"] = htmlspecialchars(iconv("iso-8859-1", "utf-8", $unaTupla["DomicilioReal"]));
                    // $unaTupla["DomicilioLegal"] = htmlspecialchars(iconv("iso-8859-1", "utf-8", $unaTupla["DomicilioLegal"]));
                    // array_push($response, $unaTupla);
                    $response = $unaTupla;
                }
            }
        };
        $objServiceModulo = new ModuloService();
        $objServicePregunta = new PreguntaService();
        $response['Modulo'] = $objServiceModulo->listarModuloCuestionario($params);
        foreach ($response['Modulo'] as $key => $value) {
            // var_dump($response['Modulo'][$key]);
            $params['modulo_id']=$value['id_modulo'];
            // array_push($response['Modulo'][$key],$objServicePregunta->listarPreguntasModulo($params));
            $response['Modulo'][$key]['Preguntas']=$objServicePregunta->listarPreguntasModulo($params);
        }
        return $response;
    }
}
