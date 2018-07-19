<?php

function save_this_activity($url, $wpspy_activity) {
    global $wpdb;
    $wpdb->show_errors = true; // true : show errors in development
    $table_name = $wpdb->prefix.'wpspy_activity_log';
    $date_now = date("Y-m-d H:i:s");
    $sql = "SELECT id, activity_date FROM ".$table_name." WHERE (url='$url') AND (DATE_FORMAT(activity_date,'%Y-%m-%d') = DATE_FORMAT('$date_now', '%Y-%m-%d')) ORDER BY id DESC LIMIT 1";
	$res = $wpdb->get_results($sql);

	$wpspy_activity["activity_date"] = $date_now;

	if( !isset($wpspy_activity["url"]) ){
		$wpspy_activity["url"] = $url;
	}

	foreach ($wpspy_activity as $key => $value) {
		$wpspy_activity[$key] = (string)$value;
		if( $value === null ){
			unset($wpspy_activity[$key]);
		}
	}

   	if( $res != 0 && !empty($res) ){
   		// Update old record in the same day
	        $where = array('id' => $res[0]->id);
	        if( $wpdb->update( $table_name, $wpspy_activity, $where ) ){
	        	return true;
	        }
	        return false;
   	}

	// Insert new record
	    if( $wpdb->insert( $table_name, $wpspy_activity ) ){
	    	return true;
	    }
	    return false;	
}

function get_history_all($url){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_log';

	$sql = "SELECT id, DATE_FORMAT(activity_date,'%W, %M %e, %Y @ %h:%i %p') as formatted_activity_date,
	 		activity_date FROM ".$table_name." WHERE url = '".$url."' ORDER BY id DESC";
	$res = $wpdb->get_results( $sql, ARRAY_A );
	if(!empty($res)){
		return $res;
	}

	return false;
}

function getToolsLimit(){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_log';

	$sql = "SELECT id, DATE_FORMAT(activity_date,'%W, %M %e, %Y @ %h:%i %p') as formatted_activity_date,
	 		activity_date FROM ".$table_name." WHERE url = '".$url."' ORDER BY id DESC";
	$res = $wpdb->get_results( $sql, ARRAY_A );
	if(!empty($res)){
		return $res;
	}
}

function get_site_metrics($url){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_log';

	$sql = "SELECT alexa_rank, google_page_rank, quantcast_traffic_rank, alexa_rank_in_country, 
			bounce_rate, dailytime_onsite, daily_pageviews_per_visitor 
			FROM ".$table_name." WHERE url='".$url."' ORDER BY activity_date DESC LIMIT 1";

	$res = $wpdb->get_results( $wpdb->prepare($sql, ARRAY_A) );
	
	if( count($res) > 0 ){
		return array(
			"alexa_rank" => $res[0]->alexa_rank,
			"google_page_rank" => $res[0]->google_page_rank,
			"quantcast_traffic_rank" => $res[0]->quantcast_traffic_rank,
			"alexa_rank_in_country" => $res[0]->alexa_rank_in_country,
			"bounce_rate" => $res[0]->bounce_rate,
			"dailytime_onsite" => $res[0]->dailytime_onsite,
			"daily_pageviews_per_visitor" => $res[0]->daily_pageviews_per_visitor
		);
	}

	return array(
		"alexa_rank" => "N/A",
		"google_page_rank" => "N/A",
		"quantcast_traffic_rank" => "N/A",
		"alexa_rank_in_country" =>"N/A",
		"bounce_rate" => "N/A",
		"dailytime_onsite" => "N/A",
		"daily_pageviews_per_visitor" => "N/A"
	);
	
}

function getRecommendedToolsLimit(){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_settings';

	$sql = "SELECT recommended_tools_limit FROM $table_name ORDER BY id DESC LIMIT 1";
	$res = $wpdb->get_row($sql);
	if( !empty($res) && isset($res->recommended_tools_limit) ){
		return $res->recommended_tools_limit;
	}
	return 10;
}


function checkDataStatus($option, $url){
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
						h1, h2, bold_strong, italic_em, body";

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
		if( $id == "" ) { 
			return "false";	
		}

		$sql = "SELECT ".$columns." FROM ".$table_name." WHERE id = '".$id."'";
		$results = $wpdb->get_results($sql, ARRAY_A);
		return @$results[0];
		
 	}else{
 		return "false";
 	}

}
