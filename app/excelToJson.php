<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require 'vendor/autoload.php';


$path = '/home/majid/Projects/location-service/.docker-compose/web-server/feed/excel/';

$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$inputFileType = 'Excel2007';
$inputFileName = 'Mabdaa.xlsx';


$objReader = PHPExcel_IOFactory::createReader($inputFileType);

$objPHPExcel = $objReader->load($path.$inputFileName);


$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
$count = count($sheetData);
$result = [];


for ($i = 2; $i < $count; $i++){

    if(
        !empty($sheetData[$i]['H']) &&
        !empty($sheetData[$i]['I']) &&
        !empty($sheetData[$i]['J']) &&
        !empty($sheetData[$i]['K']) &&
        !empty($sheetData[$i]['C'])

    ){



        $result[] = [
            'id' => $i - 1,
            'type' => 'intersection',
            'preview' => rtrim(ltrim($sheetData[$i]['I'] )),
            'location' => [
                'lat' => (float) explode(',', $sheetData[$i]['H'])[0],
                'lon' => (float) explode(',', $sheetData[$i]['H'])[1]
            ],
            'district' => $sheetData[$i]['B'],
            'Area' => $sheetData[$i]['C'],
            'first' => [
                'type' => $sheetData[$i]['D'],
                'names' => [
                    'name' => rtrim(ltrim($sheetData[$i]['E'] )),
                    'slug' => 'empty'
                ]
            ],
            'second' => [
                'type' => $sheetData[$i]['F'],
                'names' => [
                    'name' => rtrim(ltrim($sheetData[$i]['G'] )),
                    'slug' => 'empty'
                ]
            ],
            'search' => implode(',', [ $sheetData[$i]['J'], $sheetData[$i]['K']  ]),
        ];



    }




}

$filename = $path.'newIntersections.json';


$text = json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
$file = fopen( $filename, 'w');
fwrite($file, $text);
