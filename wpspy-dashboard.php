<?php 

	// include "site-info.php";
	

	$page = 'wpspy-site-info';
	include plugin_dir_path( __FILE__ )."classes/config.php";
	include plugin_dir_path( __FILE__ )."_nav.php"; 
	include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
	include plugin_dir_path( __FILE__ )."classes/data.php";

	$parsed = parse_url($_GIVEN_URL);

	if (!isset($parsed['host'])) {
		$domain = $parsed['path'];
	} else {
		$domain = $parsed['host'];
	}

	// echo $url = "https://www.bing.com/search?q=site:$domain&go=Submit&qs=bs&form=QBRE&scope=web";
	echo $url = "https://www.bing.com/search?q=site:$domain";
	$result_in_html = getPageData($url);


	
	echo $result_in_html;
	libxml_use_internal_errors(true);
	$dom = new DOMDocument;

	$dom->loadHTML($result_in_html);
	$tags = $dom->getElementsByTagName('span');
	
	$count = 0;

	foreach ($tags as $tag) {
	    $value = (string) $tag->getAttribute( 'class' );
	    if ($value == 'sb_count') {
	    	$temp = $tag->nodeValue;
	    	echo $temp; echo '<br>';
	    	$count= number_format(filter_numbers($temp), 0); // filter response to allow only numbers
	    }
	}

	var_dump("COUNT: $count");

// 	$dom = str_get_html($html);
// echo $dom->plaintext;
// 	$res = $dom->find('span.sb_count', 0);

// 	var_dump($res->innertext);