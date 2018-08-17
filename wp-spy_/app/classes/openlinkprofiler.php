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