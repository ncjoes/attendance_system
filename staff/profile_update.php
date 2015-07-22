<?php
require_once 'Staff.php';
if (empty($array)) {
    $array = $user->getUserData();
}

if (isset($array["editProfileForm"])) {
    //Handle a post request from form
    //Validating details and update user
    try {
        $user->updateUserInfo($array);
        $success = true;
        $error_message = "";
    } catch (Exception $exc) {
        $success = false;
        $error_message = $exc->getMessage();
    }
}
?>
<div>
    <h4>UPDATE PROFILE</h4>
    <br/>
    <div class="padding5 grid">

        <div class="row">
            <a class="place-right button default bg-hover-dark fg-hover-white" href="view.php?p=1">
                Cancel Edit
            </a>
        </div>
        <div class="row bg-grayLighter grid">
            <div class="padding10">
                <?php
                if (isset($array["editProfileForm"])) {
                    if ($success) {
                        ?>
                        <p class="fg-green">Profile was successfully updated.</p>
                    <?php } else { ?>
                        <p class="fg-red"><?= $error_message ?></p>
                        <?php
                    }
                }
                ?>
                <form method="post" enctype="multipart/form-data" action="view.php?p=11">
                    <div class="row" >
                        <h2 class="bg-grayLight padding5">Personal Information</h2>
                        <div class="row ntm">
                            <label class="span2">First name<span class="fg-red">*</span></label>
                            <div class="span4">
                                <input class="" style="width: inherit" type='text' required maxlength="30" placeholder="First name" name='first_name'
                                       <?= isset($array['first_name']) ? "value='" . $array['first_name'] . "'" : ""; ?> tabindex='4' />
                            </div>
                        </div>
                        <div class="row">
                            <label class="span2">Last name<span class="fg-red">*</span></label>
                            <div class="span4">
                                <input class="" style="width: inherit" type='text' required maxlength="30" placeholder="Last name" name='last_name'
                                       <?= isset($array['last_name']) ? "value='" . $array['last_name'] . "'" : ""; ?> tabindex='3' />
                            </div>
                        </div>
                        <div class="row" >
                            <label class="span2">Other names</label>
                            <div class="span4">
                                <input type='text' maxlength="30" style="width: inherit" name='other_names'
                                       <?= isset($array['other_names']) ? "value='" . $array['other_names'] . "'" : ""; ?> tabindex='5'   />
                            </div>
                        </div>
                        <div class="row" >
                            <label class="span2">Date of Birth</label>
                            <div class="span4">
                                <!--old data-format="dddd, mmmm d, yyyy"-->
		                        <div class="input-control text" data-role="datepicker"
                                     data-date="<?= isset($array['dob']) ? $array['dob'] : strftime("%Y-%m-%d", mktime()); ?>"
                                     data-format="yyyy-mm-dd"
                                     data-position="top"
                                     data-effect="slide">
                                    <input type="text" name="dob">
                                    <button type="button" class="btn-date"></button>
                                </div>
                            </div>
                        </div>                    
                        <div class="row">
                            <label class="span2">Sex</label>
                            <div class="span4">
                                <select name="sex">
                                    <?php
                                        $mselected = isset($array['sex']) ? strcasecmp("M", $array['sex']) === 0 : FALSE;
                                        echo "<option " . ($mselected ? "selected" : "") . ">MALE</option>";
                                        $fselected = isset($array['sex']) ? strcasecmp("F", $array['sex']) === 0 : FALSE;
                                        echo "<option " . ($fselected ? "selected" : "") . ">FEMALE</option>";
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <label class="span2">Change Picture <small><em>(max 250kb)</em></small></label>
                            <div class="span4">
                                <div class="input-control file">
                                    <input type="file" name="pic_url" value="<?= isset($array['pic_url']) ? $array['pic_url'] : ""; ?>"/>
                                    <button class="btn-file"></button>
                                </div>
                            </div>
                        </div>
                        <h2 class="bg-grayLight padding5">Contact Information</h2>
                        <div class="row" >
                            <label class="span2">Phone<span class="fg-red">*</span></label>
                            <div class="span4">
                                <input name='phone' required style="width: inherit" type='tel' 
                                       <?= isset($array['phone']) ? "value='" . $array['phone'] . "'" : ""; ?> tabindex='8'  />
                            </div>
                        </div>
                        <div class="row" >
                            <label class="span2">Email<span class="fg-red">*</span>
                            </label>
                            <div class="span4">
                                <input name='email' style="width: inherit" required type='email' 
                                       <?= isset($array['email']) ? "value='" . $array['email'] . "'" : ""; ?>  tabindex='9'   />
                            </div>
                        </div>
                        <div class="row" >
                            <label class="span2">Address 1</label>
                            <div class="span4">
                                <textarea name='address' style="width: inherit" tabindex='10'><?=
                                    isset($array['address']) ?
                                            $array['address'] :
                                            "";
                                    ?></textarea>
                            </div>
                        </div>
                        <h2 class="bg-grayLight padding5">Employmrnt Details</h2>
                        <div class="row" >
                            <label class="span2">Staff ID<span class="fg-red">*</span></label>
                            <div class="span4">
                                <input name='staff_id' readonly="readonly" required="required" style="width: inherit" maxlength="11" type='text' 
                                       <?= isset($array['staff_id']) ? "value='" . $array['staff_id'] . "'" : ""; ?>  tabindex='6'  />
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
                                <select name="Designation" style="width: inherit">
                                    <?php
                                    foreach ($DesignationOption as $value) {
                                        $selected = isset($array['designation']) ? strcasecmp($value, $array['designation']) === 0 : FALSE;
                                        echo "<option " . ($selected ? "selected" : "") . ">$value Designation</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
<!--                    <h2 class="bg-grayLight padding5">Enter Password</h2>
                        <div class="row" >
                            <label class="span2">Password<span class="fg-red">*</span></label>
                            <div class="span4">
                                <input class="password" name='password' style="width: inherit" type='password' tabindex='2' />
                            </div>
                        </div>
-->                     <div class="row no-phone offset2">
                            <input class="button default bg-green bg-hover-dark" type='submit'
                                   name='editProfileForm' value='Update' tabindex='9'/>
                        </div>
                        <div class="on-phone no-tablet no-desktop padding20 ntp nbp">
                            <input class="button default bg-green bg-hover-dark" type='submit'
                                   name='editProfileForm' value='Update' tabindex='9'/>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>    
</div>
