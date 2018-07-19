<div class="wpspy-wrapper">
	<?php 
		$page = 'wpspy-social-stats';
		include plugin_dir_path( __FILE__ )."classes/config.php";
		include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
		include plugin_dir_path( __FILE__ )."_nav.php"; 
		include plugin_dir_path( __FILE__ )."classes/data.php";
	?>
	<div class="wpspy-content">
		<?php
			include  plugin_dir_path( __FILE__ )."_form.php";

			if( isset($_GIVEN_URL) && trim($_GIVEN_URL) != "" ){
				$cached = checkDataStatus('social_stats', $_GIVEN_URL);

				if( ($cached !== 'false') && ( isset($cached["score_sentiment"]) && 
				$cached["score_sentiment"] != '-') ) {
					$socialstats = new stdClass();
					$socialmention = new stdClass();
					$socialstats->social_shares = new stdClass();

					foreach ($cached as $key => $value) {
						$socialstats->social_shares->$key = $value;
					}

					$socialmention->score_strength = $cached["score_strength"];
					$socialmention->score_sentiment = $cached["score_sentiment"];
					$socialmention->score_passion = $cached["score_passion"];
					$socialmention->score_reach = $cached["score_reach"];
					echo '<script>exportableData = '.json_encode($cached).';</script>';

				}else{
					function getsm(){
						$_GIVEN_URL = "";

						if (isset($_GET['url'])) {
							$_GIVEN_URL = preg_replace(
							  '#((https?|ftp)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i',
							  "$1",
							  $_GET['url']
							);
						}

						return json_decode(getSocialMention($_GIVEN_URL));
					}

					$socialstats = json_decode(getSociaLStats($_GIVEN_URL, 'json'));

					$limit = 5;
					
					for($x = 0; $x < $limit; $x++){
						$socialmention = getsm();
						if(isset($socialmention) && count($socialmention) < 1 ){
							$socialmention = getsm();
						}else{
							break;
						}
					}

					$data_array = array();
					if( isset($socialstats) ){
						foreach ($socialstats->social_shares as $key => $value) {
							$data_array[$key] = (string) $value;
						}
					}

					if( isset($socialmention) ){
						foreach ($socialmention as $key => $value) {
							$data_array[$key] = (is_object($value) || is_array($value)) ? json_encode($value) : $value;
						}
					}

					echo '<script>exportableData = '.json_encode($data_array).';</script>';
					save_this_activity('http://'.$_GIVEN_URL, $data_array);
				}
			}
		?>
		<div class="wpspy-results row">
			<div class="col-3">
				<div class="box social-sns">
					<div class="title">Social (SNS)</div>
					<div class="content">

						<div class="entry facebook_count">
							<div class="left">
								<span class="spy-icon-facebook spy-icon"></span>Facebook shares
							</div>
							<div class="right">
								<span id="facebook_likes">
									<?php echo isset($socialstats->social_shares->facebook_count) ? $socialstats->social_shares->facebook_count : '';?>
								</span>
							</div>
						</div>

						<div class="entry google_count">
							<div class="left">
								<span class="spy-icon-gplus spy-icon"></span>Google Plus
							</div>
							<div class="right">
								<span id="gplus">
									<?php echo isset($socialstats->social_shares->google_count) ? $socialstats->social_shares->google_count : '';?>
								</span>
							</div>
						</div>

						<div class="entry stumbleupon_count">
							<div class="left">
								<span class="spy-icon-stumbleupon spy-icon"></span>StumbleUpon
							</div>
							<div class="right">
								<span id="stumbleupon">
									<?php echo isset($socialstats->social_shares->stumbleupon_count) ? $socialstats->social_shares->stumbleupon_count : '';?>
								</span>
							</div>
						</div>

						<div class="entry twitter_count">
							<div class="left">
								<span class="spy-icon-twitter spy-icon"></span>Twitter tweets
							</div>
							<div class="right">
								<span id="twitter">
									<?php echo isset($socialstats->social_shares->twitter_count) ? $socialstats->social_shares->twitter_count : '';?>
								</span>
							</div>
						</div>

						<div class="entry linkedin_count">
							<div class="left">
								<span class="spy-icon-linkedin spy-icon"></span>LinkedIn
							</div>
							<div class="right">
								<span id="linkedin">
									<?php echo isset($socialstats->social_shares->linkedin_count) ? $socialstats->social_shares->linkedin_count : '';?>
								</span>
							</div>
						</div>

						<div class="entry pinterest_count">
							<div class="left">
								<span class="spy-icon-pinterest spy-icon"></span>Pinterest
							</div>
							<div class="right">
								<span id="pinterest">
									<?php echo isset($socialstats->social_shares->pinterest_count) ? $socialstats->social_shares->pinterest_count : '';?>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-3">
				<div class="box social-metrics">
					<div class="title">Social Metrics</div>
					<div class="content">
						<div class="entry score_strength">
							<div class="left">
								Strength
							</div>
							<div class="right">
								<span id="strength">
									<?php echo isset($socialmention->score_strength) ? $socialmention->score_strength : '';?>
								</span>
							</div>
						</div>

						<div class="entry score_sentiment">
							<div class="left">
								Sentiment
							</div>
							<div class="right">
								<span id="sentiment">
									<?php echo isset($socialmention->score_sentiment) ? $socialmention->score_sentiment : '';?>
								</span>
							</div>
						</div>

						<div class="entry score_passion">
							<div class="left">
								Passion
							</div>
							<div class="right">
								<span id="passion">
									<?php echo isset($socialmention->score_passion) ? $socialmention->score_passion : '';?>
								</span>
							</div>
						</div>

						<div class="entry score_reach">
							<div class="left">
								Reach
							</div>
							<div class="right">
								<span id="reach">
									<?php echo isset($socialmention->score_reach) ? $socialmention->score_reach : '';?>
								</span>
							</div>
						</div>

						<div class="entry mentions">
							<div class="left">
								Mentions
							</div>
							<div class="right">
								<a <?php echo isset($socialmention) ? 'href="http://socialmention.com/search?t=all&q='.urlencode('http://'.$_GIVEN_URL).'&btnG=Search" class="spy-icon spy-icon-eye"' : 'href="#"';?> target="_blank" id="view_social_mentions"></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php include "_export.php"; ?>
		</div>
	</div>
</div>