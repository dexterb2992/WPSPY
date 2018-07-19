<?php
class SEOStats
{
	private $url;
	
	public function __construct($url) {
	    $this->url = $url;
	    if (strpos($url, "https://") !== false) {
	    	$this->url_domain = substr($this->url, 8);
	    } else if (strpos($url, "http://") !== false) {
	    	$this->url_domain = substr($this->url, 7);
	    } else {
	    	$this->url_domain = $this->url;
	    }
	}
	public function get_PR() {
		// $query="https://toolbarqueries.google.com/tbr?client=navclient-auto&ch=".$this->CheckHash($this->HashURL($this->url))."&features=Rank&q=info:".$this->url."&num=100&filter=0";
		// echo $query;
		// $data=getPageData($query);
		// $pos = strpos($data, "Rank_");
		// if($pos === false){return 0;} else{
		// 	$pagerank = substr($data, $pos + 9);
		// 	return $pagerank;
		// }
		return "<span title='As of March 7th 2016, Google has removed the public PageRank metric completely.'>(Not supported)</span>";
	}
	public function get_GIP(){
		// echo $query='https://ajax.googleapis.com/ajax/services/search/web?v=1.0&q='.urlencode('site:'.$this->url_domain).'&filter=0&rsz=1';

		// echo $query = "https://www.googleapis.com/customsearch/v1?key=AIzaSyD32PIMgkd78HoZRXSFWf6sD68aAOJOa38&cx=006523864226390021390:h8mnbbe44qs&q=site:".$this->url.'&filter=0&rsz=1';

		// $data=getPageData($query);
		// $data=json_decode($data,true);
	    // return isset($data['responseData']['cursor']['estimatedResultCount'])?$data['responseData']['cursor']['estimatedResultCount']:0;
		
		// echo $url = "https://www.google.com.ph/search?q=links:%20+%22".$this->url."%22";

		$aContext = array(
		    'http' => array(
		        'proxy' => '23.24.218.69:7004',
		        'request_fulluri' => true,
		    ),
		);
		$cxContext = stream_context_create($aContext);

		
		$url = 'https://www.google.com/search?num=100&&source=hp&q=site:'.$this->url_domain;
		$result_in_html = getPageData($url);

		if (preg_match('/Results .*? of about (.*?) from/', $result_in_html, $regs)){
		    $indexed_pages = trim(strip_tags($regs[1])); //use strip_tags to remove bold tags
		    return $indexed_pages;
		} elseif (preg_match('/About (.*?) results/', $result_in_html, $regs)){
		    $indexed_pages = trim(strip_tags($regs[1])); //use strip_tags to remove bold tags
		    return $indexed_pages;
		} else{
		    return 'N/A';
		}
	}
	public function get_GBL(){
		// $query="https://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=link:".$this->url_domain."%20site:".$this->url_domain."&filter=0&rsz=1";
		// $data=getPageData($query);

		// $data=json_decode($data,true);
	 	// return isset($data['responseData']['cursor']['estimatedResultCount'])?$data['responseData']['cursor']['estimatedResultCount']:0;
		
		$url = 'https://www.google.com/search?hl=en&source=hp&q=link:'.$this->url_domain."%20site:".$this->url_domain;

		$result_in_html = @getPageData($url);

		$dom = new DOMDocument;

		$dom->loadHTML($result_in_html);
		$tags = $dom->getElementsByTagName('div');
		$count = 0;

		foreach ($tags as $tag) {
		    $value = (string) $tag->getAttribute( 'id' );
		    if ($value == 'resultStats') {
		    	$result_in_html = DOMinnerHTML($tag);
		    	break;
		    }
		}
		
		if (preg_match('/Results .*? of about (.*?) from/', $result_in_html, $regs)){
		    $indexed_pages = trim(strip_tags($regs[1])); //use strip_tags to remove bold tags
		    return $indexed_pages;
		}elseif( preg_match('/About (.*?) results/', $result_in_html, $regs) ){
		    $indexed_pages = trim(strip_tags($regs[1])); //use strip_tags to remove bold tags
		    return $indexed_pages;
		}elseif( preg_match('/(.*?) results/', $result_in_html, $regs) ){
			$indexed_pages = trim(strip_tags($regs[1])); //use strip_tags to remove bold tags
		    return $indexed_pages;
		}else{
		    return 'N/A';
		}	
	
	}

	public function get_SEM(){
		$query = 'https://us.backend.semrush.com/?action=report&type=domain_rank&domain='.$this->url;
		$data=getPageData($query);
		$data=json_decode($data,true);

		if(isset($data['rank']['data'][0]))
			return array('rank'=>$data['rank']['data'][0]['Rk'],'keywords'=>$data['rank']['data'][0]['Or'],'traffic'=>$data['rank']['data'][0]['Ot'],'cost'=>$data['rank']['data'][0]['Oc']);
		else
			return array('rank'=>'NA','keywords'=>'NA','traffic'=>'NA','cost'=>'NA');
	}
	 public function get_Alexa(){
		 $query="https://data.alexa.com/data?cli=10&dat=snbamz&url=".$this->url;
		 $data=getPageData($query);
		 $rank = preg_match("/<POPULARITY[^>]*TEXT=\"([\d]*)\"/",$data,$match)?$match[1]:0;
		 $speed = preg_match("/<SPEED[^>]*TEXT=\"([\d]*)\"/",$data,$match)?$match[1]:0;
		 $isdmoz=preg_match("/FLAGS=\"DMOZ\"/",$data,$match)?1:0;
		 $links=preg_match("/<LINKSIN[^>]*NUM=\"([\d]*)\"/",$data,$match)?$match[1]:0;
		 return array(
		 	"rank" 	=> $rank,
		 	"dmoz"	=> $isdmoz,
		 	"links"	=> $links,
		 	"speed"	=> $speed);
	 }

	public function get_BaiduIP(){
		$result_in_html = getPageData('https://www.baidu.com/s?ie=utf-8&f=8&rsv_bp=1&tn=baidu&wd=site%3A%20'.$this->url_domain);

		
		@$dom = new DOMDocument;

		@$dom->loadHTML($result_in_html);
		$tags = $dom->getElementsByTagName('div');

		foreach ($tags as $tag) {
		    $value = (string) $tag->getAttribute( 'class' );
		    if ($value == 'nums') {
		    	$temp = getTextFromNode($tag);
		    	return number_format(filter_numbers($temp), 0); // filter response to allow only numbers
		    }
		}
	}

	 public function get_BingIP(){
		$result_in_html = getPageData('https://www.bing.com/search?q=site:'.$this->url_domain.'&go=Submit&qs=bs&form=QBRE&scope=web');

		
		@$dom = new DOMDocument;

		@$dom->loadHTML($result_in_html);
		$tags = $dom->getElementsByTagName('span');

		foreach ($tags as $tag) {
		    $value = (string) $tag->getAttribute( 'class' );
		    if ($value == 'sb_count') {
		    	$temp = $tag->nodeValue;
		    	return number_format(filter_numbers($temp), 0); // filter response to allow only numbers
		    }
		}
	}

	public function get_GooIP(){
		$count = 0;
		$_url = 'https://search.goo.ne.jp/web.jsp?OE=UTF-8&mode=0&IE=UTF-8&MT=site'.urlencode(':'.$this->url_domain);
		$result_in_html = getPageData($_url);
		
		@$dom = new DOMDocument;

		@$dom->loadHTML($result_in_html);
		$tags = $dom->getElementsByTagName('div');
		$count = 0;
		foreach ($tags as $tag) {
		    $value = (string) $tag->getAttribute( 'class' );
		    if ($value == 'result') {
		    	$count++;
		    }
		}
		return $count;
	}

	public function get_YahooIP(){
		$result_in_html = getPageData('https://search.yahoo.com/search?fr=sfp&p=site:'.$this->url);

		
		@$dom = new DOMDocument;

		@$dom->loadHTML($result_in_html);
		$tags = $dom->getElementsByTagName('div');

		foreach ($tags as $tag) {
		    $value = (string) $tag->getAttribute( 'class' );
		    if ($value == 'compPagination') {
		    	$temp = getTextFromNode($tag->lastChild);
		    	return number_format(filter_numbers($temp), 0); // filter response to allow only numbers
		    }
		}
	}

	public function get_SogouIP(){
		$result_in_html = getPageData('https://www.sogou.com/web?query=domain:'.$this->url_domain);

		
		@$dom = new DOMDocument;

		@$dom->loadHTML($result_in_html);
		$tags = $dom->getElementsByTagName('p');

		foreach ($tags as $tag) {
		    $value = (string) $tag->getAttribute( 'class' );
		    if ($value == 'num-tips') {
		    	$temp = getTextFromNode($tag);
		    	return number_format(filter_numbers($temp), 0); // filter response to allow only numbers
		    }
		}
		return "N/A";
	}

	// Sogou Backlinks
	public function get_SogouBL(){
		$result_in_html = getPageData('https://www.sogou.com/web?query=domain:"'.$this->url_domain.'" url:"'.$this->url_domain.'"');
		@$dom = new DOMDocument;

		@$dom->loadHTML($result_in_html);
		$tags = $dom->getElementsByTagName('p');

		foreach ($tags as $tag) {
		    $value = (string) $tag->getAttribute( 'class' );
		    if ($value == 'num-tips') {
		    	$temp = getTextFromNode($tag);
		    	return number_format(filter_numbers($temp), 0); // filter response to allow only numbers
		    }
		}
		return "N/A";
	}

	public function get_360Ip(){
		$result_in_html = getPageData('https://www.haosou.com/s?ie=utf-8&shb=1&src=home_360com&q=domain:'.$this->url_domain);

		
		@$dom = new DOMDocument;

		@$dom->loadHTML($result_in_html);
		$tags = $dom->getElementsByTagName('span');

		foreach ($tags as $tag) {
		    $value = (string) $tag->getAttribute( 'class' );
		    if ($value == 'nums') {
		    	$temp = getTextFromNode($tag);
		    	return number_format(filter_numbers($temp), 0); // filter response to allow only numbers
		    }
		}
	}

	public function get_YandexIp(){
		return 'https://www.yandex.com/yandsearch?text=site%3A'.$this->url_domain.'&lr=87';	
	}

	public function get_Cached(){
		return array(
			"archive" => "https://web.archive.org/web/*/".$this->url,
			"google" => "https://webcache.googleusercontent.com/search?cd=1&hl=en&ct=clnk&gl=us&q=cache:".$this->url
		);
	}

	public function QuantcastRank(){
		$html = @getPageData("https://www.quantcast.com/".$this->url_domain);

		if( !empty($html) ){
			
			@$dom = new DOMDocument;

			@$dom->loadHTML($html);
			$tags = $dom->getElementsByTagName('li');

			foreach ($tags as $tag) {
			    $value = (string) $tag->getAttribute( 'class' );
			    if ($value == 'rank') {
			    	$temp = getTextFromNode($tag);
			    	return number_format(filter_numbers($temp), 0); // filter response to allow only numbers
			    }
			}
		}
		return 'N/A';
	}

	private function StrToNum($Str, $Check, $Magic)
	 {
	 $Int32Unit = 4294967296;
	 $length = strlen($Str);
	 for ($i = 0; $i < $length; $i++) {
	 $Check *= $Magic;
	 if ($Check >= $Int32Unit) {
	 $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
	 $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
	 }
	 $Check += ord($Str{$i});
	 }
	 return $Check;
	 }
	 private function HashURL($String)
	 {
	 $Check1 = $this->StrToNum($String, 0x1505, 0x21);
	 $Check2 = $this->StrToNum($String, 0, 0x1003F);
	 $Check1 >>= 2;
	 $Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
	 $Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
	 $Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);
	 $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
	 $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );
	 return ($T1 | $T2);
	 }
	 private function CheckHash($Hashnum)
	 {
	 $CheckByte = 0;
	 $Flag = 0;
	 $HashStr = sprintf('%u', $Hashnum) ;
	 $length = strlen($HashStr);
	 for ($i = $length - 1; $i >= 0; $i --) {
	 $Re = $HashStr{$i};
	 if (1 === ($Flag % 2)) {
	 $Re += $Re;
	 $Re = (int)($Re / 10) + ($Re % 10);
	 }
	 $CheckByte += $Re;
	 $Flag ++;
	 }
	 $CheckByte %= 10;
	 if (0 !== $CheckByte) {
	 $CheckByte = 10 - $CheckByte;
	 if (1 === ($Flag % 2) ) {
	 if (1 === ($CheckByte % 2)) {
	 $CheckByte += 9;
	 }
	 $CheckByte >>= 1;
	 }
	 }
	 return '7'.$CheckByte.$HashStr;
	 }
	 private function getPageData($url) {
	    if(function_exists('curl_init')) {
	        $ch = curl_init($url);
	        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        if((ini_get('open_basedir') == '') && (ini_get('safe_mode') == 'Off')) {
	            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	        }
	        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
	        return curl_exec($ch);
	    }  else {
	        return @file_get_contents($this->url);
	    }
	}

	private function curlUrl($url){
		$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if((ini_get('open_basedir') == '') && (ini_get('safe_mode') == 'Off')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        return @curl_exec($ch);
	}
}
?>