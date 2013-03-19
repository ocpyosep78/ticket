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
 * Description of comments
 *
 * @author ferdhie
 */
class Comments extends MY_Controller {
    
    public function handle_attachments($client_id, $var_name, $id, $attachment) {
        $upload_tmp_dir = str_replace('\\', '/', FCPATH) . '/files/plupload';
        $upload_dir = str_replace('\\', '/', FCPATH) . '/files/attachments';
        if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777);
        $upload_dir .= "/{$client_id}";
        if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777);
        $upload_dir .= "/{$this->orca_auth->user->id}";
        if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777);
        if ($attachment)
        {
            $this->db->query("DELETE FROM files WHERE $var_name = {$id}");
            foreach($attachment as $att)
            {
                if ( is_file("$upload_tmp_dir/$att") )
                {
                    rename("$upload_tmp_dir/$att", "$upload_dir/$att");
                    $this->db->query("INSERT INTO files ( $var_name, client_id, filename, user_id  )
                                    VALUES ( ?, ?, ?, ? )",
                            array( $id, $client_id, "{$client_id}/{$this->orca_auth->user->id}/$att", $this->orca_auth->user->id ));
                } 
                else 
                {
                    $this->db->query("INSERT INTO files ( $var_name, client_id, filename, user_id  )
                                    VALUES ( ?, ?, ?, ? )",
                            array( $id, $client_id, "$att", $this->orca_auth->user->id ));
                }
            }
        }
    }

    public function add() {
        $strClient = $this->orca_auth->user->client_id ? " AND client_id = {$this->orca_auth->user->client_id}" : "";
        if (!isset($_SESSION)) session_start();
        if (is_post_request())
        {
            $comment_text = empty($_POST['comment_text']) ? '' : trim($_POST['comment_text']);
            if ($comment_text)
            {
                $check = strtolower($comment_text);
                if ( isset($_SESSION['comment']) && $_SESSION['comment'] == $check )
                {
                    flashmsg_set("You have entered the comment, please wait for change");
                    return;
                }
                
                $this->ticket_id = $ticket_id = intval($this->input->get_post('ticket_id'));
                $ticket = false;
                if ($ticket_id)
                {
                    $this->ticket = $ticket = $this->db->query("SELECT * FROM tickets WHERE id = ? $strClient",array($ticket_id))->row();
                    if (!$ticket) show_404 ();
                    $this->db->query("INSERT INTO comments ( comment_text, user_id, ticket_id, client_id ) VALUES (?,?,?,?)",
                            array( $comment_text, $this->orca_auth->user->id, $ticket->id, $ticket->client_id ));
                    flashmsg_set( "Comment has been added" );
                    
                    $attachment = isset($_POST['attachment']) ? $_POST['attachment'] : array();
                    $this->handle_attachments( $ticket->client_id, 'comment_id', $this->db->insert_id(), $attachment );
                    
                    do_action('after_comment_add',array( $this ));
                    
                    redirect(site_url('tickets/detail?id='.$ticket->id.'&reload='.rand()));
                    return;
                }
                

                $task = false;
                $this->task_id = $task_id = intval($this->input->get_post('task_id'));
                if ($task_id)
                {
                    $this->task = $task = $this->db->query("SELECT * FROM tasks WHERE id = ? $strClient",array($task_id))->row();
                    if (!$task) show_404 ();
                    
                    $this->db->query("INSERT INTO comments ( comment_text, user_id, task_id, client_id ) VALUES (?,?,?,?)",
                            array( $comment_text, $this->orca_auth->user->id, $task->id, $task->client_id ));
                    flashmsg_set( "Comment has been added" );
                    
                    $attachment = isset($_POST['attachment']) ? $_POST['attachment'] : array();
                    $this->handle_attachments( $task->client_id, 'comment_id', $this->db->insert_id(), $attachment );
                    
                    do_action('after_comment_add', array( $this ));
                    
                    redirect(site_url('tasks/detail?id='.$task->id.'&reload='.rand()));
                    return;
                }
            }
        }
        
        show_404();
    }
    
}

