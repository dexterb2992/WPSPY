<?php 
	$page = 'wpspy-links';
	include plugin_dir_path( __FILE__ )."classes/config.php";
	include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
	include plugin_dir_path( __FILE__ )."classes/data.php";
?>

<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div>
        <?php
        	include "_nav.php";

        	if( isset($_GET['url']) && trim($_GET['url']) != "" ){
				$_url = $_GIVEN_URL;
				
				$links = getLinks($_url);
				echo '<script>exportableData = '.json_encode($links).';</script>';
				$data_array = array();
			}
        ?>
        <section class="content">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <?php include  plugin_dir_path( __FILE__ )."_form.php"; ?>
                </div>
            </div>

            <!-- INTERNAL & EXTERNAL LINKS -->
            <div class="row">
            	<div class="col-lg-7 connectedSortable">
            		<div class="box box-solid box-info ie-links">
                        <div class="box-header">
                            <div class="pull-right box-tools">
                                <button type="button" class="btn bg-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
                                <i class="fa fa-minus"></i></button>
                            </div>
                            <i class="ion ion-android-share-alt"></i>
                            <h3 class="box-title">
                                External &amp; Internal Links
                            </h3>
                        </div>
                        <div class="box-body no-padding"></div>
                        <div class="box-footer no-border table-responsive">
                        	<table class="table table-bordered table-striped tbl-links">
								<thead>
									<tr>
										<th>#</th>
										<th>URL</th>
										<th>Anchor Text</th>
									</tr>
								</thead>
								<tbody class="external-links-tbody">
									<tr class="external-links-outer-row">
										<td colspan="3">
											<strong class="text-green">
												External links: 
												<span id="external_links_count">
													<?php echo isset($links["external_links"]["links"]) ? count($links["external_links"]["links"]) : '';   ?>	
												</span>
												(<span id="external_nofollow_count">
													<?php echo isset($links["external_links"]["links"]) ? $links["external_links"]["nofollow"] : '0';   ?>
												</span> nofollow)
											</strong>

											<a href="javascript:void(0)" class="btn-collapse btn btn-xs text-green pull-right" style="margin-right: 5px" data-toggle="tooltip" data-original-title="Collapse">
												<i class="fa fa-minus"></i>
											</a>
										</td>
									</tr>
									<?php  
										$data_array["external_links"] = json_encode(
											array(
												"links" => isset($links["external_links"]["links"]) ? count($links["external_links"]["links"]) : '0',
												"nofollow" => isset($links["external_links"]["links"]) ? $links["external_links"]["nofollow"] : '0'
											)
										);

										$data_array["internal_links"] = json_encode(
											array(
												"links" => isset($links["internal_links"]["links"]) ? count($links["internal_links"]["links"]) : '0',
												"nofollow" => isset($links["internal_links"]["links"]) ? $links["internal_links"]["nofollow"] : '0'
											)
										);

										if( isset($_GET['url'] ) && trim($_GET['url']) != "" ){ 
											$status = save_this_activity("http://".$_GET['url'], $data_array);
										}

										if( isset($links["external_links"]["links"]) ){

											$x = 1;
											foreach ($links["external_links"]["links"] as $link) {
												echo '
													<tr class="url external-link">
														<td>'.$x.':</td>
														<td>
															<a href="'.$link["url"].'" target="_blank">'.$link["url"].'</a>
														</td>
														<td><div class="anchor-text">'.$link["text"].'</div></td>
													</tr>
												';
												$x++;
											}
										}
									?>
								</tbody>
								<tbody class="internal-links-tbody">
									<tr class="internal-links-outer-row">
										<td colspan="3">
											<strong class="text-aqua">
												Internal links: 
												<span id="internal_links_count">
													<?php echo isset($links["internal_links"]["links"]) ? count($links["internal_links"]["links"]) : '';   ?>
												</span>
												(<span id="internal_nofollow_count">
													<?php echo isset($links["internal_links"]["links"]) ? $links["internal_links"]["nofollow"] : '0';   ?>
												</span> nofollow)
											</strong>

											<a href="javascript:void(0)" class="btn-collapse btn btn-xs text-aqua pull-right" style="margin-right: 5px" data-toggle="tooltip" data-original-title="Collapse">
												<i class="fa fa-minus"></i>
											</a>
										</td>
									</tr>
									<?php 
										if( isset($links["internal_links"]["links"]) ){
											$x = 1;
											foreach ($links["internal_links"]["links"] as $link) {
												echo '
													<tr class="url internal-links">
														<td>'.$x.':</td>
														<td>
															<a href="'.$link["url"].'" target="_blank">'.$link["url"].'</a>
														</td>
														<td><div class="anchor-text">'.$link["text"].'</div></td>
													</tr>
												';
												$x++;
											}
										}
									?>
								</tbody>
							</table>
                        </div>
                    </div>

                    <!-- BACKLINKS -->
		            <div class="box box-solid bg-teal-gradient backlinks">
		                <div class="box-header">
		                    <div class="pull-right box-tools">
		                        <button type="button" class="btn bg-teal btn-sm pull-right" data-widget="collapse"
		                            data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
		                        <i class="fa fa-minus"></i></button>
		                    </div>
		                    <i class="ion ion-ios-world-outline"></i>
		                    <h3 class="box-title">
		                        Backinks
		                    </h3>
		                </div>
		                <div class="box-body no-padding">
		                    
		                </div>
		                <div class="box-footer no-border">
		                    <div class="content">
								<div id="div_backlinks">
									<div class="backlinks-head">
										<strong>Show </strong>
										<select id="backlinks_num">
											<option>20</option>
											<option>50</option>
											<option>100</option>
										</select>
										<a id="show_backlinks" class="btn bg-teal btn-sm" href="javascript:void(0)">
											<i class="fa fa-search"></i> Get Backlinks
										</a>
										<div class="export-div">
											<a href="javascript:void(0)" class="export-backlinks btn btn-sm bg-maroon" data-type="csv">
												<i class="fa fa-file-excel-o"></i> Export CSV
											</a>
											<a href="javascript:void(0)" class="export-backlinks btn btn-sm bg-purple" data-type="pdf">
												<i class="fa fa-file-pdf-o"></i> Export PDF
											</a>
										</div>
										<div id="openlink_invitation" style="display: none;">
											<div class="alert alert-info">
												You've reach the rate limit for this time. Please try again in few minutes. <br/>
												<strong>Note:</strong> You can only query a couple of times a day on the same IP address for Free. But if you want more queries you will need to sign up for the free trial service <a href="https://www.seoprofiler.com/#a_aid=cjsuccessteam">here</a>.
											</div>
										</div>
									</div>
									<div class="tbl-backlinks-holder table-responsive"></div>
								</div>
								<div id="backlinks_pagination">
									
								</div>
							</div>
							<div class="hidden">
								<table id="exportable_backlinks" class="hidden">
									<thead></thead>
									<tbody></tbody>
								</table>
							</div>
		                </div>
		            </div>
            	</div>

            	<div class="col-lg-5 connectedSortable">
            		<!-- SEARCH ENGINE RESULTS -->
                    <div class="box box-solid bg-yellow-gradient search-engine-results">
                        <div class="box-header">
                            <i class="fa fa-globe"></i>
                            <h3 class="box-title">
		                        Search Engine Results
		                    </h3>
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-warning btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="box-body no-padding">
                        </div>
                       
                        <div class="box-footer text-black">
                            <div class="row">
                                <div class="col-md-12">
                                	<div class="entry">
										<div class="left">Google</div>
										<div class="right">	
											<a class="<?php echo !empty($_url) ? 'spy-icon-eye spy-icon' : ''; ?>" target="_blank" href="<?php echo !empty($_url) ? 'http://www.google.com/search?q=site%3A'.urlencode($_GIVEN_URL_DOMAIN) : '#'; ?>" data-link="http://www.google.com/search?q=site%3A" id="google_search_results"></a>
										</div>
									</div>

									<div class="entry">
										<div class="left">Bing</div>
										<div class="right">	
											<a class="<?php echo !empty($_url) ? 'spy-icon-eye spy-icon' : ''; ?>" target="_blank" href="<?php echo !empty($_url) ? 'http://www.bing.com/search?q=site%3A'.urlencode($_GIVEN_URL_DOMAIN).'&go=Submit' : '#'; ?>" data-link="http://www.bing.com/search?q=site%3A" id="bing_search_results"></a>
										</div>
									</div>

									<div class="entry">
										<div class="left">Yahoo</div>
										<div class="right">	
											<a class="<?php echo !empty($_url) ? 'spy-icon-eye spy-icon' : ''; ?>" target="_blank" href="<?php echo !empty($_url) ? 'http://search.yahoo.com/?p=site:+'.urlencode($_GIVEN_URL_DOMAIN).'&go=Submit' : '#'; ?>" data-link="http://search.yahoo.com/?p=site:+" id="yahoo_search_results"></a>
										</div>
									</div>

									<div class="entry">
										<div class="left">Ask</div>
										<div class="right">	
											<a class="<?php echo !empty($_url) ? 'spy-icon-eye spy-icon' : ''; ?>" target="_blank" href="<?php echo !empty($_url) ? 'http://www.ask.com/web?q='.urlencode($_GIVEN_URL_DOMAIN).'&qsrc=0&o=0&l=dir&qo=homepageSearchBox' : '#'; ?>" data-link="http://www.ask.com/web?q="  id="ask_search_results"></a>
										</div>
									</div>

									<div class="entry">
										<div class="left">Aol Search</div>
										<div class="right">	
											<a class="<?php echo !empty($_url) ? 'spy-icon-eye spy-icon' : ''; ?>" target="_blank" href="<?php echo !empty($_url) ? 'http://search.aol.com/aol/search?s_chn=prt_aol20&v_t=comsearch&q='.urlencode($_GIVEN_URL_DOMAIN).'&s_it=topsearchbox.search' : '#';?>" data-link="http://search.aol.com/aol/search?s_chn=prt_aol20&v_t=comsearch&q=" id="aol_search_results"></a>
										</div>
									</div>

									<div class="entry">
										<div class="left">DuckDuckGo</div>
										<div class="right">	
											<a class="<?php echo !empty($_url) ? 'spy-icon-eye spy-icon' : ''; ?>" target="_blank" href="<?php echo !empty($_url) ? 'https://duckduckgo.com/?q='.urlencode($_GIVEN_URL_DOMAIN) : '#'; ?>" data-link="https://duckduckgo.com/?q=" id="duckduckgo_search_results"></a>
										</div>
									</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RECOMMENDED TOOLS -->
                    <div class="box box-solid bg-blue-gradient recommended-tools">
                        <div class="box-header">
                            <i class="ion ion-thumbsup"></i>
                            <h3 class="box-title">
                            	Recommended tools
                            	<span class="pull-right settings btn btn-xs bg-blue-gradient btn-flat" data-toggle="modal" data-target="#settings_dialog">
                            		<i class="ion ion-ios-settings"></i> Settings
                            	</span>
                            </h3>
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body no-padding">
                        </div>
                        <div class="box-footer text-black">
                            <div class="row">
                                <div class="col-md-12">
                                	<div class="results">
                                		<?php 
											// TEMPORARY LIMIT 10
											$tmp = getRecommendedToolsLimit();

												if( isset($links["external_links"]["links"]) ){
													$count = count($links["external_links"]["links"]);
													$limit = ( $tmp < 1) ? $count : ( ($tmp > $count)? $count : $tmp );

													if( $count > 0 ){
														$random = array_rand($links["external_links"]["links"], $limit);
														for ($x = 0; $x < $limit; $x++) {

															if( $count >= $x ){
																echo '
																	<div class="entry">
																		<div class="left">
																			<a class="anchor-text" href="'.$links["external_links"]["links"][ $random[$x] ]["url"].'" target="_blank">'.$links["external_links"]["links"][ $random[$x] ]["url"].'</a>
																		</div>
																	</div>
																';
															}
														}
													}
												}else{
													echo '<span>No data available</span>';
												}
											?>
                                	</div>
                                </div>
                            </div>
                        </div>
                    </div>
            	</div>
            </div>
            <div class="row">
                <section class="col-md-12">
                    <?php include "_export.php"; ?>
                </section>
            </div>
        </section>
    </div>
</div>


<!-- Tools settings Modal -->
<div class="modal fade in" id="settings_dialog" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
				<h4 class="modal-title">Set Recommended Tools Limit</h4>
			</div>
			<div class="modal-body">
				<iframe src="about:blank" class="hidden" id="remember" name="remember"></iframe>
				<form id="rtl_settings" method="post" action="" target="remember" class="form-inline">
					<div class="form-group">
						<input type="text" data-type="number" required="required" id="settings" value="<?php echo ( $tmp < 1) ? 10 : $tmp;?>" placeholder="Set limit" class="form-control">
					</div>
					
					<div class="form-group">
						<button type="submit" name="save_settings" id="save_settings" class="btn btn-info">Save</button>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>