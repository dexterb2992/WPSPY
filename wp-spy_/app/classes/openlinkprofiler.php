<?php 
if( !function_exists('file_get_html') ){
	include "simple_html_dom.php";
}

if( !function_exists('getPageData') ){
	include "helpers.php";
}


class OpenLinkProfiler{

	function __construct ($url = NULL, $page = 1, $num = 20) {
		if ($url !== NULL){
			$this->url = $url;
			$this->page = $page;
			$this->num = $num;
		}
    }

  //   function getBacklinks(){
  //   	$this->html = getPageData("http://openlinkprofiler.org/r/".$this->url."?page=".$this->page."&num=".$this->num);

  //   	libxml_use_internal_errors(true);

		// $dom = new DOMDocument;

		// @$dom->loadHTML($this->html);

		// $tables = $dom->getElementsByTagName("table");

		// $flag = false;

		// $index = 0;

		// foreach ($tables as $table) {
		//     $index++;

		//     $value = (string) $table->getAttribute( 'class' );
		//     if ($value == "linktext") {
		//     	$content = $dom->saveHTML($table); 

		//     	$ps = $dom->getElementsByTagName("p");

		//     	foreach ($ps as $p) {
		//     		$value = (string) $p->getAttribute( 'style' );
		// 		    if ($value == "margin-top:20px;") {
		// 		    	$this->pagination = $dom->saveHTML($p);
		// 		    }
		//     	}

		//     	return $content;
		//     }
		// }
		
  //   }

    function getBackLinks(){
    	$this->html = getPageData("http://openlinkprofiler.org/r/".$this->url."?page=".$this->page."&num=".$this->num);
    	$html = str_get_html($this->html);

    	$content = $html->find("table", 0);
    	$pagination = $html->find("ul.pagination", 0);
    	$this->pagination = $pagination->outertext;
    	// echo $content->outertext;
    	return $content->outertext;
    }

	function getPagination(){
		return $this->pagination;
	}
}