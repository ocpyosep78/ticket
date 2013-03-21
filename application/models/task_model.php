<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Task_model extends CI_Model {
    function __construct() {
        parent::__construct();
       
        $this->Field = array('id', 'project_id', 'timeline_id', 'parent_id', 'task', 'description', 'status', 'client_id', 'user_id', 'due', 'complete_date');
    }
	
    function Update($Param) {
        $Result = array();
       
        if (empty($Param['id'])) {
            $InsertQuery  = GenerateInsertQuery($this->Field, $Param, TASK);
            $InsertResult = mysql_query($InsertQuery) or die(mysql_error());
           
            $Result['id'] = mysql_insert_id();
            $Result['QueryStatus'] = '1';
            $Result['Message'] = 'Data successfully stored.';
        } else {
            $UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, TASK);
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
            $SelectQuery  = "SELECT * FROM ".TASK." WHERE id = '".$Param['id']."' LIMIT 1";
        }
       
        $SelectResult = mysql_query($SelectQuery) or die(mysql_error());
        if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
            $Array = $this->Sync($Row);
        }
       
        return $Array;
    }
	
    function GetArray($Param = array()) {
        $Array = array();
        $StringSearch = (isset($Param['NameLike'])) ? "AND task LIKE '" . $Param['NameLike'] . "%'"  : '';
        $StringFilter = GetStringFilter($Param);
       
        $PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
        $PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		$StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'task ASC';
       
        $SelectQuery = "
            SELECT Task.*, User.name user_name
            FROM ".TASK." Task
			LEFT JOIN ".USER." User ON User.id = Task.user_id
            WHERE 1 $StringSearch $StringFilter
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

    function GetCount($Param = array()) {
        $TotalRecord = 0;
       
        $StringSearch = (isset($Param['NameLike'])) ? "AND task LIKE '" . $Param['NameLike'] . "%'"  : '';
        $StringFilter = GetStringFilter($Param);
       
        $SelectQuery = "
            SELECT COUNT(*) AS TotalRecord
            FROM ".TASK." Task
            WHERE 1 $StringSearch $StringFilter
        ";
        $SelectResult = mysql_query($SelectQuery) or die(mysql_error());
        while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
            $TotalRecord = $Row['TotalRecord'];
        }
       
        return $TotalRecord;
    }
	
	function Delete($Param) {
		$DeleteQuery  = "DELETE FROM ".TASK." WHERE id = '".$Param['id']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());

		$Result['success'] = true;
		$Result['Message'] = 'Data has been deleted.';

        return $Result;
    }
	
	function Sync($Row) {
		$Result = StripArray($Row);
		
		return $Result;
	}
	
	function GetArrayCalender($Param) {
		$ArrayParam = array(
			'filter' => '[{"type":"numeric","comparison":"eq","value":"'.$Param['project_id'].'","field":"Task.project_id"}]',
			'sort' => '[{"property":"Task.id","direction":"DESC"}]',
			'limit' => 100
		);
		$ArrayResult = $this->GetArray($ArrayParam);
		
		$ArrayCalender = array();
		foreach ($ArrayResult as $record) {
			$ArrayDate = ($record['status'] == 1) ? ConvertDateToArray($record['complete_date']) : ConvertDateToArray($record['due']);
			$color = ($record['status'] == 1) ? '#3A87AD' : '#006DCC';
			
			$ArrayCalender[] = array(
				'title' => $record['task'],
				'start_text' => 'new Date('.$ArrayDate['Year'].', '.($ArrayDate['Month'] - 1).', '.$ArrayDate['Day'].')',
				'color' => $color,
				'textColor' => '#FFFFFF',
				'desc' => json_encode($record)
			);
		}
		
		return $ArrayCalender;
	}
}