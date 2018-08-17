<?php 
    $page = 'wpspy-traffic';
    include plugin_dir_path( __FILE__ )."classes/config.php";
    include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
    include plugin_dir_path( __FILE__ )."classes/data.php";
?>

<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div>
        <?php
        	include "_nav.php";

			if( isset( $_GIVEN_URL ) && trim($_GIVEN_URL) != "" ){
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
        <section class="content">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <?php include  plugin_dir_path( __FILE__ )."_form.php"; ?>
                </div>
            </div>

            <!-- PING.JS -->
            <div class="row geolocation">
            	<!-- IP -->
                <div class="col-md-3 col-sm-6 col-xs-12">
					<div class="info-box site-ping">
						<span class="info-box-icon bg-aqua"><i class="ion-ios-speedometer-outline"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">PING</span>
							<span class="info-box-number" id="site_ping"></span>
						</div>
					</div>
				</div>
            </div>

            <div class="row">
            	<section class="col-lg-7 connectedSortable">
                	<!-- TRAFFIC -->
                    <div class="box box-solid bg-teal-gradient traffic">
                        <div class="box-header">
                            <div class="pull-right box-tools">
                                <button type="button" class="btn bg-teal btn-sm pull-right" data-widget="collapse"
                                    data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
                                <i class="fa fa-minus"></i></button>
                            </div>
                            <i class="fa fa-globe"></i>
                            <h3 class="box-title">
                                Traffic
                            </h3>
                        </div>
                        <div class="box-body no-padding"></div>
                        <div class="box-footer no-border">
                            <div class="row">
                                <div class="col-md-12">
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
										<div>
											<span class="spy-icon-alexa spy-icon"></span>Alexa Traffic Rank in Country
										</div>
										<span id="alexa_rank_in_country">
											<?php 
												$alexa_rank_in_country = @json_decode(stripslashes($site_metrics["alexa_rank_in_country"]));
											?>
												<table class="rank-in-country table table-bordered table-striped">
													<thead>
														<tr>
															<th colspan="2">Country</th>
															<th>Percent of Visitors</th>
															<th>Rank in Country</th>
														</tr>
													</thead>
													<tbody>
											<?php
												if(isset($alexa_rank_in_country) && !empty($alexa_rank_in_country) 
													&& (count($alexa_rank_in_country) > 1) && (is_array($alexa_rank_in_country)
													|| is_object($alexa_rank_in_country))
												):
													
													foreach ($alexa_rank_in_country as $key):
											?>
														<tr>
															<td>
																<span class="flag flag-<?php echo $key->country_codew; ?>"></span>
															</td>
															<td>
																<span><?php echo $key->country; ?></span>
															</td>
															<td><?php echo $key->percent_of_visitors; ?></td>
															<td><?php echo $key->rank; ?></td>
														</tr>
											<?php
													endforeach;
													
												else:
											?>
													<tr>
															<td>
																<span class="flag flag-">-</span>
															</td>
															<td>
																<span></span>
															</td>
															<td>-</td>
															<td>-</td>
														</tr>
											<?php
												endif;
											?>

												</tbody>
											</table>
										</span>
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="col-lg-5 connectedSortable">
                    <!-- TRAFFIC GRAPHS -->
                    <div class="box box-solid bg-green-gradient traffic-graphs">
                        <div class="box-header">
                            <i class="fa fa-line-chart"></i>
                            <h3 class="box-title">Traffic Graphs</h3>
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="box-body no-padding"></div>
                        
                        <div class="box-footer text-black">
                            <div class="row">
                                <div class="col-md-12">
                                    <div>
										<select id="choose_traffic_graph" class="form-control">
											<option value="traffic_trend6m" selected>Alexa - Traffic Rank Trend (10 Months)</option>
											<option value="search_visits">Alexa - Search Visits (percent)</option>
										</select>
									</div>
									<div id="canvas-holder">
										<div id="traffic-graph-area">
											<img id="traffic_graph" src="http://traffic.alexa.com/graph?o=lt&y=t&b=ffffff&n=666666&f=999999&p=4e8cff&h=150&w=340&z=30&c=1&y=t&r=6m&u=<?php echo isset($_GIVEN_URL) ? $_GIVEN_URL : '';?>">
										</div>
									</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SITE METRICS -->
                    <div class="box box-solid bg-blue-gradient site-metrics">
                        <div class="box-header">
                            <i class="ion ion-stats-bars"></i>
                            <h3 class="box-title">Site Metrics</h3>
                            <!-- tools box -->
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /. tools -->
                        </div>
                        
                        <div class="box-body no-padding">
                        </div>
                        
                        <div class="box-footer text-black">
                            <div class="row">
                                <div class="col-md-12">
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
                    </div>
                </section>
            </div>

            <div class="row">
                <section class="col-md-12">
                    <?php include "_export.php"; ?>
                </section>
            </div>
        </section>
    </div>
</div>