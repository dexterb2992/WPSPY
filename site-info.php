<?php 
    $page = 'wpspy-site-info';
    include plugin_dir_path( __FILE__ )."classes/config.php";
    include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
    include plugin_dir_path( __FILE__ )."classes/data.php";
?>

<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div>
        <?php
        	include "_nav.php";
        	
		    if( !empty($_GIVEN_URL) && trim($_GIVEN_URL) != "" ){
				$cached = checkDataStatus('site_info', ''.$_GIVEN_URL);
				
				if( ($cached !== 'false') && ( isset($cached['ip']) && $cached["ip"] != "N/A" ) ){
					$cached["wordpress_data"] = str_replace('\\', '', $cached["wordpress_data"]);
					$cached["dns"] = str_replace('\\', '', $cached["dns"]);

					$onsite = new stdClass();
					$onsite->robot = $cached['robot'];
					$onsite->sitemap_index = $cached['sitemap_index'];

					$whois = new stdClass();
					$whois->geolocation = new stdClass();
					$whois->geolocation->ip = $cached['ip'];
					$whois->geolocation->city = $cached['city'];
					$whois->geolocation->country = $cached['country'];
					$whois->geolocation->country_code = $cached['country_code'];
					$whois->dns = json_decode($cached["dns"]);

					$wordpress_data = json_decode($cached['wordpress_data']);
					
					echo '<script>exportableData = '.json_encode($cached).';</script>';
				}else{
					$cached = "false";
					
					$onsite = json_decode(getOnSite($_GIVEN_URL, 'json'));
					$whois = json_decode(getWhOIS($_GIVEN_URL, 'json'));
					$wordpress_data = json_decode(getWordpressData($_GIVEN_URL, 'json'));

					$data_array = array();
				}
			}
        ?>
        <section class="content">
            <div class="row" style="margin-bottom: 20px;">
                <div class="col-md-12">
                    <?php include  plugin_dir_path( __FILE__ )."_form.php"; ?>
                </div>
            </div>

            <!-- GEOLOCATION -->
            <div class="row geolocation">
            	<!-- IP -->
                <div class="col-md-3 col-sm-6 col-xs-12">
					<div class="info-box">
						<span class="info-box-icon bg-aqua"><i class="ion ion-ios-cloud-outline"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">IP</span>
							<span class="info-box-number" id="ip">
								<?php
									if(	isset($whois->geolocation) ){
										$data_array["ip"] = $whois->geolocation->ip;
										$data_array["city"] = $whois->geolocation->city;
										$data_array["country_code"] = $whois->geolocation->country_code;
										$data_array["country"] = $whois->geolocation->country;
									} 
									echo isset($whois->geolocation->ip) ? $whois->geolocation->ip : '-'; 
								?>
							</span>
						</div>
						<!-- /.info-box-content -->
					</div>
					<!-- /.info-box -->
				</div>

                <!-- CITY -->
                <div class="col-md-3 col-sm-6 col-xs-12">
					<div class="info-box">
						<span class="info-box-icon bg-red"><i class="ion ion-android-star-outline"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">City</span>
							<span class="info-box-number" id="city">
								<?php echo isset($whois->geolocation->city) ? $whois->geolocation->city : '-'; ?>
							</span>
						</div>
						<!-- /.info-box-content -->
					</div>
					<!-- /.info-box -->
				</div>

                <!-- COUNTRY -->
                 <div class="col-md-3 col-sm-6 col-xs-12">
					<div class="info-box">
						<span class="info-box-icon bg-green"><i class="ion ion-android-globe"></i></span>

						<div class="info-box-content">
							<span class="info-box-text">Country</span>
							<span class="info-box-number" id="country">
								 <?php echo isset($whois->geolocation->country) ? 
										'<span class="flag flag-'.$whois->geolocation->country_code.'"></span>
										<span>'.$whois->geolocation->country.'</span>' : '-'; 
								?>
							</span>
						</div>
						<!-- /.info-box-content -->
					</div>
					<!-- /.info-box -->
				</div>
            </div>

            <div class="row">
                <section class="col-lg-6 connectedSortable">
                    <!-- ON SITE -->
                    <div class="box box-solid bg-green-gradient on-site">
                        <div class="box-header">
                            <i class="fa fa-pagelines"></i>
                            <h3 class="box-title">On-site</h3>
                            <!-- tools box -->
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-success btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /. tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer text-black">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="entry">
										<div class="left">
											<span class="spy-icon spy-icon-bw"></span>BuiltWith
										</div>
										<div class="right">
											<a <?php echo !empty($_GIVEN_URL) ? 'href="http://builtwith.com/'.$_GIVEN_URL.'" class="spy-icon spy-icon-eye"' : '#' ?> target="_blank" id="builtwith"></a>
										</div>
									</div>
									<div class="entry">
										<div class="left">
											<span class="spy-icon spy-icon-robots"></span>robots.txt
										</div>
										<div class="right">
											<?php 
												if(!empty($_GIVEN_URL)){
													if( isset($onsite->robot) && $onsite->robot == 'true' || $onsite->robot == '1' ){
														$data_array["robot"] = $onsite->robot;
														echo '<span id="robots" class="spy-icon-check spy-icon"></span>';
													}else{
														echo !empty($_GIVEN_URL) ? '<span>N/A</span>' : '';
													}
												}else{
													echo '<span id="robots"></span>';
												}
											?>
										</div>
									</div>
									<div class="entry">
										<div class="left">
											<span class="spy-icon spy-icon-sitemap"></span>sitemap.xml
										</div>
										<div class="right">
											<?php 
												if(!empty($_GIVEN_URL)){
													if( isset($onsite->sitemap_index) && $onsite->sitemap_index == 'true' || $onsite->sitemap_index == '1' ){
														$data_array["sitemap_index"] = $onsite->sitemap_index;
														echo '<span id="sitemap" class="spy-icon-check spy-icon"></span>';
													}else{
														echo !empty($_GIVEN_URL) ? '<span>N/A</span>' : '';
													}
												}else{
													echo '<span id="sitemap"></span>';
												}
											?>
										</div>
									</div>
									<!-- <div class="entry">
										<div class="left">
											<span class="spy-icon spy-icon-sourcecode"></span>Sourcecode
										</div>
										<div class="right">
											<a <?php echo !empty($_GIVEN_URL) ? 'href="view-source:'.$_GIVEN_URL.'" class="spy-icon spy-icon-eye"' : '#' ?> id="source_code" target="_blank"></a>
										</div>
									</div> -->
                                </div>   
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                    </div>

                    <!-- DOMAIN INFO -->
                    <div class="box box-solid bg-teal-gradient domain-info">
                        <div class="box-header">
                            <div class="pull-right box-tools">
                                <button type="button" class="btn bg-teal btn-sm pull-right" data-widget="collapse"
                                    data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
                                <i class="fa fa-minus"></i></button>
                            </div>
                            <i class="fa fa-globe"></i>
                            <h3 class="box-title">
                                Domain Info
                            </h3>
                        </div>
                        <div class="box-body no-padding">
                            
                        </div>
                        <div class="box-footer no-border">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="entry">
										<div class="left">
											<span class="spy-icon spy-icon-ipwhois"></span>WHOIS Lookup
										</div>
										<div class="right">
											<a <?php echo isset($_GIVEN_URL) ? 'href="http://who.is/whois/'.$_GIVEN_URL.'" class="spy-icon spy-icon-eye"' : '' ?> target="_blank" id="whois_lookup"></a>
										</div>
									</div>
									<div class="dns">
										<?php 
										$x = 1;
											if( isset($whois->dns) ){
												$data_array["dns"] = json_encode($whois->dns);
												foreach ($whois->dns as $key => $value) {
													?>
													<div class="entry">
														<div class="left">
															<span class="spy-icon spy-icon-dns"></span>DNS <?php echo $x;?>
														</div>
														<div class="right">
															<span><?php echo $value;?></span>
														</div>
													</div>
													<?php
													$x++;
												}
											}
										?>
									</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SITE SECURITY -->
                    <div class="box box-solid bg-blue-gradient site-security">
                        <div class="box-header">
                            <i class="ion ion-android-lock"></i>
                            <h3 class="box-title">Site Security</h3>
                            <!-- tools box -->
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /. tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body no-padding">
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer text-black">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="entry">
										<div class="left">
											<span class="spy-icon spy-icon-mcafee"></span>
											McAfee Site Advisor
										</div>
										<div class="right">
											<a <?php echo isset($_GIVEN_URL) ? 'href="http://www.siteadvisor.com/sites/'.$_GIVEN_URL.'" class="spy-icon spy-icon-eye"' : 'href="#"'; ?> target="_blank" id="mcafee"></a>
										</div>
									</div>

									<div class="entry">
										<div class="left">
											<span class="spy-icon spy-icon-norton"></span>
											Norton Safe Web
										</div>
										<div class="right">
											<a <?php echo isset($_GIVEN_URL) ? 'href="http://safeweb.norton.com/report/show?url='.$_GIVEN_URL.'" class="spy-icon spy-icon-eye"' : 'href="#"'; ?> target="_blank" id="norton"></a>
										</div>
									</div>

									<div class="entry">
										<div class="left">
											<span class="spy-icon spy-icon-wot"></span>
											WOT
										</div>
										<div class="right">
											<a <?php echo isset($_GIVEN_URL) ? 'href="https://www.mywot.com/en/scorecard/'.$_GIVEN_URL.'" class="spy-icon spy-icon-eye"' : 'href="#"'; ?> target="_blank" id="wot"></a>
										</div>
									</div>

									<div class="entry">
										<div class="left">
											<span class="spy-icon spy-icon-wot"></span>
											Sucuri
										</div>
										<div class="right">
											<a <?php echo isset($_GIVEN_URL) ? 'href="http://sitecheck.sucuri.net/results/'.$_GIVEN_URL.'" class="spy-icon spy-icon-eye"' : 'href="#"'; ?> target="_blank" id="sucuri"></a>
										</div>
									</div>
                                </div>   
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                    </div>
                </section>
                
                <section class="col-lg-6 connectedSortable">
                    <!-- PAGES INDEXEWD -->
                    <div class="box box-solid box-warning wordpress-data">
                        <div class="box-header">
                            <div class="pull-right box-tools">
                                <button type="button" class="btn bg-orange btn-sm pull-right" data-widget="collapse"
                                    data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
                                <i class="fa fa-minus"></i></button>
                            </div>
                            <i class="fa fa-wordpress"></i>
                            <h3 class="box-title">
                                WordPress Data
                            </h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                   <div class="entry">
										<div class="left">
											Wordpress Version
										</div>
										<div class="right">
											<span id="wordpress_version">
												<?php 
													isset($wordpress_data) ? $data_array["wordpress_data"] = @json_encode($wordpress_data) : '';
													echo ( @$wordpress_data->version != 0) ? @$wordpress_data->version : 'N/A'; 
												?>
											</span>
										</div>
									</div>
									<div class="plugins">
										<?php 
											if( isset($wordpress_data->free_plugins) && $wordpress_data->free_plugins != 0 ){
												foreach ($wordpress_data->free_plugins as $key) {
													?>
													<div class="entry">
														<div class="left"><?php echo cleanStr($key->name); ?></div>
														<div class="right">
															<?php 
															echo isset($key->download) ? '<a href="'.$key->download.'" class="spy-icon spy-icon-download"></a>' : '';
															echo isset($key->link) ? '<a href="'.$key->link.'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '';
															?>
															<span class="spy-icon spy-icon-plugin"></span>
														</div>
													</div>
													<?php
												}
											}

											if( @isset($wordpress_data->commercial_plugins)  && $wordpress_data->commercial_plugins != 0 ){
												foreach ($wordpress_data->commercial_plugins as $key) {
													?>
													<div class="entry">
														<div class="left"><?php echo cleanStr($key->name); ?></div>
														<div class="right">
															<?php 
															echo ($key->download != "N/A") ? '<a href="'.$key->download.'" class="spy-icon spy-icon-download"></a>' : '';
															echo ($key->link != "N/A") ? '<a href="'.$key->link.'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '<a href="http://google.com/search?q=wordpress%20'.$key->name.'" target="_blank" class="spy-icon spy-icon-eye"></a>';
															?>
															<span class="spy-icon spy-icon-plugin"></span>
														</div>
													</div>
													<?php
												}
											}
										?>
									</div>
									<div class="theme">
										<?php 
											if( isset($wordpress_data->theme) ){
												?>
												<div class="entry">
													<div class="left"><?php echo cleanStr($wordpress_data->theme->name); ?></div>
													<div class="right">
														<?php 
															if( $wordpress_data->theme->name != null && $wordpress_data->theme->name != '' ){
																echo ($wordpress_data->theme->download == "N/A") ? '<a href="'.$wordpress_data->theme->download.'" class="spy-icon spy-icon-download"></a>' : '';
																echo ($wordpress_data->theme->link != "N/A") ? '<a href="'.$wordpress_data->theme->link.'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '<a href="http://google.com/search?q=wordpress%20'.$wordpress_data->theme->name.'" target="_blank" class="spy-icon spy-icon-eye"></a>'; 
																echo '<span class="spy-icon spy-icon-theme"></span>';
															}
														?>
													</div>
												</div>
												<?php
											}
											
											// Save to database
											if( @$cached == 'false' ){
												echo '<script>exportableData = '.json_encode($data_array).';</script>';
												$status = save_this_activity($_GIVEN_URL, $data_array);
											}
										?>
									</div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer no-border"></div>
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