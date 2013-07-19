<?php
function get_zhihudaily($user,$server,$time,$date){
	if($date!=''){
		$webcode=json_decode(file_get_contents('http://news.at.zhihu.com/api/1.2/news/before/'.$date), 1);
	}
	else{
	$webcode=json_decode(file_get_contents('http://news.at.zhihu.com/api/1.2/news/latest'), 1);
	}
	$length=sizeof($webcode['news']);

	if($length<=10){
		$rtmsg="<xml>
			<ToUserName><![CDATA[$user]]></ToUserName>
			<FromUserName><![CDATA[$server]]></FromUserName>
			<CreateTime>$time</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<ArticleCount>$length</ArticleCount>
			<Articles>";

		for($i=0;$i<$length;$i++){
			$title=$webcode['news'][$i]['title'];
			$url=$webcode['news'][$i]['share_url'];
			$picurl=$webcode['news'][$i]['image'];
			$Description="test";
			$rtmsg.="<item>
				<Title><![CDATA[$title]]></Title> 
				<Description><![CDATA[$Description]]></Description>
				<PicUrl><![CDATA[$picurl]]></PicUrl>
				<Url><![CDATA[$url]]></Url>
				</item>";
		}
	}


	else{
		$rtmsg="<xml>
			<ToUserName><![CDATA[$user]]></ToUserName>
			<FromUserName><![CDATA[$server]]></FromUserName>
			<CreateTime>$time</CreateTime>
			<MsgType><![CDATA[news]]></MsgType>
			<ArticleCount>10</ArticleCount>
			<Articles>";

		for($i=0;$i<10;$i++){

			$title=$webcode['news'][$i]['title'];
			$url=$webcode['news'][$i]['share_url'];
			$picurl=$webcode['news'][$i]['image'];
			$Description="test";
			$rtmsg.="<item>
				<Title><![CDATA[$title]]></Title> 
				<Description><![CDATA[$Description]]></Description>
				<PicUrl><![CDATA[$picurl]]></PicUrl>
				<Url><![CDATA[$url]]></Url>
				</item>";
		}	
	}
	$rtmsg.="</Articles>
		<FuncFlag>1</FuncFlag>
		</xml>";
	return $rtmsg;
}
?>
