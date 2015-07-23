<?php
require_once("../staff/Staff.php");
//Process form inputs here

$isFormRequest = false; //true if a request was sent from form
$success = true; //true if no error in processing
$error_message = ""; //error message if processing was unsuccessful

if(isset($array['submit'])){
	$isFormRequest = true;
	switch($array['submit']){
		case 'Register Staff': {
			$id_exists = Utility::rowExists('staff','staff_id',$array['staff_id']);
			$phone_exists = Utility::rowExists('staff','phone',$array['phone']);
			$email_exists = Utility::rowExists('staff','email',$array['email']);
			if(!$id_exists and !$phone_exists and !$email_exists){//validate data
				//create user account
				try{
					$staff = new Staff();
					$staff->registerStaff( $array['first_name'], $array['last_name'], $array['staff_id'], $array['password1'], $array['password2'], $array['department'], $array['designation'], $array['phone'], $array['email'] );
					//$staff->login($array['staff_id'], $array['password1']);
					//header('Location:index.php');
					
				}catch(Exception $exp){
					$success = false;
					$error_message = $exp->getMessage();
				}
			}
			else{
				if($email_exists){$success = false; $error_message = "Email already belongs to another staff.";}
				if($phone_exists){$success = false; $error_message = "Phone number already belongs to another staff.";}
				if($id_exists){$success = false; $error_message = "Staff ID already belongs to another staff.";}
			}
		} break;
		default : {}
	}
}
?>
<h2 class="panel-header bg-grayDark fg-white" style="padding:1%">Register New Staff</h2>
<?php if ($isFormRequest && !$success) { ?>
    <div class="panel-content">
        <p class="fg-red"><?= $error_message ?></p>
    </div>
<?php } ?>
<div  class="panel-content">
<?php
if ($isFormRequest && $success) {
?>
    <div class="panel-content">
        <p class="fg-green">New staff <?= $array['last_name'].' '.$array['first_name'] ?> registered.</p>
    </div>

<?php }else{ ?>                               
<form method='post' action='view.php?p=13'>
        <input name="type" value="2" hidden=""/>
        <div class="row ntm">
            <label class="span2">Name<span class="fg-red">*</span></label>
            <div class="span4">
                <input type='text' required maxlength="30" placeholder="Last name" name='last_name' tabindex='1' style="width: inherit" value="<?= isset($array['last_name']) ? $array['last_name'] : '';?>"/>
                <input type='text' required maxlength="30" placeholder="First name" name='first_name' tabindex='2' style="width: inherit" value="<?= isset($array['first_name']) ? $array['first_name'] : '';?>"/>
            </div>
        </div>
        <div class="row" >
            <label class="span2">Staff ID<span class="fg-red">*</span></label>
            <div class="span4">
                <input name='staff_id' required style="width: inherit" maxlength="6" type='text' tabindex='3' value="<?= isset($array['staff_id']) ? $array['staff_id'] : '';?>"/>
            </div>
        </div>
        <div class="row" >
            <label class="span2">Password<span class="fg-red">*</span></label>
            <div class="span4">
                <input class="password" required name='password1' style="width: inherit" type='password' tabindex='4' />
                <label class="fg-lime">
                    <small>Should be up to 8 characters long and contain both upper and lower cases</small>
                </label> 
            </div>                                                                                                
        </div>
        <div class="row" >
            <label class="span2">Confirm Password<span class="fg-red">*</span></label>
            <div class="span4">
                <input class="password" required name='password2' style="width: inherit" type='password' tabindex='5' />
            </div>
        </div>
		<?php
        $deptOption = Staff::getDeptOptions();
        $DesignationOption = Staff::getDesignationOptions();
        ?>
        <div class="row">
        <label class="span2">Department</label>
        <div class="span4">
        <select name="department" style="width: inherit">
        <?php
        foreach ($deptOption as $value) {
            $selected = isset($array['department']) ? strcasecmp($value, $array['department']) === 0 : FALSE;
            echo "<option " . ($selected ? "selected" : "") . ">$value</option>";
        }
        ?>
        </select>
        </div>
        </div>
        <div class="row">
        <label class="span2">Designation</label>
        <div class="span4">
        <select name="designation" style="width: inherit">
        <?php
        foreach ($DesignationOption as $value) {
            $selected = isset($array['designation']) ? strcasecmp($value, $array['designation']) === 0 : FALSE;
            echo "<option " . ($selected ? "selected" : "") . ">$value Designation</option>";
        }
        ?>
        </select>
        </div>
        </div>
        <div class="row" >
        <label class="span2">Phone<span class="fg-red">*</span></label>
        <div class="span4">
        <input name='phone' required style="width: inherit" type="tel" tabindex='8' value="<?= isset($array['phone']) ? $array['phone'] : '';?>"/>
        </div>
        </div>
        <div class="row" >
        <label class="span2">Email<span class="fg-red">*</span>
        </label>
        <div class="span4">
        <input name='email' style="width: inherit" required type='email' tabindex='9' value="<?= isset($array['email']) ? $array['email'] : '';?>"/>
        
        <label class="fg-lime">
        <small>This will be used for password recovery and account verification</small>
        </label> 
        </div>
        </div>
        <div class="no-phone offset2">
        <input class="button default  bg-hover-dark" type='submit'
        name='submit' value='Register Staff' tabindex='10'/>
        </div>
        <div class="on-phone no-tablet no-desktop padding20 ntp nbp">
        <input class="button default  bg-hover-dark" type='submit'
        name='submit' value='Register Staff' tabindex='10'/>
        </div>
</form>
<?php } ?>
</div>