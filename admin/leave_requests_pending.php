<?php
if(isset($array['approve_button'])){
	if(!empty($array['checkbox'])){
		$user->approveLeaveRequest($array['checkbox']);
	}
}

if(isset($array['disapprove_button'])){
	if(!empty($array['checkbox'])){
		$user->disapproveLeaveRequest($array['checkbox']);
	}
}

$requests = getPendingRequests();
?>
<div>
    <h4>LEAVE REQUEST</h4>
    <div class="row">
        <a href="view.php?p=202" class="button bg-blue bg-hover-dark fg-white place-right">Disapproved</a>
        <a href="view.php?p=201" class="button bg-blue bg-hover-dark fg-white place-right">Approved</a>
        <a href="view.php?p=2" class="button disabled place-right">Pending</a>
    </div>
    <div class="row">
        <?php
        if (empty($requests)) {
            echo '<p>No pending application</p>';
        } else {
            ?>
            <div id="top">
            <form method="post" enctype="multipart/form-data" action="view.php?p=2" name="form1">
                <div class="row">
                    <input name="approve_button" type="submit" value="Approve"/>
                    <input name="disapprove_button" type="submit" value="Disapprove"/>
                </div>
                <br/>
                <div class="row ntm">
                    <table class="table hovered bordered">
                        <thead>
                            <tr>
                            	<th class="text-left">SN</th>
                                <th class="text-left">&hellip;</th>
                                <th class="text-left">Names</th>
                                <th class="text-left">Date of Submission</th>
                                <th class="text-left">Type</th>
                                <th class="text-left">Reason</th>
                                <th class="text-left">Proposed Start Time</th>
                                <th class="text-left">Proposed Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($index = 0; $index < count($requests); $index++) {
                                if ($index != 0 && $index % 20 === 0) {
                            echo '<tr><td colspan="8"><a href="#top">back to top</a></td></tr>';
                                }
                                ?>
                                <tr>                            
                                    <td class="text-left"><?= $index+1 ?></td>
                                    <td class="text-left"><input type="checkbox" name="checkbox[]" value="<?= $requests[$index]['id'] ?>"/></td>
                                    <td class="text-left"><?= $requests[$index]['last_name'].' '.$requests[$index]['first_name'] ?></td>
                                    <td class="text-left"><?= $requests[$index]['request_time'] ?></td>
                                    <td class="text-left"><?= $requests[$index]['type'] ?></td>
                                    <td class="text-left"><?= $requests[$index]['reason'] ?></td>
                                    <td class="text-left"><?= $requests[$index]['request_start_time'] ?></td>
                                    <td class="text-left"><?= $requests[$index]['request_duration'] ?></td>
                                </tr>
                                <?php
                            }
                            echo '<tr><td colspan="8"><a href="#top">back to top</a></td></tr>';
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <input name="approve_button" type="submit" value="Approve"/>
                    <input name="disapprove_button" type="submit" value="Disapprove"/>
                </div>
			</form>
            </div>

        <?php } ?>
    </div>
</div>