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

$page_title = 'Permission Management';
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
            
            function public_select( $name, $value=1, $class='' )
            {
                $str = '<select name="'.$name.'" '.($class ? " class=\"$class\"" : '').'>';
                $array = array( 'protected', 'public', 'hidden' );
                foreach( $array as $key => $val )
                    $str .= '<option value="'.$key.'" '.($key == $value ? 'selected="selected"' : '').'>'.$val.'</option>';
                $str .= '</select>';
                return $str;
            }

            function print_tree( $rows, $parent_id=0 )
            {
                echo '<ul id="perm-'.$parent_id.'" class="perm">';

                if ($parent_id == 0 && !isset($first))
                {
                    $first = true;

                    echo '<li>
                    <input type="text" class="input-mini" name="new_order[0]" size="2" value="" placeholder="order" />
                    <input type="text" name="new_name[0]" size="10" value="" placeholder="name" />
                    <input type="text" name="new_path[0]" size="20" value="" placeholder="path"  />
                    <input type="text" class="input-mini" name="new_class[0]" size="10" value="" placeholder="class" />
                    ' . public_select( 'new_public[0]', 1, 'input-small' ) .'
                    <input type="submit" class="btn" value="add" />
                    ';
                }

                $maps = array('protected', 'public', 'hidden');

                foreach( $rows as $perm_id => $perm )
                {
                    if ( $perm['parent_id'] != $parent_id )
                        continue;

                    echo '<li>
                    <input class="block2" type="checkbox" name="selected[]" value="'.$perm['perm_id'].'" />
                    <span rel="'.h($perm['perm_id']).'" class="w1 block inline_text">'.h($perm['perm_order']?$perm['perm_order']:'0').'</span><input class="input-mini inline_edit block2" type="text" name="perm_order['.$perm['perm_id'].']" size="2" value="'.h($perm['perm_order']).'" placeholder="order" />
                    <span rel="'.h($perm['perm_id']).'" class="w1 block inline_text">'.h($perm['perm_name']?$perm['perm_name']:'empty').'</span><input class="inline_edit block2" type="text" name="perm_name['.$perm['perm_id'].']" size="10" value="'.h($perm['perm_name']).'" placeholder="name" />
                    <span rel="'.h($perm['perm_id']).'" class="w2 block inline_text">'.h($perm['perm_path']?$perm['perm_path']:'empty').'</span><input class="inline_edit block2" type="text" name="perm_path['.$perm['perm_id'].']" size="20" value="'.h($perm['perm_path']).'" placeholder="path"  />
                    <span rel="'.h($perm['perm_id']).'" class="w2 block inline_text">'.h($perm['perm_class']?$perm['perm_class']:'empty').'</span><input class="input-mini inline_edit block2" type="text" name="perm_class['.$perm['perm_id'].']" size="10" value="'.h($perm['perm_class']).'" placeholder="class"  />
                    <span rel="'.h($perm['perm_id']).'" class="block inline_text">'.h($maps[$perm['public']]).'</span>' . public_select( 'public['.$perm['perm_id'].']', $perm['public'], 'inline_edit block2 input-small' ) .'
                    <input type="button" class="btn" value="+" onclick="addChild('.$perm['perm_id'].')" />
                    <input type="submit" class="btn" value="save" />
                    <div class="cl"></div>
                    ';

                    print_tree( $rows, $perm['perm_id'] );
                    echo '</li>';

                }
                echo '</ul>';
            }

            ?>

            <form method="POST" action="<?php echo site_url('/perms/update'); ?>">

            <?php 
            $query = $this->db->order_by('parent_id,perm_order')->get('perms');
            $perms = array();
            foreach($query->result_array() as $row)
            {
                $perms[$row['perm_id']] = $row;
            }

            print_tree($perms); 

            ?>

            <p>
                <input type="submit" class="btn btn.large" value="Save" />
                <input type="submit" class="btn btn.large" value="Delete" name="delete" />
            </p>

            <div id="inline_holder"></div>
            </form>

            <script type="text/javascript">

                function public_select( $name, $value, $class ) {
                    var $str = '<select name="'+$name+'" '+($class ? " class=\""+$class+"\"" : '')+'>';
                    var $array = ['protected', 'public', 'hidden'];
                    for( var i=0, len=$array.length; i<len; i++ ) {
                        $str += '<option value="'+i+'" '+(i == $value ? 'selected="selected"' : '')+'>'+$array[i]+'</option>';
                    }
                    $str += '</select>';
                    return $str;
                }

                function addChild(parent) {
                    $("#perm-"+parent).prepend('<li>'+
                        '<input type="text" class="input-mini" name="new_order['+parent+']" size="2" value="" placeholder="order" /> ' +
                        '<input type="text" name="new_name['+parent+']" size="10" value="" placeholder="name" /> ' +
                        '<input type="text" name="new_path['+parent+']" size="20" value="" placeholder="path" /> ' +
                        '<input type="text" class="input-small" name="new_class['+parent+']" size="10" value="" placeholder="class" /> ' +
                        public_select('new_public['+parent+']', 0, 'input-small') +
                        '<input type="submit" class="btn" value="add" /></li>');
                }

            </script>
        </div>
    </div>
</div>
<?php include 'footer.php';
