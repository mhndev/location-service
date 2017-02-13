<?php

include 'vendor/autoload.php';

$jsonDir = '/home/majid/Projects/location-service/.docker-compose/web-server/feed/locations/';

$data = json_decode(file_get_contents($jsonDir.'sources.json'), true);



//intersection($data);
sources($data);

function intersection($data)
{

    $reformattedData = [];

    $reformattedData[] = [
        'id',
        'location_type',
        'lat',
        'lon',
        'preview',
        'search',
        'first_location_type',
        'first_location_names',
        'first_location_slugs',
        'second_location_type',
        'second_location_names',
        'second_location_slugs',
    ];



    foreach ($data as $record){

        $first_names = [];
        $first_slugs = [];

        $second_names = [];
        $second_slugs = [];


        foreach ($record['first']['names'] as $name){
            $first_names[] = $name['name'];
            $first_slugs[] = $name['slug'];
        }




        foreach ($record['second']['names'] as $name){
            $second_names[] = $name['name'];
            $second_slugs[] = $name['slug'];
        }


        $reformattedData[] = [
            'id'                    => $record['id'],
            'location_type'         => $record['type'],
            'lat'                   => $record['location']['lat'],
            'lon'                   => $record['location']['lon'],
            'preview'               => $record['preview'],
            'search'                => $record['search'],

            'first_location_type'   => $record['first']['type'],
            'first_location_names'  => implode(',', $first_names),
            'first_location_slugs'  => implode(',', $first_slugs),

            'second_location_type'  => $record['second']['type'],
            'second_location_names' => implode(',', $second_names),
            'second_location_slugs'  => implode(',', $second_slugs),


        ];
    }

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getActiveSheet()->fromArray($reformattedData, null, 'A1');

    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

    $excelDir = '/home/majid/Projects/location-service/.docker-compose/web-server/feed/excel/intersections.xlsx';

    $writer->save($excelDir);


}





function sources($data)
{

    $reformattedData = [];

    $reformattedData[] = [
        'id',
        'location_type',
        'lat',
        'lon',
        'preview',
        'search',
        'area',
        'first_location_type',
        'first_location_names',
        'first_location_slugs',
        'second_location_type',
        'second_location_names',
        'second_location_slugs',
    ];



    foreach ($data as $record){

        $first_names = [];
        $first_slugs = [];

        $second_names = [];
        $second_slugs = [];


        foreach ($record['first']['names'] as $name){
            $first_names[] = $name['name'];
            $first_slugs[] = $name['slug'];
        }




        foreach ($record['second']['names'] as $name){
            $second_names[] = $name['name'];
            $second_slugs[] = $name['slug'];
        }


        $reformattedData[] = [
            'id'                    => $record['id'],
            'location_type'         => $record['type'],
            'lat'                   => $record['location']['lat'],
            'lon'                   => $record['location']['lon'],
            'preview'               => $record['preview'],
            'search'                => $record['search'],
            'area'                  => $record['area'],

            'first_location_type'   => $record['first']['type'],
            'first_location_names'  => implode(',', $first_names),
            'first_location_slugs'  => implode(',', $first_slugs),

            'second_location_type'  => $record['second']['type'],
            'second_location_names' => implode(',', $second_names),
            'second_location_slugs'  => implode(',', $second_slugs),


        ];
    }

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->getActiveSheet()->fromArray($reformattedData, null, 'A1');

    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

    $excelDir = '/home/majid/Projects/location-service/.docker-compose/web-server/feed/excel/sources.xlsx';

    $writer->save($excelDir);


}
