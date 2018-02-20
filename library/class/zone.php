<?php

	class zone
	{
		public $id;
		public $id_zone;
		public $barcode;
		public $name;
		public $zone_name;
		public $id_warehouse;


		public function __construct($id = '')
		{
			if( $id )
			{
				$qs = dbQuery("SELECT * FROM tbl_zone WHERE id_zone = '".$id."'");
				if( dbNumRows($qs) == 1 )
				{
					$rs = dbFetchObject($qs);
					$this->id		= 	$rs->id_zone;
					$this->id_zone = $rs->id_zone;
					$this->barcode	= $rs->barcode_zone;
					$this->barcode_zone = $rs->barcode_zone;
					$this->name	= $rs->zone_name;
					$this->zone_name = $rs->zone_name;
					$this->id_warehouse = $rs->id_warehouse;
				}
			}
		}



		public function add(array $ds)
		{
			$fields 	= '';
			$values 	= '';
			$n 		= count($ds);
			$i 			= 1;
			foreach( $ds as $key => $val )
			{
				$fields .=	 $key;
				if( $i < $n ){ $fields .= ', '; }
				$values .= "'".$val."'";
				if( $i < $n ){ $values .= ', '; }
				$i++;
			}
			$qs = dbQuery("INSERT INTO tbl_zone (".$fields.") VALUES (".$values.")");
			if( $qs )
			{
				return dbInsertId();
			}
			else
			{
				return FALSE;
			}
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
			return dbQuery("UPDATE tbl_zone SET ".$set." WHERE id_zone = ".$id);
		}


		public function deleteZone($id)
		{
			if( $this->isZoneEmpty($id) === TRUE && $this->isExistsTransection($id) === FALSE )
			{
				return $this->actionDelete($id);
			}
			else
			{
				return FALSE;
			}
		}


		public function isZoneEmpty($id)
		{
			$sc = TRUE;
			$qs = dbQuery("SELECT id_stock FROM tbl_stock WHERE id_zone = ".$id);
			if( dbNumRows($qs) > 0 )
			{
				$sc = FALSE;
			}
			return $sc;
		}


		private function actionDelete($id)
		{
			return dbQuery("DELETE FROM tbl_zone WHERE id_zone = ".$id);
		}




		public function getWarehouseId($id)
		{
			$sc = "";
			$qs = dbQuery("SELECT id_warehouse FROM tbl_zone WHERE id_zone = ".$id);
			if( dbNumRows($qs) == 1 )
			{
				list( $sc ) = dbFetchArray($qs);
			}
			return $sc;
		}




		public function getId($barcode)
		{
			$sc = FALSE;
			$qs = dbQuery("SELECT id_zone FROM tbl_zone WHERE barcode_zone ='".$barcode."'");
			if( dbNumRows($qs) == 1 )
			{
				list( $sc ) = dbFetchArray($qs);
			}
			return $sc;
		}



		public function getName($id)
		{
			$sc = "";
			$qs = dbQuery("SELECT zone_name FROM tbl_zone WHERE id_zone = '".$id."'");
			if( dbNumRows($qs) == 1 )
			{
				list( $sc ) = dbFetchArray($qs);
			}
			return $sc;
		}




		public function isExistsZoneCode($code, $id = '')
		{
			$sc = FALSE;
			if( $id != '' )
			{
				$qs = dbQuery("SELECT id_zone FROM tbl_zone WHERE barcode_zone = '".$code."' AND id_zone != ".$id);
			}
			else
			{
				$qs = dbQuery("SELECT id_zone FROM tbl_zone WHERE barcode_zone = '".$code."'");
			}
			if( dbNumRows($qs) > 0 )
			{
				$sc = TRUE;
			}

			return $sc;
		}





		public function isExistsZoneName($name, $id = '')
		{
			$sc = FALSE;
			if( $id != '' )
			{
				$qs = dbQuery("SELECT id_zone FROM tbl_zone WHERE zone_name = '".$name."' AND id_zone != ".$id);
			}
			else
			{
				$qs = dbQuery("SELECT id_zone FROM tbl_zone WHERE zone_name = '".$name."'");
			}
			if( dbNumRows($qs) > 0 )
			{
				$sc = TRUE;
			}

			return $sc;
		}




		public function getZoneDetail($id)
		{
			$sc = FALSE;
			$qs = dbQuery("SELECT * FROM tbl_zone WHERE id_zone = '".$id."'");
			if( dbNumRows($qs) == 1 )
			{
				$sc = dbFetchObject($qs);
			}

			return $sc;
		}


	} 	//----- End class


?>
