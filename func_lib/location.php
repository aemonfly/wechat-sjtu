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

function nearestStation($x, $y,$time_array) {
	if($time_array[0]==-1&&$time_array[1]==-1)  return "今天没车了";
	static $positions = array(
			"研究生服务中心 " => array(31.03013,121.42846,9,15),
			"图书信息楼 " => array(31.02738,121.43274,18,5),
			"东中院 " => array(31.02581,121.43330,19,4),
			"东上院 " => array(31.02367,121.43423,20,3),
			"行政B楼 " => array(31.02845,121.43613,17,6),
			"电信学院" => array(31.02757,121.43730,16,7),
			"南大门" => array(31.02517,121.44149,15,9),
			"机动学院" => array(31.02840,121.44350,14,10),
			"东大门" => array(31.03078,121.44234,13,11),
			"船建学院" => array(31.03237,121.44063,12,12),
			"文选医学楼" => array(31.03288,121.43751,11,13),
			"学生公寓(西35-70)" => array(31.02865,121.42392,8,16),
			"第四餐饮大楼" => array(31.02704,121.42207,7,17),
			"华联生活中心" => array(31.02657,121.42574,5,18),
			"包玉刚图书馆" => array(31.02474,121.42621,4,19),
			"材料学院" => array(31.02356,121.42339,2,20),
			"菁菁堂广场" => array(31.02056,121.42574,0,0),
			"校医院" => array(31.02139,121.42862,21,1)
			);
	static $offset_x = 0.002034666666666851;
	static $offset_y = -0.004671000000001868;
	$p = $positions;
	foreach ($positions as $key => $value) {
		$positions[$key] = haversineGreatCircleDistance($x+$offset_x, $y+$offset_y, 
				$value[0], $value[1]);
	}
	asort($positions);
	$Nearest_K_Stop='';
	$k=3;
	if($time_array[1]==-1) {
		foreach (array_slice($positions,0,$k) as $key => $v){
			{
				$Nearest_K_Stop.=$key."\n(逆)".(date('G:i',($time_array[0]+($p[$key][3])*60)))."\n";
			}
		}
		$Nearest_K_Stop.="\n顺时针没车了。";
	}
	else {
		foreach (array_slice($positions,0,$k) as $key => $v) {
			{
				$Nearest_K_Stop .= $key."\n(顺)".(date('G:i',($time_array[1]+($p[$key][2])*60)))
					."\n(逆)".(date('G:i',($time_array[0]+($p[$key][3])*60)))."\n";
			}
		}
	}
	return "你附近的车站及其下一班预计到达时间:\n ${Nearest_K_Stop}";
}
?>
