<?php
$array = $user->getUserData();
?>
<div>
    <h4>PROFILE</h4>
    <br/>
    <div class="padding5 grid">
        <div class="row">
            <a class="place-right button default bg-hover-dark fg-hover-white" href="logout.php">
                Log out
            </a>
            <div class="place-right">
                &nbsp;
            </div>
            <a class="place-right button default bg-hover-dark fg-hover-white" href="view.php?p=11">
                Update Profile
            </a>
        </div>
        <div class="row ntp ntm">
            <div class="">
                <div class="grid">
                    <div class="row">
                        <div class="span2 no-phone no-tablet bg-grayLighter">
                            <img class="image shadow padding5" src="<?=
                            isset($array['pic_url']) && !empty($array['pic_url']) ?
                                    $array['pic_url'] :
                                    "img/picture5.png"
                            ?>" alt=""/>
                        </div>
                        <!--For phones and tablets-->
                        <div style="height: 100px; width: 100px" class="on-phone on-tablet no-desktop bg-grayLighter">
                            <img style="height: inherit; width: inherit" class="image padding5" src="<?=
                            isset($array['pic_url']) && !empty($array['pic_url']) ?
                                    $array['pic_url'] :
                                    "img/picture5.png"
                            ?>" alt=""/>
                        </div>
                        <div class="span7 bg-grayLighter shadow">
                            <div class="padding10">
                                <!--Name-->
                                <h2>
                                    <?php
                                    echo strtoupper($array['first_name']) . " ";
                                    echo empty($array['last_name']) ? "" : strtoupper($array['other_names']) . " ";
                                    echo strtoupper($array['last_name'])
                                    ?>
                                </h2>
                                <!--Registration number-->
                                <p>
                                    <?= $array['staff_id'] ?> 
                                </p>
                                <!--Department and level-->
                                <?php
                                if (isset($array['department']) && !empty($array['department'])) {
                                    echo ucwords($array['department']) . " Department";
                                    echo '<br/>';
                                    echo ucwords($array['designation']);
                                    echo '<br/>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row ntm">
            <div class="">
                <div class="panel no-border bg-transparent" data-role="panel">
                    <p class="panel-header">Personal Information</p>
                    <div class="panel-content bg-grayLighter">
                        <p><strong>Names:</strong> 
                            <!--Displays date in the format: Saturday, 29, July 1995-->
                            <?= empty($array['first_name']) ? "" : $array['first_name']; ?> 
                            <?= empty($array['last_name']) ? "" : $array['last_name']; ?> 
                            <?= empty($array['other_names']) ? "" : $array['other_names']; ?>
                        </p>
                        <p><strong>Date of Birth:</strong> 
                            <!--Displays date in the format: Saturday, 29, July 1995-->
                            <?= empty($array['dob']) ? "" : strftime("%A, %#d, %B %Y", strtotime($array['dob'])) ?>
                        </p>
                        <p><strong>Sex:</strong>
                            <?php
                            if (isset($array['sex']) && !empty($array['sex'])) {
                                echo strcasecmp($array['sex'],"M") === 0 ? "Male" : "Female";
                            }
                            ?>
                        </p>
                    </div>                    
                </div>
                <br/>
                <div class="panel no-border bg-transparent" data-role="panel">
                    <p class="panel-header">Contact Information</p>
                    <div class="panel-content bg-grayLighter">
                        <p><strong>Phone:</strong> <?= $array['phone'] ?></p>
                        <p><strong>Email:</strong> <?= $array['email'] ?></p>
                        <p><strong>Address:</strong> <?= $array['address'] ?></p>                  
                    </div>
                </div>
                <br/>
                <div class="panel no-border bg-transparent" data-role="panel">
                    <p class="panel-header">Employment Details</p>
                    <div class="panel-content bg-grayLighter">
                        <p><strong>Staff ID:</strong> <?= $array['staff_id'] ?></p>
                        <p><strong>Department:</strong> <?= $array['department'] ?></p>
                        <p><strong>Designation:</strong> <?= $array['designation'] ?></p>                  
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>