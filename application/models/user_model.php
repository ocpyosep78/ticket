<?php

class User_model extends CI_Model
{
	static $companies = array();
	private $table_name = 'users';			// user accounts
        var $perms;
        var $perm_tables;
        var $allperms;

	public function __construct()
	{
            parent::__construct();
	}
	
	public function get_by_userpass( $username, $password=NULL )
	{
            $sql = "SELECT u.*,ug.group_id FROM {$this->table_name} AS u LEFT JOIN user_groups AS ug ON u.id = ug.user_id WHERE username = ?";
            $params = array($username);
            if ( !is_null($password) )
            {
                $sql .= " AND password = ?";
                $params[] = $password;
            }
            $query = $this->db->query($sql, $params);
            return $query->num_rows() > 0 ? $query->row() : null;
	}
	
	public function get_company( $client_id )
	{
		if (!isset(self::$companies[$client_id]))
		{
			$r = $this->db->get_where('clients', array('client_id' => $client_id), 1);
			if ($r->num_rows() > 0)
			{
				self::$companies[$client_id] = $r->row();
			}
		}
		return isset(self::$companies[$client_id])?self::$companies[$client_id]:0;
	}
	
	public function check_perms($user_id, $uri)
	{
		$sql = "(SELECT t3.* FROM user_groups t1
                        LEFT JOIN group_perms t2 ON t1.group_id = t2.group_id
                        LEFT JOIN perms t3 ON t2.perm_id = t3.perm_id WHERE t1.user_id = ? AND t3.perm_path = ?
                        ORDER BY t3.parent_id, t3.perm_order, t3.perm_id)
                        UNION
                        (SELECT t5.* FROM user_perms t4
                        LEFT JOIN perms t5 ON t4.perm_id = t5.perm_id
                        WHERE t4.user_id = ? AND t5.perm_path = ?
                        ORDER BY t5.parent_id, t5.perm_order, t5.perm_id)";
		$query = $this->db->query($sql, array($user_id, $uri, $user_id, $uri));
		$this->perms = array();
		if ( $query->num_rows() > 0 )
		{
			$this->perms = $query->result();
		}
		return $this->perms;
	}
        
        public function has_perms( $user_id, $path=NULL, $parent=NULL )
        {
            $this->my_perms($user_id);
            if (is_null($path) && is_null($parent))
                return false;
            
            foreach($this->perm_tables[$user_id] as $perm) {
                if (!is_null($path)) {
                    if ( $perm->perm_path == $path )
                        return true;
                }
                if (!is_null($parent)) {
                    if ( !empty($this->perm_tables[$user_id][$parent]) )
                        return true;
                }
            }
            
            return false;
        }
	
	public function my_perms($user_id)
	{
            if ( !isset($this->perms[$user_id]) )
            {
                $sql = "(SELECT t3.* FROM user_groups t1
                            LEFT JOIN group_perms t2 ON t1.group_id = t2.group_id
                            LEFT JOIN perms t3 ON t2.perm_id = t3.perm_id WHERE t1.user_id = ?
                            ORDER BY t3.parent_id, t3.perm_order, t3.perm_id)
                        UNION
                        (SELECT t5.* FROM user_perms t4
                            LEFT JOIN perms t5 ON t4.perm_id = t5.perm_id
                            WHERE t4.user_id = ?
                            ORDER BY t5.parent_id, t5.perm_order, t5.perm_id)";
                $query = $this->db->query($sql, array($user_id, $user_id));
                $this->perms[$user_id] = array();
                $this->perm_tables[$user_id] =array();
                if ( $query->num_rows() > 0 )
                {
                        $this->perms[$user_id] = $query->result();
                        foreach($this->perms[$user_id] as $perm) {
                            $this->perm_tables[$user_id][$perm->perm_id] = $perm;
                        }
                }
            }
            return $this->perms[$user_id];
	}
}
