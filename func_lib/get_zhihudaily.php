<?php

require_once("sphinxapi.php");

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

function search_zhihudaily($user,$server,$time,$keyword) {
	$cl = new SphinxClient();
	$cl->SetServer('localhost', 9312);
	$cl->SetConnectTimeout(3);
	$cl->SetLimits(0, 10);
	$cl->SetMaxQueryTime(2000);
	$cl->SetArrayResult(true);
	$cl->SetMatchMode(SPH_MATCH_EXTENDED);
	$cl->SetRankingMode(SPH_RANK_PROXIMITY_BM25);
	$cl->SetSortMode(SPH_SORT_EXTENDED, '@relevance desc, @weight desc');

	$query_res = $cl->Query($keyword, "zhihu");
	if ($query_res['total'] == 0) {
		return "0";
	}
	$Description="test";
	$rtmsg="<xml>
		<ToUserName><![CDATA[$user]]></ToUserName>
		<FromUserName><![CDATA[$server]]></FromUserName>
		<CreateTime>$time</CreateTime>
		<MsgType><![CDATA[news]]></MsgType>
		<ArticleCount>{$query_res['total']}</ArticleCount>
		<Articles>";
	$dailyzhihu_url = "http://daily.zhihu.com/story/";

	if (!empty($query_res)) {
	    foreach ($query_res['matches'] as $v) {
			$title=$v['attrs']['a_title'];
			$url=$dailyzhihu_url.$v['attrs']['a_id'];
			$picurl=$v['attrs']['img_url'];
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
