<?php

class Ahrefs{

	public static $html;

	public function setHtml($url){
		self::$html = getPageData("https://ahrefs.com/site-explorer/export/csv/subdomains/?target=".substr($url, 7),  false, $context);	
	}

	public function getBackLinks(){
		return $this->getInfo("raw_export_type_backlinks");
	}

	public function getReferringDomains(){
		return $this->getInfo("raw_export_type_referring_domains");
	}

	public function getReferringIps(){
		return $this->getInfo("raw_export_type_referring_ips");
	}


	public function getFBLikes(){
		return $this->getInfo("soc_stats_flikes_user_query");
	}

	public function getFBShares(){
		return $this->getInfo("soc_stats_fshares_user_query");
	}

	public function getTweets(){
		return $this->getInfo("soc_stats_tweets_user_query");
	}

	public function getGPlus(){
		return $this->getInfo("soc_stats_gplus_user_query");
	}
	
	public function getInfo($type){
		libxml_use_internal_errors(true);

		if( !isset(self::$html) ){
			return "Please specify the url.";
		}


		$dom = new DOMDocument;

		$dom->loadHTML(self::$html);


		switch ($type) {
			case 'raw_export_type_referring_ips':
			case 'raw_export_type_referring_domains':
			case 'raw_export_type_backlinks':
				$tag = "label";
				$attribute = "for";
				break;

			case 'soc_stats_flikes_user_query': 	// get fb likes
			case 'soc_stats_tweets_user_query': 	// get tweets
			case 'soc_stats_fshares_user_query':	// get fb shares
			case 'soc_stats_gplus_user_query': 		// get gplus
				$tag = "p";
				$attribute = "id";
				break;

			default:
				break;
		}

		$tags = $dom->getElementsByTagName($tag);

		$index = 0;

		foreach ($tags as $tag) {
		    $index++;
		    $value = (string) $tag->getAttribute( $attribute );
		    if ($value == $type) {
		    	$temp = $tag->nodeValue;
		    	return number_format(filter_numbers($temp), 0); // filter response to allow only numbers
		    }
		}
		return 0;
	}

}	
