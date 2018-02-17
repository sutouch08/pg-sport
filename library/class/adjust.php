<?php
class adjust
{
	public $adjust_id;
	public $adjust_no;
	public $adjust_reference;
	public $adjust_date;
	public $adjust_note;
	public $adjust_status;
	public $id_employee;
	public function __construct($id = '')
	{
		if( $id != '' )
		{
			$qs = dbQuery("SELECT * FROM tbl_adjust WHERE id_adjust = ".$id);
			if( dbNumRows( $qs ) == 1 )
			{
				$rs = dbFetchArray($qs);
				$this->adjust_id		= $id;
				$this->adjust_no		= $rs['adjust_no'];	
				$this->adjust_reference	= $rs['adjust_reference'];
				$this->adjust_date		= $rs['adjust_date'];
				$this->adjust_note		= $rs['adjust_note'];
				$this->adjust_status	= $rs['adjust_status'];
				$this->id_employee	= $rs['id_employee'];
			}
		}
	}
	
	public function add(array $ds)
	{
		$sc 		= FALSE;
		$fields	= '';
		$values	= '';
		$n			= count($ds);	
		$i			= 1;
		foreach( $ds as $field => $val )
		{
			$fields .= $field;
			$values .= "'".$val."'";
			if( $i < $n )
			{
				$fields .= ', ';
				$values .= ', ';
			}
			$i++;			
		}
		
		$qs = dbQuery("INSERT INTO tbl_adjust (".$fields.") VALUES (".$values.")");
		if( $qs )
		{
			$sc = dbInsertId();
		}
		return $sc;
	}
	
	public function update($id, array $ds)
	{
		$set = '';
		$n = count($ds);
		$i = 1;
		foreach( $ds as $key => $val )
		{
			$set .= $key." = '".$val."'";
			if( $i < $n ){ $set .= ", "; }
			$i++;	
		}
		return dbQuery("UPDATE tbl_adjust SET ".$set." WHERE id_adjust = ".$id);	
	}
	
	public function insertDetail( array $ds )
	{
		$sc 		= FALSE;
		$fields	= '';
		$values	= '';
		$n			= count($ds);	
		$i			= 1;
		foreach( $ds as $field => $val )
		{
			$fields .= $field;
			$values .= "'".$val."'";
			if( $i < $n )
			{
				$fields .= ', ';
				$values .= ', ';
			}
			$i++;			
		}
		
		$qs = dbQuery("INSERT INTO tbl_adjust_detail (".$fields.") VALUES (".$values.")");
		if( $qs )
		{
			$sc = dbInsertId();
		}
		return $sc;
	}
	
	public function updateDetail($id, $increase, $decrease)
	{
		return dbQuery("UPDATE tbl_adjust_detail SET adjust_qty_add = adjust_qty_add + ".$increase.", adjust_qty_minus = adjust_qty_minus + ".$decrease." WHERE id_adjust_detail = ".$id);	
	}
	
	public function deleteDetail($id)
	{
		return dbQuery("DELETE FROM tbl_adjust_detail WHERE id_adjust_detail = ".$id);	
	}	
	
	
	public function getNewReference($date = '')
	{
		$date 	= $date == '' ? date("Y-m-d") : $date;
		$Y			= date('y', strtotime($date));
		$M			= date('m', strtotime($date));
		$prefix	= getConfig('PREFIX_ADJUST');
		$preRef	= $prefix . '-' . $Y . $M;
		$qs		= dbQuery("SELECT MAX(adjust_no) AS max FROM tbl_adjust WHERE adjust_no LIKE '".$preRef."%' ORDER BY adjust_no DESC");
		list( $ref ) = dbFetchArray($qs);
		if( ! is_null($ref) )
		{
			$runNo = mb_substr($ref, -4, NULL, 'UTF-8')+1;
			$adjNo = $prefix . '-' . $Y . $M . sprintf('%04d', $runNo);
		}
		else
		{
			$adjNo = $prefix . '-' . $Y . $M	. '0001';
		}
		
		return $adjNo;
	}
	
	public function isExistsDetail($id, $id_pa, $id_zone)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT id_adjust_detail FROM tbl_adjust_detail WHERE id_adjust = ".$id." AND id_product_attribute = ".$id_pa." AND id_zone = ".$id_zone);
		if( dbNumRows($qs) > 0 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}
	
	public function getAdjustDetail($id)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT * FROM tbl_adjust_detail WHERE id_adjust_detail = ".$id);
		if( dbNumRows($qs) == 1 )
		{
			$sc = dbFetchArray($qs);	
		}
		return $sc;
	}
	
	//------ get all adjust_detail  row in id_adjust
	public function getAllDetail($id)
	{
		return dbQuery("SELECT * FROM tbl_adjust_detail WHERE id_adjust = ".$id);
	}
	
	public function setStatus($id_adj, $status)
	{
		return dbQuery("UPDATE tbl_adjust SET adjust_status = ".$status." WHERE id_adjust = ".$id_adj);	
	}
	
	public function setDetailStatus($id, $status)
	{
		return dbQuery("UPDATE tbl_adjust_detail SET status_up = ".$status." WHERE id_adjust_detail = ".$id);	
	}
	
	public function setDiff($id_diff, $status)
	{
		return dbQuery("UPDATE tbl_diff SET status_diff = ".$status." WHERE id_diff = ".$id_diff);	
	}
	
}// end class


?>