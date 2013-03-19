<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

function datatable_script() { ?>
    <script type="text/javascript" src="<?php echo base_url('assets/js/admin.js'); ?>"></script>
<?php }
add_action('page_foot', 'datatable_script');

$page_title = 'Client Management';
include 'header.php';
include 'pagetop.php';
?>
<div class="container-fluid" id="maincontent">
    <div class="row-fluid">
        <div class="span12" id="content">
            <h1><?php echo $page_title; ?></h1>
                    
            <?php
                $msg = flashmsg_get();
                if ($msg)
                    echo '<div class="alert alert-error">'.htmlentities($msg).'</div>';
            ?>
            
            <form method="post" action="<?php echo site_url('clients/update'); ?>">
            <table class="list-table table table-stripped table-bordered">
            <tr>
                <th><input type="checkbox" id="id_checkall" /></th>
                <th>Name</th>
                <th>Description</th>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="text" name="name[new]" value="<?php echo isset($name['new']) ? h($name['new']) : ''; ?>" size="20" /></td>
                <td><input type="text" name="description[new]" value="<?php echo isset($description['new']) ? h($description['new']) : ''; ?>" size="20" /></td>
            </tr>
            
            <?php
            $query = $this->db->query("SELECT * FROM clients");
            foreach($query->result_array() as $row)
            {
                echo '
                <tr>
                    <td><input type="checkbox" class="chkrow" name="selected[]" value="'.h($row['client_id']).'" /></td>
                    <td><span rel="'.h($row['client_id']).'" class="inline_text">'.h($row['name']?$row['name']:'empty').'</span><input class="inline_edit" type="text" name="name['.h($row['client_id']).']" value="'.h($row['name']).'" size="20" /></td>
                    <td><span rel="'.h($row['client_id']).'" class="inline_text">'.h($row['description']?$row['description']:'empty').'</span><input class="inline_edit" type="text" name="description['.h($row['client_id']).']" value="'.h($row['description']).'" size="20" /></td>
                </tr>';
            }
            ?>
            <tr>
                <th colspan="6">
                    <input class="btn" type="submit" value="Save" />
                    <input class="btn" type="submit" id="delete_button" name="delete" value="Delete" />
                </th>
            </tr>
            </table>

            <div id="inline_holder"></div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php';
