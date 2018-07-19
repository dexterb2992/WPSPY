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
		array_push( $date, array("label" => date( 'M j, Y, g:i a', $d )) );
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
