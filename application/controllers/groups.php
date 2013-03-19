<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: herdian ferdianto
 * ferdhie@orca.web.id
 * http://ferdianto.com/
 */

/**
 * Description of groups
 *
 * @author ferdhie
 */
class Groups extends MY_Controller
{
    function index()
    {
        $this->load->view('groups');
    }
    
    function update()
    {
        $groups = p('group_name');
        $admin_group = p('admin_group');
        $ids = p('id');

        if ( p('delete') )
        {
            $selected = p('selected');
            if ( is_array($selected) && $selected )
            {
                $selected = implode(", ", array_map( 'intval', $selected));
                if ($selected)
                {
                    $this->db->query("DELETE FROM groups WHERE group_id in ( $selected )");
                    flashmsg_set('update groups success');
                    do_action( 'after_save', array( $this->orca_auth->user, 'groups', '0', "Delete groups with ID (".implode(",",$selected).")" ) );
                }
            }
        }
        else
        {
            if ( isset($groups['new']) && $groups['new'] )
            {
                $ag = isset($admin_group['new']) ? 1 : 0;
                if ($this->orca_auth->user->client_id) $ag = 0;
                $this->db->query("INSERT INTO groups ( group_name, admin_group ) VALUES ( ?, ? )", array($groups['new'], $ag));
                $id = $this->db->insert_id();
                do_action( 'after_save', array( $this->orca_auth->user, 'groups', $id, "Add group $id with name $groups[new]" ) );
            }

            if ( $ids )
            {
                foreach($ids as $id)
                {
                    $group_name = isset($groups[$id]) ? $groups[$id] : '';
                    $admin_group = isset($admin_group[$id]) && !$this->orca_auth->user->client_id ? ",admin_group={$admin_group[$id]}" : '';
                    $this->db->query("UPDATE groups SET group_name=? $admin_group WHERE group_id = ?", array($group_name, $id));
                    do_action( 'after_save', array( $this->orca_auth->user, 'groups', $id, "Update group $id with name $group_name" ) );
                }
            }
        }
        
        redirect(site_url('groups'));
    }
    
    function perms()
    {
        $group_id = $this->input->get('id');
        if ( !$group_id )
        {
            show_404();
        }

        if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
        {
            $selected = p('selected');
            if ($selected && is_array($selected))
            {
                $selected = array_filter(array_map('intval', $selected));
                if ( $selected )
                {
                    $sql = "DELETE FROM group_perms WHERE group_id = ?";
                    $this->db->query($sql, array($group_id));

                    $sql = "INSERT INTO group_perms (group_id, perm_id) VALUES (?,?)";
                    foreach( $selected as $sel )
                    {
                        $this->db->query($sql, array($group_id, $sel));
                    }
                    
                    do_action( 'after_save', array( $this->orca_auth->user, 'groups', $group_id, "Update group $group_id with permission ".implode(", ", $selected) ) );
                    flashmsg_set('Update group permission success');
                }
            }

            redirect(site_url('groups/perms?id='.$group_id.'&ok=1'));
        }

        $query = $this->db->query( "SELECT perm_id FROM group_perms WHERE group_id = ?", array($group_id) );
        $selected = array();
        foreach( $query->result_array() as $row )
        {
            $selected[$row['perm_id']] = 1;
        }

        $sql = "SELECT * FROM perms ORDER BY parent_id, perm_order";
        $query = $this->db->query( $sql );
        foreach( $query->result_array() as $row )
        {
            $perms[$row['perm_id']] = $row;
        }

        $this->load->view( 'group_perms', array('perms' => $perms, 'selected' => $selected, 'group_id' => $group_id) );
    }
}

