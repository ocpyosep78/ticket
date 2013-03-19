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
 * Description of clients
 *
 * @author ferdhie
 */
class Clients extends  MY_Controller
{
    function index()
    {
        $this->load->view('clients');
    }
    
    function update()
    {
        if ( empty($_POST['delete']) )
        {
            $names = empty($_POST['name'])?array():$_POST['name'];
            $descriptions = empty($_POST['description'])?array():$_POST['description'];
            if ( !empty($names['new']) )
            {
                $name = $names['new'];
                $description = empty($descriptions['new']) ? '' : $descriptions['new'];
                $this->db->query("INSERT INTO clients (name,description) VALUES (?,?)",array($name,$description));
                $id = $this->db->insert_id();
                flashmsg_set("Client has been added");
                do_action( 'after_save', array( $this->orca_auth->user, 'clients', $id, "Add client $id with name $name and description $description" ) );
            }

            $ids = empty($_POST['id'])?array():$_POST['id'];
            foreach($ids as $id)
            {
                $name = empty($names[$id]) ? '' : $names[$id];
                $description = empty($descriptions[$id]) ? '' : $descriptions[$id];
                $this->db->query("UPDATE clients SET name = ?, description = ? WHERE client_id = ?",array($name,$description,$id));
                flashmsg_set("Client has been updated");
                do_action( 'after_save', array( $this->orca_auth->user, 'clients', $id, "Update client $id with name $name and description $description" ) );
            }
        }
        else
        {
            $selected = empty($_POST['selected'])?array():$_POST['selected'];
            if ($selected)
            {
                $selected = array_map('intval',array_filter($selected));
                $this->db->query("DELETE FROM clients WHERE client_id IN (".implode(",",$selected).")");
                flashmsg_set("Client has been deleted");
                do_action( 'after_save', array( $this->orca_auth->user, 'clients', '0', "Delete client with ID (".implode(",",$selected).")" ) );
            }
        }
        
        redirect( site_url('/clients?update=true') );
    }
}

