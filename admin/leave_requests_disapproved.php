<?php
$requests = getDisapprovedRequests();
?>
<div>
    <h4>LEAVE HISTORY</h4>
    <div class="row">
        <a href="view.php?p=202" class="button disabled place-right">Disapproved</a>
        <a href="view.php?p=201" class="button bg-blue bg-hover-dark fg-white place-right">Approved</a>
        <a href="view.php?p=2" class="button bg-blue bg-hover-dark fg-white place-right">Pending</a>
    </div>
    <div class="row">
        <?php
        if (empty($requests)) {
            echo '<p>No disapproved application</p>';
        } else {
            ?>
            <div id="top">
                <div class="row ntm">
                    <table class="table hovered bordered">
                        <thead>
                            <tr>
                            	<th class="text-left">SN</th>
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
                                    echo '<tr><td colspan="7"><a href="#top">back to top</a></td></tr>';
                                }
                                ?>
                                <tr>                            
                                    <td class="text-left"><?= $index+1 ?></td>
                                    <td class="text-left"><?= $requests[$index]['last_name'].' '.$requests[$index]['first_name'] ?></td>
                                    <td class="text-left"><?= $requests[$index]['request_time'] ?></td>
                                    <td class="text-left"><?= $requests[$index]['type'] ?></td>
                                    <td class="text-left"><?= $requests[$index]['reason'] ?></td>
                                    <td class="text-left"><?= $requests[$index]['request_start_time'] ?></td>
                                    <td class="text-left"><?= $requests[$index]['request_duration'] ?></td>
                                </tr>
                                <?php
                            }
                                    echo '<tr><td colspan="7"><a href="#top">back to top</a></td></tr>';
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php } ?>
    </div>
</div>