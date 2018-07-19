<?php
error_reporting(E_ALL);

require CLASS_FOLDER."helpers.php";
if(!function_exists("file_get_html")){
	require CLASS_FOLDER."simple_html_dom.php";
}
require CLASS_FOLDER."wp-spy.php";
require CLASS_FOLDER."alexa.php";
require CLASS_FOLDER."ahrefs.php";
require CLASS_FOLDER."socialstats/socialstats.php";
require CLASS_FOLDER."seostats/seostatsClass.php";
require CLASS_FOLDER."openlinkprofiler.php";


function getSocialMention($key){
	$social = new SocialStat();

	return $social->social_mention($key, false);
}


function getSocialMentionLinks($key){
	$social = new SocialStat();
	return $social->social_mention_links($key, false);
}


function getWHOIS($domain, $format='json'){
	$whois = array();
	$geolocation = array();
	$dns = array();
	$parse_url = parse_url($domain);


	/* get DNS */

		$result = dns_get_record($parse_url['host']);
		foreach ($result as $key) {
			if($key['type'] == "NS"){
				$dns[] = $key["target"];
			}
		}

		$whois["dns"] = $dns;


	// get IP Address
		$geolocation["ip"] = gethostbyname($parse_url['host']);

	/* get geolocation */
		$data = json_decode( @file_get_contents("http://www.geoplugin.net/json.gp?ip=".$geolocation["ip"]) );
		$geolocation["ip"] = $geolocation["ip"];
		$geolocation["country"] = $data->geoplugin_countryName;
		$geolocation["city"] = $data->geoplugin_city;
		$geolocation["region"] = $data->geoplugin_regionName;
		$geolocation["country_code"] = strtolower($data->geoplugin_countryCode);
		$whois["geolocation"] = $geolocation;

	if ( $format == "json" ){
		return json_encode($whois);
	}else{
		return $whois;
	}
}

function getOnSite($domain, $format = 'json'){
	/* check for robots.txt */
		$robot = if_file_exists($domain."/robots.txt");
	/* check for sitemap_index.xml */
		$sitemap_index = if_file_exists($domain."/sitemap.xml");

	if( $format == "json" ){
		return json_encode( array('robot' => $robot, 'sitemap_index' => $sitemap_index ) );
	}else{
		return array('robot' => $robot, 'sitemap_index' => $sitemap_index );
	}

}

function getWordpressData($domain, $format = 'json'){
	$wordpress_data = array();
	if( WpSpy::checkUrl($domain) == false){
		return json_encode( array("status" => "500", "msg" => "Plugins and Themes information not available. \n This is not a wordpress site.") );
	}

	WpSpy::init($domain);
	WpSpy::disableProgress(); // make sure not to return progress status
	WpSpy::getInfo();
	$wordpress_data["version"] = WpSpy::getVersion();
	$wordpress_data["free_plugins"] = WpSpy::getFreePlugins();
	$wordpress_data["commercial_plugins"] = WpSpy::getCommercialPlugins();
	$wordpress_data["theme"] = WpSpy::getTheme();
	$wordpress_data["keywords"] = capitalize(WpSpy::getSiteKeywords());
	$wordpress_data["description"] = capitalize(WpSpy::getSiteDescription());
	

	$array2 = array_map(function($value) {
	   return is_null($value) ? "N/A" : $value;
	}, $wordpress_data);

	if( $format == "json" ){
		return json_encode($array2);	
	}else{
		return $array2;
	}
}

function getPageInfo($domain, $format = 'json'){
	$data = getPageData($domain);

	$page_info = array();
	$arr_meta = array();
	$internal = array();
	$external = array();
	$internal_links = array();
	$external_links = array();
	$arr_h1s = "";
	$arr_h2s = "";
	$arr_bolds = "";
	$arr_ems = "";
	$arr_strongs = "";
	$arr_ems = "";
	$arr_italics = "";

	libxml_use_internal_errors(true);

	$dom = new DOMDocument;

	@$dom->loadHTML($data);

	$titles = $dom->getElementsByTagName("title");
	$metas = $dom->getElementsByTagName('meta');
	$imgs = $dom->getElementsByTagName("img");
	$links = $dom->getElementsByTagName("link");
	$h1s = $dom->getElementsByTagName("h1");
	$h2s = $dom->getElementsByTagName("h2");
	$bolds = $dom->getElementsByTagName("b");
	$strongs = $dom->getElementsByTagName("strong");
	$italics = $dom->getElementsByTagName("i");
	$anchors = $dom->getElementsByTagName("a");
	$ems = $dom->getElementsByTagName("em");

	// extract title tag
		foreach ($titles as $title) {
			$page_info["title"] = DOMinnerHTML($title);
		}

	// extract meta tags
		foreach ($metas as $meta) {
			$content = (string) $meta->getAttribute( "content" ); //check for script src
			$name = (string) $meta->getAttribute( "name" );       	
			
			if( $name == "keywords" || $name == "description" || $name == "robots" ){
				array_push($arr_meta, array($name => $content));
			}
		}

	// extract canonical url
		foreach ($links as $link) {
			if( $link->getAttribute("rel") == "canonical" ){
				$page_info["canonical_url"] = $link->getAttribute("href");
			}
		}

	// extract text inside the body tag
		$html = str_get_html($data);
		if( is_object($html) ){
			$body = preg_replace('!\s+!', ' ', strip_tags( trim($html->find('body', 0)->plaintext) ) );
			$page_info["body"] = array("content" => limitString($body), "length" => strlen($body));
		}else{
			$page_info["body"] = "-";
		}

	$url_parsed = parse_url($domain);

	$nfollow_internal = 0;
	$nfollow_external = 0;
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

	$external_links = array("nofollow" => $nfollow_internal, "links" => $internal);
	$internal_links = array("nofollow" => $nfollow_external, "links" => $external);

	// extract h1
		foreach ($h1s as $h1) {
			$arr_h1s = $arr_h1s.DOMinnerHTML($h1)."\n";
		}

	// extract h2
		foreach ($h2s as $h2) {
			$arr_h2s = $arr_h2s.DOMinnerHTML($h2)."\n";
		}

	// extract bold
		foreach ($bolds as $bold) {
			$arr_bolds = $arr_bolds.DOMinnerHTML($bold)."\n";
		}

	// extract strong
		foreach ($strongs as $strong) {
			$arr_strongs = $arr_strongs.DOMinnerHTML($strong)."\n";
		}

	// extract em
		foreach ($ems as $em) {
			$arr_ems = $arr_ems.DOMinnerHTML($em)."\n";
		}

	// extract italics
		foreach ($italics as $italic) {
			$arr_italics = $arr_italics.DOMinnerHTML($italic)."\n";
		}

	$page_info["meta"] = $arr_meta;
	$page_info["external_links"] = $external_links;
	$page_info["internal_links"] = $internal_links;
	$page_info["h1"] = $arr_h1s;
	$page_info["h2"] = $arr_h2s;
	$page_info["bold_strong"] = $arr_bolds.$arr_strongs;
	$page_info["italic_em"] = $arr_italics.$arr_ems;
	if( $format == 'json' ){
		return json_encode($page_info);
	}else{
		return $page_info;
	}
}	


function getSeoStats($domain, $format = 'json'){
	$alexa = new Alexa();
	$seo = new SEOStats($domain);
	// $ahrefs = new Ahrefs();

	$alexa->setXml($domain);
	// $ahrefs->setHtml($domain);

	$_url = parse_url($domain);

	$seostats = array();
	$seostats["rank"] = array( 
		"alexa_traffic_rank" => $alexa->getRank(),
		"quantcast_traffic_rank" => $seo->QuantcastRank(),
		"google_page_rank" => $seo->get_PR(),
		"alexa_rank_in_country" => $alexa->getRankInCountry()
	);

	$seostats["backlinks"] = array(
		"alexa" => "http://www.alexa.com/siteinfo/".$domain,
		"open_site_explorer" => "https://moz.com/researchtools/ose/links?site=".urlencode($domain),
		"google" => $seo->get_GBL(),
		// "ahrefs" => ($ahrefs->getBackLinks() > 0) ? $ahrefs->getBackLinks() : 'https://ahrefs.com/site-explorer/export/csv/subdomains/?target='.substr($domain, 7),
		"ahrefs" => '<span title="Not supported anymore.">(Not supported)</a>',
		"sogou" => ($seo->get_SogouBL() != "N/A") ? $seo->get_SogouBL() : "http://www.sogou.com/web?query=link: ".$_url["host"]
		// "sogou" => "http://www.sogou.com/web?query=link: ".urlencode($domain)
	);

	$seostats["pages_indexed"] = array(
		"ask" => "http://www.ask.com/web?q=".urlencode($domain),
		"baidu" => $seo->get_BaiduIP(),
		"bing" => $seo->get_BingIP(),
		"goo" => $seo->get_GooIP(),
		"google" => $seo->get_GIP(),
		"sogou" => $seo->get_SogouIP(),
		// "sogou" => "http://www.sogou.com/web?query=link: ".urlencode($domain),
		"yahoo" => $seo->get_YahooIP(),
		"yandex" => $seo->get_YandexIp(),
		"_360" => $seo->get_360Ip()
	);

	$seostats["site_metrics"] = array(
		"bounce_rate" => $alexa->getBounceRate(),
		"dailytime_onsite" => $alexa->getDailyPageView(),
		"daily_pageviews_per_visitor" => $alexa->getTimeOnSite()
	);

	$seostats["cached"] = $seo->get_Cached();

	if( $format == 'json' ){
		return json_encode($seostats);
	}else{
		pre($seostats);
	}
}

function getSociaLStats($domain, $format = 'json'){
	$social = new SocialStat();
	$social_stats = array();
	$social_stats["social_shares"]['facebook_count'] = $social->facebook_counter($domain, false);
	$social_stats["social_shares"]['twitter_count'] = $social->twitter_counter($domain, false);
	$social_stats["social_shares"]['google_count'] = $social->gplus_counter($domain, false);
	$social_stats["social_shares"]['linkedin_count'] = $social->linkedin_counter($domain, false);
	$social_stats["social_shares"]['pinterest_count'] = $social->pinterest_counter($domain, false);
	$social_stats["social_shares"]['stumbleupon_count'] = $social->stumbleupon_counter($domain, false);
	return json_encode($social_stats);
}


// *** SPECIFIED DATA FOR SPEED ADVANCEMENT IN DATA GRABBING PURPOSES ****
	function get_alexa_traffic_rank($domain){
		$alexa = new Alexa();
		$alexa->setXml($domain);
		return $alexa->getRank();
	}

	function get_quantcast_rank($domain){
		$seo = new SEOStats($domain);
		return $seo->QuantcastRank();
	}

	function get_google_page_rank($domain){
		$seo = new SEOStats($domain);
		return $seo->get_PR();
	}
