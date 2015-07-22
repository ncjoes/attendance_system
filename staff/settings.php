<?php
//password processing/change code has been moved to view.php to enable setting of cookie headers
?>
<div>
    <h4>SETTINGS</h4>
    <br/>
    <p>Change Password</p>
    <div class="padding5 grid">
        <?php
        if (isset($isPasswordChangeRequest)) {
            if ($success) {
                ?>
                <p class="fg-green">Password changed</p>
            <?php } else { ?>
                <p class="fg-red"><?= $error_message ?></p>
                <?php
            }
        }
        ?>
        <form method="post" action="view.php?p=4">
            <div class="row" >
                <label class="span2">Old Password<span class="fg-red">*</span></label>
                <div class="span4">
                    <input class="password" name='password' style="width: inherit" type='password' tabindex='2' />
                </div>
            </div>
            <div class="row" >
                <label class="span2">New Password<span class="fg-red">*</span></label>
                <div class="span4">
                    <input class="password" name='password1' style="width: inherit" type='password' tabindex='2' />
                </div>
            </div>
            <div class="row" >
                <label class="span2">Confirm Password<span class="fg-red">*</span></label>
                <div class="span4">
                    <input class="password" name='password2' style="width: inherit" type='password' tabindex='2' />
                </div>
            </div>

            <div class="row">
                <input class="button bg-blue bg-hover-dark fg-white" type='submit' name='change_password' value='Change' tabindex='9'/>
            </div>
        </form>
    </div>
</div>