<div class="wpspy-wrapper">
	<?php 
		$page = 'previous-searches';
		include plugin_dir_path( __FILE__ )."classes/config.php";
		include plugin_dir_path( __FILE__ )."_nav.php";
		include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
	?>
	<div class="wpspy-content">
		<?php 
			include  plugin_dir_path( __FILE__ )."_form.php";
			if( isset($_GIVEN_URL) && trim($_GIVEN_URL) != "" ){
				$history = get_history_all($_GIVEN_URL);
			}
		?>
		<div class="wpspy-results row">
			<div class="col-12">
				<div class="history box">
					<div class="title">
						Previous Searches 
						<?php  echo isset($_GIVEN_URL) ? 'for '.$_GIVEN_URL : ''; ?>
					</div>
					<div class="content" id="div_history_table_outer">
						<table class="table" id="history">
							<thead>
								<tr>
									<th>Date</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
					<!-- Modal -->
					<div id="dialog" style="display: none;"></div>
					<div id="history_data" style="display: none;">
						<div class="dns box">
							<div class="title">Domain Info</div>
							<div class="content"></div>
						</div>
						<div class="wordpress-data box">
							<div class="title">WordPress Data</div>
							<div class="content">
								<div class="entry">
									<div class="left">
										Wordpress Version
									</div>
									<div class="right">
										<span id="wordpress_version"></span>
									</div>
								</div>
								<div class="plugin"></div>
								<div class="theme">
									<div class="entry">
										<div class="left"></div>
										<div class="right"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div style="display: none;">
						<div class="box" id="div_page_info_history">
							<table id="page_info_history" class="table tbl-page-info">
								<thead>
									<tr>
										<th>Date</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
						<div class="box" id="div_page_info_history_hidden">
							<table id="page_info_history_hidden" class="table tbl-page-info">
								<thead>
									<tr>
										<th>Tag</th>
										<th>Content</th>
										<th>Length</th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>

						<div class="box social-sns" id="div_social_stats_history">
							<div class="title">Social (SNS)</div>
							<div class="content">
								<div class="entry facebook_count">
									<div class="left">
										<span class="spy-icon-facebook spy-icon"></span>Facebook shares
									</div>
									<div class="right">
										<span id="facebook_likes"></span>
									</div>
								</div>

								<!-- <div class="entry google_count">
									<div class="left">
										<span class="spy-icon-gplus spy-icon"></span>Google Plus
									</div>
									<div class="right">
										<span id="gplus"></span>
									</div>
								</div> -->

								<div class="entry stumbleupon_count">
									<div class="left">
										<span class="spy-icon-stumbleupon spy-icon"></span>StumbleUpon
									</div>
									<div class="right">
										<span id="stumbleupon"></span>
									</div>
								</div>

								<div class="entry twitter_count">
									<div class="left">
										<span class="spy-icon-twitter spy-icon"></span>Twitter tweets
									</div>
									<div class="right">
										<span id="twitter"></span>
									</div>
								</div>

								<!-- <div class="entry linkedin_count">
									<div class="left">
										<span class="spy-icon-linkedin spy-icon"></span>LinkedIn
									</div>
									<div class="right">
										<span id="linkedin"></span>
									</div>
								</div> -->

								<div class="entry pinterest_count">
									<div class="left">
										<span class="spy-icon-pinterest spy-icon"></span>Pinterest
									</div>
									<div class="right">
										<span id="pinterest"></span>
									</div>
								</div>
							</div>
						</div>

						<div class="box social-metrics" id="div_social_metrics_history">
							<div class="title">Social Metrics</div>
							<div class="content">
								<div class="entry score_strength">
									<div class="left">
										Strength
									</div>
									<div class="right">
										<span id="strength"></span>
									</div>
								</div>

								<div class="entry score_sentiment">
									<div class="left">
										Sentiment
									</div>
									<div class="right">
										<span id="sentiment"></span>
									</div>
								</div>

								<div class="entry score_passion">
									<div class="left">
										Passion
									</div>
									<div class="right">
										<span id="passion"></span>
									</div>
								</div>

								<div class="entry score_reach">
									<div class="left">
										Reach
									</div>
									<div class="right">
										<span id="reach"></span>
									</div>
								</div>
							</div>
						</div>

						<div class="box traffic" id="div_traffic_history">
							<div class="title">Traffic</div>
							<div class="content">
								<div class="entry">
									<div class="left">
										<span class="spy-icon-alexa spy-icon"></span>Alexa Traffic Rank
									</div>
									<div class="right">
										<span id="alexa_rank"></span>
									</div>
								</div>

								<div class="entry">
									<div>
										<div>
											<span class="spy-icon-alexa spy-icon"></span>Alexa Traffic Rank in Country
										</div>
										<span id="alexa_rank_in_country">
											<table class="rank-in-country">
												<thead>
													<tr>
														<th colspan="2">Country</th>
														<th>Percent of Visitors</th>
														<th>Rank in Country</th>
													</tr>
												</thead>
												<tbody></tbody>
											</table>
										</span>
									</div>
								</div>

								<div class="entry">
									<div class="left">
										<span class="spy-icon-quantcast spy-icon"></span>Quantcast Traffic Rank
									</div>
									<div class="right">
										<span id="quantcast_traffic_rank"></span>
									</div>
								</div>
							</div>
						</div>

						<div class="box site-metrics" id="div_site_metrics_history">
							<div class="title">Site Metrics</div>
							<div class="content">
								<div class="entry">
									<div class="left">Bounce Rate</div>
									<div class="right">
										<span id="bounce_rate"></span>
									</div>
								</div>

								<div class="entry">
									<div class="left">Daily Pageviews per Visitor</div>
									<div class="right">
										<span id="daily_pageviews_per_visitor"></span>
									</div>
								</div>

								<div class="entry">
									<div class="left">Daily Time on Site</div>
									<div class="right">
										<span id="dailytime_onsite"></span>
									</div>
								</div>
							</div>
						</div>

						<div class="links box" id="div_links_history">
							<table class="table tbl-links">
								<thead>
									<tr>
										<th>Links count</th>
									</tr>
								</thead>
								<tbody>
									<tr class="external-links-outer-row">
										<td>
											<strong>
												External links: 
												<span id="external_links_count"></span>
												(<span id="external_nofollow_count"></span> nofollow)
											</strong>
										</td>
									</tr>
									<tr>
										<td></td>
									</tr>
									<tr class="internal-links-outer-row">
										<td>
											<strong>
												Internal links: 
												<span id="internal_links_count"></span>
												(<span id="internal_nofollow_count"></span> nofollow)
											</strong>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>