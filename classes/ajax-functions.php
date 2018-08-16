<?php

function get_sites_json(){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_log';

	$sql = "SELECT DISTINCT url FROM ".$table_name." ORDER BY url ASC";
	$res = $wpdb->get_results( $wpdb->prepare($sql, ARRAY_A) );

	return json_encode($res);
}

function store_activity($url, $wpspy_activity) {
    global $wpdb;
    $wpdb->show_errors = true; // true : show errors in development
    $table_name = $wpdb->prefix.'wpspy_activity_log';
    $date_now = date("Y-m-d H:i:s");
    $sql = "SELECT id, activity_date FROM $table_name WHERE (url='$url') AND (DATE_FORMAT(activity_date,'%Y-%m-%d') = DATE_FORMAT('$date_now', '%Y-%m-%d')) ORDER BY id DESC LIMIT 1";
	$res = $wpdb->get_results($sql);

   	if( $res != 0 && !empty($res) ){
   		// Update old record in the same day
   			$wpspy_activity["activity_date"] = $date_now;
	        $where = array('id' => $res[0]->id);
	        if( $wpdb->update( $table_name, $wpspy_activity, $where ) ){
	        	return json_encode(array("status_code"=>"200", "msg" => "Success!"));
	        }else{
		    	return json_encode(array("status_code"=>"500", "msg" => "Sorry, something went wrong. Please try again later.", "error" =>  $wpdb->print_error(), "more_info" => $wpdb->last_query));
	        }
   	}else{
   		// Insert new record
	   		$wpspy_activity["activity_date"] = $date_now;

		    if( $wpdb->insert( $table_name, $wpspy_activity ) ){
		    	return json_encode(array("status_code"=>"200", "msg" => "Success!"));
		    }else{
		    	return json_encode(array("status_code"=>"500", "msg" => "Sorry, something went wrong. Please try again later.", "error" =>  $wpdb->print_error(), "more_info" => $wpdb->last_query));
		    }
   	}
}

function get_alexa_rank($url){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_log';

	$sql = "SELECT alexa_rank, activity_date FROM ".$table_name." WHERE url='".$url."/' ORDER BY activity_date ASC";
	$res = $wpdb->get_results( $wpdb->prepare($sql, ARRAY_A) );
	
	$date = array();
	$alexa_rank = array();
	foreach ($res as $r) {
		$d = strtotime( $r->activity_date );
		array_push( $date, array("label" => date( 'M j, Y, g:i a', $d )) );
		array_push( $alexa_rank, array("value" => (int) str_replace(",", "", $r->alexa_rank)) );
	}

	return json_encode(array("dates"=>$date, "alexa_ranks"=>$alexa_rank));
}

function get_chart_data($url, $column){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_log';

	$sql = "SELECT $column, activity_date FROM ".$table_name." WHERE url='".$url."' ORDER BY activity_date ASC";
	
	$res = $wpdb->get_results( $wpdb->prepare($sql, ARRAY_A) );
	
	$date = array();
	$values = array();
	foreach ($res as $r) {
		$d = strtotime( $r->activity_date );
		$val = (int) str_replace(",", "", $r->$column);
		array_push( $date, array("label" => date( 'm/d/Y, H:i', $d )) );
		array_push( $values, array("value" => $val) );
	}

	return json_encode(array("dates"=> json_encode($date), "values"=>json_encode($values)));
}

function get_links_on_page($domain){
	echo $data = getPageData($domain);
}

function get_ie_links($domain){
	$links = getLinks($domain);
	return json_encode($links);
}

function get_sitemetrics($domain){
	include "dbhelper.php";
	$site_metrics = get_site_metrics($domain);
	$alexa_rank_in_country = json_decode( str_replace("\\", "", $site_metrics["alexa_rank_in_country"]) );
	$site_metrics["alexa_rank_in_country"] = $alexa_rank_in_country;
	echo json_encode($site_metrics);
}

function get_history_list($domain){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_log';

	$sql = "SELECT id, DATE_FORMAT(activity_date,'%W, %M %e, %Y @ %h:%i %p') as formatted_activity_date,
	 		activity_date FROM ".$table_name." WHERE url = '".$domain."' ORDER BY id DESC";
	$res = $wpdb->get_results( $sql, ARRAY_A );
	
	$records = array();

	if(count($res) > 0){
		
		foreach ($res as $key) {
			$action = '<a href="javascript:void(0);" class="history-actions" data-action="site_info" data-id="'.$key["id"].'">Site Info</a>
				<a href="javascript:void(0);" class="history-actions" data-action="page_info" data-id="'.$key["id"].'">Page Info</a>
				<a href="javascript:void(0);" class="history-actions" data-action="seo_stats" data-id="'.$key["id"].'">SEO Stats</a>
				<a href="javascript:void(0);" class="history-actions" data-action="social_stats" data-id="'.$key["id"].'">Social Stats</a>
				<a href="javascript:void(0);" class="history-actions" data-action="traffic" data-id="'.$key["id"].'">Traffic</a>
				<a href="javascript:void(0);" class="history-actions" data-action="link" data-id="'.$key["id"].'">Link</a>';
			$record = array( $key["formatted_activity_date"], $action );
			array_push($records, $record);
		}
		return json_encode( array( "data" =>$records ) );
	}else{
		return json_encode( array( "data" => array() ) );
	}
}

function get_history($id, $option){
	
	if($option == "site_info"){
		$columns = "robot, sitemap_index, ip, city, country, country_code, wordpress_data, dns";

	}else if($option == "page_info"){
		$columns = "canonical_url, title, meta_keywords, meta_description, meta_robots, 
					h1, h2, bold_strong, italic_em, body";

	}else if($option == "seo_stats"){
		$columns = "page_indexed_ask, page_indexed_baidu, page_indexed_bing, page_indexed_goo, 
			page_indexed_google, page_indexed_sogou, page_indexed_yahoo, page_indexed_yandex, page_indexed__360,
			backlinks_alexa, backlinks_google, backlinks_open_site_explorer, backlinks_sogou, backlinks_ahrefs,
			alexa_rank, google_page_rank, quantcast_traffic_rank";

	}else if($option == "traffic"){
		$columns = "alexa_rank, alexa_rank_in_country, quantcast_traffic_rank, bounce_rate, google_page_rank,
			daily_pageviews_per_visitor, dailytime_onsite ";

	}else if( $option == "link" ){
		$columns = "external_links, internal_links ";

	}else if( $option == "social_stats" ){
		$columns = "facebook_count, twitter_count, google_count, linkedin_count, 
			pinterest_count, stumbleupon_count, score_strength, score_sentiment, score_passion, score_reach ";
	}

	$res = query_history_sql($id, $columns);
	return json_encode($res);
	
}

function query_history_sql($id, $columns){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_log';

	$sql = "SELECT ".$columns." FROM ".$table_name." WHERE id = '".$id."'";
	$res = $wpdb->get_results( $sql, ARRAY_A );

	if( $res ){
		return $res;
	}
	return $wpdb->last_query;
}

// save recommended tools limit settings
function saveRTLSettings($val){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_settings';

	$sql = "SELECT * FROM ".$table_name." ORDER BY id DESC LIMIT 1";
	$res = $wpdb->get_row( $sql );
	
	if( !empty($res) ){
		$sql = $wpdb->update( $table_name, array('recommended_tools_limit' => $val), array("id" => $res->id) );
	}else{
		$wpdb->insert($table_name, array('recommended_tools_limit' => $val));
	}
	return $wpdb->last_query;
}

function getRTLimit(){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_settings';

	$sql = "SELECT recommended_tools_limit FROM $table_name ORDER BY id DESC LIMIT 1";
	$res = $wpdb->get_row($sql);
	if( !empty($res) && isset($res->recommended_tools_limit) ){
		return $res->recommended_tools_limit;
	}
	return 10;
}


function ajaxCheckDataStatus($option, $url){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_log';

	$date_now = date("Y-m-d H:i:s");
 	$sql = "SELECT id FROM ".$table_name." WHERE (url='$url') AND 
 			(DATE_FORMAT(activity_date,'%Y-%m-%d') = DATE_FORMAT('$date_now', '%Y-%m-%d')) 
 			ORDER BY id DESC LIMIT 1";
 	$res = $wpdb->get_var($sql);
	
 	if( $res > 0 ){
 		$id = $res;

 		if($option == "site_info"){
			$columns = "robot, sitemap_index, ip, city, country, country_code, wordpress_data, dns";

		}else if($option == "page_info"){
			$columns = "canonical_url, title, meta_keywords, meta_description, meta_robots, 
						h1, h2, bold_strong, italic_em, body, external_links, internal_links";

		}else if($option == "seo_stats"){
			$columns = "page_indexed_ask, page_indexed_baidu, page_indexed_bing, page_indexed_goo, 
				page_indexed_google, page_indexed_sogou, page_indexed_yahoo, page_indexed_yandex, page_indexed__360,
				backlinks_alexa, backlinks_google, backlinks_open_site_explorer, backlinks_sogou, backlinks_ahrefs,
				alexa_rank, google_page_rank, quantcast_traffic_rank";

		}else if($option == "traffic"){
			$columns = "alexa_rank, alexa_rank_in_country, quantcast_traffic_rank, bounce_rate, 
				daily_pageviews_per_visitor, dailytime_onsite ";

		}else if( $option == "link" ){
			$columns = "external_links, internal_links ";

		}else if( $option == "social_stats" ){
			$columns = "facebook_count, twitter_count, google_count, linkedin_count, 
				pinterest_count, stumbleupon_count, score_strength, score_sentiment, score_passion, score_reach ";
		}

		// get the details
		$sql = "SELECT ".$columns." FROM ".$table_name." WHERE id = '".$id."'";

		$results = $wpdb->get_results($sql, ARRAY_A);
		if( $option == "site_info" ){
			if($results[0]["wordpress_data"] == null || $results[0]["wordpress_data"] == "null" || $results[0]["wordpress_data"] == ""){
				return  "false";
				exit(0);
			}else{
				$json = str_replace("\\", "", $results[0]["wordpress_data"]);
				$wordpress = json_decode($json);
				if($wordpress->version == 0 || $wordpress->version == "0"){
					return "false"; exit(0);
				}
			}
		}

		return json_encode($results);
 	}else{
 		return "false";
 	}
}

/* start functions for Rapid Indexer */
	function get_indexes($domain) {
		$data = 'http://1.bp.blogspot.com/_rMCadGGpNFM/S6F-k8MuexI/AAAAAAAAALQ/b1lflkjtlSQ/s1600-h/go2.{website}.htm
			http://1.bp.blogspot.com/_rZ9rhnN-yu8/S37WhPOsGpI/AAAAAAAAAOo/N8WWKtwGeGA/s1600-h/go2.{website}.htm
			http://1.bp.blogspot.com/_tKod7x0SiiY/S6IncKrIv_I/AAAAAAAAAgo/Z9CuusLeCq8/s1600-h/go2.{website}.htm
			http://1.bp.blogspot.com/_xNxkeDxmL6Y/S5mNBcV8bRI/AAAAAAAABA8/5hP-3NntYOc/s1600-h/go2.{website}.htm
			http://1.bp.blogspot.com/_ZIIFV1TpaKE/S3oLxeODnlI/AAAAAAAAC1Q/Zujb-HroSZE/s1600-h/go2.{website}.htm
			http://1.zgtgwz.com/alexa/index.asp?domain={website}
			http://100.kutikomi.net/s/{website}
			http://108shot.com/o.php?out=http://www.{website}
			http://108shot.com/o.php?out=http://www.{website}/cafe/camera/listerO.php?subgroup=2
			http://115.com/s?q=site%3A{website}
			http://123any.com/movies/{website}
			http://123aspx.com/Search.aspx?lookfor=www.{website}&wording=3&pNo=1&ob=ratings&obd=desc
			http://123aspx.com/Search.aspx?submit=search&wording=3&lookfor=www.{website}
			http://123kuma.com/ufo-youtube/tag/{website}
			http://125.206.119.161/http://www.{website}/cocomin/627488/627518
			http://1303.at/deref/redir.php?id=http://www.{website}/download_ccleaner/changelog
			http://1314dh.com/Tools/ZhanZhang/SEO/Index.asp?domain={website}&Search=+%B2%E9%D1%AF+&rank=checked&sogou_rank=checked&google=checked&baidu=checked&yahoo=checked&sogou=checked&youdao=checked&SEIPcode1=%3Ca+href%3D%22http%3A%2F%2F1314dh.com%2FTools%2FZhanZhang%2FSEO%2FIndex.asp%3Fdomain%3D%26type%3Dall%22+target%3D%22_blank%22+title%3D%22%B2%E9%BF%B4%CA%D5%C2%BC%C7%E9%BF%F6%22%3E%CD%F8%D5%BE%CA%D5%C2%BC%B2%E9%D1%AF%3C%2Fa%3E
			http://173158.net/search.asp?q={website}
			http://174.139.1.210/link.php/http://www.{website}/people/pixelmuser/art/2658423-2-the-last-post-sorrento
			http://17-th.net/berita/Abc+News+abcnews.{website}+breitbart.com+cbn.net.id+bellsouth+.html
			http://17-th.net/berita/www5e.{website}.html
			http://18qt.com/tp/out.php?to={website}
			http://19.dee.cc/~cm/lib/ime.php?{website}/qa2719539.html
			http://19.dee.cc/~cm/lib/ime.php?{website}/qa4806282.html
			http://19.dee.cc/~cm/lib/ime.php?id43.{website}/18/kcafe
			http://19.dee.cc/~cm/lib/ime.php?minkara.{website}/userid/177227/blog
			http://19.dee.cc/~cm/lib/ime.php?s6.{website}/ujhonk/hatujyouyuka
			http://19.dee.cc/~cm/lib/ime.php?www.{website}/gold/ida-online/top.htm
			http://19.dee.cc/~cm/lib/ime.php?www.{website}/product/pic_b/1602007C/657262pb01_16007C.jpg
			http://19.zhoroi9.com/06/index.cgi?mode=rank&cat=id32.{website}/248/t01ht0p0/&target=
			http://19.ztakayuki12.com/18/edit.cgi?mode=edit&no=d55.{website}/127/tkcmgen012
			http://194.72.238.59/up/graph?site=www.fastighetsbyra.{website}&amp;probe=1
			http://1board.ru/redirect/?http://www.{website}
			http://1news.ru/go.php?http://www.{website}/articles/2007/02/01/621501.shtml
			http://1news.ru/go.php?http://www.{website}/articles/2007/02/01/621514.shtml
			http://1site-cannes.fr/index.php/actualites-cannes-nice-grasse-antibes/cannes/1937-le-festival-de-cannes-2010---{website}-cin%C3%A9ma.html
			http://1stat.ru/?domain={website}
			http://1stat.ru/?domain=change-{website}
			http://1stat.ru/?domain=rambler-{website}
			http://1stat.ru/?ns={website}
			http://1stat.ru/?ns={website}&amp;date1=01-01-2007&amp;date2=01-02-2007&amp;page=&amp;p=1
			http://1stat.ru/?ns={website}&amp;date1=01-02-2010&amp;date2=01-03-2010&amp;page=&amp;p=1
			http://1stat.ru/?ns={website}&date1=01-09-2008&date2=01-10-2008&page=&p=1
			http://1stat.ru/?ns={website}.%20livejournal.ru.
			http://1stat.ru/?ns=hosting.{website}&date1=01-06-2009&date2=01-07-2009
			http://1stat.ru/?ns=ns1.{website}.+ns1.beelinegprs.ru.+ns2.{website}.+ns2.beelinegprs.ru.
			http://1sthotwomen.com/szh/search.php?name=%D5%D4%DE%B1%C7%B1%B9%E6%D4%F2+site:yule.msn.{website}
			http://1trader.ru/redirect/?http://www.{website}
			http://1-vl.ru/lj/1383-tigr-i-er-srach-na-forume-{website}.html
			http://1whois.ru/?url={website}
			http://1whois.ru/?url=gdsz.{website}
			http://1whois.ru/?url=ns2.{website}
			http://1whois.ru/?url=sym.gdsz.{website}
			http://2.bp.blogspot.com/_QvXCH5ldJxk/SyzYL1QUu3I/AAAAAAAABZU/Rj0Cnk5ImTo/s1600-h/go2.{website}.htm
			http://2.bp.blogspot.com/_R5w02jhx_zA/S5mb77xnfeI/AAAAAAAABLE/mDfG3Pnit0o/s1600-h/go2.{website}.htm
			http://2.bp.blogspot.com/_VXpJNdv3RHA/S56DqEDVGTI/AAAAAAAABC4/gbnjVhcAhho/s1600-h/go2.{website}.htm
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=ccne.{website}/383661&w=high+voltage&d=VnylJ0LUThwU&sig=77b571c082fa0b37288aad52fd487f05&cq=high+voltage
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=ent.{website}/costume/shenhua/classpage/video/20100108/101902.shtml&w=%E7%94%B5%E8%A7%86%E5%89%A7+%E7%A5%9E%E8%AF%9D&d=f7t_ZkLUUKEh&sig=ced1c162f128be8a172b3c5101f4ff47&cq=%E7%94%B5%E8%A7%86%E5%89%A7+%E7%A5%9E%E8%AF%9D
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=music.{website}/music.aspx%3Fid%3D1240&w=%E4%B8%A4%E5%8F%AA%E8%9D%B4%E8%9D%B6&d=ViRmJULUUJUz&sig=784461f6905fbda4118b607bbdc1c255&cq=%E4%B8%A4%E5%8F%AA%E8%9D%B4%E8%9D%B6
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=my.{website}/pigeon/&w=one&d=RkBJwkLUUfF3&sig=8349cead1d011e10b000efbcd644ed91&cq=and+one
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=nanjing.{website}/z_a7c492cb&w=%E5%8D%97%E4%BA%AC+%E5%B7%A5%E7%A8%8B+%E5%A4%A7%E5%AD%A6&d=ci1R1ULUUbzL&sig=83398d222a87666b1b46b0a8aacd4286&cq=%E5%8D%97%E4%BA%AC%E5%B7%A5%E7%A8%8B%E5%A4%A7%E5%AD%A6
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=news.{website}/video/1041230.htm&w=%E6%A2%81%E8%8C%B5&d=TmXAtELUUXMM&sig=ee3bea5e1f5a7d3dc2f7181de214f4c9&cq=%E6%A2%81%E8%8C%B5
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=suzhou.{website}/so.php%3Fk%3Dp%26q%3D%25B3%25B5%25B9%25DC%25CB%25F9&w=%E6%B1%9F%E5%8D%97+%E8%BD%A6%E7%AE%A1%E6%89%80&d=E8eUckLUTDZ8&sig=e623883867c0c829f36fe73364eb1a37&cq=%E6%B1%9F%E5%8D%97%E8%BD%A6%E7%AE%A1%E6%89%80
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=www.{website}/dp/enbk611942&w=one&d=U966o0LUUaO8&sig=8349cead1d011e10b000efbcd644ed91&cq=and+one
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=www.{website}/dp/zjbk114998&w=%E6%A2%81%E8%8C%B5&d=d4SfkkLUUeIi&sig=ee3bea5e1f5a7d3dc2f7181de214f4c9&cq=%E6%A2%81%E8%8C%B5
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=www.{website}/page/tag/Step%2520Up/&w=step&d=NRQHe0LUUfB9&sig=f6f0414af069a5f62e3b6f5db694887e&cq=Step+Up
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=www.{website}/product/Product.aspx%3FProductID%3DM0105459012&w=part&d=BryNKkLUUa5l&sig=fa65483258a38eb35ff1f837f8a7f1f5&cq=Part+Of+Me
			http://203.209.253.185/snap/webcache.php?ei=UTF-8&icp=1&u=www.{website}/Product/Singer.aspx%3FSingerID%3D12965&w=one&d=JmoCykLUUbhD&sig=8349cead1d011e10b000efbcd644ed91&cq=and+one
			http://203.209.253.250/snap/webcache.php?ei=UTF-8&icp=1&u={website}/abstain.htm&w=abstain&d=GftYvELUUOAr&sig=6c117a1c7c344d778dd67d307a89eaa6&cq=abstain
			http://203.209.253.250/snap/webcache.php?ei=UTF-8&icp=1&u={website}/watch/2648668.html&w=%E5%8F%98%E5%BD%A2%E9%87%91%E5%88%9A+2&d=cfViX0LUUNXI&sig=7b722615dab7f631108d853a4c1c8d26&cq=%E5%8F%98%E5%BD%A2%E9%87%91%E5%88%9A2
			http://203.209.253.250/snap/webcache.php?ei=UTF-8&icp=1&u={website}/watch/3856885.html&w="%E5%90%83+%E6%89%8B"&d=Z0qud0LUUEb3&sig=ba244a0195a8baec8db261f7c1c3aeac&cq=%E5%90%83%E6%89%8B
			http://203.209.253.250/snap/webcache.php?ei=UTF-8&icp=1&u=2008.{website}/&w=%E5%A5%A5%E8%BF%90&d=BSC9H0LUUC_g&sig=8bd6979d709d7337c66044fcd919e6d0&cq=%E5%A5%A5%E8%BF%90';

		$list = explode("\n", $data);
		$x=0;

		$res = '<table id="wpindexer_results" class="table table-striped">';
		$res.= '<thead><tr><th>Results</th></tr></thead><tbody>';
		foreach ($list as $key) {
	        $key = trim($key);
			# code...
			if ($x > 0) {
				$key = str_replace('{website}', $domain, $key);
				$res.= '<tr><td><div class="wpindexer_entry"><a href="'.$key.'" target="_blank" data-id="'.$x.'">'.$key.'</a><div></td></tr>';
			}
			$x++;
		}
		$res.= "</tbody></table>";
		echo $res;
	}


	function check_url($domain){
	    $domain = trim($domain);
		//check, if a valid url is provided
		if(!filter_var($domain, FILTER_VALIDATE_URL)){
		    return false;
		}

		//initialize curl
		$curlInit = curl_init($domain);
		curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
		curl_setopt($curlInit,CURLOPT_HEADER,true);
		curl_setopt($curlInit,CURLOPT_NOBODY,true);
		curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);

		//get answer
		$response = curl_exec($curlInit);

		curl_close($curlInit);

		if ($response) return true;

		return false;
	}

/* end rapid indexer functions */



/* start functions for  Keyword Research */
	
	function fix_latin1_mangled_with_utf8($str)
	{
	    return preg_replace_callback('#[\\xA1-\\xFF](?![\\x80-\\xBF]{2,})#', 'utf8_encode_callback', $str);
	}

	function utf8_encode_callback($m)
	{
	    return utf8_encode($m[0]);
	}

	function getKeywordSuggestions($keyword){
		$alpha = range('A', 'Z');

		$x = 0;
		$res = "";
		$foundkeywords = array();

		foreach($alpha as $a) {
			$data = file_get_contents("https://suggestqueries.google.com/complete/search?output=toolbar&hl=en&q=".urlencode($keyword).urlencode($a));
			
			$xml = simplexml_load_string( fix_latin1_mangled_with_utf8($data) );
			
			foreach($xml->children() as $child) {
				foreach($child->suggestion->attributes() as $dta) {
					if( !in_array($dta, $foundkeywords) ){
						$foundkeywords[] = $dta;
					}
				}
			}		
		}

		$foundkeywords = array_unique($foundkeywords);
		$x = count($foundkeywords);
		foreach ($foundkeywords as $key => $value) {
			$res.="<tr><td>".$value."</td></tr>";
		}


		$res = "<table id='table_keywords'><thead><tr><td>We found ".$x." keywords phrases for  ".$keyword.".</td></tr></thead>
				<tbody>".$res."</tbody></table><div><br/>
				<a href='javascript:void(0);' class='btn btn-success btn-flat' id='export_keywords' data-keyword='".$keyword."'>
					<i class='fa fa-file-excel-o'></i> Export
				</a>
				<a href='javascript:void(0);' class='btn bg-orange btn-flat' id='import_list' data-keyword='".$keyword."'>
					<i class='fa fa-paste'></i> Import List
				</a>
				</div>";
		$fi = array("count"=>$x, "data" => $res);

		return json_encode($fi);
	}



	// function to generate domains
	function checkDomain($domain){
		// $api_key = "g9iu0nh18a"; // here we set our freedomain api key
		// $url = "http://freedomainapi.com/?key=".$api_key."&domain=".$domain;
		$user = "automatedtoolkit2016";
		$pass = "wala123";

		$url = "https://www.whoisxmlapi.com/whoisserver/WhoisService?cmd=GET_DN_AVAILABILITY&domainName={$domain}&username={$user}&password={$pass}&getMode=DNS_AND_WHOIS";

		// echo $url;
		$data = @file_get_contents($url);

		$xml = simplexml_load_string($data);
		$json = json_encode($xml);

		return $json;

	}


	function getKeywordIdeas(){
		return array(
			'buy',
			'order',
			'get'
		);
	}


/* end Keyword Research module */