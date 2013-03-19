<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

function show_history( $type, $id )
{
    $ci = &get_instance();
    $rows = $ci->db->query("SELECT change_history.*, users.username 
            FROM change_history LEFT JOIN users ON change_history.user_id = users.id
            WHERE change_history.object_type = ? AND  change_history.object_id = ?
            ORDER BY change_history.history_date DESC LIMIT 5", array($type, $id))->result();
    ?>
    <?php if ($rows): ?>
<div class="history-div">
    <h4>History</h4>
    <ul class="history-list">
        <?php foreach($rows as $row): ?>
        <li>
            <span class="label"><?php echo time_since2($row->history_date); ?></span>
            <span class="label label-info">@<?php echo $row->username; ?></span>
            <span class="history"><?php echo $row->message; ?></span>
        </li>
        <?php endforeach; ?> 
    </ul>
</div>
    <?php endif;
}