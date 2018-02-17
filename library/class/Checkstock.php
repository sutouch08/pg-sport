<?php
	class checkstock{
		public $id_employee_open;
		public $id_employee_close;
		public $id_check;
		public $name_check;
		public $date_start;
		public $date_stop;
		public $id_product_attribute;
		public $id_zone;
		public $qty_after;
		public $date_upd;
		public $warehouse_name;
		public $id_warehouse;
		public function __construct()
		{
		}
		
		public function add_stock($id_check, $id_zone, $id_product_attribute, $qty)
		{
			$qs = dbQuery("SELECT id_stock_check FROM tbl_stock_check WHERE id_zone = ".$id_zone." AND id_product_attribute = ".$id_product_attribute." AND id_check = ".$id_check);
			if( dbNumRows($qs) == 1 )
			{
				list($id) = dbFetchArray($qs);
				$rs = dbQuery("UPDATE tbl_stock_check SET qty_after = qty_after + ".$qty." WHERE id_stock_check = ".$id);
			}else{
				$rs = dbQuery("INSERT INTO tbl_stock_check (id_check, id_zone, id_product_attribute, qty_after) VALUES (".$id_check.", ".$id_zone.", ".$id_product_attribute.", ".$qty.")");
			}
			return $rs;
		}
		
		public function detail($id_check) 
		{
			$qs = dbQuery("SELECT name_check, id_employee_open, id_employee_close, status, date_start, date_stop, id_warehouse FROM tbl_check WHERE id_check = ".$id_check);
			if( dbNumRows($qs) == 1 )
			{
				$rs = dbFetchArray($qs);
				$this->name_check = $rs['name_check'];
				$this->id_employee_open = $rs['id_employee_open'];
				$this->id_employee_close = $rs['id_employee_close'];
				$this->status = $rs['status'];
				$this->date_start = $rs['date_start'];
				$this->date_stop = $rs['date_stop'];
				$this->id_warehouse = $rs['id_warehouse'];
			}
		}
		
		public function check_open()
		{
			$rs = false;
			$qs = dbQuery("SELECT id_check FROM tbl_check WHERE status = 1");
			if(dbNumRows($qs) == 1 )
			{
			 	$rs = true;
			}
			return $rs;
		}
		
		
		public function get_id_zone($barcode, $id)
		{
			$id_zone = false;
			$this->detail($id);
			$qs = dbQuery("SELECT id_zone FROM tbl_zone WHERE barcode_zone = '".$barcode."' AND id_warehouse = ".$this->id_warehouse);
			if(dbNumRows($qs) == 1)
			{
				$rs = dbFetchArray($qs);
				$id_zone = $rs['id_zone'];
			}
			return $id_zone;
		}
		
		
		public function get_id_check()
		{
			$id = "";
			$qs = dbQuery("SELECT id_check FROM tbl_check WHERE status = 1");
			if(dbNumRows($qs) == 1 )
			{
				$rs = dbFetchArray($qs);
				$id = $rs['id_check'];
			}
			return $id;
		}
		
		
		public function get_stock_check($id_stock_check)
		{
			list($id_product_attribute,$id_zone,$qty_after,$date_upd) = dbFetchArray(dbQuery("SELECT id_product_attribute,id_zone,qty_after,date_upd FROM tbl_stock_check WHERE id_stock_check = $id_stock_check"));
			$this->id_product_attribute = $id_product_attribute;
			$this->id_zone = $id_zone;
			$this->qty_after = $qty_after;
			$this->date_upd = $date_upd;
		}
		
		
		public function table_stock_diff($id_product,$id_check)
		{
				$check_stock = new checkstock();
				$check_stock->detail($id_check);
				$id_warehouse = $check_stock->id_warehouse;
			echo "<table class='table table-bordered table-striped'>
										<thead>
											<th style='width:5%; text-align:center;'>ลำดับ</th><th style='width:50%;'>รหัสสินค้า</th>
											<th style='width:15%; text-align:center;'>จำนวนสต็อก</th><th style='width:15%; text-align:center;'>จำนวนที่นับได้</th>
											<th style='width:15%; text-align:center;'>ยอดต่าง</th>
										</thead>";
											$qr = dbQuery("SELECT tbl_product_attribute.id_product_attribute,reference FROM tbl_product_attribute where id_product = $id_product ");
											$row = dbNumRows($qr); 
											$i = 0;
											$n = 1;
											if($row == 0){
												echo "<tr><td align='center' colspan='5'><h4>ยังไม่มีสินค้า</h4></td></tr>";
											}
											while($i<$row){
												list($id_product_attribute,$reference,) = dbFetchArray($qr);
												list($qty_stock,$qty_check) = dbFetchArray(dbQuery("SELECT SUM(qty_before),SUM(qty_after) FROM tbl_stock_check WHERE id_check = $id_check AND id_product_attribute = $id_product_attribute"));
												echo "<tr><td align='center'>$n</td><td>$reference</td><td align='center'>".number_format($qty_stock)."</td><td align='center'>".number_format($qty_check)."
												</td><td align='center'>".number_format($qty_check-$qty_stock)."
												</td></tr>";
												$i++;
												$n++;
											}
											echo "</table>";
									  
		}
	}

?>