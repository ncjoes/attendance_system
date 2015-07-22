<?php
//Initializing variables with default values
$defaultPage = "view.php?p=11";
$sort_type = SORT_STAFF_TYPE_LASTNAME;
$order = ORDER_STAFF_ASC;

$searchQuery = "";

if (isset($array['search_button']) || //$array from index.php
        isset($array['delete_button']) ||
        isset($array['activate_button'])) {

    //process POST requests
    $page = 1;

    $searchQuery = html_entity_decode(filter_input(INPUT_POST, "search"));
    $sort_type = html_entity_decode(filter_input(INPUT_POST, "sort_type"));
    $order = html_entity_decode(filter_input(INPUT_POST, "sort_order"));

    try {
        if (isset($array['delete_button']) && isset($array['checkbox'])) {
            $actionPerformed = true;
            $user->deleteStaff($array['checkbox']);
        } elseif (isset($array['activate_button']) && isset($array['checkbox'])) {
            $actionPerformed = true;
            $user->activateStaff($array['checkbox']);
        }
        $success = true;
        $error_message = "";
    } catch (Exception $exc) {
        $success = false;
        $error_message = $exc->getMessage();
    }
        $staff = getSuspendedStaff();
} else {
    //Process GET requests or no requests
    $page = filter_input(INPUT_GET, "pg");
    if (isset($page)) {
        //if switching page, repeat search
        $searchQuery = filter_input(INPUT_GET, "q");
        $sort_type = filter_input(INPUT_GET, "s");
        $order = filter_input(INPUT_GET, "o");

        $staff = searchStaff($searchQuery, false, true, $sort_type, $order);
    } else {
        $page = 1;
        $staff = getSuspendedStaff();
    }
}
?>
<div>
    <h4>USERS</h4>
    <div class="row">
        <a href="view.php?p=12" class="button bg-blue bg-hover-dark fg-white place-right">Deleted</a>
        <a href="view.php?p=11" class="button disabled place-right">Suspended</a>
        <a href="view.php?p=1" class="button bg-blue bg-hover-dark fg-white  place-right">Active</a>
    </div>
    <div class="row">
        <?php
        if (empty($staff) and ! isset($array['search_button'])) {
            echo '<p>No suspended user</p>';
        } else {
            ?>
            <div class="bg-grayLighter padding5">
                <form method="post" action="view.php?p=11">
                    <div class="input-control text" data-role="input-control">
                        <input type="text" value="<?= $searchQuery ?>" placeholder="Search Users" name="search"/>
                        <button class="btn-search" name="search_button" type="submit"></button>
                    </div>

                    <div class="row ntm">
                        <div class="span5">
                            <label class="span1">Sort by: </label>
                            <div class="span4">
                                <input type="radio" name="sort_type" 
                                <?=
                                isset($sort_type) ?
                                        ($sort_type == SORT_STAFF_TYPE_STAFF_ID ? "checked" : "") :
                                        "checked"
                                ?>
                                       value="<?= SORT_STAFF_TYPE_STAFF_ID ?>"/> Staff ID
                                <input type="radio" name="sort_type"
                                <?=
                                isset($sort_type) ?
                                        ($sort_type == SORT_STAFF_TYPE_LASTNAME ? "checked" : "") :
                                        ""
                                ?>
                                       value="<?= SORT_STAFF_TYPE_LASTNAME ?>"/> Last Name
                                <input type="radio" name="sort_type"
                                <?=
                                isset($sort_type) ?
                                        ($sort_type == SORT_STAFF_TYPE_LEVEL ? "checked" : "") :
                                        ""
                                ?>
                                       value="<?= SORT_STAFF_TYPE_LEVEL ?>"/> Designation
                            </div>
                        </div>
                        <div class="span3">
                            <label class="span1">Order: </label>
                            <div class="span2">
                                <input type="radio" name="sort_order"
                                <?= isset($order) ? ($order == ORDER_STAFF_ASC ? "checked" : "") : "checked" ?>
                                       value="<?= ORDER_STAFF_ASC ?>"/> Asc
                                <input type="radio" name="sort_order"
                                <?= isset($order) ? ($order == ORDER_STAFF_DESC ? "checked" : "") : "" ?>
                                       value="<?= ORDER_STAFF_DESC ?>"/> Desc
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <?php
            if (isset($actionPerformed)) {
                if ($success) {
                    ?>
                    <p class="fg-NACOSS-UNN">Action successful</p>
                <?php } else { ?>
                    <p class="fg-red"><?= $error_message ?></p>
                    <?php
                }
            }
            ?>
            <div id="top">
                <form action="view.php?p=11" method="post">
                    <input class="span1" name="search" hidden value="<?= $searchQuery ?>"/>
                    <input class="span1" name="sort_type" hidden value="<?= $sort_type ?>"/>
                    <input class="span1" name="sort_order" hidden value="<?= $order ?>"/>
                    <div class="row">
                        <input name="delete_button" type="submit" value="Delete"/>
                        <input name="activate_button" type="submit" value="Activate"/>
                    </div>
                    <div class="row ntm">
                        <table class="table hovered bordered">
                            <thead>
                                <tr>
                                    <th class="text-left"></th>
                                    <th class="text-left">Staff ID</th>
                                    <th class="text-left">Last Name</th>
                                    <th class="text-left">First Name</th>
                                    <th class="text-left">Other Names</th>
                                    <th class="text-left">Department</th>
                                    <th class="text-left">Designation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($index = 0; $index < count($staff); $index++) {
                                    if ($index != 0 && $index % 20 === 0) {
                                        echo '<tr><td></td><td><a href="#top">back to top</a></td></tr>';
                                    }
                                    ?>
                                    <tr>                            
                                        <td class="text-left"><input type="checkbox" name="checkbox[]" value="<?= $staff[$index]['staff_id'] ?>"/></td>
                                        <td class="text-left"><?= $staff[$index]['staff_id'] ?></td>
                                        <td class="text-left"><?= $staff[$index]['last_name'] ?></td>
                                        <td class="text-left"><?= $staff[$index]['first_name'] ?></td>
                                        <td class="text-left"><?= $staff[$index]['other_names'] ?></td>
                                        <td class="text-left"><?= $staff[$index]['department'] ?></td>
                                        <td class="text-left"><?= $staff[$index]['designation'] ?></td>
                                    </tr>
                                    <?php
                                }
                                echo '<tr><td></td><td><a href="#top">back to top</a></td></tr>';
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row ntm">
                        <input name="delete_button" type="submit" value="Delete"/>
                        <input name="activate_button" type="submit" value="Activate"/>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
</div>