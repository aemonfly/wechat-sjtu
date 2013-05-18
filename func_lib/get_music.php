<?php

require("/home/lx/public_html/func_lib/simple_html_dom.php");

function get_music($title)
{
	$value=urlencode($title);
	$qurl='http://music.baidu.com/search?key='.$value;//搜索链接
	$html1=file_get_html($qurl);
	$div=$html1->find('span[class=song-title]',0);
	$link1=$div->first_child ()->href;

	$link2='http://music.baidu.com/'.$link1.'/download';
	$html2=file_get_html($link2);
	$download=$html2->getElementById('download');  
	$url=$download->href;
	$title=$html2->find('span[class=fwb]',0)->plaintext;//获得歌名
	$author=$html2->find('span[class=author_list]',0)->plaintext;//获得歌手  

# $finalurl="http://music.baidu.com".$url;
#str_replace(' ','',$title);
#str_replace(' ','',$author);

# return $title." ".$author." ".$finalurl;
	return $download;


}

?>
