<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Project_model extends CI_Model {
    function __construct() {
        parent::__construct();
       
        $this->Field = array('id', 'title', 'description', 'created_on', 'user_id', 'client_id');
    }
	
    function Update($Param) {
        $Result = array();
       
        if (empty($Param['id'])) {
            $InsertQuery  = GenerateInsertQuery($this->Field, $Param, PROJECT);
            $InsertResult = mysql_query($InsertQuery) or die(mysql_error());
           
            $Result['id'] = mysql_insert_id();
            $Result['QueryStatus'] = '1';
            $Result['Message'] = 'Data successfully stored.';
        } else {
            $UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, PROJECT);
            $UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
           
            $Result['id'] = $Param['id'];
            $Result['QueryStatus'] = '1';
            $Result['Message'] = 'Data successfully updated.';
        }
       
        return $Result;
    }

    function GetByID($Param) {
        $Array = array();
       
        if (isset($Param['id'])) {
            $SelectQuery  = "SELECT * FROM ".PROJECT." WHERE id = '".$Param['id']."' LIMIT 1";
        }
       
        $SelectResult = mysql_query($SelectQuery) or die(mysql_error());
        if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
            $Array = $this->Sync($Row);
        }
       
        return $Array;
    }
	
    function GetArray($Param = array()) {
        $Array = array();
        $StringSearch = (isset($Param['NameLike'])) ? "AND title LIKE '" . $Param['NameLike'] . "%'"  : '';
        $StringFilter = GetStringFilter($Param);
       
        $PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
        $PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		$StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'title ASC';
       
        $SelectQuery = "
            SELECT Project.*
            FROM ".PROJECT." Project
            WHERE 1 $StringSearch $StringFilter
            ORDER BY $StringSorting
            LIMIT $PageOffset, $PageLimit
        ";
        $SelectResult = mysql_query($SelectQuery) or die(mysql_error());
        while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
            $Array[] = $this->Sync($Row);
        }
		
        return $Array;
    }
	
    function GetCount($Param = array()) {
        $TotalRecord = 0;
       
        $StringSearch = (isset($Param['NameLike'])) ? "AND title LIKE '" . $Param['NameLike'] . "%'"  : '';
        $StringFilter = GetStringFilter($Param);
       
        $SelectQuery = "
            SELECT COUNT(*) AS TotalRecord
            FROM ".PROJECT." Project
            WHERE 1 $StringSearch $StringFilter
        ";
        $SelectResult = mysql_query($SelectQuery) or die(mysql_error());
        while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
            $TotalRecord = $Row['TotalRecord'];
        }
       
        return $TotalRecord;
    }
	
	function Delete($Param) {
		$DeleteQuery  = "DELETE FROM ".PROJECT." WHERE id = '".$Param['id']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());

		$Result['success'] = true;
		$Result['Message'] = 'Data has been deleted.';

        return $Result;
    }
	
	function Sync($Record) {
		$Result = StripArray($Record);
		
		return $Result;
	}
}