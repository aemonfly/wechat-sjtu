<?php

function get_face($user, $server, $time, $query) {
    $title="Face for ".$query;
    $description="";	
    switch (is_numeric(substr($query, 0, -1))) {
        case true: # id num
            if (strlen($query) != 18) return "0";
            $imageurl="http://adapt.seiee.sjtu.edu.cn/~ed/faces/".$query.".jpg";
            $clickurl="http://adapt.seiee.sjtu.edu.cn/~ed/faces/".$query.".jpg";
            return "<xml>
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
            break;
        case false: # name
            $name_id_json = file_get_contents("static/final_name_id.json");
            $name_id = json_decode($name_id_json, true); # get ARRAY!
            if (!array_key_exists($query, $name_id)) return "0";
            $ids = $name_id[$query];
            
            # prepare return message
            $num_of_id = count($ids);
            $rtmsg="<xml>
                <ToUserName><![CDATA[$user]]></ToUserName>
                <FromUserName><![CDATA[$server]]></FromUserName>
                <CreateTime>$time</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>$num_of_id</ArticleCount>
                <Articles>";
            foreach ($ids as $key => $value) {
                $title=$value;
                $imageurl="http://adapt.seiee.sjtu.edu.cn/~ed/faces/".$value.".jpg";
                $clickurl="http://adapt.seiee.sjtu.edu.cn/~ed/faces/".$value.".jpg";
                $rtmsg.="<item>
                    <Title><![CDATA[$value]]></Title>
                    <Description><![CDATA[$description]]></Description>
                    <PicUrl><![CDATA[$imageurl]]></PicUrl>
                    <Url><![CDATA[$clickurl]]></Url>
                    </item>";
            }
            $rtmsg.="</Articles>
                <FuncFlag>1</FuncFlag>
                </xml>";
            return $rtmsg;
    }
}

?>
