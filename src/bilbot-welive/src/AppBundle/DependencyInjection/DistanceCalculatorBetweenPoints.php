<?php

namespace AppBundle\DependencyInjection;


class DistanceCalculatorBetweenPoints
{
    public function getMetersBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2) {
        if (($latitude1 == $latitude2) && ($longitude1 == $longitude2)) { return 0; }
        $p1 = deg2rad($latitude1);
        $p2 = deg2rad($latitude2);
        $dp = deg2rad($latitude2 - $latitude1);
        $dl = deg2rad($longitude2 - $longitude1);
        $a = (sin($dp/2) * sin($dp/2)) + (cos($p1) * cos($p2) * sin($dl/2) * sin($dl/2));
        $c = 2 * atan2(sqrt($a),sqrt(1-$a));
        $r = 6371008; // Earth's average radius, in meters
        $d = $r * $c;
        return $d; // distance, in meters
    }
}