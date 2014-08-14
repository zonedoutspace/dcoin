<script src="js/index.js"></script>
<script>

	$('#save').bind('click', function () {
		$('#alert').css("display", "none");

		var latitude = $('#latitude').val();
		var longitude = $('#longitude').val();
		$.post( 'ajax/save_geolocation.php', { 'geolocation' : latitude+', '+longitude } ,
				function (data) {
					if (data.error) {
						$('#alert').css("display", "block");
					}
					else {
						fc_navigate('upgrade_5');
					}
				}, "JSON");
	});

	$('#show_map').bind('click', function () {

		map_init (<?php echo $tpl['geolocation']?>, 'map_canvas', true);
		google.maps.event.trigger(map, 'resize');
	});

	$("#main_div input").addClass( "form-control" );
	$("#main_div input").width( 150 );

</script>
<div id="main_div">
<h1 class="page-header"><?php echo $lng['upgrade_title']?></h1>
<ol class="breadcrumb">
	<li><a href="#" onclick="fc_navigate('mining_menu')"><?php echo $lng['mining'] ?></a></li>
	<li class="active"><?php echo $lng['upgrade_title'] ?></li>
</ol>
	<?php require_once( ABSPATH . 'templates/alert_success.php' );?>
    <ul class="nav nav-tabs">
		<li><a href="#" onclick="fc_navigate('upgrade_0')">Step 0</a></li>
		<li><a href="#" onclick="fc_navigate('upgrade_1')">Step 1</a></li>
		<li><a href="#" onclick="fc_navigate('upgrade_2')">Step 2</a></li>
		<li><a href="#" onclick="fc_navigate('upgrade_3')">Step 3</a></li>
	    <li class="active"><a href="#" onclick="map_navigate('upgrade_4')">Step 4</a></li>
	    <li><a href="#" onclick="fc_navigate('upgrade_5')">Step 5</a></li>
    </ul>
    
	<h3><?php echo $lng['your_location']?></h3>
<button type="button" class="btn btn-primary" id="show_map">Show map</button><br><br>
	<div id="map_canvas" style="width: 640px; height: 480px; margin-bottom:20px; display:none"></div>
	<input id="latitude" class="input" type="text" placeholder="latitude" value="<?php echo $tpl['geolocation_lat']?>"><input id="longitude" class="input" type="text" placeholder="longitude" value="<?php echo $tpl['geolocation_lon']?>">
	<br>
	<button class="btn btn-success" id="save"><?php echo $lng['save_and_goto_step_5']?></button>

	<br><br><br><br><br><br><br>
	</div>