<?php
class transfer
{
	public $id_tranfer;
	public $reference;
	public $warehouse_from;
	public $warehouse_to;
	public $id_employee;
	public $date_add;
	public $date_upd;
	public $comment;
	public $error;
	
	public function __construct($id = "" )
	{
		if( $id != "" )
		{
			$qs = dbQuery("SELECT * FROM tbl_tranfer WHERE id_tranfer = ".$id);
			if( dbNumRows($qs) == 1 )
			{
				$rs = dbFetchObject($qs);
				$this->id_tranfer	= $rs->id_tranfer;
				$this->reference	= $rs->reference;
				$this->warehouse_from	= $rs->warehouse_from;
				$this->warehouse_to		= $rs->warehouse_to;
				$this->id_employee	= $rs->id_employee;
				$this->date_add	= $rs->date_add;
				$this->date_upd	= $rs->date_upd;
				$this->comment 	= $rs->comment;
			}
		}
	}
	
	
	public function add(array $ds)
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			$fields	= "";
			$values	= "";
			$i			= 1;
			foreach( $ds as $field => $value )
			{
				$fields 	.= $i == 1 ? $field : ", ".$field;
				$values	.= $i == 1 ? "'".$value."'" : ", '".$value."'";
				$i++;	
			}
			$rs = dbQuery("INSERT INTO tbl_tranfer (".$fields.") VALUES (".$values.")");
			if( $rs === TRUE )
			{
				$sc = dbInsertId();
			}
		}
		
		return $sc;
	}
	
	
	public function update($id, array $ds)
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			$set 	= "";
			$i 		= 1;
			foreach( $ds as $field => $value )
			{
				$set .= $i == 1 ? $field ." = '".$value."'" : ", " . $field ." = '" .$value."'";
				$i++;	
			}
			$sc = dbQuery("UPDATE tbl_tranfer SET ".$set." WHERE id_tranfer = ".$id);
			if( $sc === FALSE )
			{
				$this->error = 'บันทึกรายการไม่สำเร็จ กรุณาลองใหม่อีกครั้ง';
			}
		}
		return $sc;
	}
	
	
	public function delete($id)
	{
		return dbQuery("DELETE FROM tbl_tranfer WHERE id_tranfer = ".$id);	
	}
	
	
	public function addDetail(array $ds)
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			$fields	= "";
			$values	= "";
			$i			= 1;
			foreach( $ds as $field => $value )
			{
				$fields 	.= $i == 1 ? $field : ", ".$field;
				$values	.= $i == 1 ? "'".$value."'" : ", '".$value."'";
				$i++;	
			}
			$rs = dbQuery("INSERT INTO tbl_tranfer_detail (".$fields.") VALUES (".$values.")");
			if( $rs === TRUE )
			{
				$sc = dbInsertId();
			}
		}
		
		return $sc;
	}
	
	
	
	public function updateDetail($id, array $ds)
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			$qs = dbQuery("UPDATE tbl_tranfer_detail SET tranfer_qty = tranfer_qty + ".$ds['tranfer_qty']." WHERE id_tranfer_detail = ".$id);
			if( $qs )
			{
				$sc = $id; //----- ถ้า update สำเร็จ ส่ง id กลับไปใช้
			}
		}
		return $sc;
	}
	
	public function deleteDetail($id_tranfer_detail)
	{
		return dbQuery("DELETE FROM tbl_tranfer_detail WHERE id_tranfer_detail = ".$id_tranfer_detail);	
	}
	
	public function isExistsDetail(array $ds)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT id_tranfer_detail FROM tbl_tranfer_detail WHERE id_tranfer = ".$ds['id_tranfer']." AND id_product_attribute = ".$ds['id_product_attribute']." AND id_zone_from = ".$ds['id_zone_from']." AND valid = 0");
		if( dbNumRows($qs) == 1 ) 
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}
	
	
	public function addTransferTemp(array $ds )
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			$fields	= "";
			$values	= "";
			$i			= 1;
			foreach( $ds as $field => $value )
			{
				$fields 	.= $i == 1 ? $field : ", ".$field;
				$values	.= $i == 1 ? "'".$value."'" : ", '".$value."'";
				$i++;	
			}
			$sc = dbQuery("INSERT INTO tbl_tranfer_temp (".$fields.") VALUES (".$values.")");
			
		}
		
		return $sc;
	}
	
	public function updateTransferTemp(array $ds )
	{
		$sc = FALSE;
		if( count( $ds ) > 0 )
		{
			$sc = dbQuery("UPDATE tbl_tranfer_temp SET qty = qty + ".$ds['qty']." WHERE id_tranfer_detail = ".$ds['id_tranfer_detail']);
		}
		return $sc;
	}
	
	
	
	public function deleteTransferTemp($id_tranfer_detail)
	{
		return dbQuery("DELETE FROM tbl_tranfer_temp WHERE id_tranfer_detail = ".$id_tranfer_detail);	
	}
	
	
	
	public function updateStock($id_zone, $id_pa, $qty)
	{
		$qs = dbQuery("SELECT id_stock FROM tbl_stock WHERE id_zone = ".$id_zone." AND id_product_attribute = ".$id_pa);
		if( dbNumRows($qs) == 1 )
		{
			list( $id_stock ) = dbFetchArray($qs);
			$sc = dbQuery("UPDATE tbl_stock	SET qty = qty + ".$qty." WHERE id_stock = ".$id_stock);
		}
		else
		{
			$sc = dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty ) VALUES (".$id_zone.", ".$id_pa.", ". $qty.")");
		}
		return $sc;
	}
	
	
	
	public function clearStockZeroZone($id_zone)
	{
		return dbQuery("DELETE FROM tbl_stock WHERE id_zone = ".$id_zone." AND qty = 0");	
	}
	
	
	
	//-----------------  New Reference --------------//
	public function getNewReference($date = '')
	{
		$date = $date == '' ? date('Y-m-d') : $date;
		$Y		= date('y', strtotime($date));
		$M		= date('m', strtotime($date));
		$prefix = getConfig('PREFIX_TRANFER');
		$preRef = $prefix . '-' . $Y . $M;
		$qs = dbQuery("SELECT MAX(reference) AS reference FROM tbl_tranfer WHERE reference LIKE '".$preRef."%' ORDER BY reference DESC");
		list( $ref ) = dbFetchArray($qs);
		if( ! is_null( $ref ) )
		{
			$runNo = mb_substr($ref, -4, NULL, 'UTF-8') + 1;
			$reference = $prefix . '-' . $Y . $M . sprintf('%04d', $runNo);
		}
		else
		{
			$reference = $prefix . '-' . $Y . $M . '0001';
		}
		return $reference;
	}
	
	
	
	
	public function getMoveList($id)
	{
		return dbQuery("SELECT * FROM tbl_tranfer_detail WHERE id_tranfer = ".$id);	
	}
	
	
	public function hasDetail($id_tranfer)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT count(*) FROM tbl_tranfer_detail WHERE id_tranfer = ".$id_tranfer);
		list( $rs ) = dbFetchArray($qs);
		if( $rs > 0 )
		{
			$sc = TRUE;
		}
		return $sc;
	}
	
	
}//---end class

?>