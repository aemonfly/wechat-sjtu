<?php

/**
 * Calculates the great-circle distance between two points, with
 * the Haversine formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
function haversineGreatCircleDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;

  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  return $angle * $earthRadius;
}

function nearestStation($x, $y) {
  $positions = array(
    "研究生服务中心 " => array(31.03013,121.42846),
    "图书信息楼 " => array(31.02738,121.43274),
    "东中院 " => array(31.02581,121.43330),
    "东上院 " => array(31.02367,121.43423),
    "行政B楼 " => array(31.02845,121.43613),
    "电信学院" => array(31.02757,121.43730),
    "南大门" => array(31.02517,121.44149),
    "机动学院" => array(31.02840,121.44350),
    "东大门" => array(31.03078,121.44234),
    "船建学院" => array(31.03237,121.44063),
    "文选医学楼" => array(31.03288,121.43751),
    "学生公寓(西35-70)" => array(31.02865,121.42392),
    "第四餐饮大楼" => array(31.02704,121.42207),
    "华联生活中心" => array(31.02657,121.42574),
    "包玉刚图书馆" => array(31.02474,121.42621),
    "材料学院" => array(31.02356,121.42339),
    "菁菁堂广场" => array(31.02056,121.42574),
    "校医院" => array(31.02139,121.42862)
  );
  $m_val = 100000;
  $m_station = "";
  foreach ($positions as $key => $value) {
    $d = haversineGreatCircleDistance($x, $y, 
      $value[0], $value[1]);
    if ($d < $m_val) {
        $m_val = $d;
        $m_station = $key;
    }
  }
  # $station = array_search( min(array_values($positions)), $positions);
  return "猜离你最近的车站是: ${m_station}";
}

?>
