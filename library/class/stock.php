<?php
class stock
{
	public $error = '';

	public function __construct()
	{

	}


	public function updateStockZone($id_zone, $id_pa, $qty)
	{
		$sc = FALSE;

		if($this->isExists($id_zone, $id_pa) === TRUE)
		{
			$sc = $this->update($id_zone, $id_pa, $qty);
		}
		else
		{
			$sc = $this->add($id_zone, $id_pa, $qty);
		}

		$this->removeZero();

		return $sc;
	}





	private function add($id_zone, $id_pa, $qty)
	{
		return dbQuery("INSERT INTO tbl_stock (id_zone, id_product_attribute, qty) VALUES (".$id_zone.", '".$id_pa."', ".$qty.")");
	}





	private function update($id_zone, $id_pa, $qty)
	{
		return dbQuery("UPDATE tbl_stock SET qty = (qty + ".$qty.") WHERE id_zone = ".$id_zone." AND id_product_attribute = '".$id_pa."'");
	}






	private function removeZero()
	{
		dbQuery("DELETE FROM tbl_stock WHERE qty = 0");
	}





	public function isExists($id_zone, $id_pa)
	{
		$sc = FALSE;
		$qs = dbQuery("SELECT id_stock FROM tbl_stock WHERE id_zone = '".$id_zone."' AND id_product_attribute = '".$id_pa."'");
		if( dbNumRows($qs) == 1 )
		{
			$sc = TRUE;
		}
		return $sc;
	}





	//---	มีสต็อกคงเหลือเพียงพอให้ตัดหรือไม่
	public function isEnough($id_zone, $id_pa, $qty)
	{
		$qs = dbQuery("SELECT id_stock FROM tbl_stock WHERE id_zone = '".$id_zone."' AND id_product_attribute = '".$id_pa."' AND qty >= ".$qty);
		return dbNumRows($qs) == 1 ? TRUE : FALSE;
	}




	public function getStockZone($id_zone, $id_pa)
	{
		$sc = 0;
		$qs = dbQuery("SELECT qty FROM tbl_stock WHERE id_zone = ".$id_zone." AND id_product_attribute = '".$id_pa."'");
		if( dbNumRows($qs) == 1 )
		{
			list( $sc ) = dbFetchArray($qs);
		}
		return $sc;
	}


}//--- end class

 ?>
