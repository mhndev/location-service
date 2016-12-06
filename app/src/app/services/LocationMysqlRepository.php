<?php

namespace mhndev\locationService\services;

/**
 * Class LocationMysqlRepository
 * @package mhndev\locationService\services
 */
class LocationMysqlRepository implements iLocationRepository
{

    /**
     * @var \PDO
     */
    protected $client;


    /**
     * LocationMysqlRepository constructor.
     * @param \PDO $client
     */
    public function __construct(\PDO $client)
    {
        $this->client = $client;
    }

    /**
     * @param $lat
     * @param $long
     * @param $distance
     * @param $size
     * @param $from
     * @param array $fields
     * @return array
     */
    public function geoSearch($lat, $long, $distance = 100, $size = 10, $from = 0, $fields = [])
    {
        // TODO: Implement geoSearch() method.
    }

    /**
     * @param $q
     * @param $size
     * @param $from
     * @param array $fields
     * @return array
     */
    public function locationSearch($q, $size = 10, $from = 0, $fields = [])
    {
        $query = "SELECT * FROM locations WHERE MATCH (name, slug) AGAINST ($q)";

        $stmt = $this->client->query($query);

        return $stmt->fetchAll();
    }


    /**
     * @param $data
     */
    public function store($data)
    {
        try {
            $this->client->prepare("INSERT INTO $this->type VALUES (NULL,?,?,?,?)")->execute($data);
        } catch (\PDOException $e) {
            if ($e->getCode() == 1062) {
                // Take some action if there is a key constraint violation, i.e. duplicate name
            } else {
                throw $e;
            }
        }
    }



}
