<div class="wpspy-wrapper">
	<?php 
		$page = 'wpspy-traffic';
		include plugin_dir_path( __FILE__ )."classes/config.php";
		include plugin_dir_path( __FILE__ )."_nav.php"; 
		include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
		include plugin_dir_path( __FILE__ )."classes/data.php";
	?>
	<div class="wpspy-content">
		<div class="wpspy-form">
			<?php 
				if( isset( $_GET['url'] ) && trim($_GIVEN_URL) != "" ){
					$site_metrics = get_site_metrics($_GIVEN_URL);

					$exportableData = $site_metrics;
					unset($exportableData["alexa_rank_in_country"]);
					$exportableData = str_replace("\\n", "", json_encode($exportableData));

					$alexaRankInCountry = str_replace("\\", "", $site_metrics["alexa_rank_in_country"]);
					$alexaRankInCountry = $alexaRankInCountry == 'N/A'? '"N/A"' : $alexaRankInCountry;

					echo '<script>';
					echo 'exportableData = '.str_replace("\\", "", $exportableData).';';
					echo 'exportableData.alexa_rank_in_country = '.$alexaRankInCountry.';';
					echo '</script>';
				}
			?>
			<iframe src="about:blank" id="remember" name="remember" class="hidden"></iframe>
			<form method="post" action="" id="form_wpspy" target="remember">
				<input	type="text" name="wpspy_url" id="wpspy_url" placeholder="www.example.com" value="<?php echo isset($_GIVEN_URL) && trim($_GIVEN_URL) != "" ? $_GIVEN_URL : ''; ?>"/>
				<input type="submit" class="wpspy_btn" name="wpspy_submit" data-page="traffic" id="wpspy_submit" value="Go" />
			</form>
		</div>
		<div class="wpspy-results row">
			<div class="col-7">
				<div class="traffic-graphs box">
					<div class="title">Traffic graphs</div>
					<div class="content">
						<div class="entry">	
							<div class="center">
								<div>
									<select id="choose_traffic_graph">
										<option value="traffic_trend6m" selected>Alexa - Traffic Rank Trend (10 Months)</option>
										<option value="search_visits">Alexa - Search Visits (percent)</option>
									</select>
								</div>
								<div id="canvas-holder">
									<div id="traffic-graph-area">
										<img id="traffic_graph" src="http://traffic.alexa.com/graph?o=lt&y=t&b=ffffff&n=666666&f=999999&p=4e8cff&h=150&w=340&z=30&c=1&y=t&r=6m&u=<?php echo isset($_GIVEN_URL) ? $_GET['url'] : '';?>">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="box traffic">
					<div class="title">Traffic</div>
					<div class="content">
						<div class="entry">
							<div class="left">
								<span class="spy-icon-alexa spy-icon"></span>Alexa Traffic Rank
							</div>
							<div class="right">
								<span id="alexa_rank">
									<?php echo isset($site_metrics["alexa_rank"]) ? $site_metrics["alexa_rank"] : "N/A"; ?>
								</span>
							</div>
						</div>

						<div class="entry">
							<div class="">
								<div>
									<span class="spy-icon-alexa spy-icon"></span>Alexa Traffic Rank in Country
								</div>
								<span id="alexa_rank_in_country">
									<?php 
										$alexa_rank_in_country = @json_decode(stripslashes($site_metrics["alexa_rank_in_country"]));
										echo '<table class="rank-in-country">
											<thead>
												<tr>
													<th colspan="2">Country</th>
													<th>Percent of Visitors</th>
													<th>Rank in Country</th>
												</tr>
											</thead>
											<tbody>
										';
										if(isset($alexa_rank_in_country) && !empty($alexa_rank_in_country) && (count($alexa_rank_in_country) > 1) && (is_array($alexa_rank_in_country) || is_object($alexa_rank_in_country)) ){
											
											foreach ($alexa_rank_in_country as $key) {
												echo '<tr>
													<td>
														<span class="flag flag-'.$key->country_code.'"></span>
													</td>
													<td>
														<span>'.$key->country.'</span>
													</td>
													<td>'.$key->percent_of_visitors.'</td>
													<td>'.$key->rank.'</td>
												</tr>';
											}
											
										}else{
											echo '<tr>
													<td>
														<span class="flag flag-">-</span>
													</td>
													<td>
														<span></span>
													</td>
													<td>-</td>
													<td>-</td>
												</tr>';
										}

										echo '</tbody></table>';
										
									?>
								</span>
							</div>
						</div>

						<div class="entry">
							<div class="left">
								<span class="spy-icon-quantcast spy-icon"></span>Quantcast Traffic Rank
							</div>
							<div class="right">
								<span id="quantcast_traffic_rank">
									<?php echo isset($site_metrics["quantcast_traffic_rank"]) ? $site_metrics["quantcast_traffic_rank"] : "N/A"; ?>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>	
			<div class="col-4">
				<div class="box site-metrics">
					<div class="title">Site Metrics</div>
					<div class="content">
						<div class="entry">
							<div class="left">Bounce Rate</div>
							<div class="right">
								<span id="bounce_rate">
									<?php echo isset($site_metrics["bounce_rate"]) ? $site_metrics["bounce_rate"] : '';  ?>
								</span>
							</div>
						</div>

						<div class="entry">
							<div class="left">Daily Pageviews per Visitor</div>
							<div class="right">
								<span id="daily_pageviews_per_visitor">
									<?php echo isset($site_metrics["daily_pageviews_per_visitor"]) ? $site_metrics["daily_pageviews_per_visitor"] : '';  ?>
								</span>
							</div>
						</div>

						<div class="entry">
							<div class="left">Daily Time on Site</div>
							<div class="right">
								<span id="dailytime_onsite">
									<?php echo isset($site_metrics["dailytime_onsite"]) ? $site_metrics["dailytime_onsite"] : '';  ?>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php include "_export.php"; ?>
		</div>
	</div>
</div>