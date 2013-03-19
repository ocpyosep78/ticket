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
 * Description of dashboard
 *
 * @author ferdhie
 */
class Dashboard extends MY_Controller {
    function index() {
        $this->load->view('dashboard');
    }
    
    function broadcast() {
        if (is_post_request() )
        {
            $usernames = empty($_POST['usernames'])?array():array_filter(array_map('trim', explode(',',$_POST['usernames'])));
            $subject = empty($_POST['subject'])?'':trim($_POST['subject']);
            $message = empty($_POST['message'])?'':trim($_POST['message']);
            
            if (!$usernames || !$subject ||  !$message)
            {
                flashmsg_set("Everything is required, please fill the form");
            }
            else
            {
                $strClient = $this->orca_auth->user->client_id?" AND client_id = {$this->orca_auth->user->client_id}" : "";
                $sql = "SELECT * FROM users WHERE username IN ('".implode("','", $usernames)."') $strClient";
                $users = $this->db->query($sql)->result();
                $cch = array();
                foreach($users as $user)
                    $cch[] = "$user->username <$user->email>";
                @mail( $this->orca_auth->user->email, $subject, $message, "Cc: ".implode(", ", $cch) . "\r\n" );
                flashmsg_set("Message sent to ".implode(", ", $cch));
                redirect( site_url('dashboard/?sent='.rand()) );
            }
        }
        
        $this->load->view('broadcast');
    }
    
    function getperms() {
        $this->Users->my_perms( $this->orca_auth->user->id );
        echo json_encode( $this->Users->perm_tables[ $this->orca_auth->user->id ] );
    }
}

