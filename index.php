<?php

require("/home/lx/public_html/func_lib/twitter_Search.php");
require("/home/lx/public_html/func_lib/get_notice.php");
require("/home/lx/public_html/func_lib/get_music.php");
require("/home/lx/public_html/func_lib/get_bus.php");

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
	$receivemsg=$post_obj->Content;
	$msgtype=$post_obj->MsgType;
	$time = time();

	$instructions="使用说明: \n1.发送 \"n\"获取最新校园通告\n2.发送\"校车(+all)\"查询最近的校车或所有时刻表\n3.发送\"校历\"查询交大校历\n4.发送\"t 关键词\"查询twitter\n5.待续\n";
	switch($msgtype)
	{
		case "text":{ 

						$arr=explode(" ",$receivemsg,2);
						$target=strtolower($arr[0]);
						$keyword=urlencode($arr[1]);
						$keyword=strtolower($keyword);
						
						switch($target){

							case "t":{
										 $s=twitter_Search($keyword,6);
										 if($s==null) {$err="无法找到您要搜索的内容";}
										 $returnmsg = "<xml>
											 <ToUserName><![CDATA[$user]]></ToUserName>
											 <FromUserName><![CDATA[$server]]></FromUserName>
											 <CreateTime>$time</CreateTime>
											 <MsgType><![CDATA[text]]></MsgType>
											 <Content><![CDATA[$s$err]]></Content>
											 <FuncFlag>0</FuncFlag>
											 </xml>";
									 }break;//twitter


							case "n": {
										  $tmp=get_notice(6); 
										  $returnmsg = "<xml>
											  <ToUserName><![CDATA[$user]]></ToUserName>
											  <FromUserName><![CDATA[$server]]></FromUserName>
											  <CreateTime>$time</CreateTime>
											  <MsgType><![CDATA[text]]></MsgType>
											  <Content><![CDATA[$tmp]]></Content>
											  <FuncFlag>0</FuncFlag>
											  </xml>"; 
									  }break;

							case "xl": {

											 $title="校历";
											 $description="2012-2013";
											 $imageurl="http://edfward.com/~lx/static/198109_1.jpg";
											 $clickurl="http://edfward.com/~lx/static/198109_1.jpg";
											 $returnmsg ="<xml>
												 <ToUserName><![CDATA[$user]]></ToUserName>
												 <FromUserName><![CDATA[$server]]></FromUserName>
												 <CreateTime>$time</CreateTime>
												 <MsgType><![CDATA[news]]></MsgType>
												 <ArticleCount>1</ArticleCount>
												 <Articles>
												 <item>
												 <Title><![CDATA[$title]]></Title> 
												 <Description><![CDATA[$description]]></Description>
												 <PicUrl><![CDATA[$imageurl]]></PicUrl>
												 <Url><![CDATA[$clickurl]]></Url>
												 </item>
												 </Articles>
												 <FuncFlag>1</FuncFlag>
												 </xml> ";


										 } break;

							case "m":
										 {		
											 $keyword=urldecode($keyword);
											 $keyword=str_replace(" ","%2B", $keyword);
											 $geturl="http://iopenapi.duapp.com/search.php?key=".$keyword;
											 $html=file_get_contents($geturl);
											 $j=json_decode($html);

											 $music_title=$j->title;
											 $music_author=$j->author;
											 $music_url=$j->url;

											 if($music_url==null) {
												 $returnmsg="<xml>
													 <ToUserName><![CDATA[$user]]></ToUserName>
													 <FromUserName><![CDATA[$server]]></FromUserName>
													 <CreateTime>$time</CreateTime>
													 <MsgType><![CDATA[text]]></MsgType>
													 <Content><![CDATA[无法找到音乐]]></Content>
													 <FuncFlag>0</FuncFlag>
													 </xml>";}
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
										 }break;//music

							case "校车": {
											 $keyword=urldecode($keyword);
											if($keyword==null){
												 $tmp=get_bus($time);
												if((date('w',$time)==6)||(date('w',$time)==7)) 
												$tmp="周末没校车 T_T";
												
												}
											 else if($keyword=="all") 
											 {
													 $tmp=get_bus("all");
												 }
											 $returnmsg = "<xml>
												 <ToUserName><![CDATA[$user]]></ToUserName>
												 <FromUserName><![CDATA[$server]]></FromUserName>
												 <CreateTime>$time</CreateTime>
												 <MsgType><![CDATA[text]]></MsgType>
												 <Content><![CDATA[$tmp]]></Content>
												 <FuncFlag>0</FuncFlag>
												 </xml>"; 
										 }break;




							case "to complete": break;

							default:

												$returnmsg = "<xml>
													<ToUserName><![CDATA[$user]]></ToUserName>
													<FromUserName><![CDATA[$server]]></FromUserName>
													<CreateTime>$time</CreateTime>
													<MsgType><![CDATA[text]]></MsgType>
													<Content><![CDATA[$instructions]]></Content>
													<FuncFlag>0</FuncFlag>
													</xml>";

						}




					}break;

		case "image" :{}break;

		case "location":{}break;

		case "link":{}break;

		case "event" :{}break;

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
