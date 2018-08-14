var $ = jQuery.noConflict();

/** Image preview*/
	this.imagePreview = function() {	
		/* CONFIG */
		
		xOffset = 10;
		yOffset = 30;
		
		// these 2 variable determine popup's distance from the cursor
		// you might want to adjust to get the right result
		
		/* END CONFIG */
		$("a.preview").hover(function(e) {
			this.t = this.title;
			this.title = "";	
			var c = (this.t !== "") ? "<br/>" + this.t : "";
			$("body").append("<p id='preview'><img src='"+ this.href +"' alt='Image preview' />"+ c +"</p>");								 
			$("#preview")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(e.pageX + yOffset) + "px")
				.fadeIn("fast");						
	    },
		function() {
			this.title = this.t;	
			$("#preview").remove();
	    });	

		$("a.preview").mousemove(function(e) {
			$("#preview")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(e.pageX + yOffset) + "px");
		});			
	};

	var getUrlParameter = function getUrlParameter(sParam) {
	    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
	        sURLVariables = sPageURL.split('&'),
	        sParameterName,
	        i;

	    for (i = 0; i < sURLVariables.length; i++) {
	        sParameterName = sURLVariables[i].split('=');

	        if (sParameterName[0] === sParam) {
	            return sParameterName[1] === undefined ? true : sParameterName[1];
	        }
	    }
	};

$(document).ready(function() {
	$( window ).unload(function() {
		show_loader();
	});

	window.onload = function() {
		hide_loader();
	};

	/*$( document ).ajaxStart(function() {
	  show_loader();
	});
	
	$( document ).ajaxStop(function() {
		hide_loader();
	});*/

	setTimeout(function() {}, 10);

	function onLoadingState(classSelector) {
		$(classSelector+' .box-title').append('<span class="success thin"> (<i class="fa fa-spinner fa-spin"></i> Loading data...)</span>');
		$(classSelector+' button[data-widget="collapse"]').click();
		console.log(classSelector+" onLoadingState");
	}

	function removeLoadingState(classSelector) {
		$(classSelector+' .box-title .success').fadeOut(function () {
			$(this).remove();
		});
		if (! $(classSelector+' .entry').is(":visible")) {
			$(classSelector+' button[data-widget="collapse"]').click();
		}
		console.log(classSelector+" removeLoadingState");
	}

	function extractHostname(url) {
	    var hostname;
	    //find & remove protocol (http, ftp, etc.) and get hostname

	    if (url.indexOf("//") > -1) {
	        hostname = url.split('/')[2];
	    }
	    else {
	        hostname = url.split('/')[0];
	    }

	    //find & remove port number
	    hostname = hostname.split(':')[0];
	    //find & remove "?"
	    hostname = hostname.split('?')[0];

	    return hostname;
	}

	function extractRootDomain(url) {
	    var domain = extractHostname(url),
	        splitArr = domain.split('.'),
	        arrLen = splitArr.length;

	    //extracting the root domain here
	    //if there is a subdomain 
	    if (arrLen > 2) {
	        domain = splitArr[arrLen - 2] + '.' + splitArr[arrLen - 1];
	        //check to see if it's using a Country Code Top Level Domain (ccTLD) (i.e. ".me.uk")
	        if (splitArr[arrLen - 2].length == 2 && splitArr[arrLen - 1].length == 2) {
	            //this is using a ccTLD
	            domain = splitArr[arrLen - 3] + '.' + domain;
	        }
	    }
	    return domain;
	}


	// # of attempts when getting the social mention data
		var social_mention_attempts = 0; 

	var $results_div = $(".wpspy-results");
	var old_html = $results_div.html();
	var globalDomain;

	// recommended tools limit
		var recommended_tools_limit = 0;
		$.ajax({
			url : ajaxurl,
			type: 'post',
			data : { action: 'wpspy_ajax', q : 'get_rtlimit' }
		}).done(function (data) {
			recommended_tools_limit = data;
		});

	var x = 1 ;
	/** Initialize our datatables here */
	    var history_table = $('#history').DataTable( {
	        ajax : {
	        	url : ajaxurl,
	        	type : 'post',
	        	data : {action: 'wpspy_ajax', q : 'get_history_list', url : $("#wpspy_url").val()}
	        }
	    });

	    $(".number, input[data-type='number']").keydown(function(event) {
	    	// Allow only backspace and delete
	    	if (event.keyCode === 46 || event.keyCode === 8) {
	    		// let it happen, don't do anything
	    	} else {
	    		// Ensure that it is a number and stop the keypress
	    		if (event.keyCode < 48 || event.keyCode > 57) {
	    			event.preventDefault();	
	    		}	
	    	}
	    });


	// JAVASCRIPT EVENTS

		$("#choose_traffic_graph").change(function() {
			var val = $(this).val();
			var url = $("#wpspy_url").val();

			update_traffic_graph(val, url);
		});

		$("#form_wpspy").submit(function(e) {
			var domain = $("#wpspy_url").val(), page = $("#wpspy_submit").data("page"), btn = $("#wpspy_submit"),
				progress_limit = 0, progress_current = 0;
			disable_button(btn, "Please wait..");
			domain = check_url(domain);

			exportableData = {};
			

			if (is_valid_url(domain)) {
				var domain_raw = $("#wpspy_url").val();

				var scheme_index = 7;

				if (domain_raw.substr(0,8) == "https://") {
					scheme_index = 8;
				}

				domain = domain_raw.substring(scheme_index);
				globalDomain = domain;
				exportableData.url = domain_raw;

				$(".wpspy-content").append('<div class="loading"><div class="center">Grabbing data all over the web...</div></div>');
			
				// site-info page
				if (page === "site-info") {
					// let's disable the button to prevent submitting more than once
						disable_button(btn, "Please wait..");

					// make sure eye spy-icon is not shown
						var viewlinks = "#builtwith, #source_code, #whois_lookup, #mcafee, #norton, #wot, #sucuri";
						$(viewlinks).removeClass("spy-icon");
						$(viewlinks).removeClass("spy-icon-eye");

					$("#builtwith").attr("href", "//builtwith.com/"+domain);
					$("#source_code").attr("href", "view-source:"+domain_raw);
					$("#whois_lookup").attr("href", "//who.is/whois/"+extractHostname(domain_raw));
					$("#mcafee").attr("href", "//www.siteadvisor.com/sites/"+domain_raw);
					$("#norton").attr("href", "//safeweb.norton.com/report/show?url="+domain_raw);
					$("#wot").attr('href', "//www.mywot.com/en/scorecard/"+domain_raw);
					$("#sucuri").attr("href", "//sitecheck.sucuri.net/results/"+domain_raw);

					$(viewlinks).addClass("spy-icon");
					$(viewlinks).addClass("spy-icon-eye");

					// check if data already exists as cached
					$.ajax({
						url : ajaxurl,
						type : 'post',
						dataType : 'json',
						data : { action: 'wpspy_ajax', option : 'site_info', url : domain_raw, q : 'check_status' }
					}).done(function (data) {
						console.log(typeof(data));

						// verify if there a cached data
						if ((data !== false) && (data.ip === "N/A")) {
							data = false;
						}

						if (data ===  false) {
							// set the progress limit 
							progress_limit = 3;

							// prepare variable to handle data which will be saved to database
								var data_array = {
									q : "save_activity",
									url : domain_raw,
									action: 'wpspy_ajax',
								};

								$.ajax({
									url : ajaxurl,
									type : 'post',
									data : { action: 'wpspy_ajax', q : 'get_onsite', url : domain_raw },
									dataType : 'json',
									beforeSend: function () {
										onLoadingState(".on-site");
									},
									success: function (data) {
										// update progress 
										progress_current++;
									
										console.log(data);
										exportableData.on_site = data;
										$("#robots, #sitemap").removeClass("spy-icon-eye").removeClass("spy-icon").html("");

										if (data.robot === true  || data.robot === "true" || data.robot === "1") {
											$("#robots").addClass("spy-icon");
											$("#robots").addClass("spy-icon-check").html("");
										} else {
											$("#robots").removeClass("spy-icon-check").html("N/A");
										}
										
										if (data.sitemap_index === true || data.sitemap_index === "true" || data.sitemap_index === "1") {
											$("#sitemap").addClass("spy-icon");
											$("#sitemap").addClass("spy-icon-check").html("");
										} else {
											$("#sitemap").removeClass("spy-icon-check").html("N/A");
										}
										data_array.robot = (data.robot);
										data_array.sitemap_index = (data.sitemap_index);
										check_progress_and_save(progress_limit, progress_current, data_array);
									},
									error: function (data) {
										console.warn(data);
									},
									complete: function () {
										removeLoadingState(".on-site");
									}
								});
								
								$.ajax({
									url : ajaxurl,
									type : 'post',
									data : { action: 'wpspy_ajax', q : 'get_whois', url : domain_raw },
									dataType : 'json',
									beforeSend: function () {
										onLoadingState(".domain-info");
									},
									success: function (data) {
										console.log(data);
										// update current progress
											progress_current++;
											
										if (data !== null && typeof(data) !== null) {
											// append to html body
												if (typeof(data.link) !== 'undefined' && typeof(data.link) !== null) {
													$("#whois_lookup").attr("href", extractRootDomain(data.link));
												}
												$("div.domain-info .dns").html("");
												$.each(data.dns, function (i, row) {
													$("div.domain-info .dns").append(
														'<div class="entry">'+
															'<div class="left">'+
																'<span class="spy-icon spy-icon-dns"></span>DNS '+(i+1)+
															'</div>'+
															'<div class="right">'+
																'<span>'+row+'</span>'+
															'</div>'+
														'</div>'
													);
												});
												$("#ip").html(data.geolocation.ip);
												$("#country").html("<span class='flag flag-"+data.geolocation.country_code+"'></span><span>"+data.geolocation.country+"</span>");
												$("#city").html(data.geolocation.city);

												var dns = (data.dns);
												console.log(dns);

											data_array.ip = (data.geolocation.ip);
											data_array.country = (data.geolocation.country);
											data_array.country_code = (data.geolocation.country_code);
											data_array.city = (data.geolocation.city);
											data_array.dns = JSON.stringify(dns);

											exportableData.geolocation = data.geolocation;
											exportableData.dns = dns;

											check_progress_and_save(progress_limit, progress_current, data_array);
										}
									},
									error: function (data) {
										console.log("error:");
										console.log(data);
										// here we enable the submit button again
											enable_button(btn, "Go");
										// update current progress
											progress_current++;
											check_progress_and_save(progress_limit, progress_current, data_array);
										fetching_failure($('.geolocation'), 'WHOIS Data');
									},
									complete: function () {
										removeLoadingState(".domain-info");
									}
								});

								// get wordpress data
									$.ajax({
										url : ajaxurl,
										type : 'post',
										data : { action: 'wpspy_ajax', q : 'get_wordpress_data', url : domain_raw },
										dataType : 'json',
										beforeSend: function () {
											onLoadingState(".wordpress-data");
										},
										success: function (data) {
											data_array.wordpress_data = JSON.stringify(data);
											data_array.action = 'wpspy_ajax';

											// update current progress
												progress_current++;
												check_progress_and_save(progress_limit, progress_current, data_array);

											// save to database
												$.ajax({
													url : ajaxurl,
													type : 'post',
													dataType : 'json',
													data : data_array
												}).done(function (data) {
													console.log(data);
												});

											// here we enable the submit button again
												enable_button(btn, "Go");

											// Check if it's a wordpress site
											if ((typeof(data.version) !== 'undefined') && (typeof(data.version) !== null)) {
												exportableData.wordpress_data = data;
												
												$("#wordpress_version").html(data.version);
												$("div.wordpress-data div.plugins, div.wordpress-data div.theme").html("");
												if (data.free_plugins !== 0) {
													// extract free plugins
														$.each(data.free_plugins, function (i, row) {
															var name = capitalizeSlug(row.name);
															console.log(name);
															var download = ($.trim(row.download) !== "N/A") ? '<a href="'+row.download+'" class="spy-icon spy-icon-download"></a>' : '';
															var link = ($.trim(row.link) !== "N/A") ? '<a href="'+$.trim(row.link)+'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '<a href="//google.com/search?q=wordpress%20'+row.name+'" target="_blank" class="spy-icon spy-icon-eye"></a>';
															$("div.wordpress-data div.plugins").append(
																'<div class="entry">'+
																	'<div class="left">'+
																		name+
																	'</div>'+
																	'<div class="right">'+
																		download+
																		link+
																		'<span class="spy-icon spy-icon-plugin"></span>'+
																	'</div>'+
																'</div>');
														});
												}

												if (data.commercial_plugins !== 0) {
													// extract commercial plugins
													$.each(data.commercial_plugins, function (i, row) {
														var name = capitalizeSlug(row.name);
														console.log(name);
														var download = ($.trim(row.download) !== "N/A") ? '<a href="'+row.download+'" class="spy-icon spy-icon-download"></a>' : '';
														var link = ($.trim(row.link) !== "N/A") ? '<a href="'+$.trim(row.link)+'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '<a href="//google.com/search?q=wordpress%20'+row.name+'" target="_blank" class="spy-icon spy-icon-eye"></a>';
														$("div.wordpress-data div.plugins").append(
															'<div class="entry">'+
																'<div class="left">'+
																	name+
																'</div>'+
																'<div class="right">'+
																	download+
																	link+
																	'<span class="spy-icon spy-icon-plugin"></span>'+
																'</div>'+
															'</div>');
													});
												}

												var t_download = ($.trim(data.theme.link) !== "N/A") ? '<a href="'+$.trim(data.theme.download)+'" target="_blank" class="spy-icon spy-icon-download"></a>' : '';
												var t_link = ($.trim(data.theme.link) !== "N/A") ? '<a href="'+$.trim(data.theme.link)+'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '<a href="//google.com/search?q=wordpress%20'+data.theme.name+'" target="_blank" class="spy-icon spy-icon-eye"></a>';
												
												if (data.theme.name !== "" && data.theme.name !== "null" && data.theme.name !== null) {
													$("div.wordpress-data div.theme").html('<div class="entry">'+
														'<div class="left">'+data.theme.name+'</div>'+
														'<div class="right">'+
															t_download+
															t_link+
															'<span class="spy-icon spy-icon-theme"></span>'+
														'</div>'+
													'</div>');
												}

											}
										},
										error: function (data) {
											console.log("error:");
											console.log(data.responseText);
											// here we enable the submit button again
												enable_button(btn, "Go");

											// update current progress
												progress_current++;
												check_progress(progress_limit, progress_current);

											fetching_failure($('.wordpress-data'), 'WordPress Data');
										},
										complete: function () {
											removeLoadingState(".wordpress-data");
										}
									});
						} else {
							progress_limit = 1;
							enable_button(btn, "Go");
							$("#robots, #sitemap").removeClass("spy-icon-eye").removeClass("spy-icon").html("");
							data = data[0];
							// robots.txt
								if (data.robot === "true" || data.robot === "1") {
									$("#robots").addClass("spy-icon-check").addClass("spy-icon").html("");
								} else {
									$("#robots").removeClass("spy-icon-check").html("N/A");
								}

							// sitemap_index
								if (data.sitemap_index === "true" || data.sitemap_index === "1") {
									$("#sitemap").addClass("spy-icon-check").addClass("spy-icon").html("");
								} else {
									$("#sitemap").removeClass("spy-icon-check").html("N/A");
								}
								exportableData.on_site = { sitemap_index : data.sitemap_index, robot : data.robot }; 


							// dns
							$("div.domain-info div.dns").html("");
							var dns =  String(data.dns).replace(/\\/g, "");
							dns = $.parseJSON(dns);
								$.each(dns, function (i, row) {
									$("div.domain-info div.dns").append(
										'<div class="entry">'+
											'<div class="left">'+
												'<span class="spy-icon spy-icon-dns"></span>DNS '+(i+1)+
											'</div>'+
											'<div class="right">'+
												'<span>'+row+'</span>'+
											'</div>'+
										'</div>'
									);
								});


							// geolocation
								$("#ip").html(data.ip);
								$("#city").html(data.city);
								$("#country").html('<span class="flag flag-'+data.country_code+'"></span><span>'+data.country+'</span>');

								exportableData.geolocation = {ip : data.ip, city : data.city, country : data.country, country_code : data.country_code};

							
							// wordpress data
								var str_data = String(data.wordpress_data).replace(/\\/g, "");
								
								var wordpress_data = $.parseJSON(str_data);

								exportableData.wordpress_data = wordpress_data;

								// wordpress version
									$("div.plugins").html("");
									if (wordpress_data.version !== '0') {
										$("#wordpress_version").html(wordpress_data.version);

										// extract plugins and theme details
											if (wordpress_data.free_plugins !== '0') {
												$.each(wordpress_data.free_plugins, function (i, row) {
													var f_d = (row.download !== "N/A") ? '<a href="'+row.download+'" class="spy-icon spy-icon-download"></a>' : '';
													var f_l = (row.link !== "N/A") ? '<a href="'+row.link+'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '<a href="//google.com/search?q=wordpress%20'+row.name+'" target="_blank" class="spy-icon spy-icon-eye"></a>';

													$("div.plugins").append('<div class="entry">'+
														'<div class="left">'+capitalizeSlug(row.name)+'</div>'+
														'<div class="right">'+f_d+f_l+'<span class="spy-icon spy-icon-plugin"></span></div></div>');
												});
											}

											if (wordpress_data.commercial_plugins !== '0') {
												$.each(wordpress_data.commercial_plugins, function (i, row) {
													var f_d = (row.download !== "N/A") ? '<a href="'+row.download+'" class="spy-icon spy-icon-download"></a>' : '';
													var f_l = (row.link !== "N/A") ? '<a href="'+row.link+'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '<a href="//google.com/search?q=wordpress%20'+row.name+'" target="_blank" class="spy-icon spy-icon-eye"></a>';

													$("div.plugins").append('<div class="entry">'+
														'<div class="left">'+capitalizeSlug(row.name)+'</div>'+
														'<div class="right">'+f_d+f_l+'<span class="spy-icon spy-icon-plugin"></span></div></div>');
												});
											}

										// wordpress theme
											if (typeof(wordpress_data.theme) !== null  && wordpress_data.theme !== null && wordpress_data.theme !== '') {
												var t_d = (wordpress_data.theme.link !== "N/A") ? '<a href="'+wordpress_data.theme.download+'" class="spy-icon spy-icon-download"></a>' : '';
												var t_l = (wordpress_data.theme.link !== "N/A") ? '<a href="'+wordpress_data.theme.link+'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '<a href="//google.com/search?q=wordpress%20'+wordpress_data.theme.name+'" target="_blank" class="spy-icon spy-icon-eye"></a>'; 
												$("div.theme").html('<div class="entry">'+
													'<div class="left">'+wordpress_data.theme.name+'</div>'+
													'<div class="right">'+t_d+t_l+'<span class="spy-icon spy-icon-theme"></div></div></div>');
											}
									} else {
										$("#wordpress_version").html('N/A');
									}
						
							progress_current++;
							check_progress(progress_limit, progress_current);
						}
					});

				} else if (page === "page-info") {
					disable_button(btn, "Please wait...");
					progress_limit = 1;

					try{
						$.ajax({
							url : ajaxurl,
							type : 'post',
							dataType : 'json',
							data : { action: 'wpspy_ajax', option : 'page_info', url : domain_raw, q : 'check_status' },
							beforeSend: function () {
								show_loader();
							},
							success: function (data) {
								if ((data !== false) && (data.body === '-' || data.body === null || data.body === "")) {
									data = false;
								}

								if (data !== false) {		// There is already a cached data 
									console.log(data);
									enable_button(btn, "Go");
									data = data[0];

									$.each(data, function (i, row) {
										if (row != null) {
											data[i] = row.replace(/\\/g, "");
										} else {
											data[i] = "N/A";
										}
									});
									console.log(data);
									

									// url
										$("tr.url td:nth-child(2)").html(domain_raw);
										$("tr.url td:last").html(domain_raw.length);

									// canonical url
										if (data.canonical_url !== null) {
											$("tr.canonical-url td:nth-child(2)").html(data.canonical_url);
											$("tr.canonical-url td:last").html(data.canonical_url.length);
										} else {
											$("tr.canonical-url td:nth-child(2)").html('Not found');
											$("tr.canonical-url td:last").html('-');
										}

									// title
										if (data.title !== null) {
											$("tr.pageinfo-title td:nth-child(2)").html(data.title);
											$("tr.pageinfo-title td:last").html(data.title.length);
										} else {
											$("tr.pageinfo-title td:nth-child(2)").html('Not found');
											$("tr.pageinfo-title td:last").html('-');
										}

									// meta keywords
										if (data.meta_keywords !== null) {
											$("tr.meta-keywords td:nth-child(2)").html(data.meta_keywords);
											$("tr.meta-keywords td:last").html(data.meta_keywords.length);
										} else {
											$("tr.meta-keywords td:nth-child(2)").html("Not found");
											$("tr.meta-keywords td:last").html("-");
										}

									// meta description
										if (data.meta_description !== null) {
											$("tr.meta-description td:nth-child(2)").html(data.meta_description);
											$("tr.meta-description td:last").html(data.meta_description.length);
										} else {
											$("tr.meta-description td:nth-child(2)").html("Not found");
											$("tr.meta-description td:last").html("-");
										}

									// meta robots
										if (data.meta_robots !== null) {
											$("tr.meta-robots td:nth-child(2)").html(data.meta_robots);
											$("tr.meta-robots td:last").html(data.meta_robots.length);
										} else {
											$("tr.meta-robots td:nth-child(2)").html("Not found");
											$("tr.meta-robots td:last").html("-");
										}

									// external links
										if (data.external_links !== null) {
											var el = JSON.parse( data.external_links );
											$("tr.external-links td:nth-child(2)").html(el.links+(" ("+el.nofollow+" nofollow)"));
										} else {
											$("tr.external-links td:nth-child(2)").html("Not yet available");
										}

									// internal links
										if (data.internal_links !== null) {
											var il = JSON.parse( data.internal_links );
											$("tr.internal-links td:nth-child(2)").html(il.links+(" ("+il.nofollow+" nofollow)"));
										} else {
											$("tr.internal-links td:nth-child(2)").html("Not yet available");
										}
									// h1
										if (data.h1 !== null) {
											$("tr.h1 td:nth-child(2)").html(data.h1);
											$("tr.h1 td:last").html(data.h1.length);
										} else {
											$("tr.h1 td:nth-child(2)").html('Not found');
											$("tr.h1 td:last").html('-');
										}

									// h2
										if (data.h2 !== null) {
											$("tr.h2 td:nth-child(2)").html(data.h2);
											$("tr.h2 td:last").html(data.h2.length);
										} else {
											$("tr.h2 td:nth-child(2)").html('Not found');
											$("tr.h2 td:last").html('-');
										}

									// bold/strong
										if (data.bold_strong !== null) {
											$("tr.bold-strong td:nth-child(2)").html(data.bold_strong);
											$("tr.bold-strong td:last").html(data.bold_strong.length);
										} else {
											$("tr.bold-strong td:nth-child(2)").html('Not found');
											$("tr.bold-strong td:nth-child(2)").html('-');
										}

									// italic/em
										if (data.italic_em !== null) {
											$("tr.italic_em td:nth-child(2)").html(data.italic_em);
											$("tr.italic_em td:last").html(data.italic_em.length);
										} else {
											$("tr.italic_em td:nth-child(2)").html('Not found');
											$("tr.italic_em td:nth-child(2)").html('-');
										}

									// body
										if (data.body !== null) {
											var body = JSON.parse(data.body);
											$('tr.body-text td:nth-child(2)').html(body.content);
											$('tr.body-text td:last').html(body.length);
										} else {
											$('tr.body-text td:nth-child(2)').html('N/A');
											$('tr.body-text td:last').html('-');
										}

									exportableData = data;
									
									progress_current++;
									check_progress(progress_limit, progress_current);
								} else {
									$.ajax({
										url : ajaxurl,
										type : 'post',
										data : { action: 'wpspy_ajax', q : 'get_page_info', url : domain_raw },
										dataType : 'json',
										beforeSend: function () {
											onLoadingState(".page-info");
										},
										success: function (data) {
											console.log(data);

											// here we enable the submit button again
												enable_button(btn, "Go");
												

											try{
												data.meta.description = ( typeof(data.meta[0].description) !== 'undefined' ) ? data.meta[0].description : data.meta.description;
												data.meta.keywords = ( typeof(data.meta[1].keywords) !== 'undefined' ) ? data.meta[1].keywords : data.meta.keywords;
												data.meta.robots = ( typeof(data.meta[2].robots) !== 'undefined' ) ? data.meta[2].robots : data.meta.robots;
											}catch(e) {
												console.log(e);
											}

											

											var ex_l = {}, in_l = {};
											// save to database
												var data_array = {
													"action" : "wpspy_ajax",
													"q" : "save_activity",
													"url" : domain_raw,
													"canonical_url" : data.canonical_url,
													"title" : data.title,
													"meta_keywords" : data.meta.keywords,
													"meta_description" : data.meta.description,
													"meta_robots" : data.meta.robots,
													"h1" : data.h1,
													"h2" : data.h2,
													"bold_strong" : data.bold_strong,
													"italic_em" : data.italic_em,
													"body" : JSON.stringify(data.body),
													"external_links" : JSON.stringify( { links : data.external_links.links.length, nofollow : data.external_links.nofollow } ),
													"internal_links" : JSON.stringify( { links : data.internal_links.links.length, nofollow : data.internal_links.nofollow } )
												};

												exportableData = data_array;

												$.ajax({
													url : ajaxurl,
													type : 'post',
													// dataType : 'json',
													data : data_array,
													beforeSend : function() {
														console.log(data_array);
													}
												}).done(function (res) {
													console.log(res);
												});


											// append data to html body
												var tbl = "table.tbl-page-info tbody ";
												$(tbl+'tr.url td:nth-child(2)').html(domain_raw);
												$(tbl+'tr.url td:last').html(domain_raw.length);

												var meta_name = "";
												var meta_length = "";

												meta_name = (data.canonical_url !== "") ? data.canonical_url : "Not found";
												console.log("meta_name: "+meta_name);
												meta_length = (meta_name === "Not found" || typeof(meta_name) === 'undefined' || meta_name === null) ? '-' : meta_name.length;

												
												$(tbl+'tr.canonical-url td:nth-child(2)').html(meta_name);
												$(tbl+'tr.canonical-url td:last').html(meta_length);

												meta_name = (data.title !== "") ? data.title : "Not found";
												console.log("meta_name: "+meta_name);
												meta_length = (meta_name === "Not found" || typeof(meta_name) === 'undefined' || meta_name === null) ? '-' : meta_name.length;
											
												$(tbl+'tr.pageinfo-title td:nth-child(2)').html(meta_name);
												$(tbl+'tr.pageinfo-title td:last').html(meta_length);
												
												meta_name = ( typeof(data.meta.keywords) !== "undefined" ) ? data.meta.keywords : "Not found";
												console.log("meta_name: "+meta_name);
												meta_length = (meta_name === "Not found" || typeof(meta_name) === 'undefined' || meta_name === null) ? '-' : meta_name.length;

												$(tbl+'tr.meta-keywords td:nth-child(2)').html(meta_name);
												$(tbl+'tr.meta-keywords td:last').html(meta_length);

												meta_name = ( typeof(data.meta.description) !== "undefined" ) ? data.meta.description : "Not found";
												console.log("meta_name: "+meta_name);
												meta_length = (meta_name === "Not found" || typeof(meta_name) === 'undefined' || meta_name === null) ? '-' : meta_name.length;

												$(tbl+'tr.meta-description td:nth-child(2)').html(meta_name);
												$(tbl+'tr.meta-description td:last').html(meta_length);


												meta_name = ( typeof(data.meta.robots) !== "undefined" ) ? data.meta.robots : "Not found";
												console.log("meta_name: "+meta_name);
												meta_length = (meta_name === "Not found" || typeof(meta_name) === 'undefined' || meta_name === null) ? '-' : meta_name.length;

												$(tbl+'tr.meta-robots td:nth-child(2)').html(meta_name);
												$(tbl+'tr.meta-robots td:last').html(meta_length);
												
												meta_name = (data.external_links.links.length !== 0) ? data.external_links.links.length : '0';
												console.log("meta_name: "+meta_name);
												$(tbl+'tr.external-links td:nth-child(2)').html(meta_name+" ("+data.external_links.nofollow+" nofollow) ");

												meta_name = (data.internal_links.links.length !== 0) ? data.internal_links.links.length : '0';
												console.log("meta_name: "+meta_name);
												$(tbl+'tr.internal-links td:nth-child(2)').html(meta_name+" ("+data.internal_links.nofollow+" nofollow) ");

												meta_name = (data.h1 !== "") ? data.h1 : "Not found";
												console.log("meta_name: "+meta_name);
												meta_length = (meta_name === "Not found" || typeof(meta_name) === 'undefined' || meta_name === null) ? '-' : meta_name.length;

												$(tbl+'tr.h1 td:nth-child(2)').html(meta_name);
												$(tbl+'tr.h1 td:last').html(meta_length);

												meta_name = (data.h2 !== "") ? data.h2 : "Not found";
												console.log("meta_name: "+meta_name);
												meta_length = (meta_name === "Not found" || typeof(meta_name) === 'undefined' || meta_name === null) ? '-' : meta_name.length;

												$(tbl+'tr.h2 td:nth-child(2)').html(meta_name);
												$(tbl+'tr.h2 td:last').html(meta_length);

												meta_name = (data.bold_strong !== "") ? data.bold_strong : "Not found";
												console.log("meta_name: "+meta_name);
												meta_length = (meta_name === "Not found" || typeof(meta_name) === 'undefined' || meta_name === null) ? '-' : meta_name.length;

												$(tbl+'tr.bold-strong td:nth-child(2)').html( limitString(meta_name) );
												$(tbl+'tr.bold-strong td:last').html(meta_length);

												meta_name = (data.italic_em !== "") ? data.italic_em : "Not found";
												console.log("meta_name: "+meta_name);
												meta_length = (meta_name === "Not found" || typeof(meta_name) === 'undefined' || meta_name === null) ? '-' : meta_name.length;

												$(tbl+'tr.italic-em td:nth-child(2)').html( limitString(meta_name) );
												$(tbl+'tr.italic-em td:last').html(meta_length);

												meta_name = (data.body.content !== "") ? data.body.content : "Not found";
												console.log("meta_name: "+meta_name);
												meta_length = (data.body.length === "") ? '-' : data.body.length;

												$(tbl+'tr.body-text td:nth-child(2)').html(meta_name);
												$(tbl+'tr.body-text td:last').html(meta_length);

											progress_current++;
											check_progress(progress_limit, progress_current);
										},
										error: function (data) {
											console.warn(data);
										},
										complete: function () {
											removeLoadingState(".page-info");
										}
									});
								}
							},
							error: function () {

							},
							complete: function () {
								hide_loader();
							}
						});
					}catch(Exception) {
						console.warn(Exception);
					}

					
				} else if (page === "links") {
					e.preventDefault();
					disable_button(btn, 'Please wait...');
					progress_limit = 1;
					$.ajax({
						url : ajaxurl,
						type : 'post',
						dataType : 'json',
						data : { action: 'wpspy_ajax', q : 'get_ie_links', url : domain_raw }
					}).done(function (data) {
						enable_button(btn, 'Go');
						exportableData = data;
						
						var _data_i =  {"nofollow" : data.internal_links.nofollow, "links" : data.internal_links.links.length };
						var _data_e =  {"nofollow" : data.external_links.nofollow, "links" : data.external_links.links.length };
						console.log("_data_i: ");
						console.log(_data_i);
						console.log("_data_e: ");
						console.log(_data_e);
						var data_array = {
							"action" : "wpspy_ajax",
							"q" : "save_activity",
							"url" : domain_raw,
							"internal_links" : JSON.stringify(_data_i),
							"external_links" : JSON.stringify(_data_e)
						};

						// save to database
							$.ajax({
								url : ajaxurl,
								type : 'post',
								dataType : 'json',
								data : data_array
							}).done(function (res) {
								console.log(res);
							});	

						// append data to html body
							$("#external_links_count").html(data.external_links.links.length);
							$("#external_nofollow_count").html(data.external_links.nofollow);

							$("#internal_links_count").html(data.internal_links.links.length);
							$("#internal_nofollow_count").html(data.internal_links.nofollow);

						// extract all external links
							$.each($("tr.external-link"), function (i, row) {
								$(row).remove();
							});
							var li = "";
							if (data.external_links.links.length > 0) {
								$.ajax({
									url : ajaxurl,
									type: 'post',
									data : { action: 'wpspy_ajax', q : 'get_rtlimit' }
								}).done(function (data) {
									recommended_tools_limit = data;
								});
								
								$.each(data.external_links.links, function (i, row) {
									li += '<tr class="url external-link">'+
											'<td>'+(i+1)+':</td>'+
											'<td>'+
												'<a href="'+row.url+'" target="_blank">'+row.url+'</a>'+
											'</td>'+
											'<td><div class="anchor-text">'+row.text+'</div></td>'+
										'</tr>';
								});
								$("tr.external-links-outer-row").after(li);
								var random_array = data.external_links.links.sort(randomize);
								$("div.recommended-tools .content").html("");
								$.each(random_array, function (i, row) {
									console.log('row: '+row);
									if (i < recommended_tools_limit) {
										$("div.recommended-tools .content").append('<div class="entry">'+
											'<div class="left">'+
											'<a href="'+row.url+'" target="_blank">'+row.url+'</a></div></div>');
									}
								});
							} else {
								$("div.recommended-tools .content").html("");
							}

							$.each($("tr.internal-link"), function (i, row) {
								$(row).remove();
							});

						// extract all internal links
							if (data.internal_links.links.length > 0) {
								li = "";
								$.each(data.internal_links.links, function (i, row) {
									li += '<tr class="url internal-link">'+
											'<td>'+(i+1)+':</td>'+
											'<td>'+
												'<a href="'+row.url+'" target="_blank">'+row.url+'</a>'+
											'</td>'+
											'<td><div class="anchor-text">'+row.text+'</div></td>'+
										'</tr>';
								});
								$("tr.internal-links-outer-row").after(li);
							}

						progress_current++;
						check_progress(progress_limit, progress_current);

						findLinksImages();
						imagePreview();
						$.each($("div.search-engine-results .content .entry .right a"), function (i, row) {
							$(row).addClass("spy-icon spy-icon-eye");
							$(row).attr("href", $(row).data('link')+encodeURIComponent(domain_raw));
						});
					});
				} else if (page === "seo-stats") {
					e.preventDefault();
					disable_button(btn, 'Please wait...');
					
					$.ajax({
						url : ajaxurl,
						type : 'post',
						data : { action: 'wpspy_ajax', q : 'check_status', url : domain_raw, option : 'seo_stats' },
						dataType : 'json'
					}).done(function (data) {
						enable_button(btn, "Go");
						console.log( typeof(data) );
						

						if (data !== false && data[0].alexa_rank === 0) {
							data = false;
						}

						$("#archived").attr('href', '//web.archive.org/web/*/'+domain_raw).addClass('spy-icon').addClass('spy-icon-eye');
						$("#google_cached").attr('href', '//webcache.googleusercontent.com/search?cd=1&hl=en&ct=clnk&gl=us&q=cache:'+domain_raw)
							.addClass('spy-icon').addClass('spy-icon-eye');

						// if (data === false) {
						if (typeof(data) != 'object') { // No previous results for this domain
							progress_limit = 4;
							progress_current = 0;
							hide_loader();

							data_array = {};

							data_array.q = "save_activity";
							data_array.url = domain_raw;
							data_array.action = 'wpspy_ajax';

							/* let's get the all the data for the Ranks Box */
								$.ajax({
									url : ajaxurl,
									type : 'post',
									data : { action: 'wpspy_ajax', q : 'get_ranks', url : domain_raw },
									dataType : 'json',
									beforeSend : function () {
										onLoadingState(".rank");
									},
									success: function (data) {
										console.log(data);
										$.each(data, function (i, row) {
											$("#"+i).html(row);
											if (i === "quantcast_traffic") {
												i = i+"_rank";
											}
											data_array[i] = row;
										});
									},
									error: function (data) {
										console.log(data);
										progress_current++;
									},
									complete: function () {
										removeLoadingState(".rank")
									}
								});

							/* let's get all the backlinks data */
								$.ajax({
									url : ajaxurl,
									type : 'post',
									data : { action: 'wpspy_ajax', q : 'get_backlinks', url : domain_raw },
									dataType : 'json',
									beforeSend : function () {
										onLoadingState(".backlinks");
									},
									success: function (data) {
										$.each(data, function (i, row) {
											extract_json_to_div(row, $("div.backlinks ."+i+" .right"), i);
											data_array["backlinks_"+i] = row;
										});
										$('.backlinks .box-title .success').fadeOut(function() { $(this).remove(); });
										$('.backlinks button[data-widget="collapse"]').trigger('click');
										progress_current++;
										check_progress_and_save(progress_limit, progress_current, data_array);
									},
									error: function (data) {
										console.log(data);
										progress_current++;
										check_progress_and_save(progress_limit, progress_current, data_array);
									},
									complete: function () {
										removeLoadingState(".backlinks")
									}
								});

							/* let's get all the pages indexed data */
								$.ajax({
									url : ajaxurl,
									type : 'post',
									data : { action: 'wpspy_ajax', q : 'get_pages_indexed', url : domain_raw },
									dataType : 'json',
									beforeSend : function () {
										onLoadingState(".pages-indexed");
									},
									success: function (data) {
										$.each(data, function (i, row) {
											extract_json_to_div(row, $("div.pages-indexed ."+i+" .right"), i);
											data_array["page_indexed_"+i] = row;
										});
										$('.pages-indexed .box-title .success').fadeOut(function() { $(this).remove(); });
										$('.pages-indexed button[data-widget="collapse"]').trigger('click');
										progress_current++;
										check_progress_and_save(progress_limit, progress_current, data_array);
									},
									error: function (data) {
										console.log(data);
										$('.pages-indexed .box-title span').fadeOut(function() { $(this).remove(); });
										$('.pages-indexed button[data-widget="collapse"]').trigger('click');
										progress_current++;
										check_progress_and_save(progress_limit, progress_current, data_array);
									},
									complete: function () {
										removeLoadingState(".pages-indexed")
									}
								});

							/* let's get the alexa rank in country */
								$.ajax({
									url : ajaxurl,
									type : 'post',
									data : { action: 'wpspy_ajax', q : 'get_alexa_rank_in_country', url : domain_raw },
									dataType : 'json'
								}).done(function (data) {
									console.log(data);

									$.each(data, function (i, row) {
										if (i === "alexa_rank_in_country") {
											row = JSON.stringify(row);
										}
										data_array[i] = row;
									});

									progress_current++;
									check_progress_and_save(progress_limit, progress_current, data_array);
								}).fail(function() {
									data_array.alexa_rank_in_country = "";
									progress_current++;
									check_progress_and_save(progress_limit, progress_current, data_array);
								});

							/* let's set the exportable data */
							exportableData = data_array;
						} else {
							progress_limit = 1;
							console.log(data);
							data = data[0];
							exportableData = data;
							enable_button(btn, "Go");

							$("#alexa_rank").html(data.alexa_rank);
							$("#quantcast_traffic").html(data.quantcast_traffic);
							// $("#google_page_rank").html(data.google_page_rank);
							$.each(data, function (i, row) {
								// pages indexed
									var index = i.indexOf('page_indexed_', 0);
									if (index > -1) {
										extract_json_to_div(row, $("div.pages-indexed ."+i.replace("page_indexed_", "")+" .right"), i);
									}

									index = i.indexOf('backlinks_', 0);
									if (index > -1) {
										extract_json_to_div(row, $("div.backlinks ."+i.replace("backlinks_", "")+" .right"), i);
									}
								
							});

							progress_current++;
							check_progress(progress_limit, progress_current);

							if (data.alexa_rank_in_country === null) {
								/* let's get the alexa rank in country */
								data_array = { action : 'wpspy_ajax', q : 'save_activity', url : domain_raw };
								$.ajax({
									url : ajaxurl,
									type : 'post',
									data : { action: 'wpspy_ajax', q : 'get_alexa_rank_in_country', url : domain_raw },
									dataType : 'json'
								}).done(function (data) {
									console.log(data);

									$.each(data, function (i, row) {
										if (i === "alexa_rank_in_country") {
											row = JSON.stringify(row);
										}
										data_array[i] = row;
									});

									data_array.action = 'wpspy_ajax';

									$.ajax({
										url : ajaxurl,
										type : post,
										data : data_array,
										dataType : json
									}).done(function (data) {
										console.log(data);
									}).fail(function (data) {
										console.log(data);
										console.log("<>alexa_rank_in_country!!!");
									});

								}).fail(function (data) {
									console.log(data);
								});
							}

						}
					});

					

				} else if (page === "social-stats") {
					e.preventDefault();
					disable_button(btn, "Please wait...");

					$.ajax({
						url : ajaxurl,
						type : 'post',
						data : { action: 'wpspy_ajax', q : 'check_status', option : 'social_stats', url : domain_raw },
						dataType : 'json'
					}).done(function (data) {
						progress_limit = 1;
						if (data !== false && data[0].score_sentiment == "-") {
							data = false;
						} else {
							if (typeof(data[0]) !== 'undefined') {
								data = data[0];
							}
						}

						if (data !== false) {
							console.log(data);
							enable_button(btn, "Go");
							exportableData = data;
							$.each(data, function (i, row) {
								$("."+i+" .right span").html(row);
							});
							$(".social-metrics .mentions .right").html('<a href="//socialmention.com/search?t=all&q='+encodeURIComponent('//'+domain_raw)+'&btnG=Search" target="_blank" class="spy-icon spy-icon-eye"></a>');
							

							progress_current++;
							check_progress(progress_limit, progress_current);
						} else {
							$.ajax({
								url : ajaxurl,
								type : 'post',
								dataType : 'json',
								data : { action: 'wpspy_ajax', q : 'get_social_stats', url : domain_raw },
								beforeSend : function() {
									$('div.social-sns .entry .right').html('');
									$(".site-metrics .entry .right").html("");
									$('.social-sns .title, .social-metrics .box-title').append('<span class="success thin"> (<i class="fa fa-spinner fa-spin"></i> Loading data...)</span>');
									$('.social-sns button[data-widget="collapse"]').trigger('click');
								}
							}).done(function (data) {
								console.log(data);

								// prepare variable to handle data
									var data_array = {
										url : domain_raw,
										q : "save_activity"
									};

								var span = $('<span></span>');
								$.each(data.social_shares, function (i, row) {
									console.log('i: '+i+" row: "+row);
									span.attr("id", i).html(row);
									// '<span id="'+i+'">'+row+'</span>'
									if (i == 'twitter_count') {
										data_array[i] = span.text();
									} else {
										data_array[i] = row;
									}
									$("div.social-sns ."+i+" .right").html( $('<span></span>').html(span).html() );
									
								});

								data_array.action = 'wpspy_ajax';

								// save to database
									$.ajax({
										url : ajaxurl,
										type : 'post',
										dataType : 'json',
										data : data_array
									}).done(function (res) {
										console.log(res);
									});

								exportableData = data_array;
								
								$('.social-sns .box-title .success').fadeOut(function() { $(this).remove(); });
								$('.social-sns button[data-widget="collapse"]').trigger('click');
							});

							


							$(".site-metrics .entry .right span").html("");

							get_social_mention(domain_raw);
							enable_button(btn, "Go");	

							progress_current++;
							check_progress(progress_limit, progress_current);
						}
					});
					
					

				} else if (page === "traffic") {
					progress_limit = 1;

					$.ajax({
						url : ajaxurl,
						type : "post",
						dataType : "json",
						data : { action: 'wpspy_ajax', q : 'get_site_metrics', url : domain_raw },
						beforeSend : function() {
							disable_button(btn, "Please wait...");
						}
					}).success(function (data) {
						console.log(data);

						enable_button(btn, "Go");
						var c_graph = $("#choose_traffic_graph option:selected").val();
						
						update_traffic_graph(c_graph, domain_raw);
						
						exportableData = data;

						$.each(data, function (i, row) {
							if (i !== "alexa_rank_in_country") {
								$("div.entry .right #"+i).html(row);
							} else {
								$(".rank-in-country tbody").html("");

								if (typeof(row) === null || row === null || row === undefined || row.length === 0) {
									$(".rank-in-country tbody").append('<tr>'+
										'<td colspan="4">'+
											'<span class="failed">No data available for now. Check out the <a href="'+$("#nav_seo_stats").data("href")+'">SEO Stats</a>  for '+domain_raw+' and try again.</span>'+
										'</td>'+
									'</tr>');
								} else {
									$.each(row, function (i2, row2) {
										if (is_numeric(i2)) {
											$(".rank-in-country tbody").append('<tr>'+
												'<td>'+
													'<span class="flag flag-'+row2.country_code+'"></span>'+
												'</td>'+
												'<td>'+
													'<span>'+row2.country+'</span>'+
												'</td>'+
												'<td>'+row2.percent_of_visitors+'</td>'+
												'<td>'+row2.rank+'</td>'+
											'</tr>');
										} else {
											$(".rank-in-country tbody").append('<tr>'+
												'<td>'+
													'<span class="flag flag-"></span>'+
												'</td>'+
												'<td>'+
													'<span>-</span>'+
												'</td>'+
												'<td>-</td>'+
												'<td>-</td>'+
											'</tr>');
										}
									});
								}
							}
						});

						progress_current++;
						check_progress(progress_limit, progress_current);
					}).fail(function (data) {
						enable_button(btn, "Go");

						progress_current++;
						check_progress(progress_limit, progress_current);
					});
				} else if (page === "previous-searches") {
					disable_button(btn, "Please wait...");
					var content = "";
					progress_limit = 1;

					$.ajax({
						url : ajaxurl,
						type : 'post',
						dataType : 'json',
						data : { action: 'wpspy_ajax', url : $('#wpspy_url').val(), q : 'get_history_list' }
					}).done(function (data) {
						$.each(data.data, function (i, row) {
							content += '<tr><td>'+row[0]+'</td><td>'+row[1]+'</td></tr>';
						});

						$("#div_page_info_history, #history").html("");
						var tbl = '<table id="page_info_history2" class="table tbl-page-info">'+
										'<thead>'+
											'<tr>'+
												'<th>Date</th>'+
												'<th>Action</th>'+
											'</tr>'+
										'</thead>'+
										'<tbody>'+content+'</tbody>'+
									'</table>';

						$("#div_page_info_history").html(tbl);
						
						if ($("#page_info_history2 body").html() !== "") {
							var data_tbl = $('#page_info_history2').DataTable();
						    $("#div_history_table_outer").html($("#div_page_info_history").html());
						}

						enable_button(btn, "Go");
						progress_current++;
						check_progress(progress_limit, progress_current);
					}).fail(function() {
						progress_current++;
						check_progress(progress_limit, progress_current);
					});
				} else if (page === "graphs") {
					progress_limit = 1;

					$(".chart-options select").children('option').removeAttr("selected").children('option:first').attr("selected", "selected");

					$.ajax({
						url : ajaxurl,
						type : 'post',
						dataType : 'json',
						data : { action: 'wpspy_ajax', "q" : "get_chart_data", "col" : $("#chart_options option:selected").val(), "url" : $("#wpspy_url").val() }
					}).done(function (data) {
						progress_current++;
						check_progress(progress_limit, progress_current);

						enable_button(btn, "Go");
						FusionCharts.ready(function() {
							var revenueChart = new FusionCharts({
								type: "MSColumn2D",
								width: "950",
								height: "450",
								dataFormat: "json",
								dataSource: {
								   "chart":{
									  "caption":"Alexa Rank - "+$("#wpspy_url").val(),
									  "bgcolor":"ffffff,ffffff",
									  "showlabels":"1",
									  "showvalues":"0",
									  "showborder":"0",
									  "decimals":"2"
									  // "numberprefix":""
								   },
								   "categories":[
									  {
										 "category": eval(data.dates).sort(comp)
									  }
								   ],
								   "dataset":[
									  {
										"seriesname": $("#wpspy_url").val(),
										"color":getRandomColor(),
										"data": eval(data.values)
									  },
								   ]
								   
								}
							});
							revenueChart.render("chart-area");
						});

					}).fail(function() {
						progress_current++;
						check_progress(progress_limit, progress_current);
					});
				}

			} else {
				e.preventDefault();
				btn.after('<span class="red">Oops! That\'s not a good url.</span>');
				btn.next('span').fadeOut(4000, function() {
					$(this).remove();
				});
				enable_button(btn, "Go");
			}
		});

		$(document).on("click", "a.history-actions", function() {
			var $this = $(this);
			var domain_raw = $("#wpspy_url").val();
			var $option = $this.data('action');

			$.ajax({
				url : ajaxurl,
				type : 'post',
				dataType : 'json',
				data : { action: 'wpspy_ajax', q : 'get_history', id : $this.data('id'), option : $option }
			}).success(function (data) {
				console.log(data);

				if ($option === "site_info") {
					$("#history_data .dns").html('');
					var whois = '', wordpress_data = '', onsite = '', plugins_str = '';

					data[0].wordpress_data = String(data[0].wordpress_data).replace(/\\/g, "");
					data[0].dns = String(data[0].dns).replace(/\\/g, "");

					var sitemap = (data[0].sitemap_index === 'true') ? '<span class="spy-icon-check spy-icon"></span>' : '<span>N/A</span>';
					var robot = (data[0].robot === 'true') ? '<span class="spy-icon-check spy-icon"></span>' : '<span>N/A</span>';
					onsite ='<div class="on-site box">'+
								'<div class="title">On-site</div>'+
								'<div class="content">'+
									'<div class="entry">'+
										'<div class="left"><span class="spy-icon spy-icon-robots"></span>robots.txt</div>'+
										'<div class="right">'+robot+'</div>'+
									'</div>'+
									'<div class="entry">'+
										'<div class="left"><span class="spy-icon spy-icon-sitemap"></span>sitemap_index.xml</div>'+
										'<div class="right">'+sitemap+'</div>'+
									'</div>'+
								'</div>'+
							'</div>';
					whois = '<div class="geolocation box">'+
									'<div class="title">Geolocation</div>'+
									'<div class="content">'+
										'<div class="entry">'+
											'<div class="left">'+
												'<span class="spy-icon spy-icon-ip"></span>IP'+
											'</div>'+
											'<div class="right">'+
												'<span id="ip">'+data[0].ip+'</span>'+
											'</div>'+
										'</div>'+
										'<div class="entry">'+
											'<div class="left">'+
												'<span class="spy-icon spy-icon-city"></span>City'+
											'</div>'+
											'<div class="right">'+
												'<span id="city">'+data[0].city+'</span>'+
											'</div>'+
										'</div>'+
										'<div class="entry">'+
											'<div class="left">'+
												'Country'+
											'</div>'+
											'<div class="right">'+
												'<span id="city">'+data[0].country+'</span>'+
												'<span class="flag flag-'+data[0].country_code+'"></span>'+
											'</div>'+
										'</div>'+
									'</div>'+
								'</div>';
					
					var wp_data = JSON.parse(data[0].wordpress_data);
					console.log(wp_data);

					if (wp_data !== null && wp_data.hasOwnProperty('theme')) {
						if (wp_data.theme.hasOwnProperty('name') && typeof(wp_data.theme.name) !== null && typeof(wp_data.theme.name) !== 'undefined') {
							$("#wordpress_version").html(wp_data.version);
							$(".wordpress-data .content .plugin").html("");

							if (wp_data.free_plugins !== 0) {

								$.each(wp_data.free_plugins, function (i, row) {
									var name = capitalizeSlug(row.name);
									console.log(name);
									var download = ($.trim(row.download) !== "N/A") ? '<a href="'+row.download+'" class="spy-icon spy-icon-download"></a>' : '';
									var link = ($.trim(row.link) !== "N/A") ? '<a href="'+$.trim(row.link)+'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '<a href="//google.com/search?q=wordpress%20'+row.name+'" target="_blank" class="spy-icon spy-icon-eye"></a>';
									plugins_str += '<div class="entry">'+
											'<div class="left">'+
												name+
											'</div>'+
											'<div class="right">'+
												download+
												link+
												'<span class="spy-icon spy-icon-plugin"></span>'+
											'</div>'+
										'</div>';
								});
							}

							if (wp_data.commercial_plugins !== 0) {
								// extract commercial plugins
								$.each(wp_data.commercial_plugins, function (i, row) {
									var name = capitalizeSlug(row.name);
									console.log(name);
									var download = ($.trim(row.download) !== "N/A") ? '<a href="'+row.download+'" class="spy-icon spy-icon-download"></a>' : '';
									var link = ($.trim(row.link) !== "N/A") ? '<a href="'+$.trim(row.link)+'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '<a href="//google.com/search?q=wordpress%20'+row.name+'" target="_blank" class="spy-icon spy-icon-eye"></a>';
									plugins_str += '<div class="entry">'+
											'<div class="left">'+
												name+
											'</div>'+
											'<div class="right">'+
												download+
												link+
												'<span class="spy-icon spy-icon-plugin"></span>'+
											'</div>'+
										'</div>';
								});
							}

							$(".wordpress-data .content .plugin").html(plugins_str);

							var t_download = ($.trim(wp_data.theme.download) !== "N/A") ? '<a href="'+$.trim(wp_data.theme.download)+'" target="_blank" class="spy-icon spy-icon-download"></a>' : '';
							var t_link = ($.trim(wp_data.theme.link) !== "N/A") ? '<a href="'+$.trim(wp_data.theme.link)+'" target="_blank" class="spy-icon spy-icon-eye"></a>' : '<a href="//google.com/search?q=wordpress%20'+wp_data.theme.name+'" target="_blank" class="spy-icon spy-icon-eye"></a>';
							$("div.wordpress-data div.theme").html('<div class="entry">'+
								'<div class="left">'+wp_data.theme.name+'</div>'+
								'<div class="right">'+
									t_download+
									t_link+
									'<span class="spy-icon spy-icon-theme"></span>'+
								'</div>'+
							'</div>');
						}
					}
					var wp_dns = JSON.parse(data[0].dns);
					var dns = "";
					if (wp_dns !== null && wp_dns !== "") {
						$("#history_data .dns .content").html("");
						$.each(wp_dns, function (i, row) {
							dns += '<div class="entry"><div class="left"><span class="spy-icon spy-icon-dns"></span>DNS '+(i+1)+'</div><div class="right"><span>'+row+'</span></div></div>';
						});
					}
					dns = '<div class="box dns"><div class="title">Domain Info</div><div class="content">'+dns+'</div></div>';
					var history_data = $("#history_data").html();

					$("#dialog").html('<div class="col-5">'+onsite+dns+'</div><div class="col-5">'+whois+history_data+'</div>');
					
					$("#dialog").attr("title", $this.text()).dialog({
						height: 440,
						width: 910,
						modal: true
					});
				} else if ($option === "page_info") {
					try{
						// append data to html body
							$("#page_info_history_hidden tbody").html("");
							$("div#div_page_info_history_hidden table#page_info_history_hidden tbody").append('<tr><td>URL:</td><td>'+domain_raw+'</td><td>'+domain_raw.length+'</td></tr>');
							$.each( data[0], function (i, row) {
								if (i !== "body") {
									if (typeof(row) !== null && typeof(row) !== 'undefined' && row !== null) {
										$("#page_info_history_hidden tbody").append('<tr><td>'+capitalizeSlug(i.replace(/[_]/g, ' '))+'</td><td>'+row+'</td><td>'+row.length+'</td></tr>');
									} else {
										$("#page_info_history_hidden tbody").append('<tr><td>'+capitalizeSlug(i.replace(/[_]/g, ' '))+'</td><td>Not found</td><td>-</td></tr>');
									}
								} else {
									var body = {};
									body = $.parseJSON(row);
									$("#page_info_history_hidden tbody").append('<tr><td>'+capitalizeSlug(i.replace(/[_]/g, ' '))+'</td><td>'+body.content+'</td><td>'+body.length+'</td></tr>');

								}
							});
							
					}catch(e) {
						console.log(e);
					}
					$("#dialog").html("");
					$("#dialog").html('<div class="box">'+$("#div_page_info_history_hidden").html()+'</div>');
					$(".ui-dialog-title").html($this.text());
					$("#dialog").attr("title", $this.text()).dialog({
						height: 440,
						width: 910,
						modal: true
					});

					
				} else if ($option === "seo_stats") {
					var rank = '<div class="box rank">'+
						'<div class="title">Rank</div>'+
						'<div class="content">'+
							'<div class="entry">'+
								'<div class="left">'+
									'<span class="spy-icon-alexa spy-icon"></span>'+
									'Alexa Traffic Rank'+
								'</div>'+
								'<div class="right">'+
									'<span id="alexa_rank">'+data[0].alexa_rank+'</span>'+
								'</div>'+
							'</div>'+
							'<div class="entry">'+
								'<div class="left">'+
									'<span class="spy-icon-quantcast spy-icon"></span>'+
									'Quantcast Traffic Rank'+
								'</div>'+
								'<div class="right">'+
									'<span id="quantcast_traffic">'+data[0].quantcast_traffic_rank+'</span>'+
								'</div>'+
							'</div>'+
						'</div>'+
					'</div>';
					var pages_indexed_arr = [
						"page_indexed_ask", "page_indexed_baidu", "page_indexed_bing", "page_indexed_goo", 
						"page_indexed_google", "page_indexed_sogou", "page_indexed_yahoo", "page_indexed_yandex", 
						"page_indexed__360"
					];

					var backlinks_arr = [
						"backlinks_alexa",
						"backlinks_google",
						"backlinks_open_site_explorer",
						"backlinks_sogou"
					];
					var pages_indexed = '', backlinks = '';

					$.each(data[0], function (i, row) {

						if (inArray(i, pages_indexed_arr)) {

							var value = '';

							if (i =="_360") { i = "360"; }

							if (/(http)\S+/i.test(row) === true) {
								value = '<a href="'+row+'" target="_blank" class="spy-icon spy-icon-eye"></a>';
							} else {
								value = '<span id="'+i+'">'+row+'</span>';
							}
							pages_indexed+='<div class="entry '+i.substring(13)+'">'+
								'<div class="left">'+
									'<span class="spy-icon-'+i.substring(13)+' spy-icon"></span>'+capitalizeSlug(i.substring(13))+
								'</div>'+
								'<div class="right">'+
									value+
								'</div>'+
							'</div>';
						}
					});

					var fi_pages_indexed = '<div class="box pages-indexed"><div class="title">Pages Indexed</div><div class="content">'+pages_indexed+'</div></div>';

					
					
					$.each(data[0], function (i, row) {

						if (inArray(i, backlinks_arr)) {

							var value = '';

							if (/(http)\S+/i.test(row) === true) {
								value = '<a href="'+row+'" target="_blank" class="spy-icon spy-icon-eye"></a>';
							} else {
								value = '<span id="'+i+'">'+row+'</span>';
							}
							backlinks+='<div class="entry '+i.substring(10)+'">'+
								'<div class="left">'+
									'<span class="spy-icon-'+i.substring(10)+' spy-icon"></span>'+capitalizeSlug(i.substring(10))+
								'</div>'+
								'<div class="right">'+
									value+
								'</div>'+
							'</div>';
						}

					});

					backlinks = '<div class="box backlinks"><div class="title">Backlinks</div><div class="content">'+backlinks+'</div></div>';

					$("#dialog").html('<div class="col-4">'+rank+backlinks+'</div><div class="col-4">'+fi_pages_indexed+'</div>');
					
					$("#dialog").attr("title", $this.text()).dialog({
						height: 440,
						width: 910,
						modal: true
					});
				} else if ($option === "social_stats") {
					data = data[0];
					$("#facebook_likes").html(data.facebook_count);
					$("#gplus").html(data.google_count);
					$("#stumbleupon").html(data.stumbleupon_count);
					$("#twitter").html(data.twitter_count);
					
					var linkedin_count  = (typeof(data.linkedin_count ) !== 'undefined') ? data.linkedin_count  : 'N/A';
					var pinterest_count = (typeof(data.pinterest_count) !== 'undefined') ? data.pinterest_count : 'N/A';
					
					$("#linkedin").html(linkedin_count);
					$("#pinterest").html(pinterest_count);

					$("#strength").html(data.score_strength);
					$("#sentiment").html(data.score_sentiment);
					$("#passion").html(data.score_passion);
					$("#reach").html(data.score_reach);

					$("#dialog").html('<div class="col-4"><div class="box">'+$("#div_social_stats_history").html()+
						'</div></div><div class="col-4"><div class="box">'+
						$("#div_social_metrics_history").html()+
						'</div></div>');
					$(".ui-dialog-title").html($this.text());
					$("#dialog").attr("title", $this.text()).dialog({
						height: 440,
						width: 910,
						modal: true
					});
				} else if ($option === "traffic") {
					data = data[0];
					$("#alexa_rank").html(data.alexa_rank);
					$("#quantcast_traffic_rank").html(data.quantcast_traffic_rank);

					$("#alexa_rank_in_country table tbody").html("<tr><td></td><td></td><td></td><td></td></tr>");
					$("div.traffic").parent("div.box").parent("div").removeClass("col-4").addClass("col-6");
					
					var _country_code = "", _percent_of_v = "", _rank_in_c = "", _country = "";
					if ((data.alexa_rank_in_country !== null) && (typeof(data.alexa_rank_in_country) !== 'undefined')) {
						var a_rank_in_country = JSON.parse(data.alexa_rank_in_country.replace(/\\/g, ''));
						console.log("a_rank_in_country:");
						console.log(a_rank_in_country);
						if (a_rank_in_country.length > 1) {
							$.each(a_rank_in_country, function (i, row) {
								_country_code = (row.country_code === "N/A") ? "-" : "<span class='flag flag-"+row.country_code+"'></span>";
								_percent_of_v = (row.percent_of_visitors === "N/A") ? "-" : row.percent_of_visitors;
								_rank_in_c = (row.rank === "N/A") ? "-" : row.rank;
								_country = (row.country === "N/A") ? "-" : row.country;
								$("#alexa_rank_in_country table tbody").append('<tr><td>'+_country_code+'</td><td>'+_country+
									'</td><td>'+_percent_of_v+'</td><td>'+_rank_in_c+'</td></tr>');
							});
						} else {
							$("#alexa_rank_in_country table tbody").append('<tr><td>-</td><td></td><td>-</td><td>-</td></tr>');
						}
					}
					$("#bounce_rate").html(data.bounce_rate);
					$("#daily_pageviews_per_visitor").html(data.daily_pageviews_per_visitor);
					$("#dailytime_onsite").html(data.dailytime_onsite);

					$("#dialog").html('<div class="col-4"><div class="box">'+
						$("#div_traffic_history").html()+'</div></div>'+
						'<div class="col-4"><div class="box">'+$("#div_site_metrics_history").html()+'</div></div>');
					$(".ui-dialog-title").html($this.text());
					$("#dialog").attr("title", $this.text()).dialog({
						height: 440,
						width: 910,
						modal: true
					});
				} else if ($option === "link") {
					data = data[0];
					var str = JSON.stringify(data);
					data = JSON.parse(str);
					

					if (data.external_links !== null) {
						var ex_l = (data.external_links).replace(/\\/g, "");
						console.log("ex_l: "+ex_l);
						var external = JSON.parse(ex_l);
						if (typeof(external) !== 'undefined') {
							$("#external_links_count").html(external.links);
							$("#external_nofollow_count").html(external.nofollow);
						}
					} else {
						$("#external_links_count").html("N/A");
						$("#external_nofollow_count").html("0");
					}

					if (data.internal_links !== null) {
						var in_l = (data.internal_links).replace(/\\/g, "");
						console.log("in_l: "+in_l);
						var internal = JSON.parse(in_l);

						if (typeof(internal) !== 'undefined') {
							$("#internal_links_count").html(internal.links);
							$("#internal_nofollow_count").html(internal.nofollow);
						}
					} else {
						$("#internal_links_count").html("N/A");
						$("#internal_nofollow_count").html("0");
					}

					$("#dialog").html('<div class="box">'+$("#div_links_history").html()+'</div>');
					$(".ui-dialog-title").html($this.text());
					$("#dialog").attr("title", $this.text()).dialog({
						height: 440,
						width: 910,
						modal: true
					});
				}

			}).fail(function (data) {
				console.log(data);
			});
		});

		$(document).on("click", "#recommended_tools_settings", function () {
			$("#settings_dialog").dialog({
				height: 145,
				width: 310,
				modal: true
			});
		});


		$(document).on("click", "#show_backlinks", function(e) {
			e.preventDefault();
			var url = $.trim( $("#wpspy_url").val() );
			if (url == "") {
				return false;
			}

			var $this = $(this);
			globalDomain = (url).substr(7);

			try{
				$.ajax({
					url : ajaxurl,
					type : 'post',
					dataType : 'json',
					data : { action: 'wpspy_ajax', 'q' : 'get_external_backlinks', 'page' : '1', url : globalDomain, 'num' : $("#backlinks_num").val() },
					beforeSend : function() {
						disable_button($this, "Please wait...");
					}
				}).done(function (data) {
					enable_button($this, "Get Backlinks");
					$(".tbl-backlinks-holder").html(data.backlinks_html);
					$("img[src='//www.openlinkprofiler.org/public/img/lm-add.png']").parent('a').remove();
					$("#backlinks_pagination").html(data.pagination);

					showToolTip();

					$("#openlink_invitation").fadeOut();
				}).error(function (data) {
					console.warn(data);
					// let's invite the user to signup for an openlinkprofiler account
					enable_button($this, "Get Backlinks");
					$("#openlink_invitation").fadeIn();
				});
			}catch(Exception) {
				console.log(Exception);
				enable_button($this, "Get Backlinks");
				$("#openlink_invitation").fadeIn();
			}
		});

		$(document).on("click", "#backlinks_pagination ul li a", function(e) {
		   	e.preventDefault();
		   	var $this = $(this);

		   	if ($this.hasClass("disabled") || $this.attr("disabled") == "disabled") {
		   		return false;
		   	}

		   	var page = $.trim( $this.html().replace(/<\/?[^>]+(>|$)/g, "") );

		   	if (page == "...") {
		   		page = parseInt($this.parent('li').children('a').text()) + 1;
		   	}

		   	globalDomain = ($("#wpspy_url").val()).substr(7);

		   	try{

		   		if (page == "Previous" || page == "Next") {
		   			var href = $this.attr("href");
					var regex = /page=(.*?)&num/;
					var matches = regex.exec(href);
					page = matches[1];
		   		}

			   $.ajax({
					url : ajaxurl,
					type : 'post',
					dataType : 'json',
					data : { action: 'wpspy_ajax', 'q' : 'get_external_backlinks', 'page' : page, url : globalDomain, 'num' : $("#backlinks_num").val() },
					beforeSend : function() {
						disable_button($this, "Please wait...");
					}
				}).done(function (data) {
					console.log(data);
					enable_button($this, "Get Backlinks");
					$(".tbl-backlinks-holder").html(data.backlinks_html);
					$("img[src='//www.openlinkprofiler.org/public/img/lm-add.png']").parent('a').remove();
					$("#backlinks_pagination").html(data.pagination);

					showToolTip();
					
				});
			}catch(Exception) {
				console.log(Exception);
				enable_button($this, "Get Backlinks");
			}
		});

		$(document).on("submit", $("#rtl_settings"), function () {
			var btn = $("#save_settings");
			var val = $("#settings").val();
			$.ajax({
				url : ajaxurl,
				type : 'post',
				data : { action: 'wpspy_ajax', q : 'update_rtl_settings', val : val },
				beforeSend : function () {
					disable_button(btn, "Please wait...");
				}
			}).done(function (data) {
				console.log(data);
				enable_button(btn, "Save");
				btn.after('<span class="success">Successfully saved! This will take effect on your next searches.</span>');
				btn.next('span').fadeOut(5000, function() {
					$(this).remove();
				});
			}).fail(function() {
				enable_button(btn, "Save");
				btn.after('<span class="error">Something went wrong...</span>');
				btn.next('span').fadeOut(5000, function() {
					$(this).remove();
				});
			});
		});	

		function check_url(domain) {
			if (domain.substr(domain.length-1, 1) !== '/') {
			    domain = domain + '/';
			}

			if (domain.substr(0,7) !== 'http://' && domain.substr(0,8) !== 'https://') {
			    domain = '//' + domain;
			}
			return domain;
		}

	 	// Check if the entered url is valid
			function is_valid_url(url) {
				if (url.substr(0,7) === 'http://') {
				    url = url.substr(7);
				} else if (url.substr(0,8) === 'https://') {
					url = url.substr(8);
				}
			    return url.match(/^[a-z0-9-\.]+\.[a-z]{2,4}/);
			}


		$("#wpspy_url").change(function() {
			var url = $(this).val();
			if (is_valid_url(url)) {
				if (url.substr(0,7) !== 'http://' && url.substr(0,8) !== 'https://') {
				    url = 'http://' + url;
				    $("#wpspy_url").val(url);
				}

				var anchors = $(".nav-menu a");
				var link_href = "";
				$.each(anchors, function (i, row) {
					link_href = $(row).data("href");
					$(row).attr("href", link_href+"&url="+url);
				});


				$("#show_previous_results").show();
			} else {
				$("#show_previous_results").attr("data-action", "show_prev");
				$("#show_previous_results").text("Show Previous Results");
				$("#show_previous_results").hide();
				$("#wpspy_prev_results").html("");
			}
		});
	

		$("#show_charts").click(function() {
			var $this = $(this);

			disable_button($this, "Please wait...");
			$(".wpspy-results-chart").fadeIn();
			$(".wpspy-results, .wpspy-prev-results").fadeOut();
			$.ajax({
				url : ajaxurl,
				type : 'post',
				dataType : 'json',
				data : { action: 'wpspy_ajax', "q" : "get_chart_data", "col" : $("#chart_options option:selected").val(), "url" : $("#compare_sites option:first").val() }
			}).done(function (data) {
				enable_button($this, "View Charts");
				FusionCharts.ready(function() {
					var revenueChart = new FusionCharts({
						type: "MSColumn2D",
						width: "700",
						height: "350",
						dataFormat: "json",
						dataSource: {
						   "chart":{
							  "caption":"Alexa Rank - "+$("#compare_sites option:selected").val(),
							  "bgcolor":"ffffff,ffffff",
							  "showlabels":"1",
							  "showvalues":"0",
							  "showborder":"0",
							  "decimals":"2"
							  // "numberprefix":""
						   },
						   "categories":[
							  {
								 "category": eval(data.dates).sort(comp)
							  }
						   ],
						   "dataset":[
							  {
								"seriesname": $("#wpspy_url").val(),
								"color":getRandomColor(),
								"data": eval(data.values)
							  },
						   ]
						}
					});
					revenueChart.render("chart-area");
				});

			});
		});

		$("#update_chart").click(function() {
			show_loader();

			var $this = $(this);
			disable_button($this, "Please wait...");
			var items = [];
			var chart_data = [], col = $("#chart_options option:selected").val(),
				col_name = $("#chart_options option:selected").text();
			var category = [], dataset = [], counter = 0, lent = 2,
			 seriesnames = "", temp = [], compare_sites = [], dates_handler = [];

			var progress_limit = 1, progress_current = 0;


			if ($("#compare_sites option:selected").val() === $("#compare_sites2 option:selected").val()) {
				lent = 1;
				compare_sites.push($("#compare_sites option:selected").val());
			} else {
				compare_sites.push($("#compare_sites option:selected").val());
				compare_sites.push($("#compare_sites2 option:selected").val());
			}

			
			var dates1 = [], dates2 = [];
			var values1 = [], values2 = [];
			var series_name1 = "", series_name2 = "";


			$.each(compare_sites, function (i) { 
				var seriesname = compare_sites[i];
				seriesnames += seriesname+" ";
				items.push(seriesname);
				$.ajax({
					url : ajaxurl,
					type : "post",
					dataType : "json",
					data : { action: 'wpspy_ajax', 'q' : "get_chart_data", 'url' : seriesname, 'col' : col }
				}).done(function (data) {
					enable_button($this, "Update Chart");
					progress_current++;
					check_progress(progress_limit, progress_current);

					counter++;

					if (counter < lent) {
						dates1 = eval(data.dates);
						values1 = eval(data.values);
						series_name1 = seriesname;

					} else if (counter === lent) {
						dates2 = eval(data.dates);
						values2 = eval(data.values);
						series_name2 = seriesname;
					}


					if (counter === lent) {
						category = $.concat(dates1, dates2);


						var str = JSON.stringify(category);

						str = str.replace(']","[', ",");

						category = eval(JSON.parse(str));


						category.sort(comp);  //sort categories 
						var sn = {};

						$.each(category, function (i, row) {

						});
						var new_data = {};
						var new_data2 = {};
							
						// this is where we arrange and make sure that the dates and values will stick together

							$.each(dates1, function (i, row) {
								$.each(category, function (j, row2) {
									if (row.label === row2.label) {
										new_data[j] = eval(values1[i]);
									}
								});
							});

							var sn1 = {"seriesname" : series_name1, "data" : new_data};
							dataset.push(eval(sn1));


							$.each(dates2, function (i, row) {
								$.each(category, function (j, row2) {
									if (row.label === row2.label) {
										new_data2[j] = eval(values2[i]);
									}
								});
							});
							var sn2 = {"seriesname" : series_name2, "data" : new_data2};
							dataset.push(eval(sn2));


						FusionCharts.ready(function() {
							var revenueChart = new FusionCharts({
								type: (lent>1)?"msline":"MSColumn2D",
								width: "950",
								height: "450",
								dataFormat: "json",
								dataSource: {
								   "chart":{
										"caption":col_name,
										"bgcolor":"ffffff,ffffff",
										"showlabels":"2",
										"showvalues":"0",
										"showborder":"0",
										"decimals":"2",
										"showShadow": "0",
										"showCanvasBorder": "0",
										"usePlotGradientColor": "0",
										"legendBorderAlpha": "0",
										"legendShadow": "0",
										"showAxisLines": "0",
										"showAlternateHGridColor": "0",
										"divlineThickness": "1",
										"connectNullData" : "1"
									  // "numberprefix":""
								   },
								   "categories":[
									  {
										 "category": category
									  }
								   ],
								  "dataset" : dataset
								}
							});
							revenueChart.render("chart-area");
						});
						
					}
				});
			});


		});



	function get_social_mention(domain) {
		$.ajax({
			url : ajaxurl,
			type : "post",
			dataType : "json",
			data : { action: 'wpspy_ajax', q : 'get_social_mention', url : domain }
		}).done(function(data) {
			if (data.length === 0) {
				if (social_mention_attempts < 10) { // make sure to break after several attempts
					get_social_mention(domain);
					console.log("social_mention_attempts: "+social_mention_attempts);
					social_mention_attempts++;
				} else {
					social_mention_attempts = 0;
					$(".social-metrics .title span").fadeOut(function () { $(this).remove(); });
					$(".social-metrics .title").trigger('click');
					return 0;

					// x++;
					// checkx(); // save to database

				}
			} else {
				social_mention_attempts = 0;

					var data_array = {
						url : domain,
						q : 'save_activity'
					};

				
				
				var el = "";				
				$.each(data, function (i, row) {
					if (i !== "top_keywords" && i !== "sentiment") {
						el = ".social-metrics ."+i+" .right";
						console.log(el);
						$(el).html('<span>'+row+'</span>');
						data_array[i] = row;
					} else {
						data_array[i] = JSON.stringify(row);
					}
				});

				data_array.action = 'wpspy_ajax';

				$(".social-metrics .title span").fadeOut(function () { $(this).remove(); });
				$(".social-metrics .title").trigger('click');

				// save to database 
					$.ajax({
						url : ajaxurl,
						type : 'post',
						dataType : 'json',
						data : data_array
					}).done(function (data) {
						console.log(data);
					});

				$("#view_social_mentions").attr("class", "spy-icon spy-icon-eye").attr("href", "//socialmention.com/search?t=all&q="+encodeURIComponent(domain)+"&btnG=Search");

			}
		});	
	}

	$(document).on("click", "#show_social_mentions_about", function() {
		var domain = $(this).data("domain");
		$(".wpspy-results").fadeOut(function() {
			$("#wpspy_social_mention_links").html("Please wait...");
			get_social_mention_links(domain);
			$("#wpspy_social_mention_links").fadeIn();
		});
	});

	$(document).on("click", "#sml_go_back", function() {
		$("#wpspy_social_mention_links").fadeOut(function() {
			$(".wpspy-results").fadeIn();
		});
	});

	$(document).on("click", "div.box div.title", function() {
		var $this = $(this), $next = $this.next("div.content");

	    if ($next.is(":visible")) {
	    	$next.slideUp(function() {
	        	$this.next("div.content div.entry").fadeOut();
	      	});
	    } else {
	       	$next.slideDown(function() {
	        	$this.next("div.content div.entry").fadeIn();
	      	});
	    }
	});

	$(".box table thead tr").click(function() {
	  	var $this = $(this), $next = $this.parent("thead").next("tbody");

		    if ($next.is(":visible")) {
		    	$next.slideUp(function() {
		        	$this.next("tbody tr").fadeOut();
		      	});
		    } else {
		       	$next.slideDown(function() {
		        	$this.next("tbody tr").fadeIn();
		      	});
		    }
	});

	$(document).on("click", "a.preview", function (e) {
		e.preventDefault();
	});

	$(document).on("click", "#links_on_page", function (e) {
		e.preventDefault();
		$("#nav_links").click();
	});


	// JAVASCRIPT FUNCTIONS ************************************************************************ 
		function showToolTip() {
			$(".help").tooltip({
				content: function () { return $(this).prop('title'); },
				track: true,
				delay: 0,
				showURL: false,
				fixPNG: true,
				showBody: " - ",
				extraClass: "tip",
				top: -15,
				left: 5
			});
		}

		function check_progress_and_save(progress_limit, progress_current, data_array) {
			if (progress_limit === progress_current) {
				hide_loader();
				enable_button($('#wpspy_submit'), 'Go');
				$.ajax({
					url : ajaxurl,
					type : 'post',
					data : data_array,
					dataType : 'json'
				}).done(function (data) {
					console.log(data);
				});
			}
		}

		function check_progress(progress_limit, progress_current) {
			if ((progress_current === progress_limit) && progress_current !== 0) {
				hide_loader();
			}
		}

		function hide_loader() {
			$(".loading").fadeOut(function() {
				$(this).remove();
			});
		}

		function fetching_failure(div, title) {
			div.append('<div class="error red">'+title+' failed to load. Please try again.</div>');
			$(".error").delay(4000).fadeOut('slow');
		}

		function is_numeric(mixed_var) {
		  //  discuss at: http://phpjs.org/functions/is_numeric/
		  var whitespace =
		    " \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
		  return (typeof mixed_var === 'number' || (typeof mixed_var === 'string' && whitespace.indexOf(mixed_var.slice(-1)) === -
		    1)) && mixed_var !== '' && !isNaN(mixed_var);
		}

		function get_social_mention_links(domain) {
			$.ajax({
				url : ajaxurl,
				type : 'post',
				dataType : 'json',
				data : { action: 'wpspy_ajax', 'q' : 'get_social_mention_links', 'domain' : domain}
			}).done(function (data) {
				var lent = data.items.length, counter = 0;

				$("#wpspy_social_mention_links").html('<a class="wpspy_btn" href="javascript:void(0);" id="sml_go_back" title="Go back to Web and Social Statistics">Go back</a>');
				
				$("#wpspy_social_mention_links").append('<table id="social_mention_links_tbl">'+
					'<thead><tr><th class="tbl-title">Mentions about "'+domain+'"</th></tr></thead>'+
						'<tbody></tbody></table>');

				$.each(data.items, function (i,row) {
					var image = "";
					if (row.embed === "" || row.embed === null) {
						image = '<image src="'+row.image+'"/>';
								
					} else {
						image = row.embed;
					}
					$("#social_mention_links_tbl tbody").append('<tr><td>'+
							'<div class="entry">'+
								'<div class="title"><div class="favspy-icon"><img src="'+row.favspy-icon+'"/></div>'+
								'<a href="'+row.link+'" target="_blank">'+row.title+'</a></div>'+
								image+
								'<div class="description">'+row.description+'</div>'+
								'<div class="info">'+
								'<p>'+
								'<a href="'+row.link+'" target="_blank" class="link">'+row.link+'</a>'+
								'<br>'+$.timeago(convertTime(row.timestamp))+' - by  <a href="'+row.user_link+'" target="_blank">'+row.user+'</a>'+
								' on <a href="//'+row.domain+'" target="_blank">'+row.domain+'</a></p></div>'+
							'</div>'+
						'</td></tr>'
					);
					counter++;
				});
				$("#wpspy_social_mention_links").fadeIn(function() {
					if (counter === lent) {
						$("#social_mention_links_tbl").dataTable();
					}
				});
			});
		}

		function save_data(values) {
			$.ajax({
				url : ajaxurl,
				type : 'post',
				dataType : 'json',
				data : { action: 'wpspy_ajax', q : 'save_activity',  }
			}).done(function (data) {

			});
		}

	    window.setInterval(function() {
	    	remove_fcTrial();
	    }, 1000);

	    function refresh_dropdowns() {
	    	$.ajax({
	    		url : ajaxurl,
	    		type : 'post',
	    		dataType : 'json',
	    		data : { action: 'wpspy_ajax', q : 'get_sites' }
	    	}).done(function (data) {
	    		$("#compare_sites, #compare_sites2").html("");
	    		$.each(data, function (i, row) {
	    			$("#compare_sites, #compare_sites2").append('<option>'+row.url+'</option>');
	    		});
	    	});
	    }

	    function getRandomColor() {
		    var letters = '0123456789ABCDEF'.split('');
		    var color = '#';
		    for (var i = 0; i < 6; i++) {
		        color += letters[Math.floor(Math.random() * 16)];
		    }
		    return color;
		}

		function disable_button(btn, text) {
			btn.addClass("disabled");
			btn.html(text);
			btn.val(text);
			btn.attr("disabled", "disabled");
		}

		function enable_button(btn, text) {
			btn.removeClass("disabled");
			btn.html(text);
			btn.val(text);
			btn.removeAttr("disabled");
		}

		function remove_fcTrial() {
			$("text").find("tspan").filter(':contains("FusionCharts XT Trial")').remove();
		}

		function comp(a, b) {
		    return new Date(a.label).getTime() - new Date(b.label).getTime();
		}

		function convertTime(UNIX_timestamp) {
			var a = new Date(UNIX_timestamp*1000);
			var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
			var year = a.getFullYear();
			var month = months[a.getMonth()];
			var date = a.getDate();
			var hour = a.getHours();
			var min = a.getMinutes();
			var sec = a.getSeconds();
			var time = date + ',' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
			return time;
		}

		function capitalizeSlug(str) {
			str = str.replace(/-/g, ' ');
			return str.replace(/\w\S*/g, function(txt) {return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
		}

		function limitString(str) {
			if (str.length <= 255) { 
				return str; 
			}
			return str.substring(0, 255)+"...";
		}

		function findLinksImages() {
			var imgs = $(".anchor-text img");
			$.each(imgs, function (i, img) {
				var el = '<a class="preview" href="'+$(img).attr("src")+'"><img src="'+$(img).attr("src")+'"/></a>';
				$(img).replaceWith(el);
			});
		}

		function randomize(a, b) {
		    return Math.random() - 0.5;
		}

		function extract_json_to_div(val, div, id) {
			if (/(http)\S+/i.test(val) === true) {
				div.html('<a href="'+val+'" target="_blank" class="spy-icon spy-icon-eye"></a>');
			} else {
				div.html('<span id="'+id+'">'+val+'</span>');
			}
		}

		function update_traffic_graph(val, url) {
			var src = "";
			switch(val) {
				case "daily_pageview":
					src = "c=1&r=1y&t=2&y=p";
					break;
				case "daily_reach":
					src = "c=1&y=r&r=6m";
					break;
				case "traffic_trend2y":
					src = "c=1&y=t&r=2y";
					break;
				case "traffic_trend6m":
					src = "c=1&y=t&r=6m";
					break;
				case "search_visits":
					src = "c=1&y=q&r=6m";
					break;
			}
			var domain = "//traffic.alexa.com/graph?o=lt&y=t&b=ffffff&n=666666&f=999999&p=4e8cff&h=150&w=340&z=30&";
			$("#traffic_graph").attr("src", domain+src+"&u="+url);
		}

		function inArray(needle, haystack) {
		    var length = haystack.length;
		    for(var i = 0; i < length; i++) {
		        if (haystack[i] === needle) return true;
		    }
		    return false;
		}

		function show_loader() {
			$('html').append('<div class="loading"><div class="center">Grabbing data all over the web...</div></div>');
		}

		(function($) {
		    if (!$.concat) {
		        $.extend({
		            concat: function(a, b) {
		                var r = [];
		                for (x in arguments) {
		                    r = r.concat(arguments[x]);
		                }
		                return r;
		            }
		        });
		    }

		    $(window).load(function () {
		    	$(".connectedSortable").sortable({
					connectWith: ".connectedSortable"
				});
		    });

		})(jQuery);

		// starting the script on page load
			findLinksImages();
			imagePreview();
});


