function browserIsIE() 
{
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))  // If Internet Explorer, return version number
    {
        return true;
    }

    return false;
}

function browserIsSafari(){
	if( navigator.userAgent.indexOf("Safari") > -1 )
		return true;
	return false;
}

$(document).on("click", ".btn-export", function (e){

	if( $.isEmptyObject(exportableData) ){
		e.preventDefault();
		return false;
	}

	var table = $("#exportable_table"),  page = $("#wpspy_submit").data("page");
	$("#exportable_table tbody").html("");
	var $this = $(this),
		filename = "",
		domain = ($("#wpspy_url").val()).substr(7),
		tbody = "",
		separator = "<tr><td></td></tr>";
	
	if( page === "site-info" ){
		tbody = "";
		try{
			if( !exportableData.hasOwnProperty('geolocation') ){
				exportableData.geolocation = {
					ip : exportableData.ip,
					city : exportableData.city,
					country : exportableData.country,
					country_code : exportableData.country_code
				};

				exportableData.on_site = {
					robot : exportableData.robot,
					sitemap_index : exportableData.sitemap_index
				};	

				exportableData.wordpress_data = JSON.parse(exportableData.wordpress_data);
			}

			filename = "WP Spy SITE INFO";
			tbody = "<tr><td><h3>"+filename+" for "+domain+"</h3></tr>"+separator;
			
			var geolocation = "<tr><td>GEOLOCATION</td><td colspan='2'></td></tr>"+separator+
				"<tr><td><strong>IP </strong></td><td colspan='2'>"+exportableData.geolocation.ip+"</td></tr>"+
				"<tr><td><strong>City </strong></td><td colspan='2'>"+exportableData.geolocation.city+"</td></tr>"+
				"<tr><td><strong>Country </strong></td><td colspan='2'>"+exportableData.geolocation.country+"</td></tr>";

			var onsite = "<tr><td>ON-SITE</td></tr>"+
				"<tr><td><strong>BuiltWith: </strong></td><td colspan='2'>"+$("#builtwith").attr("href")+"</td></tr>"+
				"<tr><td><strong>robots.txt: </strong></td><td colspan='2'>"+(exportableData.on_site.robot == '1' || exportableData.on_site.robot == 'true' ? 'yes' : 'no')+"</td></tr>"+
				"<tr><td><strong>sitemap.xml: </strong></td><td colspan='2'>"+(exportableData.on_site.sitemap_index == '1' || exportableData.on_site.sitemap_index == 'true' ? 'yes' : 'no')+"</td></tr>";

			var dnsv = "";
			if( exportableData.hasOwnProperty('dns') ){
				dnsv = JSON.stringify(exportableData.dns).replace(/\\/g, '');
			}else{
				dnsv = "-";
			}
			var dns = "<tr><td>DNS</td></tr><tr><td>"+dnsv+"</td></tr>";

			var site_security = "<tr><td>SITE SECURITY</td></tr>"+
				"<tr><td><strong>McAfee Site Advisor: </strong></td><td colspan='2'>"+$("#mcafee").attr("href")+"</td></tr>"+
				"<tr><td><strong>Norton Safe Web: <s/trong></td><td colspan='2'>"+$("#norton").attr("href")+"</td></tr>"+
				"<tr><td><strong>WOT: </strong></td><td colspan='2'>"+$("#wot").attr("href")+"</td></tr>"+
				"<tr><td><strong>Sucuri: </strong></td><td colspan='2'>"+$("#sucuri").attr("href")+"</td></tr>";

			var sep = "<tr><td></td></tr>";


			var wordpress_data = "<tr><td>WORDPRESS DATA</td></tr>"+
				"<tr><td><strong>WordPress Version: </strong><td><td>"+exportableData.wordpress_data.version+"</td><td></td></tr>"+sep;

			var theme = ($this.attr("data-type") == "csv") ? "<tr><td>WordPress Theme: </td><td>Link: </td><td>Download Link</td><td>Type</td></tr>"+
				"<tr><td>"+exportableData.wordpress_data.theme.name+
				"</td><td>"+exportableData.wordpress_data.theme.download+
				"</td><td>"+exportableData.wordpress_data.theme.link+
				"</td><td>"+exportableData.wordpress_data.theme.type+"</td></tr>"+
				sep+sep+"<tr><td>Plugin Name: </td><td>Link: </td><td>Download Link</td><td>Type</td></tr>" :
				
				"<tr><td><strong>WordPress Theme: </strong>"+exportableData.wordpress_data.theme.name+
				"</td><td><strong>Download Link</strong>"+exportableData.wordpress_data.theme.download+
				"</td><td><strong>Link </strong>"+exportableData.wordpress_data.theme.link+"</td></tr>";

			wordpress_data += theme+sep;

			var freePlugins = "<tr><td>FREE PLUGINS</td></tr>", 
				commercialPlugins = "<tr><td>COMMERCIAL PLUGINS</td></tr>";
			var freePluginsCount = 0, commercialPluginsCount = 0;

			if( exportableData.wordpress_data.free_plugins !== 0 ){
				$.each(exportableData.wordpress_data.free_plugins, function (i, row){
					if( $this.attr("data-type") == "csv" ){
						wordpress_data += "<tr><td>"+row.name+"</td><td>"+row.link+"</td><td>"+row.download+"</td><td>Free</td></tr>";
					}else{
						freePluginsCount++;
						freePlugins += "<tr><td><strong>"+freePluginsCount+". </strong>"+row.name+
						"</td><td><strong>Link: </strong>"+row.link+
						"</td><td><strong>Download Link: </strong>"+row.download+"</td></tr>";
					}
				});
			}

			if( exportableData.wordpress_data.commercial_plugins !== 0 ){
				$.each(exportableData.wordpress_data.commercial_plugins, function (i, row){
					if( $this.attr("data-type") == "csv" ){
						wordpress_data += "<tr><td>"+row.name+"</td><td>"+row.link+"</td><td>"+row.download+"</td></tr>";
					}else{
						commercialPluginsCount++;
						commercialPlugins += "<tr><td><strong>"+commercialPluginsCount+". </strong>"+row.name+
						"</td><td><strong>Link: </strong>"+row.link+
						"</td><td><strong>Download Link: </strong>"+row.download+"</td></tr>";
					}
				});

				if( freePluginsCount !== 0 ) wordpress_data += freePlugins+separator;
				if( commercialPluginsCount !== 0 ) wordpress_data += commercialPlugins+separator;
				
			}
			tbody += geolocation+sep+onsite+sep+dns+sep+site_security+sep+wordpress_data;
			// table.children("tbody").append(tbody);
		
			filename = "SITE-INFO";
		}catch(Exception){
			console.log(Exception);
		}

	}else if( page === "page-info" ){
		tbody = "";
		separator = "<tr><td colspan='2'></td></tr>";
		filename = "WP Spy PAGE-INFO";
		tbody = "<tr><td colspan='2'><h3>"+filename+" for "+domain+"</h3></tr>"+separator;

		$.each(exportableData, function (i, row){
			if( i !== "q" ){
				tbody+= "<tr><td><strong>"+capitalizeSlug(i)+"</strong></td><td>"+row.replace(/\\/g, "")+"</td></tr>";
			}
		});

	}else if( page === "seo-stats" ){
		tbody = "";
		separator = "<tr><td colspan='2'></td></tr>";
		filename = "WP Spy SEO STATISTICS";
		tbody = "<tr><td colspan='2'><h3>"+filename+" for "+domain+"</h3></td></tr>"+separator;


		try{
			if( exportableData.hasOwnProperty('rank') ){
				$.each(exportableData.rank, function (i, row){
					console.log(i+": "+row);
					if( i == "alexa" ) i = "alexa_rank";
					exportableData[i] = row;
				});
			}

			if( exportableData.hasOwnProperty('backlinks') ){
				$.each(exportableData.backlinks, function (i, row){
					exportableData['backlinks_'+i] = row;
				});
			}

			if( exportableData.hasOwnProperty('cached') ){
				$.each(exportableData.cached, function (i, row){
					exportableData[i] = row;
				});
			}

			if( exportableData.hasOwnProperty('pages_indexed') ){
				$.each(exportableData.pages_indexed, function (i, row){
					exportableData['page_indexed_'+i] = row;
				});
			}

		}catch(Exception){

		}

		
		var rank = $this.attr("data-type") === "pdf" ? "<tr><td><strong>Alexa Rank</strong></td><td>"+exportableData.alexa_rank+"</td></tr>"+
			"<tr><td><strong>Google Page Rank</strong>"+exportableData.google_page_rank+"</td></tr>"+
			"<tr><td><strong>Quantcast Traffic Rank</strong></td><td>"+exportableData.quantcast_traffic+"</td></tr>" : 

			"<tr><td><strong>Alexa Rank:</strong> "+exportableData.alexa_rank+"</td></tr>"+
			"<tr><td><strong>Google Page Rank:</strong> "+exportableData.google_page_rank+"</td></tr>"+
			"<tr><td><strong>Quantcast Traffic Rank: </strong>"+exportableData.quantcast_traffic+"</td></tr>";

		tbody += separator+"<tr><td colspan='2'>RANK</td></tr>"+rank;

		var pagesIndexed = separator+"<tr><td colspan='2'>PAGES INDEXED</td></tr>";
		var backlinks = separator+"<tr><td>BACKLINKS</td><td></td></tr>";
		var cached = separator+"<tr><td>CACHED</td><td></td></tr>"+
			"<tr><td><strong>Archived.org</strong></td><td>"+$("#archived").attr("href")+"</td></tr>"+
			"<tr><td><strong>Google</strong></td><td>"+$("#google_cached").attr("href")+"</td></tr>";

		$.each(exportableData, function (i, row){
			// pages indexed
				var index = i.indexOf('page_indexed_', 0);
				if(index > -1){
					pagesIndexed += $this.attr("data-type") === "pdf" ? 
						"<tr><td><strong>"+i.replace("page_indexed_", "")+"</strong>: "+row+"</td></tr>" :
						"<tr><td><strong>"+i.replace("page_indexed_", "")+"</strong></td><td> "+row+"</td></tr>";

				}

				index = i.indexOf('backlinks_', 0);
				if(index > -1){
					backlinks += $this.attr("data-type") === "pdf" ? 
						"<tr><td><strong>"+i.replace("backlinks_", "")+":</strong> "+row+"</td></tr>" :
						"<tr><td><strong>"+i.replace("backlinks_", "")+"</td><td></strong> "+row+"</td></tr>" ;
				}
			
		});

		tbody += pagesIndexed+separator+backlinks+separator+cached;

	}else if( page === "social-stats" ){
		filename = "WP Spy SOCIAL STATISTICS";
		tbody = "<tr><td colspan='2'>"+filename+" for "+domain+"</td></tr>";
		var sns = separator+"<tr><td>SOCIAL (SNS)</td><td></td></tr>";
		$.each(exportableData, function (i, row){
			if( i !== "q" && i !== "url" ){
				sns += "<tr><td><strong>"+capitalizeSlug(i.replace("_", " "))+"</strong></td><td>"+row+"</td></tr>";
			}
		});

		tbody += sns;

	}else if( page === "traffic" ){
		filename = "WP Spy TRAFFIC INFO";
		tbody = "<tr><td><h3>"+filename+" for "+domain+"</h3></td></tr>";
		separator = "<tr><td></td></tr>";
		var sitemetrics = separator+separator+"<tr><td><br/>SITE METRICS<br/><hr/></td></tr>",
			traffic = separator+separator+"<tr><td><br/>TRAFFIC<br/><hr/></td></tr>",
			alexaRankInCountry = $this.attr("data-type") === "csv" ? separator+separator+"<tr><td><br/>ALEXA TRAFFIC RANK IN COUNTRY<br/><hr/></td></tr>"+
				"<tr><td>Country</td><td>Percent of Visitors</td><td>Rank in Country</td></tr>" : 
				"<tr><td><br/>ALEXA TRAFFIC RANK IN COUNTRY<br/><hr/></td></tr>"+
				"<tr><td><strong>Country</strong>   -   <strong>Percent of Visitors</strong>   -   <strong>Rank in Country</strong></td></tr>";

		$.each(exportableData, function (i, row){
			console.log(i);
			if( i === "alexa_rank_in_country" ){
				$.each(row, function (index, row2){
					if( typeof(row2) === null || row2 === null || row2 === undefined || row2.length === 0  ){
						alexaRankInCountry += $this.attr("data-type") === "csv" ? "<tr><td>"+row2.country+"("+row2.country_code+")</td><td>"+row2.percent_of_visitors+"</td><td>"+row2.rank+"</td></tr>" :
						"<tr><td>"+row2.country+" ("+row2.country_code+")   -   "+row2.percent_of_visitors+"   -   "+row2.rank+"</td></tr>";
					}else{
						alexaRankInCountry += $this.attr("data-type") === "csv" ? "<tr><td> - </td><td> - </td><td> - </td></tr>" :
						"<tr><td> -      -      -   </td></tr>";
					}
				});
				
			}else{
				if( i !== "alexa_rank" && i !== "google_page_rank" && i !== "quantcast_traffic_rank"){
					sitemetrics += $this.attr("data-type") == "pdf" ? "<tr><td><strong>"+capitalizeSlug(i.replace(/_/g, ' '))+"</strong>: "+row+"</td></tr>" :
						"<tr><td><strong>"+capitalizeSlug(i.replace(/_/g, ' '))+"</strong></td><td>"+row+"</td></tr>";
				}else{
					traffic += $this.attr("data-type") == "pdf" ? "<tr><td><strong>"+capitalizeSlug(i.replace(/_/g, ' '))+"</strong>: "+row+"</td></tr>" :
						"<tr><td><strong>"+capitalizeSlug(i.replace(/_/g, ' '))+"</strong></td><td>"+row+"</td></tr>";
				}
			}
		});

		tbody += traffic+alexaRankInCountry+sitemetrics;
	
	}else if( page === "links" ){
		filename = "WP Spy LINKS";
		separator = "<tr><td></td></tr>";
		tbody = "<tr><td><h3>"+filename+" for "+domain+"</h3></td></tr>"+separator+
				"<tr><td>External Links: "+exportableData.external_links.links.length+" ("+exportableData.external_links.nofollow+" nofollow)</td></tr>"+
				"<tr><td>Internal Links: "+exportableData.internal_links.links.length+" ("+exportableData.internal_links.nofollow+" nofollow)</td></tr>";
		
		var elinks = $this.attr("data-type") == "csv" ? separator+separator+"<tr><td><h4>EXTERNAL LINKS</h4></td></tr>"+
			"<tr><td><strong>Text</strong></td><td><strong>URL</strong></td></tr>" :
			separator+"<tr><td>EXTERNAL LINKS</td></tr>"+
			"<tr><td><strong>URL</strong></td></tr>";

		var ilinks = $this.attr("data-type") == "csv" ? separator+separator+"<tr><td><h4>INTERNAL LINKS</h4></td></tr>"+
			"<tr><td><strong>Text</strong></td><td><strong>URL</strong></td></tr>" : 
			separator+"<tr><td>INTERNAL LINKS</td></tr>"+
			"<tr><td><strong>URL</strong></td></tr>";

		var icounter = 0, ecounter = 0;
		
		$.each(exportableData.external_links.links, function (i, row){
			ecounter++;
			elinks += $this.attr("data-type") == "csv" ? "<tr><td>"+row.text+"</td><td>"+row.url+"</td></tr>" : "<tr><td>"+ecounter+". "+row.url+"</td></tr>";
		});

		$.each(exportableData.internal_links.links, function (i, row){
			icounter++;
			ilinks += $this.attr("data-type") == "csv" ? "<tr><td>"+row.text+"</td><td>"+row.url+"</td></tr>" : "<tr><td>"+icounter+". "+row.url+"</td></tr>";
		});

		tbody += ilinks+separator+elinks;
	}


	table.find("tbody").append(tbody);

	if( $this.attr("data-type") == "csv" ){
		var args = [$('#exportable_table'), "WP Spy ("+filename+") - "+domain+".csv"];
		exportTableToCSV.apply($this, args);
	}else{
		ExportAsPDF3( "WP Spy ("+filename+") - "+domain+".pdf", table );
	}

});


$(document).on("click", ".export-backlinks", function (e){
	var $this = $(this), 
		domain = ($("#wpspy_url").val()).substr(7), 
		table = $('#exportable_backlinks');

	console.log('exporting backlinks...');

	if( $("table#exportable_backlinks").length < 1 ){
		return false;
	}

	table.children("tbody").html("");

	var filename_ad = 0;

	tbody = "<tr><td><b>WP Spy BACKLINKS for  <i>"+domain+" ("+filename_ad+")</i></b></td></tr><tr><td></td></tr><tr><td></td></tr>";
	


	$.each($(".tbl-backlinks-holder table tr td:nth-child(2)"), function (i, row){
	   tbody += "<tr><td>"+$(row).children("a").attr("href")+"</td></tr>";
	   filename_ad++;
	});

	table.children("tbody").append(tbody);

	if( $this.attr("data-type") == "csv" ){
		exportTableToCSV.apply($this, [table, "WP Spy (BACKLINKS) - "+domain+"("+filename_ad+").csv"]);
	}else{
		ExportAsPDF3( "WP Spy (BACKLINKS) - "+domain+"("+filename_ad+").pdf", table );
	}
});


function exportTableToCSV($table, filename) {

    var $rows = $table.find('tr:has(td)'),

        // Temporary delimiter characters unlikely to be typed by keyboard
        // This is to avoid accidentally splitting the actual contents
        tmpColDelim = String.fromCharCode(11), // vertical tab character
        tmpRowDelim = String.fromCharCode(0), // null character

        // actual delimiter characters for CSV format
        colDelim = '","',
        rowDelim = '"\r\n"',

        // Grab text from table into CSV formatted string
        csv = '"' + $rows.map(function (i, row) {
            var $row = $(row),
                $cols = $row.find('td');

            return $cols.map(function (j, col) {
                var $col = $(col),
                    text = $col.text();

                return text.replace('"', '""'); // escape double quotes

            }).get().join(tmpColDelim);

        }).get().join(tmpRowDelim)
            .split(tmpRowDelim).join(rowDelim)
            .split(tmpColDelim).join(colDelim) + '"',

        // Data URI
        csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

   		

    // download workaround for Internet Explorer
    if( browserIsIE() ){
    	var blob = new Blob([csv],{type: "text/csv;charset=utf-8;"});
		navigator.msSaveBlob(blob, filename);
	}else{
		// other browsers
		$(this).attr({
	        'download': filename,
			'href': csvData,
			'target': '_blank'
	    });
	}
}

/* EXPORT TABLE AS PDF */
function ExportAsPDF3(filename, table){

	var pdf = new jsPDF('p', 'pt', 'letter')

	// source can be HTML-formatted string, or a reference
	// to an actual DOM element from which the text will be scraped.
	, source = table.get(0)

	// we support special element handlers. Register them with jQuery-style
	// ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
	// There is no support for any other type of selectors
	// (class, of compound) at this time.
	, specialElementHandlers = {
		// element with id of "bypass" - jQuery style selector
		'#bypassme': function(element, renderer){
			// true = "handled elsewhere, bypass text extraction"
			return true
		}
	}

	, margins = {
	    top: 20,
	    bottom: 60,
	    left: 40,
	    width: 522
	};

	  // all coords and widths are in jsPDF instance's declared units
	  // 'inches' in this case
	pdf.fromHTML(
	  	source // HTML string or DOM elem ref.
	  	, margins.left // x coord
	  	, margins.top // y coord
	  	, {
	  		'width': margins.width // max width of content on PDF
	  		, 'elementHandlers': specialElementHandlers
	  	},
	  	function (dispose) {
	  	  // dispose: object with X, Y of the last line add to the PDF
	  	  //          this allow the insertion of new lines after html
	        pdf.save(filename);
	      },
	  	margins
	  )

}

function capitalizeSlug(str){
	str = str.replace(/-/g, ' ');
	return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
}