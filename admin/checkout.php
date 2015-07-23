<?php
$query_script = "http://".$_SERVER["HTTP_HOST"]."/www/biometrics_site/q.php";
?>
<script type="text/javascript">
	var staffIdTag = "staff_select_box";
	var script_url = <?= "\"{$query_script}\""; ?>;
</script>
<script type="text/javascript" src="../js/fingerprint_helper.js"></script>
<div>
	<h4>ATTENDANCE: CHECK-OUT</h4>
	<div class="padding5 grid">
		<div class="grid">
			<form method="post" action="view.php?p=3">
				<div class="row" >
					<label class="span2 offset3">Select Staff:</label>
					<select name="staff" class="span4" id="staff_select_box">
						<?php
						$query = "SELECT * FROM staff INNER JOIN attendance WHERE staff.staff_id=attendance.staff_id
								AND attendance.login_time=attendance.logout_time ORDER BY last_name,first_name";
						$link = Utility::getDefaultDBConnection();
						$result = mysqli_query($link, $query);
						if($result){
							while($row = mysqli_fetch_array($result)){
								$staff_details[$row['staff_id']] = $row;
								echo '<option value="'.$row['staff_id'].'">'.$row['last_name'].' '.$row['first_name'].'</option>';
							}
						}
						?>
					</select>
				</div>
				<div class="row" id="loading_div" style="visibility: hidden;">
					<p style="text-align: center">
						<img src="../img/loading_dark_large.gif" width="120" height="120"/>
						<b>waiting for finger-print verification...</b>
					</p>
				</div>
				<div class="row" id="button_div">
				<?php
				if(mysqli_num_rows($result)) {
					?>
						<input class="button default  bg-hover-dark span6 offset3" type="button" id="start_button"
						       value="Checkout" onclick="doCheckout()"/>
						<input class="button default  bg-hover-darkRed span2 offset5" type="button" id="abort_button"
						       value="Abort" onclick="abortCheckout()" style="display: none"/>
					<?php
				}else{
					?>
					<p style="text-align: center">
						<b>no staff members are currently checked-in</b>
					</p>
					<?php
				}
				?>
				</div>
			</form>
		</div>
	</div>
</div>