<?php

require("/home/lx/public_html/func_lib/twitter_Search.php");
require("/home/lx/public_html/func_lib/get_notice.php");
require("/home/lx/public_html/func_lib/get_music.php");
require("/home/lx/public_html/func_lib/get_bus.php");
require("/home/lx/public_html/func_lib/location.php");

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
	/*$f = fopen('/home/lx/public_html/tmp.txt','w') or die("DIE!");
	fwrite($f,$msg);
	fclose($f);
	 */
	$post_obj = simplexml_load_string($msg,'SimpleXMLElement', LIBXML_NOCDATA);
	$user = $post_obj->FromUserName;
	$server = $post_obj->ToUserName;
	$msgtype=$post_obj->MsgType;
	$time = time();

	$instructions="分享自己地点，即可查询附近校园巴士站以及下一班到达时间\n更多小功能: \n1.发送 \"n\"获取最新校园通告\n2.发送\"xc(+all)\"查询最近的校车或所有时刻表\n3.发送\"xl\"查询交大校历\n4.发送\"t 关键词\"查询twitter\n5.发送\"m + 歌曲名\"搜索歌曲\n6.待续\n";
	switch($msgtype)
	{
	case "text": 
		$receivemsg=$post_obj->Content;
		$arr=explode(" ",$receivemsg,2);
		$target=strtolower($arr[0]);
		$keyword=urlencode($arr[1]);
		$keyword=strtolower($keyword);

		switch($target){
		case "t": // twitter msg
			$s=twitter_Search($keyword,6);
			if($s==null) {$err="无法找到您要搜索的内容";}
			$returnmsg = sprintf(txt_template($user,$server,$time), $s.$err);
			break;
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
		case "m":
			$keyword=urldecode($keyword);
			$keyword=str_replace(" ","%2B", $keyword);
			$geturl="http://iopenapi.duapp.com/search.php?key=".$keyword;
			$html=file_get_contents($geturl);
			$j=json_decode($html);

			$music_title=$j->title;
			$music_author=$j->author;
			$music_url=$j->url;

			if($music_url==null) {
				$returnmsg = sprintf(txt_template($user,$server,$time),"无法找到音乐");
			}
			else{
				$returnmsg="<xml>
					<ToUserName><![CDATA[$user]]></ToUserName>
					<FromUserName><![CDATA[$server]]></FromUserName>
					<CreateTime>$time</CreateTime>
					<MsgType><![CDATA[music]]></MsgType>
					<Music>
					<Title><![CDATA[$music_title]]></Title>
					<Description><![CDATA[$music_author]]></Description>
					<MusicUrl><![CDATA[$music_url]]></MusicUrl>
					<HQMusicUrl><![CDATA[$music_url]]></HQMusicUrl>
					</Music>
					<FuncFlag>0</FuncFlag>
					</xml>";
			}
			break;//music
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
		case "to complete": break;
		default:
			$returnmsg = sprintf(txt_template($user,$server,$time), $instructions);
		}
		break;

	case "image" :break;

	case "location":
		$loc_x = floatval($post_obj->Location_X);
		$loc_y = floatval($post_obj->Location_Y);
		$d26_x = 31.029505; $d26_y = 121.429849;
		$dis = haversineGreatCircleDistance($loc_x,$loc_y,$d26_x,$d26_y);
		$tmp = nearestStation($loc_x, $loc_y,get_bus_for_location($time));
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
