<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */
$page_title = 'Simetri Tickets';
include 'header.php';
include 'pagetop.php';
?>
<div class="container-fluid" id="maincontent">
    <div class="row-fluid">
        <div class="span8" id="content">
            <h1><?php echo $page_title; ?></h1>
                    
            <?php
                $msg = flashmsg_get();
                if ($msg)
                    echo '<div class="alert alert-error">'.htmlentities($msg).'</div>';
            ?>
            
            <?php if ( $this->Users->has_perms( $this->orca_auth->user->id, 'tasks/' ) ): ?>
            <h3>Incomplete Tasks</h3>
            <ul>
                <?php 
                $query = $this->db->query("SELECT UNIX_TIMESTAMP(tasks.due)-UNIX_TIMESTAMP() AS telat, tasks.*, projects.title AS project_title FROM tasks LEFT JOIN projects ON tasks.project_id = projects.id 
                    WHERE tasks.user_id = ? AND tasks.status = 0
                    ORDER BY 1 LIMIT 10",$this->orca_auth->user->id)->result();
                foreach($query as $row): ?>
                <li><a href="<?php echo site_url('tasks/detail?id='.$row->id); ?>"><?php echo $row->task; ?></a> on <span class="label"><?php echo $row->project_title; ?></span>
                    <span class="label label-warning"><?php echo time_since2( $row->due ); ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
            <?php if ( $this->Users->has_perms( $this->orca_auth->user->id, 'tickets/' ) ): ?>
            <h3>My Tickets</h3>
            <ul>
                <?php 
                $query = $this->db->query("SELECT tickets.*, 
                        projects.title AS project_title, 
                        user1.username AS assigned_user_name, 
                        user2.username AS ticket_user_name 
                    FROM tickets LEFT JOIN projects ON tickets.project_id = projects.id
                    LEFT JOIN users user1 ON tickets.assigned_user = user1.id
                    LEFT JOIN users user2 ON tickets.user_id = user2.id
                    WHERE tickets.user_id = ? OR tickets.assigned_user = ? OR projects.user_id = ?
                    AND tickets.status = 'open'
                    GROUP BY tickets.id
                    ORDER BY tickets.created_on
                    LIMIT 20",
                    array($this->orca_auth->user->id,$this->orca_auth->user->id,$this->orca_auth->user->id))->result();
                foreach($query as $row): ?>
                <li><a href="<?php echo site_url('tickets/detail?id='.$row->id); ?>"><?php echo $row->title; ?></a> on <span class="label"><?php echo $row->project_title; ?></span>
                    <span class="label label-warning"><?php echo time_since2( $row->created_on ); ?></span>
                    <span class="label label-info">Petugas: <?php echo $row->assigned_user_name ? $row->assigned_user_name : 'N/A'; ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
            <?php if ( $this->Users->has_perms( $this->orca_auth->user->id, 'projects/' ) ): ?>
            <h3>Projects</h3>
            <ul>
                <?php 
                $query = $this->db->query("SELECT * FROM projects WHERE client_id = ?",$this->orca_auth->user->client_id)->result();
                foreach($query as $row): ?>
                <li><a href="<?php echo site_url('projects/tasks?id='.$row->id); ?>"><?php echo $row->title; ?></a></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>
        
        <?php
        
        $tt = $this->db->query("SELECT COUNT(*) AS cnt FROM tasks WHERE client_id = ?", $this->orca_auth->user->id)->row();
        $total = $tt ? $tt->cnt : 0;
        
        $uid = $this->orca_auth->user->id;
        $tasks = $this->db->query("SELECT status, COUNT(*) AS cnt FROM tasks WHERE user_id = ? GROUP BY status", $uid)->result();
        $incomplete=$complete=$totaltask=0;
        foreach($tasks as $t)
        {
            if ( $t->status == 0 )
                $incomplete+=$t->cnt;
            else $complete+=$t->cnt;
            $totaltask+=$t->cnt;
        }
        
        $tt1 = $this->db->query("SELECT COUNT(*) AS cnt FROM tickets WHERE client_id = ?", $this->orca_auth->user->id)->row();
        $total1 = $tt1 ? $tt1->cnt : 0;
        
        $tickets = $this->db->query("SELECT status, COUNT(*) AS cnt FROM tickets WHERE assigned_user = ? GROUP BY status", $uid)->result();
        $incomplete1=$complete1=$totaltask1=0;
        foreach($tasks as $t)
        {
            if ( $t->status == 'open' )
                $incomplete1+=$t->cnt;
            else $complete1+=$t->cnt;
            $totaltask1+=$t->cnt;
        }
        
        ?>
        
        <div class="span4">
            <h3>Stats</h3>
            <table class="stats-table table table-striped">
                <tr>
                    <th>Tasks Assigned</th>
                    <td><?php echo $totaltask; ?> / <?php echo $total; if ( $total ) echo " (".number_format(($totaltask/$total)*100) . '%)'; ?></td>
                </tr>
                <tr>
                    <th>Tasks Completed</th>
                    <td><?php echo $complete; ?> / <?php echo $totaltask; if ( $totaltask ) echo " (".number_format(($complete/$totaltask)*100) . '%)'; ?></td>
                </tr>
                <tr>
                    <th>Tickets Assigned</th>
                    <td><?php echo $totaltask1; ?> / <?php echo $total1; if ( $total1 ) echo " (".number_format(($totaltask1/$total1)*100) . '%)'; ?></td>
                </tr>
                <tr>
                    <th>Tickets Closed</th>
                    <td><?php echo $complete1; ?> / <?php echo $totaltask1; if ( $totaltask1 ) echo " (".number_format(($complete1/$totaltask1)*100) . '%)'; ?></td>
                </tr>
            </table>
        </div>
        
    </div>
</div>

<?php include 'footer.php';
