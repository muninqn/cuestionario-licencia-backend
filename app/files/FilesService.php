<?php



class FilesService
{

    public function obtenerControllerSubirArchivo($idSeccion)
    {
        switch (strtolower($idSeccion)) {
            case "autorizacion":
                $controller = new AutorizacionController();
                break;
            case "aptomedico":
                $controller = new AptoMedicoController();
                break;
            case "cursos":
                $controller = new CursosController();
                break;
            case "cenat":
                $controller = new CenatController();
                break;
            case "vehiculo":
                $controller = new VehiculoController();
                break;
            case "tasamunicipal":
                //$controller = new TasaMunicipalController();
                break;
            default:
                $controller = null;
                break;
        }
        return $controller;
    }

    public function obtenerServiceGetArchivo($idSeccion)
    {
        switch (strtolower($idSeccion)) {
            case "autorizacion":
                $service = new AutorizacionService();
                break;
            case "aptomedico":
                $service = new AptoMedicoService();
                break;
            case "cursos":
                $service = new CursosService();
                break;
            case "cenat":
                $service = new CenatService();
                break;
            case "vehiculo":
                $service = new VehiculoService();
                break;
            case "tasamunicipal":
                //$service = new TasaMunicipalService();
                break;
            default:
                $service = null;
                break;
        }
        return $service;
    }

    public function subirArchivoServidor($tempFile, $fileType, $fileSize, $destinationFilepath)
    {
        if ($fileSize < 1000000) {
            //el peso del archivo no es problema
            $response = copy($tempFile, $destinationFilepath);
        } else {
            //el archivo pesa demasiado, hay que comprimirlo jaja
            if (str_contains($fileType, "image/")) {
                $response = $this->comprimirYSubirArchivoImagen($tempFile, $fileType, $destinationFilepath);
            } elseif (str_contains($fileType, "application/pdf")) {
                //$response = $this->comprimirArchivoPDF($tempFile, $fileType, $destinationFilepath);
                $response = copy($tempFile, $destinationFilepath);
            } else {
                $response = false;
            }
        }
        return $response;
    }

    public function obtenerExtensionArchivo(){

    }

    private function comprimirYSubirArchivoImagen($tempFile, $fileType, $destinationFilepath)
    {
        if ($fileType == 'image/jpg' || $fileType == 'image/jpeg') {
            $image = imagecreatefromjpeg($tempFile);
        } elseif ($fileType == 'image/png') {
            $image = imagecreatefrompng($tempFile);
        } elseif ($fileType == 'image/bmp') {
            $image = imagecreatefrombmp($tempFile);
        }

        return imagejpeg($image, $destinationFilepath, 55);
    }

    private function comprimirArchivoPDF($tempFile, $fileType, $destinationFilepath)
    {
        if ($fileType == 'image/jpeg') {
            $image = imagecreatefromjpeg($tempFile);
        } elseif ($fileType == 'image/gif') {
            $image = imagecreatefromgif($tempFile);
        } elseif ($fileType == 'image/png') {
            $image = imagecreatefrompng($tempFile);
        }

        return imagejpeg($image, $destinationFilepath, 60);
    }
}
