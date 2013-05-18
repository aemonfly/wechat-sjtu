<?php
function get_bus($now)
{

	$msg="菁菁堂发车时间\n逆时针:\n";

	$timetable1 = array('07:40','08:00','08:15','08:25','08:50','09:45','10:00','10:30','11:00','11:25','11:40','12:00','12:30','13:00','13:25','13:40','14:20','15:00','15:40','16:00','16:15','16:30','16:45','17:10','17:40','17:55','18:30','19:45','20:00');

	$timetable2=array('08:45','09:15','09:30','11:15','11:30','12:15','13:15','14:15','15:15','15:45','16:30');

	if($now=="all") {

		foreach($timetable1 as $t)
		{$msg.=$t."    ";}
		$msg.="\n顺时针：\n";
		foreach($timetable2 as $t)
		{$msg.=$t."    ";}


		return  $msg;
	}

	for($i=0;$i<count($timetable1)-1;$i++)
	{
		if(strtotime($timetable1[0])>$now) {$msg.='早上第一班:'.$timetable1[0];break;}
		if((strtotime($timetable1[$i+1])>$now)&&(strtotime($timetable1[$i])<$now))
		{$msg.= "上一班：".$timetable1[$i]."\n后一班：".$timetable1[$i+1];break;}

	}

	if($i==count($timetable1)-1)
		$msg= "没车了！";

	$msg.="\n----\n顺时针:\n";

	for($i=0;$i<count($timetable2)-1;$i++)
	{
		if(strtotime($timetable2[0])>$now) {$msg.='早上第一班:'.$timetable2[0];break;}
		if((strtotime($timetable2[$i+1])>$now)&&(strtotime($timetable2[$i])<$now))
		{$msg.= "上一班：".$timetable2[$i]."\n后一班：".$timetable2[$i+1];break;}

	}

	if($i==count($timetable2)-1)
		$msg.= "今天没车了！";  


	return $msg;
}

#$now=time();
#echo get_bus($now);

?>
