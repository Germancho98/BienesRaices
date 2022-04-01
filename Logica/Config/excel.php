<?php

// Inicializacion de cabezeras
header("Content-Type: text/csv; charset-latin1");

header("Content-Disposition: attachment; filename=" . $filename);

// Apertura en memoria del documento a escribir
$output = fopen('php://output', 'w');

// Escritura de las cabezeras de los registros
fputcsv($output, array_values(['ID', 'DIRECCION', 'CODIGO', 'CIUDAD', 'TELEFONO', 'TIPO', 'PRECIO']),';', ' ');

// Escritura de los datos
foreach ($bienes as $bien) {
    fputcsv($output,array_values([
        $bien['id'],
        $bien['direccion'],
        $bien['codigo_postal'],
        $bien['ciudad'],
        $bien['telefono'],
        $bien['tipo'],
        $bien['precio']
    ]), ';', ' ');
}

?>

