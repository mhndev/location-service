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
     * @return array
     */
    public function geoSearch($lat, $long, $distance = 100 , $size = 10, $from = 0, $fields = []);

    /**
     * @param $query
     * @param $size
     * @param $from
     * @param array $fields
     * @return array
     */
    public function locationSearch($query, $size = 10, $from = 0, $fields = []);


    /**
     * @param $data
     */
    public function store($data);

}
