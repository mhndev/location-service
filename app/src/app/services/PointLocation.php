<?php
namespace mhndev\locationService\services;


/**
 * Class pointLocation
 * @package mhndev\locationService\services
 */
class PointLocation
{

    /**
     * @var bool
     */
    var $pointOnVertex = true; // Check if the point sits exactly on one of the vertices?

    /**
     * pointLocation constructor.
     */
    function pointLocation()
    {

    }

    /**
     * @param $point
     * @param $polygon
     * @param bool $pointOnVertex
     * @return bool
     */
    function pointInPolygon($point, $polygon, $pointOnVertex = true)
    {
        $this->pointOnVertex = $pointOnVertex;

        // Transform string coordinates into arrays with x and y values
        $point = $this->pointStringToCoordinates($point);
        $vertices = array();
        foreach ($polygon as $vertex) {
            $vertices[] = $this->pointStringToCoordinates($vertex);
        }

        // Check if the point sits exactly on a vertex
        if ($this->pointOnVertex == true and $this->pointOnVertex($point, $vertices) == true) {
            return "vertex";
        }

        // Check if the point is inside the polygon or on the boundary
        $intersections = 0;
        $vertices_count = count($vertices);

        for ($i=1; $i < $vertices_count; $i++) {
            $vertex1 = $vertices[$i-1];
            $vertex2 = $vertices[$i];
            if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x'])) { // Check if point is on an horizontal polygon boundary
                return "boundary";
            }
            if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y']) {
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                if ($xinters == $point['x']) { // Check if point is on the polygon boundary (other than horizontal)
                    return "boundary";
                }
                if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters) {
                    $intersections++;
                }
            }
        }
        // If the number of edges we passed through is odd, then it's in the polygon.
        if ($intersections % 2 != 0) {
            return 1;
        } else {
            return 0;
        }
    }


    /**
     * @param $latitude
     * @param $longitude
     * @return bool
     */
    function isInTehran($latitude, $longitude)
    {
        $polygonPath = ROOT.DIRECTORY_SEPARATOR.
            'src'.
            DIRECTORY_SEPARATOR.
            'app'.
            DIRECTORY_SEPARATOR.
            'geojson'.
            DIRECTORY_SEPARATOR.
            'tehran.json';


        $jsonFileHandle = file_get_contents($polygonPath);
        $polygonArray = json_decode($jsonFileHandle, true);

        $polygon = [];

        foreach ($polygonArray['features'][0]['geometry']['coordinates'][0][0] as $point){
            $polygon[] = $point[1].' '.$point[0];
        }

        $point = $latitude. ' '. $longitude;
        $result = $this->pointInPolygon($point, $polygon );

        return $result;
    }


    /**
     * @param $latitude
     * @param $longitude
     * @return bool
     */
    function isInSource($latitude, $longitude)
    {
        $polygonPath = ROOT.DIRECTORY_SEPARATOR.
            'src'.
            DIRECTORY_SEPARATOR.
            'app'.
            DIRECTORY_SEPARATOR.
            'geojson'.
            DIRECTORY_SEPARATOR.
            'source.json';


        $jsonFileHandle = file_get_contents($polygonPath);
        $polygonArray = json_decode($jsonFileHandle, true);

        $polygon = [];

        foreach ($polygonArray as $point){
            $polygon[] = $point[1].' '.$point[0];
        }

        $point = $latitude. ' '. $longitude;
        $result = $this->pointInPolygon($point, $polygon );

        return $result;
    }


    /**
     * @param $point
     * @param $vertices
     * @return bool
     */
    function pointOnVertex($point, $vertices)
    {
        foreach($vertices as $vertex) {
            if ($point == $vertex) {
                return true;
            }
        }

    }


    /**
     * @param $pointString
     * @return array
     */
    function pointStringToCoordinates($pointString)
    {
        $coordinates = explode(" ", $pointString);
        return array("x" => $coordinates[0], "y" => $coordinates[1]);
    }

}
