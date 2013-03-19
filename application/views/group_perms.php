<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

function perms_script() { ?>
    <script type="text/javascript" src="<?php echo base_url('assets/js/admin.js'); ?>"></script>
<?php }
add_action('page_foot', 'perms_script');

$query = $this->db->query("SELECT * FROM groups WHERE group_id = ?",array($group_id));
$group= $query->row();

$page_title = "($group->group_name) Group Permissions";
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
            
            function print_tree( $rows, $selected, $parent_id=0 )
            {
                echo '<ul id="perm-'.$parent_id.'" class="perm">';

                $maps = array('protected', 'public', 'hidden');

                foreach( $rows as $perm_id => $perm )
                {
                    $perm = (array)$perm;
                    if ( $perm['parent_id'] != $parent_id )
                        continue;

                    echo '<li class="lineborder">
                    <label for="id_check_'.$perm['perm_id'].'">
                    <input class="block checkthis" id="id_check_'.$perm['perm_id'].'" type="checkbox" name="selected[]" value="'.$perm['perm_id'].'" '.(isset($selected[$perm['perm_id']]) ? 'checked="checked"' : '').' />
                    <span class="block">'.h($perm['perm_name']?$perm['perm_name']:'empty').'</span>
                    <span class="block">'.h($perm['perm_path']?$perm['perm_path']:'empty').'</span>
                    <span class="block">'.h($maps[$perm['public']]).'</span>
                    </label>
                    <br class="clear" />
                    ';

                    print_tree( $rows, $selected, $perm['perm_id'] );
                    echo '</li>';

                }
                echo '</ul>';
            }
            
            $sql = "SELECT * FROM perms ORDER BY parent_id, perm_order";
            $perms = $this->db->query( $sql )->result();

            ?>

            <form method="POST" action="<?php echo site_url('groups/perms?id='.$group_id); ?>">
            <?php print_tree($perms,$selected); ?>
            <p>
                <input type="submit" class="btn" value="Save" />
                <a href="<?php echo site_url('groups'); ?>">Back to Groups</a>
            </p>
            </form>

            <script type="text/javascript">
            $(function() {
                $(".checkthis").click(function() {
                    var checked = this.checked;
                    var li = $(this).parent().parent();
                    if (checked)
                        li.find('input.checkthis').attr( 'checked', true );
                    else
                        li.find('input.checkthis').removeAttr('checked');
                });
            });
            </script>

        </div>
    </div>
</div>
    
<?php include 'footer.php';
