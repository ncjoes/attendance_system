<?php
$attendances = getAttendance($user, $array['date']);
?>
<div>
    <h4>ATTENDANCE</h4>
    <div class="padding5 grid">
        <div class="grid">
            <form class="row" method="post" action="view.php?p=3">
                <div class="row" >
                    <label class="span2">On or Before:</label>
                    <?php
                    if (empty($array['date'])) {
                        $time = array(strftime("%Y-%m-%d", mktime()), //Current time
                            "00:00:00");
                    } else {
                        $time = explode(" ", $array['date']);
                    }
                    ?>
                    <div class="span4">
                        <div class="input-control text" data-role="datepicker"
                             data-date="<?= $time[0] ?>"
                             data-format="yyyy-mm-dd"
                             data-position="top"
                             data-effect="slide">
                            <input type="text" name="date">
                            <button type="button" class="btn-date"></button>
                        </div>
                    </div>
                    <input class="" type="submit" value="Check" name="attendanceForm"/>
                </div> 
            </form>
        </div>
        <div>            
            <div id="top">
                <div class="row ntm">
                    <table class="table hovered bordered">
                        <thead>
                            <tr>
                                <th class="text-left">Date</th>
                                <th class="text-left">Sign in time</th>
                                <th class="text-left">Sign out time</th>
                                <th class="text-left">Total Hours worked</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
							$totalHoursWorked = 0.0;
                            for ($index = 0; $index < count($attendances); $index++) {
                                if ($index != 0 && $index % 20 === 0) {
                                    echo '<tr><td colspan="4"><a href="#top">back to top</a></td></tr>';
                                }
                                ?>
                                <tr>                            
                                    <td class="text-left"><?= $attendances[$index]['day'] ?></td>
                                    <td class="text-left"><?= $attendances[$index]['login_time'] ?></td>
                                    <td class="text-left"><?= $attendances[$index]['logout_time'] ?></td>
                                    <td class="text-left">
                                        <?php
                                        $login = strtotime($attendances[$index]['login_time']);
                                        $logout = strtotime($attendances[$index]['logout_time']);
                                        $milliToHour = 60 * 60;
                                        $hoursWorked = ($logout - $login) / $milliToHour;
										$totalHoursWorked += $hoursWorked;
										echo $hoursWorked;
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            } ?>
                            	<tr>
                                	<td colspan="3">&nbsp;</td>
                                    <td><strong><?= $totalHoursWorked; ?></strong></td>
                                </tr>
                            <?php
                                    echo '<tr><td colspan="4"><a href="#top">back to top</a></td></tr>';
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>