<?php
/********************************************************************************* 
 *  This file is part of Sentrifugo.
 *  Copyright (C) 2014 Sapplica
 *   
 *  Sentrifugo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Sentrifugo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with Sentrifugo.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  Sentrifugo Support <support@sentrifugo.com>
 ********************************************************************************/

class Default_Model_Leavemanagement extends Zend_Db_Table_Abstract
{
    protected $_name = 'main_leavemanagement';
    protected $_primary = 'id';
	
	public function getLeaveManagementData($sort, $by, $pageNo, $perPage,$searchQuery)
	{
	    /* Removing isactive checking from configuration table */
		
		//$where = "l.isactive = 1 AND d.isactive=1 AND w.isactive=1 AND wk.isactive=1 AND m.isactive=1 AND b.isactive=1";
		$where = "l.isactive = 1 AND d.isactive=1 AND b.isactive=1";
		
		/*if($columnkey != '' && $columntext != '')
			$where = " ".$columnkey." like '%".$columntext."%' "; */
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$leaveManagementData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leavemanagement'),
						          array( 'l.*',
										 //'satholiday'=>'if(l.is_satholiday = 1,"yes","No")',
										 'is_halfday'=>'if(l.is_halfday = 1,"Yes","No")',
										 'is_leavetransfer'=>'if(l.is_leavetransfer = 1,"Yes","No")',
										 'is_skipholidays'=>'if(l.is_skipholidays = 1,"Yes","No")',
								 ))
						   ->joinLeft(array('w'=>'tbl_weeks'), 'w.week_id=l.weekend_startday',array('w.week_name'=>'w.week_name'))	
                           ->joinLeft(array('wk'=>'tbl_weeks'), 'wk.week_id=l.weekend_endday',array('wk.week_name'=>'wk.week_name'))						   
                           //->joinLeft(array('m'=>'main_monthslist'), 'm.id=l.cal_startmonth',array('m.month_name')) 
						   ->joinLeft(array('m'=>'tbl_months'), 'm.monthid=l.cal_startmonth',array('m.month_name'=>'m.month_name'))
                           ->joinLeft(array('d'=>'main_departments'), 'd.id=l.department_id',array('d.deptname'))
                           ->joinLeft(array('b'=>'main_businessunits'), 'b.id=l.businessunit_id',array()) 		     						   						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		//echo $leaveManagementData->__toString(); die;
		return $leaveManagementData;       		
	}
	public function getLeaveManagementData_15112013($sort, $by, $pageNo, $perPage,$searchQuery)
	{
	    /* Removing isactive checking from configuration table */
		
		//$where = "l.isactive = 1 AND d.isactive=1 AND w.isactive=1 AND wk.isactive=1 AND m.isactive=1 AND b.isactive=1";
		$where = "l.isactive = 1 AND d.isactive=1 AND b.isactive=1";
		
		/*if($columnkey != '' && $columntext != '')
			$where = " ".$columnkey." like '%".$columntext."%' "; */
		if($searchQuery)
			$where .= " AND ".$searchQuery;
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$leaveManagementData = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leavemanagement'),
						          array( 'l.*',
										 //'satholiday'=>'if(l.is_satholiday = 1,"yes","No")',
										 'is_halfday'=>'if(l.is_halfday = 1,"Yes","No")',
										 'is_leavetransfer'=>'if(l.is_leavetransfer = 1,"Yes","No")',
										 'is_skipholidays'=>'if(l.is_skipholidays = 1,"Yes","No")',
								 ))
						   ->joinLeft(array('w'=>'tbl_weeks'), 'w.week_id=l.weekend_startday',array('weekend_startday'=>'w.week_name'))	
                           ->joinLeft(array('wk'=>'tbl_weeks'), 'wk.week_id=l.weekend_endday',array('weekend_endday'=>'wk.week_name'))						   
                           //->joinLeft(array('m'=>'main_monthslist'), 'm.id=l.cal_startmonth',array('m.month_name')) 
						   ->joinLeft(array('m'=>'tbl_months'), 'm.monthid=l.cal_startmonth',array('cal_startmonth'=>'m.month_name'))
                           ->joinLeft(array('d'=>'main_departments'), 'd.id=l.department_id',array('d.deptname'))
                           ->joinLeft(array('b'=>'main_businessunits'), 'b.id=l.businessunit_id',array()) 		     						   						   
						   ->where($where)
    					   ->order("$by $sort") 
    					   ->limitPage($pageNo, $perPage);
		//echo $leaveManagementData->__toString(); die;
		return $leaveManagementData;       		
	}
	
	
	public function getGrid($sort,$by,$perPage,$pageNo,$searchData,$call,$dashboardcall,$a='',$b='',$c='',$d='')
	{	
		$monthslistmodel = new Default_Model_Monthslist();
		$weekdaysmodel = new Default_Model_Weekdays(); 	
        $searchQuery = '';
        $searchArray = array();
        $data = array();
		
		if($searchData != '' && $searchData!='undefined')
			{
				$searchValues = json_decode($searchData);
				//echo"<pre>";print_r($searchValues);
				foreach($searchValues as $key => $val)
				{
				    if($key == 'description')
					 $searchQuery .= " l.".$key." like '%".$val."%' AND ";
					
                    else if($key == 'daystartname')	
                     $searchQuery .= " w.week_name like '%".$val."%' AND "; 
                    else if($key == 'dayendname')	
                     $searchQuery .= " wk.week_name like '%".$val."%' AND ";					 
					else 
					 $searchQuery .= " ".$key." like '%".$val."%' AND ";
					$searchArray[$key] = $val;
				}
				$searchQuery = rtrim($searchQuery," AND");					
			}
		
		if($by == "cal_startmonth")	
			$by = "m.month_name";
		else if($by == "weekend_startday")	
			$by = "w.week_name";
		else if($by == "weekend_endday")	
			$by = "wk.week_name";
		
			
		$objName = 'leavemanagement';
		/*$tableFields = array('action'=>'Action','cal_startmonth' => 'Start Month',
                                     'weekend_startday' => 'Week-end 1','weekend_endday' => 'Week-end 2',
                                     'deptname' => 'Department','hours_day' => 'Hours','is_halfday' => 'Halfday',
                                     'is_leavetransfer' => 'Leave transferable','is_skipholidays' => 'Skip Holidays',
                                     'description' => 'Description');*/
		$tableFields = array('action'=>'Action','m.month_name' => 'Start Month',
                                     'w.week_name' => 'Week-end 1','wk.week_name' => 'Week-end 2',
                                     'deptname' => 'Department','hours_day' => 'Hours','is_halfday' => 'Halfday',
                                     'is_leavetransfer' => 'Leave transferable','is_skipholidays' => 'Skip Holidays',
                                     'description' => 'Description');
		$tablecontent = $this->getLeaveManagementData($sort, $by, $pageNo, $perPage,$searchQuery);
		
        $monthslistdata = $monthslistmodel->getMonthlistData();
                $month_opt = array();
			//echo "<pre>";print_r($monthslistdata);exit;
                if(sizeof($monthslistdata) > 0)
                {
                    foreach ($monthslistdata as $monthslistres)
                    {                        
                        $month_opt[$monthslistres['month_id']] = $monthslistres['month_name'];
                    }
                }
                $bool_arr = array('' => 'All',1 => 'Yes',2 => 'No');
                $week_arr = array();
                $weekdaysdata = $weekdaysmodel->getWeeklistData();
			
                if(sizeof($weekdaysdata) > 0)
                {
                    foreach ($weekdaysdata as $weekdaysres)
                    {                        
                        $week_arr[$weekdaysres['day_id']] = $weekdaysres['day_name'];
                    }
                }		
		
		$dataTmp = array(
			'sort' => $sort,
			'by' => $by,
			'pageNo' => $pageNo,
			'perPage' => $perPage,				
			'tablecontent' => $tablecontent,
			'objectname' => $objName,
			'extra' => array(),
			'tableheader' => $tableFields,
			'jsGridFnName' => 'getAjaxgridData',
			'jsFillFnName' => '',
			'searchArray' => $searchArray,
			'add' =>'add',
			'call'=>$call,
			'dashboardcall'=>$dashboardcall,
                        'search_filters' => array(
                            'cal_startmonth' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All')+$month_opt,
                            ),
                            'weekend_startday' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All')+$week_arr,
                            ),
                            'weekend_endday' => array(
                                'type' => 'select',
                                'filter_data' => array('' => 'All')+$week_arr,
                            ),
                            'is_leavetransfer' => array(
                                'type' => 'select',
                                'filter_data' => $bool_arr,
                            ),
                            'is_skipholidays' => array(
                                'type' => 'select',
                                'filter_data' => $bool_arr,
                            ),
                            'is_halfday' => array(
                                'type' => 'select',
                                'filter_data' => $bool_arr,
                            ),
                        ),
		    );		
		return $dataTmp;
	}
	
	
	public function getsingleLeaveManagementData($id)
	{
		$row = $this->fetchRow("id = '".$id."'");
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row->toArray();
	}
	
	public function SaveorUpdateLeaveManagementData($data, $where)
	{
	    if($where != ''){
			$this->update($data, $where);
			return 'update';
		} else {
			$this->insert($data);
			$id=$this->getAdapter()->lastInsertId('main_leavemanagement');
			return $id;
		}
	
	}
	
	public function getActiveRecord()
	{
	 	$select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leavemanagement'),
						          array( 'l.*',
										 'satholiday'=>'if(l.is_satholiday = 1,"yes","No")',
										 'halfday'=>'if(l.is_halfday = 1,"yes","No")',
										 'leavetransfer'=>'if(l.is_leavetransfer = 1,"yes","No")',
										 'skipholidays'=>'if(l.is_skipholidays = 1,"yes","No")',
								 ))
						   ->joinLeft(array('w'=>'main_weekdays'), 'w.id=l.week_startday',array('w.day_name'))						   						   
                           ->joinLeft(array('m'=>'main_monthslist'), 'm.id=l.cal_startmonth',array('m.month_name')) 		   
						   ->where('l.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray();   
	
	}
	
	public function getsatholidaycount()
	{
	   $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leavemanagement'),array('satholiday'=>'if(l.is_satholiday = 1,"yes","no")'))
						   ->where('l.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray(); 
	
	}
	
	public function getActiveDepartmentIds()
	{
	  $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leavemanagement'),array('deptid'=>'l.department_id'))
						   ->where('l.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray(); 
	}
	
	public function getWeekendDetails($deptid)
	{
            if($deptid != '')
            {
                $select = $this->select()
                                ->setIntegrityCheck(false)	
                                ->from(array('l'=>'main_leavemanagement'),array('weekendstartday'=>'l.weekend_startday','weekendday'=>'l.weekend_endday','is_halfday','is_leavetransfer','is_skipholidays'))
                                ->where('l.department_id = '.$deptid.' AND l.isactive = 1');  		   					   				
                return $this->fetchAll($select)->toArray(); 		
            }
            else 
                return array();
	}
	
	public function getWeekendNamesDetails($deptid)
	{
            if(!empty($deptid))
            {
	   $select = $this->select()
    					   ->setIntegrityCheck(false)	
                           ->from(array('l'=>'main_leavemanagement'),array('weekendstartday'=>'l.weekend_startday','weekendday'=>'l.weekend_endday','is_halfday','is_leavetransfer','is_skipholidays'))
						   ->joinLeft(array('w'=>'tbl_weeks'), 'w.week_id=l.weekend_startday',array('daystartname'=>'w.week_name'))	
                           ->joinLeft(array('wk'=>'tbl_weeks'), 'wk.week_id=l.weekend_endday',array('dayendname'=>'wk.week_name'))
						   ->where('l.department_id = '.$deptid.' AND l.isactive = 1');  		   					   				
		return $this->fetchAll($select)->toArray(); 
            }
            else 
                return array();
		
	}
	
	public function getActiveleavemanagementId($id)
	{
	  $select = $this->select()
						->setIntegrityCheck(false)
						->from(array('l'=>'main_leavemanagement'),array('l.*'))
					    ->where('l.id = '.$id.' AND l.isactive = 1');
	//echo $select;exit;					
		return $this->fetchAll($select)->toArray();
	
	}
}