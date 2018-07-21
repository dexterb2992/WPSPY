<?php 
class Alexa{
	public static $xml = "";
	public static $domain;
	public static $html;

	public function setXml($domain){
		self::$xml = @simplexml_load_file('http://data.alexa.com/data?cli=10&dat=snbamz&url='.$domain);
		self::$domain = $domain;

		// $html = @file_get_contents("http://www.alexa.com/siteinfo/".urlencode(self::$domain));
		$html = getPageData("http://www.alexa.com/siteinfo/".urlencode(self::$domain));
		self::$html = $html;
	}

	public function getRank(){
		$xml = self::$xml;
		$x = isset($xml->SD[1]->POPULARITY)?$xml->SD[1]->POPULARITY->attributes()->TEXT:0;
		return  number_format((double)$x, 0);
	}

	public function getDelta(){
		$xml = self::$xml;
		$x = isset($xml->SD[1]->POPULARITY)?$xml->SD[1]->RANK->attributes()->DELTA:0;
		return array(number_format((double)$x, 0), substr($x, 0, 1));
	}


	public static function setHtml(){
		$html = @file_get_contents("http://www.alexa.com/siteinfo/".urlencode(self::$domain));
		self::$html = $html;
	}

	public function getGlobalRank(){
		return self::howVisitorsEngaged("globalRank");
	}

	public function getBounceRate(){
		return self::howVisitorsEngaged("bounce_percent");
	}

	public function getDailyPageView(){
		return self::howVisitorsEngaged("pageviews_per_visitor");
	}

	public function getTimeOnSite(){
		return self::howVisitorsEngaged("time_on_site");
	}

	public function getRankInCountry(){
		libxml_use_internal_errors(true);

		if( !isset(self::$html) or empty(self::$html) ){
			self::setHtml();
		}

		if( empty(self::$html) ){
			return array(
				"country_code" => "N/A", 
				"country" => "N/A", 
				"percent_of_visitors" => "N/A", 
				"rank" => "N/A"
			);
		}

		$rank_in_country = array();

		$dom = new DOMDocument;

		$dom->loadHTML(self::$html);
		$tables = $dom->getElementsByTagName("table");

		if (!empty($tables)) {
			foreach ($tables as $table) {
		   
			    $value = (string) $table->getAttribute( 'id' );
			    if ($value == "demographics_div_country_table") {

					$html = DOMinnerHTML($table);

					libxml_use_internal_errors(true);
					$dom = new DOMDocument;
					$dom->loadHTML($html);

					$tbody = $dom->getElementsByTagName('tbody')->item(0);

					// echo '<pre>';

					// print_r($tbody);
					// echo '</pre>'; die;

					$html = DOMinnerHTML($tbody);

					libxml_use_internal_errors(true);
					$dom = new DOMDocument;
					$dom->loadHTML($html);

					$trs = $dom->getElementsByTagName('tr');


					$i=0;
					foreach ($trs as $tr) {
						$str = self::extractHtml($tr);

						$country = str_get_html($str);

						$country_name = str_replace("Ã‚Â", "", $country->find('a', 0)->plaintext);
						$country_code = strtolower( str_replace( "/topsites/countries/", "", $country->find('a', 0)->href ) );

						if( strlen($country_code) > 5 ){
							return array("country_code" => "N/A", "country" => "N/A", "percent_of_visitors" => "N/A", "rank" => "N/A");
						}

						$rank_in_country[$i] = array(
							"country" => $country_name,
							"country_code" => $country_code,
							"percent_of_visitors" => $country->find('td', 1)->plaintext,
							"rank" => $country->find('td', 2)->plaintext
						);
						
						$i++;
					}

					return $rank_in_country;
					
			    }
			}
		}
		
		return array(
			"country_code" => "N/A", 
			"country" => "N/A", 
			"percent_of_visitors" => "N/A", 
			"rank" => "N/A"
		);

	}


	public static function howVisitorsEngaged($option){
		libxml_use_internal_errors(true);

		if( !isset(self::$html) ){
			self::setHtml();
		}

		$dom = new DOMDocument;

		$dom->loadHTML(self::$html);
		$spans = $dom->getElementsByTagName("span");

		$flag = false;

		$index = 0;
		$data = 0;
	    $percentage = 0;
		foreach ($spans as $span) {
		    $index++;
		   

		    $value = (string) $span->getAttribute( 'data-cat' );
		    if ($value == $option) {

				$html = self::extractHtml($span);


				libxml_use_internal_errors(true);
				$dom = new DOMDocument;
				$dom->loadHTML($html);

				$strongs = $dom->getElementsByTagName('strong');
				$spans2 = $dom->getElementsByTagName('span');

				foreach ($strongs as $strong) {
					return getTextFromNode($strong);
				}
				
		    }
		}
		
	}

	public static function extractHtml($tag){
		libxml_use_internal_errors(true);

		$newdoc = new DOMDocument();
		$cloned = $tag->cloneNode(TRUE);
		$newdoc->appendChild($newdoc->importNode($cloned,TRUE));
		return  $newdoc->saveHTML();
	}
}