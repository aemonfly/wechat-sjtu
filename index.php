<?php
# require("/home/lx/public_html/func_lib/twitter_Search.php");
require("/home/lx/public_html/func_lib/get_notice.php");
require("/home/lx/public_html/func_lib/get_bus.php");
require("/home/lx/public_html/func_lib/location.php");
require("/home/lx/public_html/func_lib/get_zhihudaily.php");
require("/home/lx/public_html/func_lib/get_face.php");
function img_template($user,$server,$time) {
	return "<xml>
			<ToUserName><![CDATA[$user]]></ToUserName>
			<FromUserName><![CDATA[$server]]></FromUserName>
			<CreateTime>$time</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<ArticleCount>1</ArticleCount>
			<Articles>
			<item>
			<Title><![CDATA[%s]]></Title> 
			<Description><![CDATA[%s]]></Description>
			<PicUrl><![CDATA[%s]]></PicUrl>
			<Url><![CDATA[%s]]></Url>
			</item>
			</Articles>
			<FuncFlag>1</FuncFlag>
			</xml> ";
}

function txt_template($user,$server,$time) {
	return "<xml>
			<ToUserName><![CDATA[$user]]></ToUserName>
			<FromUserName><![CDATA[$server]]></FromUserName>
			<CreateTime>$time</CreateTime>
			<MsgType><![CDATA[text]]></MsgType>
			<Content><![CDATA[%s]]></Content>
			<FuncFlag>0</FuncFlag>
			</xml>";
}

function checkSignature() 
{ 
	$signature = $_GET["signature"]; 
	$timestamp = $_GET["timestamp"]; 
	$nonce = $_GET["nonce"]; 
	$token ='hjj';

	$tmpArr = array($token, $timestamp, $nonce); 
	sort($tmpArr); 
	$tmpStr = implode( $tmpArr ); 
	$tmpStr = sha1( $tmpStr ); 

	if( $tmpStr == $signature ){ 
		return true; 
	}else{ 
		return false; 
	} 
}

$msg = $HTTP_RAW_POST_DATA;

if ($msg) {
	checkSignature() or die("not from wechat!");
	$post_obj = simplexml_load_string($msg,'SimpleXMLElement', LIBXML_NOCDATA);
	$user = $post_obj->FromUserName;
	$server = $post_obj->ToUserName;
	$msgtype=$post_obj->MsgType;
	$time = time();

	$instructions="分享自己地点，即可查询附近校园巴士站以及下一班到达时间\n更多小功能: \n1.发送\"xc(+all)\"查询最近的校车或所有时刻表\n2.发送\"xl\"查询交大校历\n3.发送\"zd keyword\"全文检索知乎日报内容";
	switch($msgtype)
	{
	case "text": 
		$receivemsg=$post_obj->Content;
		$arr=explode(" ",$receivemsg,2);
		$target=strtolower($arr[0]);
		$keyword=urlencode($arr[1]);
		$keyword=strtolower($keyword);

		switch($target){
		/*
		* DEPRECATED!!
		case "t": // twitter msg
			$s=twitter_Search($keyword,6);
			if($s==null) {$err="无法找到您要搜索的内容";}
			$returnmsg = sprintf(txt_template($user,$server,$time), $s.$err);
			break;
		*/
		case "n": 
			$tmp=get_notice(6); 
			$returnmsg = sprintf(txt_template($user,$server,$time),$tmp);
			break;
		case "xl": 
			$title="校历";
			$description="2012-2013";
			$imageurl="http://edfward.com/~lx/static/xl.jpg";
			$clickurl="http://edfward.com/~lx/static/xl.jpg";
			$returnmsg = sprintf(img_template($user,$server,$time), $title, 
				$description, $imageurl, $clickurl);
			break;
		case "xc": 
			$keyword=urldecode($keyword);
			if ($keyword == "map") { // return image
				$title="校车路线";
				$description="交大校园巴士路线图";
				$imageurl="http://edfward.com/~lx/static/bus_map.jpg";
				$clickurl="http://edfward.com/~lx/static/bus_map.jpg";
				$returnmsg = sprintf(img_template($user,$server,$time), $title, 
					$description, $imageurl, $clickurl);
				break;
			}

			// return text
			if($keyword==null){
				$tmp=get_bus($time);
				if((date('w',$time)==6)||(date('w',$time)==7)) 
					$tmp="周末没校车 T_T";
			}
			else if($keyword=="all") 
			{
				$tmp=get_bus("all");
			}
			$returnmsg = sprintf(txt_template($user,$server,$time), $tmp);
			break;
		case "zd":
			$failmsg = sprintf(txt_template($user,$server,$time), "查找无结果");
			$zd_res = search_zhihudaily($user,$server,$time,$arr[1]);
			$returnmsg = ($zd_res != "0" ? $zd_res : $failmsg);
			break;
		case "face":
			if ($keyword==null) {
				$returnmsg = sprintf(txt_template($user,$server,$time), "请输入参数");
				break;
			}
			$failmsg = sprintf(txt_template($user,$server,$time), "查找无结果");
			$face_res = get_face($user, $server, $time, $keyword);
			$returnmsg = ($face_res != "0" ? $face_res : $failmsg);
			break;
		default:
			$returnmsg = sprintf(txt_template($user,$server,$time), $instructions);
		}
		break;

	case "image" :break;

	case "location":
		$loc_x = floatval($post_obj->Location_X);
		$loc_y = floatval($post_obj->Location_Y);
		$tmp = nearestStation($loc_x, $loc_y, strtotime('now'));
		$returnmsg = sprintf(txt_template($user,$server,$time), $tmp);
		break;

	case "link":break;

	case "event" :break;

	default : ;
	}

	echo $returnmsg;
}//end msg

else {
	$receivemsg=$_GET["query"];
	if($receivemsg==null) {
		$s = "
			Nothing to show here!
			H J J
			";
		echo $s;
	}
}

?>
