<?php
$attendances = getAttendance($array['staff'], $array['date']);
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
                        $time = array( strftime("%Y-%m-%d", mktime()), "00:00:00");
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
                    <select name="staff" class="span4">
                    <?php
		$query = "select * from staff order by last_name,first_name";
		$link = Utility::getDefaultDBConnection();
		$result = mysqli_query($link, $query);
		$staff_details = array();
		if($result){
			while($row = mysqli_fetch_array($result)){
				$staff_details[$row['staff_id']] = $row;
				echo '<option value="'.$row['staff_id'].'">'.$row['last_name'].' '.$row['first_name'].'</option>';
			}
		}
					?>
                    </select>
                    <input class="span1" type="submit" value="Check" name="attendanceForm"/>
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
                                <th class="text-left">Names</th>
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
                                    echo '<tr><td colspan="5"><a href="#top">back to top</a></td></tr>';
                                }
                                ?>
                                <tr>                            
                                    <td class="text-left"><?= $attendances[$index]['day'] ?></td>
                                    <td class="text-left">
									<?php
									$id = $attendances[$index]['staff_id'];
									echo $staff_details[$id]['first_name'].' '.$staff_details[$id]['last_name'];
									?>
                                    </td>
                                    <td class="text-left"><?= $attendances[$index]['login_time'] ?></td>
                                    <td class="text-left">
                                        <?=
                                        $attendances[$index]['logout_time']==$attendances[$index]['logout_time'] ? "-" :
                                            $attendances[$index]['logout_time'];
                                        ?>
                                    </td>
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
                                	<td colspan="4">&nbsp;</td>
                                    <td><strong><?= $totalHoursWorked; ?></strong></td>
                                </tr>
                            <?php
                                    echo '<tr><td colspan="5"><a href="#top">back to top</a></td></tr>';
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>