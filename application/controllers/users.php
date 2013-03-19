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
 * Description of users
 *
 * @author ferdhie
 */
class Users extends MY_Controller 
{
    function index()
    {
        $this->load->view('users');
    }

    function all()
    {
        $aColumns = array('users.id', 'username', 'email', 'users.name',
            'clients.name AS client_name', 'users.client_id', 
            'GROUP_CONCAT(groups.group_name) AS group_names', 
            );

        //LIMIT
        $sLimit = "";
        if ( isset( $_REQUEST['iDisplayStart'] ) && $_REQUEST['iDisplayLength'] != '-1' )
        {
            $start = intval($_REQUEST['iDisplayStart']);
            $length = empty($_REQUEST['iDisplayLength'])?10:$_REQUEST['iDisplayLength'];
            if (!$length) $length = 10;
            $sLimit = "LIMIT $start,$length";
        }

        //ORDER BY
        $sOrder = array();
        if ( isset( $_REQUEST['iSortCol_0'] ) && isset($_REQUEST['iSortingCols']) )
        {
            $length = intval($_REQUEST['iSortingCols']);
            for ( $i=0 ; $i<$length ; $i++ )
            {
                $colindex = isset($_REQUEST['iSortCol_'.$i])?intval($_REQUEST['iSortCol_'.$i]):FALSE;
                $sortdir = empty($_REQUEST['sSortDir_'.$i]) ? 'ASC' : strtoupper($_REQUEST['sSortDir_'.$i]);
                if ( $sortdir != 'ASC' || $sortdir != 'DESC' ) $sortdir = 'ASC';

                if ( $colindex  !== false && !empty($_REQUEST[ "bSortable_$colindex" ]) && $_REQUEST[ "bSortable_$colindex" ] == "true" )
                {
                    $colname = $colindex + 1;
                    $sOrder[] = "$colname $sortdir";
                }
            }
        }
        $sOrder = empty($sOrder) ? "" : "ORDER BY ".implode(",", $sOrder);

        $sWhere1 = array();
        if ( !empty($_REQUEST['sSearch']) )
        {
            $search = mysql_real_escape_string( $_REQUEST['sSearch'] );
            for ( $i=0 ; $i<count($aColumns)-2 ; $i++ )
            {
                $colname = get_column_name($aColumns[$i]);
                $sWhere1[] = "$colname LIKE '%$search%'";
            }
        }

        $sWhere2 = array();
        if ( $this->orca_auth->user->client_id )
            $sWhere2[] = "users.client_id = {$this->orca_auth->user->client_id}";

        for ( $i=0 ; $i<count($aColumns)-2 ; $i++ )
        {
            if ( isset($_REQUEST['bSearchable_'.$i]) && $_REQUEST['bSearchable_'.$i] == "true" && isset($_REQUEST['sSearch_'.$i]) && $_REQUEST['sSearch_'.$i] != '' )
            {
                $colname= get_column_name($aColumns[$i]);
                $sWhere2[] = "$colname LIKE '%".mysql_real_escape_string($_REQUEST['sSearch_'.$i])."%' ";
            }
        }

        $sWhere1 = empty( $sWhere1 ) ? "" : " WHERE (".implode(" OR ", $sWhere1).")";
        $sWhere = $sWhere1 . (empty( $sWhere2 ) ? "" : ( empty($sWhere1) ? "WHERE " : " AND " ) . implode( " AND ", $sWhere2 ));

        $sql = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
                FROM users
                LEFT JOIN clients ON users.client_id = clients.client_id
                LEFT JOIN user_groups ON users.id = user_groups.user_id
                LEFT JOIN groups ON user_groups.group_id = groups.group_id
                $sWhere
                GROUP BY users.id
                $sOrder
                $sLimit";
        //header("Debug-SQL: $sql");
        $query1 = $this->db->query($sql);
        $query2 = $this->db->query("SELECT FOUND_ROWS() AS cnt");
        $totalrow = $query2->row()->cnt;

        $sEcho = empty($_GET['sEcho'])?0:intval($_GET['sEcho']);
        $output = array(
                "sEcho" => $sEcho,
                "iTotalRecords" => $totalrow,
                "iTotalDisplayRecords" => 0,
                "aaData" => $query1->result()
        );

        $output["iTotalDisplayRecords"] = count($output['aaData']);
        
        echo json_encode( $output );
    }

    function save()
    {
        $error = false;
        $success = false;
        do
        {
            $data = $_POST;
            if ( $this->orca_auth->user->client_id )
                $data['client_id'] = $this->orca_auth->user->client_id;

            if (!$this->form_validation->alpha_dash($data['username']))
            {
                $error = 'Username hanya boleh karakter dan angka';
                break;
            }

            if (!$data['id'])
            {
                if (!$data['password'] || !$data['email'])
                {
                    $error = 'Password dan email tidak boleh kosong';
                    break;
                }

                if (!$this->form_validation->valid_email($data['email']))
                {
                    $error = 'Format email kurang valid';
                    break;
                }
            }
            else
            {
                if (!$data['password']) unset($data['password']);
                if (!$data['email']) unset($data['email']);
                else if (!$this->form_validation->valid_email($data['email']))
                {
                    $error = 'Format email kurang valid';
                    break;
                }
                if (!$data['name']) unset($data['name']);
            }

            $where = '';
            if ($data['id'])
            {
                $where .= "AND id <> ".$data['id'];
            }

            $row = $this->db->query("SELECT COUNT(*) AS cnt FROM users WHERE (username = ? OR email = ?) $where",array($data['username'], $data['email']))->row();
            if ($row->cnt > 0)
            {
                $error = 'Username atau email sudah terpakai';
                break;
            }

            $group_id = empty($data['group_id']) ? 0 : $data['group_id'];
            unset($data['group_id']);
            
            $plain_password = '';
            $is_add = false;
            
            if ($data['password'])
            {
                $plain_password = $data['password'];
                $data['password'] = $this->orca_auth->make_hash($data['password'], '', true);
            }
            
            do_action( 'before_user_save', array( $this ));

            if ($data['id'])
            {
                $this->db->where('id', $data['id'])->update('users', $data);
            }
            else
            {
                $this->db->insert('users', $data);
                $data['id'] = $this->db->insert_id();
                $is_add = true;
            }

            if ($group_id)
            {
                $this->db->query("DELETE FROM user_groups WHERE user_id = ?", array( $data['id'] ));
                $this->db->query("INSERT INTO user_groups (user_id, group_id) VALUES (?,?)", array( $data['id'], $group_id ));
            }
            
            do_action( 'after_user_save', array( $this ));
            do_action( 'after_save', array( $this->orca_auth->user, 'users', $data['id'], "Edit user $data[id] $data[username]" ) );

            $success = true;
        }
        while(0);

        echo json_encode(array('success' => $success, 'msg' => $error));
    }

    function groups()
    {
        $user_id = $this->input->get_post('id');
        if ( !$user_id ) show_404();

        if ( is_post_request() )
        {
            $selected = p('selected');
            if ($selected && is_array($selected))
            {
                $selected = array_filter(array_map('intval', $selected));
                if ( $selected )
                {
                    $sql = "DELETE FROM user_groups WHERE user_id = ?";
                    $this->db->query($sql, array($user_id));

                    $sql = "INSERT INTO user_groups (user_id, group_id) VALUES (?,?)";
                    foreach( $selected as $sel )
                    {
                        $this->db->query($sql, array($user_id, $sel));
                    }
                    
                    do_action( 'after_save', array( $this->orca_auth->user, 'users', $user_id, "Edit user $data[id] with group $sel" ) );
                    flashmsg_set('Success, user groups updated');
                }
            }
        }

        $rows = $this->db->query( "SELECT group_id FROM user_groups WHERE user_id = ?", array($user_id) )->result_array();
        $selected = array();
        foreach( $rows as $row )
        {
            $selected[$row['group_id']] = 1;
        }

        $sql = "SELECT * FROM groups";
        if ( $this->orca_auth->user->client_id )
            $sql .= " WHERE admin_group = 0";
        $groups = $this->db->query( $sql )->result_array();

        echo json_encode(array('success' => true, 'groups' => $groups, 'selected' => $selected, 'msg' => '', 'user_id' => $user_id));
    }
}

