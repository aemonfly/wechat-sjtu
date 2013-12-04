<?php
/********
IMPORTANT: since twitter's new api requires some form of authentication
           this functionality has to be deprecated
*********/

function twitter_Search($keyword,$NumOfTweets)
{


	$c = curl_init("http://search.twitter.com/search.json?q=$keyword");
	curl_setopt($c,CURLOPT_RETURNTRANSFER,1);
	$res = curl_exec($c);
   
	$query = json_decode($res,1);
	$tweets = array_slice($query["results"],0,$NumOfTweets);

	$s = "";
	foreach($tweets as $i => $tweet) {
		$s .= "tweet ".strval($i+1).":\n".$tweet["text"]."\n--------\n";
	}
	curl_close($c);
	return $s;

}


?>
