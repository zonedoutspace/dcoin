
<script>

$('#send_to_net').bind('click', function () {

	$.post( 'ajax/save_queue.php', {
			'type' : '<?php echo $tpl['data']['type']?>',
			'time' : '<?php echo $tpl['data']['time']?>',
			'user_id' : '<?php echo $tpl['data']['user_id']?>',
			'signature1': $('#signature1').val(),
			'signature2': $('#signature2').val(),
			'signature3': $('#signature3').val()
			}, function (data) {
				fc_navigate ('promised_amount_list', {'alert': '<?php echo $lng['sent_to_the_net'] ?>'} );
			} );
} );

</script>
<h1 class="page-header"><?php echo $lng['actualization_promised_amounts']?></h1>
<ol class="breadcrumb">
	<li><a href="#mining_menu"><?php echo $lng['mining'] ?></a></li>
	<li><a href="#promised_amount_list"><?php echo $lng['promised_amount_title'] ?></a></li>
	<li class="active"><?php echo $lng['actualization_promised_amounts'] ?></li>
</ol>

	<?php require_once( ABSPATH . 'templates/alert_success.php' );?>

    <div id="sign">
	
		<label><?php echo $lng['data']?></label>
		<textarea id="for-signature" style="width:500px;" rows="4"><?php echo "{$tpl['data']['type_id']},{$tpl['data']['time']},{$tpl['data']['user_id']}"; ?></textarea>
	    <?php
	for ($i=1; $i<=$count_sign; $i++) {
		echo "<label>{$lng['sign']} ".(($i>1)?$i:'')."</label><textarea id=\"signature{$i}\" style=\"width:500px;\" rows=\"4\"></textarea>";
	    }
	    ?>
	    <br>
		<button class="btn" id="send_to_net"><?php echo $lng['send_to_net']?></button>

    </div>


	<input type="hidden" id="user_id" value="<?php echo $_SESSION['user_id']?>">
	<input type="hidden" id="time" value="<?php echo time()?>">
	<script>
		doSign();
		<?php echo !defined('SHOW_SIGN_DATA')?'$("#send_to_net").trigger("click");':'' ?>
	</script>
