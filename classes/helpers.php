<?php

function get_sites(){
	global $wpdb;
	$table_name = $wpdb->prefix.'wpspy_activity_log';

	$sql = "SELECT DISTINCT url FROM ".$table_name;
	$res = $wpdb->get_results( $wpdb->prepare($sql, ARRAY_A) );
	$sites = array();
	foreach ($res as $key) {
		if ( (filter_var($key->url, FILTER_VALIDATE_URL) !== FALSE) && 
			( (substr_count($key->url, 'http://') == 1) || (substr_count($key->url, 'https://') == 1) ) ) {
		   array_push($sites, $key->url);
		}
	}
	return $sites;
}

function pre($msg){
	echo "<pre>";
	print_r($msg);
	echo "</pre>";
}

function roundk($number){
	$number = (double)(str_replace(",", "", $number));
	if ($number > 999 && $number <= 999999) {
		$sep = ($number > 1000)? '+' : '';
	    return $result = floor($number / 1000) . 'K'.$sep;
	}elseif ($number > 999999) {
		$sep = ($number > 1000000)? '+' : '';
	    return $result = floor($number / 1000000) . 'M'.$sep;
	}else {
	    return $result = number_format($number);
	}
}

function DOMinnerHTML(DOMNode $element, $strip_tags = false) { 
    $innerHTML = ""; 
    $children  = $element->childNodes;

    foreach ($children as $child){ 
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }

    if( $strip_tags == true ){
    	 return strip_tags($innerHTML); 
    }
    return $innerHTML;
} 

function getLinks($domain){
	$internal = array();
	$external = array();
	$html = getPageData($domain);

	libxml_use_internal_errors(true);

	$dom = new DOMDocument;

	@$dom->loadHTML($html);

	$anchors = $dom->getElementsByTagName("a");

	$nfollow_internal = 0;
	$nfollow_external = 0;
	$url_parsed = parse_url($domain);
	// extract internal and external links count
		foreach ($anchors as $anchor) {
			$href = $anchor->getAttribute("href");
			$parse_url = parse_url($href);
			
			if( isset($parse_url["host"]) ){
				if( $url_parsed["host"] == $parse_url["host"] ){
					array_push($internal, array("url" => $href, "text" => DOMinnerHTML($anchor)));
					if( $anchor->getAttribute("rel") == "nofollow" ){
						$nfollow_internal++;
					}
				}else{
					array_push($external, array("url" => $href, "text" => DOMinnerHTML($anchor)));
					if( $anchor->getAttribute("rel") == "nofollow" ){
						$nfollow_external++;
					}
				}
			}
		}

	return array("internal_links" => array("nofollow" => $nfollow_internal, "links" => $internal), 
				"external_links" =>array("nofollow" => $nfollow_external, "links" => $external) );
}

function getPageData($url) {
	if(function_exists('curl_init')) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if((ini_get('open_basedir') == '') && (ini_get('safe_mode') == 'Off')) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		return @curl_exec($ch);
	}else {
		return @file_get_contents($this->url);
	}
}


