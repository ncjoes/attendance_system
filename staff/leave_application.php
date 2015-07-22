<?php
$type = array("CASUAL", "MATERNITY");
$response = "";
$success = false;
//Process appication
if(isset($array['leaveRequestForm'])){
	try{
		if(!in_array($array['leave_type'],$type)){throw new Exception("Invalid leave type");}
		if((int)$array['request_duration'] < 1){throw new Exception("leave duration must be at least one day");}
		//if(strlen($array['reason'])<1){throw new Exception("please specify reason(s) for this leave application");}
		
		$link = Utility::getDefaultDBConnection();
		$query = "insert into leave_requests (staff_id,is_approved,type,reason,request_start_time,request_duration) values (
			'".$user->getID()."',
			'PENDING',
			'".$array['leave_type']."',
			'".$array['reason']."',
			'".$array['request_start_time']."',
			'".$array['request_duration']."'
			)";
		$result = mysqli_query($link, $query) or die(mysqli_error($link));
		if($result){
			$success = true;
			$response = "Leave application filed successfully";
		}
	}catch(Exception $ex){
		$response = $ex->getMessage();
	}
}
?>

<div>
    <h4>LEAVE APPLICATION</h4>
    <br/>
    <div class="padding5 grid">
		<?php
		if($success){
			?>
            <div class="row"><p class="fg-green"><?= $response; ?></p></div>
            <?php
		}else{
		?>
        <form method="post" action="view.php?p=21">
            <div class="row"><p class="fg-red"><?= $response; ?></p></div>
            <div class="row" >
                <div class="row">
                    <label class="span2">Leave type</label>
                    <div class="span4">
                        <select name="leave_type">
                            <?php
                            foreach ($type as $value) {
                                $selected = isset($array['type']) ? strcasecmp($value, $array['type']) === 0 : FALSE;
                                echo "<option " . ($selected ? "selected" : "") . ">$value</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>                 
                <div class="row" >
                    <label class="span2">Proposed Start Time</label>
                    <div class="span4">
                        <div class="input-control text" data-role="datepicker"
                             data-date="<?= isset($array['request_start_time']) ? $array['request_start_time'] : strftime("%Y-%m-%d", mktime()); ?>"
                             data-format="yyyy-mm-dd"
                             data-position="top"
                             data-effect="slide">
                            <input type="text" name="request_start_time">
                            <button type="button" class="btn-date"></button>
                        </div>
                    </div>
                </div>
                <div class="row" >
                    <label class="span2">Duration (in days)<span class="fg-red">*</span></label>
                    <div class="span4">
                        <input name='request_duration' required style="width: inherit" type='number' 
                               <?= isset($array['request_duration']) ? "value='" . $array['request_duration'] . "'" : ""; ?> tabindex='8'  />
                    </div>
                </div>                    
                <div class="row" >
                    <label class="span2">Reason</label>
                    <div class="span4">
                        <textarea name='reason' style="width: inherit" tabindex='7'><?=
                            isset($array['reason']) ?
                                    $array['reason'] :
                                    "";
                            ?></textarea>
                    </div>
                </div>
                <div class="row no-phone offset2">
                    <input class="button default bg-green bg-hover-dark" type='submit'
                           name='leaveRequestForm' value='Apply' tabindex='9'/>
                </div>
                <div class="on-phone no-tablet no-desktop padding20 ntp nbp">
                    <input class="button default bg-green bg-hover-dark" type='submit'
                           name='leaveRequestForm' value='Apply' tabindex='9'/>
                </div>
            </div>

        </form>
	<?php
	}
	?>
    </div>
</div>