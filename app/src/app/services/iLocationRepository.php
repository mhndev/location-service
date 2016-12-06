<?php
namespace mhndev\locationService\services;

/**
 * interface iLocationRepository
 * @package mhndev\locationService
 */
interface  iLocationRepository
{


    /**
     * @param $lat
     * @param $long
     * @param $distance
     * @param $size
     * @param $from
     * @param array $fields
     * @param $index
     * @return array
     */
    public function geoSearch($index, $lat, $long, $distance = 100 , $size = 10, $from = 0, $fields = []);

    /**
     * @param $query
     * @param $size
     * @param $from
     * @param $index
     * @param array $fields
     * @return array
     */
    public function locationSearch($index, $query, $size = 10, $from = 0, $fields = []);


    /**
     * @param $indexName
     * @param $type
     * @param $data
     */
    public function store($indexName, $type, $data);

}
