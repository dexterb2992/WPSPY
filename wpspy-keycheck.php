<div class="wpspy-wrapper">
	<?php 
		$page = 'wpspy-keycheck';
		include plugin_dir_path( __FILE__ )."classes/config.php";
		include plugin_dir_path( __FILE__ )."classes/dbhelper.php";
		include plugin_dir_path( __FILE__ )."_nav.php"; 

		function cleanStr($str){
			return ucwords( str_replace("-", " ", $str) );
		}
	?>
	<div class="wpspy-content">		
		
		<div class="wpspy-results row">
			<div class="col-5">
				<style>
#adminmenuback{z-index:1 ! important;}

.btn-success
{
/* IE10 Consumer Preview */ 
background-image: -ms-linear-gradient(top, #2E87C7 0%, #11619A 100%) !important ;

/* Mozilla Firefox */ 
background-image: -moz-linear-gradient(top, #2E87C7 0%, #11619A 100%) !important;

/* Opera */ 
background-image: -o-linear-gradient(top, #2E87C7 0%, #11619A 100%) !important;

/* Webkit (Safari/Chrome 10) */ 
background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #2E87C7), color-stop(1, #11619A)) !important;

/* Webkit (Chrome 11+) */ 
background-image: -webkit-linear-gradient(top, #2E87C7 0%, #11619A 100%) !important;

/* W3C Markup, IE10 Release Preview */ 
background-image: linear-gradient(to bottom, #2E87C7 0%, #11619A 100%) !important;

border-color: #11619a !important;
}
</style>
<?php
$u1=plugins_url();
global $wpdb;
$wpp_msg = '';
$lickey = '';
if(isset($_REQUEST['licensekey'])) {	
	$lickey = $_POST['licensekey'];
	//$ip = magic_getRealIpAddr_lead();
	$domain = $_SERVER['HTTP_HOST'];
	$location = "admin.php?page=wpspy-dashboard&atoken=$lickey";
?>
<script>
console.log("key: <?php echo $lickey; ?>");
var url = "http://topdogimsoftware.com/spylicense/index.php?licensekey=<?php echo $lickey; ?>&domainname=<?php echo $domain; ?>&wptype='premium'&format=json&jsoncallback=?";
console.log(url);
jQuery.getJSON(url,
function(data) {
var wpspyajax = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
console.log(data);
if(data.response == 'Valid'){
	var k = trim('<?PHP echo $lickey; ?>');
	$.ajax({
		url : wpspy_ajaxurl,
		type : 'post',
		dataType : 'json',
		data : { q : 'save_license', key : k }
	}).done(function (rs){
		console.log(rs);

		jQuery.ajax({
			type: "GET",
			async: false,
			url:wpspyajax,	
			data:	{  
					action: 'wp_lead_lcheckey',
					stats: data.response,											
					p_type:trim('<?PHP echo $lickey; ?>')	
			}, 	
			cache: false,
			dataType: "html",	
			success: function(data, textStatus, XMLHttpRequest)	{
			  
			if(trim(data) == 'do')	{
					
				var wppmsg = "API key activated successfully.";	
				document.getElementById("wpp_lcheckmsg").innerHTML = wppmsg;	
				document.getElementById("wpp_lcheckmsg").style.color = '#339313';

				setTimeout(function(){	
				window.location= <?php echo "'" . $location . "'"; ?>;	
				}, 2000);			
			}
			},	
			error: function(MLHttpRequest, textStatus, errorThrown) {  
				
			},		
		});

		jQuery("#wpp_lcheckmsg").css('color', '#339313')
		jQuery("#wpp_lcheckmsg").html("API key activated successfully.");

		setTimeout(function(){	
		window.location= <?php echo "'" . $location . "'"; ?>;	
		}, 2000);	
	});

	
}else if(data.response == 'Invalid'){	
		
		var wppmsg = "Invalid API key. Please contact out <a href='http://karthikramani.freshdesk.com/'>Support Team</a> ";	
		document.getElementById("wpp_lcheckmsg").innerHTML = wppmsg;	
		document.getElementById("wpp_lcheckmsg").style.color = '#ff0c0c';	
		}
});

function trim(text) {
	return text.replace(/^\s+|\s+$/g, "");
}
</script>
<style>
#btn_activate:hover{
 background-image: linear-gradient(to bottom, #2e87c7 0%, #11619a 100%);
    background-color: #6e9b13;
    color: #ffffff;
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
	 background-position: 0 -15px;
    text-decoration: none;
    transition: background-position 0.1s linear 0s;
}
</style>
<?php } ?>

<div class="wrap" >
<?php if(!isset($_REQUEST['update'])) { ?>
<?php }else{ ?>
<h2 >WP Notification API Key Settings :</h2>
<?php } ?>
<form method="post" action="">
<table class="form-table" style=' width:100%;margin:0 auto;text-align:left;'>
<tbody>
<tr valign="top">
<td scope="row" valign="top" nowrap="nowrap"> <?php _e('Enter Your License Key'); ?></td>
</tr>
<tr>
<td><input id='btn_activate' style='width:250px;' id="licensekey" name="licensekey" type="text" autocomplete="off" class="regular-text" value="<?php echo $lickey; ?>" />
</td>
</tr>
<tr>
<td>
	<span id="wpp_lcheckmsg" style="font-size:17px;height: 10px !important;"></span>
</td>
</tr>
<tr valign="top">

<td><input style='text-align:center;' type="submit" class="btn btn-success" name="submit" value="Activate"/></td></tr>
</tbody>
</table>
</form>
<div style='width:70%;margin:0 auto;text-align:center;'>
<span id="wpp_lcheckmsg" style="height: 10px !important;"></span>
</div>
</div>
				</div>
			</div>
			
		</div>
	</div>
</div>