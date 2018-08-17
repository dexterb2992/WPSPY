<?php

if( !function_exists('getPageData') ){
	include ('helpers.php');
}

require("page_rank.php");

class WpSpy{
	public static $total = 0;
	public static $progress = 0;
	public static $y = 1;
	public static $scripts, $metas, $links, $imgs, $url;
	
	public static $plugins = array();				// all plugins including free and commercial
	public static $theme; 					// value will be theme's name
	public static $free_plugins = array();			// free plugins only
	public static $commercial_plugins = array(); 	// commercial plugins only
	public static $data;							// html from url

	public static $is_wp;
	public static $show_progress;



	/* Initialize all variables here */

	static function init($url){


		libxml_use_internal_errors(true);
		self::checkUrl($url);

		self::$url = $url;
		self::$data = getPageData(self::$url);

		libxml_use_internal_errors(true);

		$dom = new DOMDocument;

		@$dom->loadHTML(self::$data);

		self::$scripts = $dom->getElementsByTagName("script");
		self::$metas = $dom->getElementsByTagName('meta');
		self::$imgs = $dom->getElementsByTagName("img");
		self::$links = $dom->getElementsByTagName("link");

	}

	static function checkUrl($url){
		$file = $url."/wp-admin/index.php";

		$file_headers = @get_headers($file);
		if($file_headers[0] == 'HTTP/1.1 404 Not Found') {

			
		   return self::$is_wp = false;
		}
		
		return self::$is_wp = true;
	}

	static function getinfo(){
		self::getAllPlugins(); 									// Get all possible plugins
	}

	static function disableProgress(){
		self::$show_progress = false;
	}

	 /**
	* find all the links and scripts that contains wp-content/(themes or plugins)
	* @param $tags = array
	* @param $theme = array
	* @param $plugins = array
	*/
	static function findThemAll($tags, $attr){
		if(self::$is_wp == false){
			return 0;
		}

		$index = 0;
		$plugins = array();
		$theme = array();
		foreach ($tags as $tag) {
		    $index++;
	    	self::calculateProgress();
	    
	    	$value = (string) $tag->getAttribute( $attr ); //check for script src
	       	
       		preg_match("~wp-content/(.*?)plugins/(.*?)/~", $value, $output);
	
			if(count($output)>0){
				array_push(self::$plugins, isset($output[2]) ? $output[2] : $output[1] );
			}

			if( !isset(self::$theme) ){

				preg_match("~wp-content/themes/(.*?)/~", $value, $output2);
				if(count($output2)>0){
					self::$theme = $output2[1];
				}
			}

			$value2 = (string) $tag->getAttribute( 'href' ); //check for link hrefs

			preg_match("~wp-content/(.*)plugins/(.*?)/~", $value2, $output);
	
			if(count($output)>0){

				array_push(self::$plugins, isset($output[2]) ? $output[2] : $output[1] );
			}

			if( !isset(self::$theme) ){
				preg_match("~wp-content/themes/(.*?)/~", $value2, $output2);
				if(count($output2)>0){
					self::$theme = $output2[1];
				}
			}
		}	
	}

	
	static function getAllPlugins(){
		if(self::$is_wp == false){
			return 0;
		}

		self::$total += count(self::$scripts);							// update progress total items
		self::findThemAll(self::$scripts, "src"); 						// find for scrpts containing wp-content/plugins or themes
		

		self::$total += count(self::$links);							// update progress total items
		self::findThemAll(self::$links, "href"); 						// find for links containing wp-content/plugins or themes
		
		
		self::$total = count(self::$imgs);								// update progress total items
		self::findThemAll(self::$imgs, "src"); 							// find for imgs containing wp-content/plugins or themes
		

		self::$plugins = array_unique(self::$plugins);  				// remove duplicates on plugins
		self::$total += count(self::$plugins);							// update progress total items


		foreach (self::$plugins as $plugin => $value) {
			self::$y++;

			self::calculateProgress();


			if(self::checkThemePlugin($value, 'plugins') == true){
				array_push(self::$free_plugins, $value);
			}else{
				array_push(self::$commercial_plugins, $value);
			}
		}
	}

	static function getFreePlugins(){
		if(self::$is_wp == false){
			return 0;
		}

		if ( count(self::$free_plugins) < 1 ){
			return 0;
		}

		$free_plugins = array();

		foreach (self::$free_plugins as $plugin => $value) {
			$dl_link = self::downloadThemePlugin($value, 'plugins');

			$val = array(
				"name" => $value,
				"link" => "https://wordpress.org/plugins/".$value,
				"download" => $dl_link
			);
			array_push($free_plugins, $val);
		}

		$_SESSION[self::$url]['free_plugins'] = $free_plugins;

		return $free_plugins;
	}


	static function getCommercialPlugins(){
		if(self::$is_wp == false){
			return 0;
		}

		if ( count(self::$commercial_plugins) < 1 ){
			return 0;
		}

		$commercial_plugins = array();
		foreach (self::$commercial_plugins as $plugin => $value) {

			$val = array(
				"name" => $value,
				"link" => "N/A",
				"download" =>  "N/A"
			);

			array_push( $commercial_plugins, $val);
		}

		$_SESSION[self::$url]['commercial_plugins'] = $commercial_plugins;

		return $commercial_plugins;
	}

	static function getTheme(){
		if(self::$is_wp == false){
			return 0;
		}

		$site_theme = array("name", "type", "link", "download");

		if( self::checkThemePlugin(self::$theme, 'themes') == true ){
			$dl_link = self::downloadThemePlugin(self::$theme, 'themes');

			$site_theme = array(
				"name"=> self::$theme, 
				"type"=>"Free", 
				"link"=> 'https://wordpress.org/themes/'.self::$theme, 
				"download" => $dl_link
			);


		}else{

			$site_theme = array(
				"name"=> self::$theme, 
				"type"=>"Commercial", 
				"link"=> 'N/A', 
				"download" => 'N/A '
			);
			
		}

		$_SESSION[self::$url]['theme'] = $site_theme;

		return $site_theme;
	}


	/**
	* get the site description and keywords
	*  @param $metas = meta tags  
	*  @param $name = name of meta tag to get
	*/ 
	static function getMeta($metas, $name){
		foreach ($metas as $meta) {
			$value = (string) $meta->getAttribute( 'name' );
			if($value == $name){
				return (string) $meta->getAttribute( 'content' );
			}
		}
	}


	static function getSiteDescription(){
		$_SESSION[self::$url]['description'] = self::getMeta(self::$metas, "description");
		return  $_SESSION[self::$url]['description'];
	} 

	static function getSiteKeywords(){
		$_SESSION[self::$url]['keywords'] = self::getMeta(self::$metas, "keywords");
		return $_SESSION[self::$url]['keywords'];
	}

	static function checkThemePlugin($plugin_theme, $type){
		if(self::$is_wp == false){
			return 0;
		}

		if($type == "plugins"){
			$file = 'https://wordpress.org/plugins/'.$plugin_theme;
			$file_headers = @get_headers($file);
			if($file_headers[0] == 'HTTP/1.1 404 Not Found') {

				
			    return false;  // Return false if 404
			}
			
			return true; // Return true if url is valid

		}else{

			libxml_use_internal_errors(true);

			$html = getPageData('https://wordpress.org/themes/'.$plugin_theme);

			$dom = new DOMDocument;
			@$dom->loadHTML($html);
			$lis = $dom->getElementsByTagName("li");

			$flag = false;

			$index = 0;

			foreach ($lis as $li) {
			    $index++;

			    $value = (string) $li->getAttribute( 'class' );
			    if ($value == "section-description" || $value == "section-description current") {
			       return true;
			    }
			}
			return false;

		}
	}


	

	static function downloadThemePlugin($plugin_theme, $type){
		libxml_use_internal_errors(true);
		$url = "https://wordpress.org/".$type."/".$plugin_theme;
		$html = getPageData($url);

		$dom = str_get_html($html);

		if ($type == 'plugins') {
			$class = "plugin-download";
		} else {
			$class = "theme-install";
		}

		$anchor = $dom->find(".$class", 0);

		return isset($anchor->href) ? $anchor->href : "N/A";
		
	}

	static function calculateProgress(){
		if(self::$show_progress == false){
			return "";
		}
		
		$a = self::$y;
		$b = self::$total;

		self::$progress = intval($a/$b * 100)."%";

		

		echo '<script data-class="progressbar">
				$(".progress").html("<div class=\"progress-bar progress-bar-striped active\" id=\"progressbar\" role=\"progressbar\" aria-valuenow=\"'.substr(self::$progress, 0, strlen(self::$progress)-1).'\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: '.self::$progress.'\"> '.self::$progress.'</div>");
			</script>';
		echo " ";

	    // This is for the buffer achieve the minimum size in order to flush data
	    echo str_repeat(' ',1024*64);
	    

		// Send output to browser immediately
		flush();
	}

	static function getVersion(){
		
		$scripts = self::$scripts;

		$res = array();

		$version = self::getMeta(self::$metas, "generator");
		if($version != "" || $version != null){
			$mystring = $version;
			$findme   = 'WordPress';
			$pos = strpos($mystring, $findme);

			// Note our use of ===.  Simply == would not work as expected
			// because the position of 'a' was the 0th (first) character.
			if ($pos === false) {
			    $version = 0;
			} else {
				if( strpos($mystring, "WordPress.com") !== false ){
					$version = trim( str_replace("WordPress.com", "", substr($version, $pos)) );
					if( $version != "" ){
						return $version;
					}
				}
			   	

				if( strpos($mystring, "WordPress") !== false ){
					$version = trim( str_replace("WordPress", "", substr($version, $pos)) );
					if( $version != "" ){
						return $version;
					}
				}

			}
		}else{
			$version = 0;
		}

		foreach ($scripts as $script) {
			$value = (string) $script->getAttribute( 'src' ); //check for link src
			preg_match('/(?<=ver=)[\d.]+/', $value, $output);

			if( count($output)>0 ) array_push($res, $output[0]);
		}

		foreach (self::$links as $link) {
			$value = (string) $link->getAttribute( 'href' ); //check for link src
			preg_match('/(?<=ver=)[\d.]+/', $value, $output);

			if( count($output)>0 ) array_push($res, $output[0]);
		}

		$temp = array_count_values($res);

		$greater = 0;
		$version = 0;

		$versionCheck = getPageData("https://api.wordpress.org/core/version-check/1.7/", false);
		

		if( $versionCheck != false && $versionCheck != "" ){
			$wpvs = json_decode($versionCheck);
			$wordpresses = $wpvs->offers;
		}

		foreach ($temp as $key => $value) {
			foreach ($wordpresses as $key => $wordpress) {
				if( $value == $wordpress->version ){
					return $value;
				}
				if( $value == $wordpress->new_bundled ){
					return $value;
				}
			}
		}

		return $version != 0 ? $version : 'N/A';
	}

}