<?php 
	$page = 'wpspy-social-stats';
	include plugin_dir_path( __FILE__ )."classes/config.php";
	include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
	include plugin_dir_path( __FILE__ )."classes/data.php";

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

<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div>
        <?php include "_nav.php"; ?>
        <section class="content">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <?php include  plugin_dir_path( __FILE__ )."_form.php"; ?>
                </div>
            </div>

            <!-- SOCIAL STATS -->
            <div class="row">
            	<!-- FACEBOOK -->
                <div class="col-md-3 col-sm-6 col-xs-12">
					<div class="info-box social-stats">
						<span class="info-box-icon bg-blue"><i class="ion ion-social-facebook"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">Facebook</span>
							<span class="info-box-number" id="facebook_count">
								<?php echo isset($socialstats->social_shares->facebook_count) ? $socialstats->social_shares->facebook_count : '';?>
							</span>
						</div>
					</div>
				</div>

				<!-- PINTEREST -->
				<div class="col-md-3 col-sm-6 col-xs-12">
					<div class="info-box social-stats">
						<span class="info-box-icon bg-red"><i class="ion-social-pinterest"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">Pinterest</span>
							<span class="info-box-number" id="pinterest_count">
								 <?php echo isset($socialstats->social_shares->pinterest_count) ? $socialstats->social_shares->pinterest_count : '';?>
							</span>
						</div>
					</div>
				</div>

				<!-- TWITTER -->
                <div class="col-md-3 col-sm-6 col-xs-12">
					<div class="info-box social-stats">
						<span class="info-box-icon bg-aqua"><i class="ion ion-social-twitter"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">Twitter tweets</span>
							<span class="info-box-number" id="twitter_count">
								 <?php echo isset($socialstats->social_shares->twitter_count) ? $socialstats->social_shares->twitter_count : '';?>
							</span>
						</div>
					</div>
				</div>

                <!-- STUMBLEUPON -->
                <div class="col-md-3 col-sm-6 col-xs-12">
					<div class="info-box social-stats">
						<span class="info-box-icon bg-orange"><i class="fa fa-stumbleupon"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">StumbleUpon</span>
							<span class="info-box-number" id="stumbleupon_count">
								<?php echo isset($socialstats->social_shares->stumbleupon_count) ? $socialstats->social_shares->stumbleupon_count : '';?>
							</span>
						</div>
					</div>
				</div>
            </div>

            <!-- SOCIAL METRICS -->
            <div class="row">
            	<div class="col-md-7">
            		<div class="box box-solid box-warning social-metrics">
                        <div class="box-header">
                            <div class="pull-right box-tools">
                                <button type="button" class="btn bg-orange btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
                                <i class="fa fa-minus"></i></button>
                            </div>
                            <i class="ion ion-ios-star"></i>
                            <h3 class="box-title">
                                Social Metrics
                            </h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
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
											<a <?php echo isset($socialmention) ? 'href="http://socialmention.com/search?t=all&q='.urlencode($_GIVEN_URL).'&btnG=Search" class="spy-icon spy-icon-eye"' : 'href="#"';?> target="_blank" id="view_social_mentions"></a>
										</div>
									</div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer no-border"></div>
                    </div>
            	</div>
            </div>
        </section>
    </div>
</div>