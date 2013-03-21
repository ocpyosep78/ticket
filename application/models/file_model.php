<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class File_model extends CI_Model {
    function __construct() {
        parent::__construct();
       
        $this->Field = array('id', 'ticket_id', 'project_id', 'client_id', 'filename', 'user_id', 'comment_id', 'task_id');
    }
	
    function Update($Param) {
        $Result = array();
       
        if (empty($Param['id'])) {
            $InsertQuery  = GenerateInsertQuery($this->Field, $Param, FILE);
            $InsertResult = mysql_query($InsertQuery) or die(mysql_error());
           
            $Result['id'] = mysql_insert_id();
            $Result['QueryStatus'] = '1';
            $Result['Message'] = 'Data successfully stored.';
        } else {
            $UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, FILE);
            $UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
           
            $Result['id'] = $Param['id'];
            $Result['QueryStatus'] = '1';
            $Result['Message'] = 'Data successfully updated.';
        }
       
        return $Result;
    }	
	
    function GetArray($Param = array()) {
        $Array = array();
        $StringFilter = GetStringFilter($Param);
       
        $PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
        $PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		$StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'filename ASC';
       
        $SelectQuery = "
            SELECT File.*
            FROM ".FILE." File
            WHERE 1 $StringFilter
            ORDER BY $StringSorting
            LIMIT $PageOffset, $PageLimit
        ";
        $SelectResult = mysql_query($SelectQuery) or die(mysql_error());
        while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
            $Row = $this->Sync($Row);
            $Array[] = $Row;
        }
		
        return $Array;
    }
	
	function Sync($Row) {
		$Record = StripArray($Row);
		$Record['file_link'] = base_url('files/attachments/'.$Row['filename']);
		return $Record;
	}
	
	function Add($Param) {
        $upload_dir = $this->config->item('base_path') . '/files/attachments';
        $upload_tmp_dir = $this->config->item('base_path') . '/files/plupload';
		$file_attach = $this->orca_auth->user->client_id.'/'.$this->orca_auth->user->id.'/'.$Param['file'];
		$file_new = $upload_dir.'/'.$file_attach;
		$file_old = $upload_tmp_dir.'/'.$Param['file'];
		@mkdir($upload_dir.'/'.$this->orca_auth->user->client_id, 0777);
		@mkdir($upload_dir.'/'.$this->orca_auth->user->client_id.'/'.$this->orca_auth->user->id, 0777);
		@rename($file_old, $file_new);
		
		$ParamUpdate = $Param;
		$ParamUpdate['filename'] = $file_attach;
		$ParamUpdate['user_id'] = $this->orca_auth->user->id;
		$ParamUpdate['client_id'] = $this->orca_auth->user->client_id;
		$this->Update($ParamUpdate);
	}
}