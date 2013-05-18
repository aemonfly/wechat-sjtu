<?php
function get_notice($numOfNotice)
{
$notice_url="http://www.jwc.sjtu.edu.cn/rss/rss_notice.aspx?SubjectID=198015&TemplateID=221009";
$notice_xml=simplexml_load_file($notice_url,'SimpleXMLElement', LIBXML_NOCDATA);
$tmp="";
for($i=0;$i<$numOfNotice;$i++)
{
	$tmp.=strval($i+1).":\n";
	$tmp.= $notice_xml->channel->item[$i]->title;
	$tmp.="\n";
	$tmp.= $notice_xml->channel->item[$i]->link;
	$tmp.="\n------\n";

}
return $tmp;}

?>
