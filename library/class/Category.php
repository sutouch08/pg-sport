<?php
class category {
		public $id_product;
		public $id_category, $category_name, $description, $parent_id, $level_depth, $postioin, $date_add, $date_upd, $active ;
		protected $connect;
		protected $sql;
		public function __construct(){}
		public function category_show_new($no_date,$id_product){
			$date = date('Y-m-d');
			list($date_upd) = dbFetchArray(dbQuery("SELECT date_add FROM tbl_product where id_product = '$id_product'"));
			$date_new = date('Y-m-d 00:00:00',strtotime("+$no_date day" ,strtotime($date_upd)));
			if($date_new > "$date 00:00:00"){
				$this->NEW = "<span class='new-product'> NEW</span>";
			}else{
				$this->NEW = "";
			}
		}
		
		public function categoryList()
		{
			$sql = "SELECT * FROM tbl_category WHERE id_category !=0";	
			return dbQuery($sql);
		}
		
	}
	// จบ class	
?>							