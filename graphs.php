<div class="wpspy-wrapper">
	<?php 
		$page = 'wpspy-graphs';
		include plugin_dir_path( __FILE__ )."classes/config.php";
		include plugin_dir_path( __FILE__ )."_nav.php"; 
		include plugin_dir_path( __FILE__ )."classes/helpers.php";
	?>
	<div class="wpspy-content">
		<div class="wpspy-form">
			<iframe src="about:blank" id="remember" name="remember" class="hidden"></iframe>
			<form method="post" action="" id="form_wpspy" target="remember">
				<input	type="text" name="wpspy_url" id="wpspy_url" placeholder="www.example.com" />
				<input type="submit" class="wpspy_btn" name="wpspy_submit" data-page="graphs" id="wpspy_submit" value="Go" />
			</form>
		</div>
		<div class="wpspy-results row">
			<div class="col-12">
				<div class="box no-border">
					<div class="chart-options">
						<div class="sites">
							<select id="compare_sites">
							<?php 
								$sites = get_sites();
								$x = 0;
								foreach ($sites as $site => $value) {
									$y = ($x<1)?'selected':'';
									echo "<option ".$y.">".$value."</opiton>";
									$x++;
								}
							?>
							</select>
							<select id="compare_sites2">
							<?php 
								$sites = get_sites();
								$x = 0;
								foreach ($sites as $site => $value) {
									$y = ($x<1)?'selected':'';
									echo "<option ".$y.">".$value."</opiton>";
									$x++;
								}
							?>
							</select>	
						</div>
						<div class="data-type">
							<select id="chart_options">
								<option value="alexa_rank" selected>Alexa Rank</option>
								<option value="google_page_rank">Google Page Rank</option>
								<option value="quantcast_traffic_rank">Quantcast Traffic Rank</option>
								<option value="backlinks_alexa">Alexa Backlinks</option>
								<option value="backlinks_google">Google Backlinks</option>
								<option value="bounce_rate">Bounce Rate</option>
								<option value="dailytime_onsite">Time on Site</option>
								<option disabled="disabled">Social Shares</option>
								<option value="facebook_count">Facebook</option>
								<option value="twitter_count">Twitter</option>
								<option value="google_count">Google Plus</option>
								<option value="linkedin_count">LinkedIn</option>
								<option value="stumbleupon_count">StumbleUpon</option>
								<option value="pinterest_count">Pinterest</option>
							</select>
						</div>
						<div class="update">
							<a href="javascript:void(0);" class="wpspy_btn" id="update_chart">Compare Sites</a>
						</div>
					</div>
					<div id="canvas-holder">
						<div id="chart-area"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

