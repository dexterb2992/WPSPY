<div class="wpspy-wrapper">
	<?php 
		$page = 'wpspy-seo-stats';
		include plugin_dir_path( __FILE__ )."classes/config.php";
		include plugin_dir_path( __FILE__ )."_nav.php"; 
		include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
		include plugin_dir_path( __FILE__ )."classes/data.php";

		if( isset( $_GET['url'] ) && trim($_GET['url']) != "" ){

			$cached = checkDataStatus('seo_stats', 'http://'.$_GET['url']);
			
			if( ($cached !== 'false') && ( isset($cached["alexa_rank"]) && 
			$cached["alexa_rank"] != 0) ){
				$seostats = array();
				$seostats["rank"] = new stdClass();
				$seostats["backlinks"] = new stdClass();
				$seostats["pages_indexed"] = new stdClass();
				$seostats["site_metrics"] = new stdClass();
				$seostats["cached"] = new stdClass();

				$seostats["rank"]->alexa_traffic_rank = $cached["alexa_rank"];
				$seostats["rank"]->quantcast_traffic_rank = $cached["quantcast_traffic_rank"];
				$seostats["rank"]->google_page_rank = $cached["google_page_rank"];
				$seostats["rank"]->alexa_rank_in_country = @json_decode($cached["alexa_rank_in_country"]);

				$seostats["backlinks"]->alexa = $cached["backlinks_alexa"];
				$seostats["backlinks"]->open_site_explorer = $cached["backlinks_open_site_explorer"];
				$seostats["backlinks"]->google = $cached["backlinks_google"];
				$seostats["backlinks"]->ahrefs = $cached["backlinks_ahrefs"];
				$seostats["backlinks"]->sogou = $cached["backlinks_sogou"];

				$seostats["pages_indexed"]->ask = $cached["page_indexed_ask"];
				$seostats["pages_indexed"]->baidu = $cached["page_indexed_baidu"];
				$seostats["pages_indexed"]->bing = $cached["page_indexed_bing"];
				$seostats["pages_indexed"]->goo = $cached["page_indexed_goo"];
				$seostats["pages_indexed"]->google = $cached["page_indexed_google"];
				$seostats["pages_indexed"]->sogou = $cached["page_indexed_sogou"];
				$seostats["pages_indexed"]->yahoo = $cached["page_indexed_yahoo"];
				$seostats["pages_indexed"]->yandex = $cached["page_indexed_yandex"];
				$seostats["pages_indexed"]->_360 = $cached["page_indexed__360"];

				$seostats["site_metrics"]->bounce_rate = @$cached["bounce_rate"];
				$seostats["site_metrics"]->dailytime_onsite = @$cached["dailytime_onsite"];
				$seostats["site_metrics"]->daily_pageviews_per_visitor = @$cached["daily_pageviews_per_visitor"];

				$seostats["cached"]->archive = "https://web.archive.org/web/*/http://".$_GET['url'];
				$seostats["cached"]->google = " https://webcache.googleusercontent.com/search?cd=1&hl=en&ct=clnk&gl=us&q=cache:http://".$_GET['url'];
				echo '<script>exportableData = '.json_encode($seostats).';</script>';
			}else{
				$html = getSeoStats("http://".$_GET['url'], 'json');
				$seostats = (array) json_decode($html);
				echo '<script>exportableData = '.$html.';</script>';

				$data_array = array();
				$data_array["url"] = 'http://'.$_GET['url'];
				$data_array["alexa_rank"] = $seostats["rank"]->alexa_traffic_rank;
				$data_array["google_page_rank"] = $seostats["rank"]->google_page_rank;
				$data_array["quantcast_traffic_rank"] = $seostats["rank"]->quantcast_traffic_rank;

				$data_array["alexa_rank_in_country"] = (string) json_encode($seostats["rank"]->alexa_rank_in_country);

				$data_array["bounce_rate"] = $seostats["site_metrics"]->bounce_rate;
				$data_array["dailytime_onsite"] = $seostats["site_metrics"]->dailytime_onsite;
				$data_array["daily_pageviews_per_visitor"] = $seostats["site_metrics"]->daily_pageviews_per_visitor;


				$data_array["backlinks_alexa"] = $seostats["backlinks"]->alexa;
				$data_array["backlinks_google"] = $seostats["backlinks"]->google;
				$data_array["backlinks_open_site_explorer"] = $seostats["backlinks"]->open_site_explorer;
				$data_array["backlinks_sogou"] = $seostats["backlinks"]->ahrefs;
				$data_array["backlinks_ahrefs"] = $seostats["backlinks"]->sogou;

				$data_array["page_indexed_ask"] = $seostats["pages_indexed"]->ask;
				$data_array["page_indexed_baidu"] = $seostats["pages_indexed"]->baidu;
				$data_array["page_indexed_bing"] = $seostats["pages_indexed"]->bing;
				$data_array["page_indexed_goo"] = $seostats["pages_indexed"]->goo;
				$data_array["page_indexed_google"] = $seostats["pages_indexed"]->google;
				$data_array["page_indexed_sogou"] = $seostats["pages_indexed"]->sogou;
				$data_array["page_indexed_yahoo"] = $seostats["pages_indexed"]->yahoo;
				$data_array["page_indexed_yandex"] = $seostats["pages_indexed"]->yandex;

				if( !empty($seostats["pages_indexed"]->_360) ){
					$data_array["page_indexed__360"] = $seostats["pages_indexed"]->_360;
				}

				$temp = $seostats["site_metrics"]->bounce_rate;

				$data_array["bounce_rate"] = $temp == "" ? 0 : $temp;
				$data_array["dailytime_onsite"] = $seostats["site_metrics"]->dailytime_onsite;
				$data_array["daily_pageviews_per_visitor"] = $seostats["site_metrics"]->daily_pageviews_per_visitor;
				$status = save_this_activity("http://".$_GET['url'], $data_array);
				$data_array["cached"] = array(
					"archive" => "https://web.archive.org/web/*/".$_GET['url'],
					"google" => "http://webcache.googleusercontent.com/search?cd=1&hl=en&ct=clnk&gl=us&q=cache:".$_GET['url']
				);
				echo '<script>exportableData = '.json_encode($data_array).';</script>';
				
			}
			
		}
	?>
	<div class="wpspy-content">
		<div class="wpspy-form">
			<iframe src="about:blank" id="remember" name="remember" class="hidden"></iframe>
			<form method="post" action="" id="form_wpspy" target="remember">
				<input	type="text" name="url" id="wpspy_url" placeholder="www.example.com" value="<?php echo isset($_GET['url']) && trim($_GET['url']) != "" ? 'http://'.$_GET['url'] : ''; ?>" />
				<input type="submit" class="wpspy_btn" name="wpspy_submit" data-page="seo-stats" id="wpspy_submit" value="Go" />
			</form>
		</div>
		<div class="wpspy-results row">
			<div class="col-3">
				<div class="box rank">
					<div class="title">Rank</div>
					<div class="content">
						<div class="entry alexa">
							<div class="left">
								<span class="spy-icon-alexa spy-icon"></span>
								Alexa Traffic Rank
							</div>
							<div class="right">
								<span id="alexa_rank">
									<?php echo isset($seostats["rank"]->alexa_traffic_rank) ? $seostats["rank"]->alexa_traffic_rank : 'N/A'; ?>
								</span>
							</div>
						</div>
						<div class="entry quantcast">
							<div class="left">
								<span class="spy-icon-quantcast spy-icon"></span>
								Quantcast Traffic Rank
							</div>
							<div class="right">
								<span id="quantcast_traffic">
									<?php echo isset($seostats["rank"]->quantcast_traffic_rank) ? $seostats["rank"]->quantcast_traffic_rank : 'N/A'; ?>
								</span>
							</div>
						</div>
						
						<div class="entry google">
							<div class="left">
								<span class="spy-icon-google spy-icon"></span>
								Google Page Rank
							</div>
							<div class="right">
								<span id="google_page_rank">
									<?php echo isset($seostats["rank"]->google_page_rank) ? $seostats["rank"]->google_page_rank : 'N/A'; ?>
								</span>
							</div>
						</div>
					</div>
				</div>

				<div class="box cached">
					<div class="title">Cached</div>
					<div class="content">
						<div class="entry">
							<div class="left">
								<span class="spy-icon-archive spy-icon"></span>
								Archive.org
							</div>
							<div class="right">
								<a href="#" id="archived" target="_blank" <?php echo isset($seostats["cached"]->archive) ? 'href="'.$seostats["cached"]->archive.'" class="spy-icon spy-icon-eye"' : 'href="#"'; ?>></a>
							</div>
						</div>

						<div class="entry">
							<div class="left">
								<span class="spy-icon-google spy-icon"></span>Google
							</div>
							<div class="right">
								<a id="google_cached" target="_blank" <?php echo isset($seostats["cached"]->google) ? 'href="'.$seostats["cached"]->google.'" class="spy-icon spy-icon-eye"' : 'href="#"'; ?>></a>
							</div>
						</div>
					</div>
				</div>

				<div class="box backlinks">
					<div class="title">Backlinks</div>
					<div class="content">
						<div class="entry alexa">
							<div class="left">
								<span class="spy-icon-alexa spy-icon"></span>
								Alexa
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["backlinks"]->alexa) ){
										if( strstr( $seostats["backlinks"]->alexa, 'http' ) != "" ){
											echo '<a href="'.$seostats["backlinks"]->alexa.'" class="spy-icon spy-icon-eye"></a>';
										}else{
											echo '<span id="alexa_backlinks">'.$seostats["backlinks"]->alexa.'</span>';
										}
									}else{
										echo '<span id="alexa_backlinks">N/A</span>';
									}
								?>
							</div>
						</div>

						<div class="entry google">
							<div class="left">
								<span class="spy-icon-google spy-icon"></span>Google
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["backlinks"]->google) ){
										if( strstr( $seostats["backlinks"]->google, 'http' ) != "" ){
											echo '<a href="'.$seostats["backlinks"]->google.'" class="spy-icon spy-icon-eye" id="google_backlinks"></a>';
										}else{
											echo '<span id="google_backlinks">'.$seostats["backlinks"]->google.'</span>';
										}
									}else{
										echo '<span id="google_backlinks">N/A</span>';
									}
								?>
							</div>
						</div>

						<div class="entry open_site_explorer">
							<div class="left">
								<span class="spy-icon-open-site-explorer spy-icon"></span>
								Open Site Explorer
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["backlinks"]->open_site_explorer) ){
										if( strstr( $seostats["backlinks"]->open_site_explorer, 'http' ) != "" ){
											echo '<a href="'.$seostats["backlinks"]->open_site_explorer.'" class="spy-icon spy-icon-eye" id="open_site_explorer"></a>';
										}else{
											echo '<span id="open_site_explorer">'.$seostats["backlinks"]->open_site_explorer.'</span>';
										}
									}else{
										echo '<span id="open_site_explorer">N/A</span>';
									}
								?>
							</div>
						</div>

						<div class="entry sogou">
							<div class="left">
								<span class="spy-icon-sogou spy-icon"></span>Sogou
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["backlinks"]->sogou) ){
										if( strstr( $seostats["backlinks"]->sogou, 'http' ) != "" ){
											echo '<a href="'.$seostats["backlinks"]->sogou.'" class="spy-icon spy-icon-eye" id="sogou"></a>';
										}else{
											echo '<span id="sogou">'.$seostats["backlinks"]->sogou.'</span>';
										}
									}else{
										echo '<span id="sogou">N/A</span>';
									}
								?>
							</div>
						</div>

						<!-- <div class="entry ahrefs">
							<div class="left">
								<span class="spy-icon-ahrefs spy-icon"></span>
								Ahrefs
							</div>
							<div class="right">
								<?php 
									// if( isset($seostats["backlinks"]->ahrefs) ){
									// 	if( strstr( $seostats["backlinks"]->ahrefs, 'http' ) != "" ){
									// 		echo '<a href="'.$seostats["backlinks"]->ahrefs.'" class="spy-icon spy-icon-eye" id="ahrefs_backlinks"></a>';
									// 	}else{
									// 		echo '<span id="ahrefs_backlinks">'.$seostats["backlinks"]->ahrefs.'</span>';
									// 	}
									// }else{
									// 	echo '<span id="ahrefs_backlinks">N/A</span>';
									// }
								?>
							</div>
						</div> -->
					</div>
				</div>
			</div>

			<div class="col-3">
				<div class="box pages-indexed">
					<div class="title">Pages indexed</div>
					<div class="content">
						<div class="entry ask">
							<div class="left">
								<span class="spy-icon-ask spy-icon"></span>Ask
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["pages_indexed"]->ask) ){
										if( strstr( $seostats["pages_indexed"]->ask, 'http' ) != "" ){
											echo '<a href="'.$seostats["pages_indexed"]->ask.'" class="spy-icon spy-icon-eye" id="ask"></a>';
										}else{
											echo '<span id="ask">'.$seostats["pages_indexed"]->ask.'</span>';
										}
									}else{
										echo '<span id="ask">N/A</span>';
									}
								?>
							</div>
						</div>

						<div class="entry baidu">
							<div class="left">
								<span class="spy-icon-baidu spy-icon"></span>Baidu
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["pages_indexed"]->baidu) ){
										if( strstr( $seostats["pages_indexed"]->baidu, 'http' ) != "" ){
											echo '<a href="'.$seostats["pages_indexed"]->baidu.'" class="spy-icon spy-icon-eye" id="baidu"></a>';
										}else{
											echo '<span id="baidu">'.$seostats["pages_indexed"]->baidu.'</span>';
										}
									}else{
										echo '<span id="baidu">N/A</span>';
									}
								?>
							</div>
						</div>

						<div class="entry bing">
							<div class="left">
								<span class="spy-icon-bing spy-icon"></span>Bing
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["pages_indexed"]->bing) ){
										if( strstr( $seostats["pages_indexed"]->bing, 'http' ) != "" ){
											echo '<a href="'.$seostats["pages_indexed"]->bing.'" class="spy-icon spy-icon-eye" id="bing"></a>';
										}else{
											echo '<span id="bing">'.$seostats["pages_indexed"]->bing.'</span>';
										}
									}else{
										echo '<span id="bing">N/A</span>';
									}
								?>
							</div>
						</div>

						<div class="entry goo">
							<div class="left">
								<span class="spy-icon-goo spy-icon"></span>Goo
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["pages_indexed"]->goo) ){
										if( strstr( $seostats["pages_indexed"]->goo, 'http' ) != "" ){
											echo '<a href="'.$seostats["pages_indexed"]->goo.'" class="spy-icon spy-icon-eye" id="goo"></a>';
										}else{
											echo '<span id="goo">'.$seostats["pages_indexed"]->goo.'</span>';
										}
									}else{
										echo '<span id="goo">N/A</span>';
									}
								?>
							</div>
						</div>

						<div class="entry google">
							<div class="left">
								<span class="spy-icon-google spy-icon"></span>Google
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["pages_indexed"]->google) ){
										if( strstr( $seostats["pages_indexed"]->google, 'http' ) != "" ){
											echo '<a href="'.$seostats["pages_indexed"]->google.'" class="spy-icon spy-icon-eye" id="google_page_indexed"></a>';
										}else{
											echo '<span id="google_page_indexed">'.$seostats["pages_indexed"]->google.'</span>';
										}
									}else{
										echo '<span id="google_page_indexed">N/A</span>';
									}
								?>
							</div>
						</div>

						<div class="entry sogou">
							<div class="left">
								<span class="spy-icon-sogou spy-icon"></span>Sogou
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["pages_indexed"]->sogou) ){
										if( strstr( $seostats["pages_indexed"]->sogou, 'http' ) != "" ){
											echo '<a href="'.$seostats["pages_indexed"]->sogou.'" class="spy-icon spy-icon-eye" id="sogou"></a>';
										}else{
											echo '<span id="sogou">'.$seostats["pages_indexed"]->sogou.'</span>';
										}
									}else{
										echo '<span id="sogou">N/A</span>';
									}
								?>
							</div>
						</div>

						<div class="entry yahoo">
							<div class="left">
								<span class="spy-icon-yahoo spy-icon"></span>Yahoo
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["pages_indexed"]->yahoo) ){
										if( strstr( $seostats["pages_indexed"]->yahoo, 'http' ) != "" ){
											echo '<a href="'.$seostats["pages_indexed"]->yahoo.'" class="spy-icon spy-icon-eye" id="yahoo"></a>';
										}else{
											echo '<span id="yahoo">'.$seostats["pages_indexed"]->yahoo.'</span>';
										}
									}else{
										echo '<span id="yahoo">N/A</span>';
									}
								?>
							</div>
						</div>

						<div class="entry yandex">
							<div class="left">
								<span class="spy-icon-yandex spy-icon"></span>Yandex
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["pages_indexed"]->yandex) ){
										if( strstr( $seostats["pages_indexed"]->yandex, 'http' ) != "" ){
											echo '<a href="'.$seostats["pages_indexed"]->yandex.'" class="spy-icon spy-icon-eye" id="yandex"></a>';
										}else{
											echo '<span id="yandex">'.$seostats["pages_indexed"]->yandex.'</span>';
										}
									}else{
										echo '<span id="yandex">N/A</span>';
									}
								?>
							</div>
						</div>

						<div class="entry _360">
							<div class="left">
								<span class="spy-icon-360 spy-icon"></span>360
							</div>
							<div class="right">
								<?php 
									if( isset($seostats["pages_indexed"]->_360) ){
										if( strstr( $seostats["pages_indexed"]->_360, 'http' ) != "" ){
											echo '<a href="'.$seostats["pages_indexed"]->_360.'" class="spy-icon spy-icon-eye" id="360"></a>';
										}else{
											echo '<span id="360">'.$seostats["pages_indexed"]->_360.'</span>';
										}
									}else{
										echo '<span id="360">N/A</span>';
									}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<?php include "_export.php"; ?>
		</div>
	</div>
</div>