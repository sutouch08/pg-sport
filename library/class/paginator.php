<?php
class paginator{
	var $items_per_page;
	var $items_total;
	var $current_page;
	var $num_pages;
	var $mid_range;
	var $low;
	var $high;
	var $limit;
	var $return;
	var $default_ipp;
	var $querystring;
	var $url_next;
	var $Num_Rows;
	var $Page;

	function paginator()
	{
		$this->current_page = 1;
		$this->mid_range = 7;
		$this->items_per_page = $this->default_ipp;
		$this->url_next = $this->url_next;
	}
	function paginate()
	{

		if(!is_numeric($this->items_per_page) OR $this->items_per_page <= 0) $this->items_per_page = $this->default_ipp;
		@$this->num_pages = ceil($this->items_total/$this->items_per_page);

		if($this->current_page < 1 Or !is_numeric($this->current_page)) $this->current_page = 1;
		if($this->current_page > $this->num_pages) $this->current_page = $this->num_pages;
		$prev_page = $this->current_page-1;
		$next_page = $this->current_page+1;


		if($this->num_pages > 10)
		{
			$this->return = ($this->current_page != 1 && $this->items_total >= 10) ? "<a class=\"paginate\" href=\"".$this->url_next.$prev_page."\">&laquo; Previous</a> ":"<span class=\"inactive\" href=\"#\">&laquo; Previous</span> ";
			$this->start_range = $this->current_page - floor($this->mid_range/2);
			$this->end_range = $this->current_page + floor($this->mid_range/2);

			if($this->start_range <= 0)
			{
				$this->end_range += abs($this->start_range)+1;
				$this->start_range = 1;
			}
			if($this->end_range > $this->num_pages)
			{
				$this->start_range -= $this->end_range-$this->num_pages;
				$this->end_range = $this->num_pages;
			}
			$this->range = range($this->start_range,$this->end_range);

			for($i=1;$i<=$this->num_pages;$i++)
			{
				if($this->range[0] > 2 And $i == $this->range[0]) $this->return .= " ... ";
				if($i==1 Or $i==$this->num_pages Or in_array($i,$this->range))
				{
					$this->return .= ($i == $this->current_page ) ? "<a title=\"Go to page $i of $this->num_pages\" class=\"current\" href=\"#\">$i</a> ":"<a class=\"paginate\" title=\"Go to page $i of $this->num_pages\" href=\"".$this->url_next.$i."\">$i</a> ";
				}
				if($this->range[$this->mid_range-1] < $this->num_pages-1 And $i == $this->range[$this->mid_range-1]) $this->return .= " ... ";
			}
			$this->return .= (($this->current_page != $this->num_pages And $this->items_total >= 10)) ? "<a class=\"paginate\" href=\"".$this->url_next.$next_page."\">Next &raquo;</a>\n":"<span class=\"inactive\" href=\"#\">&raquo; Next</span>\n";
		}
		else
		{
			for($i=1;$i<=$this->num_pages;$i++)
			{
				$this->return .= ($i == $this->current_page) ? "<a class=\"current\" href=\"#\">$i</a> ":"<a class=\"paginate\" href=\"".$this->url_next.$i."\">$i</a> ";
			}
		}
		$this->low = ($this->current_page-1) * $this->items_per_page;
		$this->high = (isset($_GET['ipp']) == 'All') ? $this->items_total:($this->current_page * $this->items_per_page)-1;
		$this->limit = (isset($_GET['ipp']) == 'All') ? "":" LIMIT $this->low,$this->items_per_page";
	}
	function display_pages()
	{
		return $this->return;
	}
	function Per_Page($tbl,$where,$Per_Page)
	{
		$Num_Rows = dbNumRows(dbQuery("SELECT * FROM $tbl $where"));
		$this->Per_Page = $Per_Page;
		$this->Num_Rows= $Num_Rows;
		if(!isset($_GET["Page"]))
		{
			$Page=1;
		}else{
			$Page = $_GET["Page"];
		}
		$this->Page = $Page;
		$this->Prev_Page = $Page-1;
		$this->Next_Page = $Page+1;
		
		$this->Page_Start = (($Per_Page*$Page)-$Per_Page);
		if($Num_Rows<=$Per_Page)
		{
			$this->Num_Pages =1;
		}
		else if(($Num_Rows % $Per_Page)==0)
		{
			$this->Num_Pages =($Num_Rows/$Per_Page) ;
		}
		else
		{
			$Num_Pages =($Num_Rows/$Per_Page)+1;
			$this->Num_Pages = (int)$Num_Pages;
		}
		
	}
	function display($get_rows,$url)
	{
		echo "<form method='post' action='' name='rows' id='rows'><br>
		จำนวน ". number_format( $this->Num_Rows ) ." รายการ |  ";
		$this->items_total = $this->Num_Rows;
		$this->mid_range = 10;
		$this->current_page = $this->Page;
		$this->default_ipp = $this->Per_Page;
		$this->url_next = "$url&Page=";
		$this->paginate();
		echo "
		แสดง <select name='get_rows' id='get_rows'>
				<option value='20' "; if(20==$get_rows){echo" selected='selected'";} echo">20</option>
				<option value='50' "; if(50==$get_rows){echo" selected='selected'";} echo">50</option>
				<option value='100' "; if(100==$get_rows){echo" selected='selected'";} echo">100</option>
				<option value='300' "; if(300==$get_rows){echo" selected='selected'";} echo">300</option>
			  </select> | ";
		echo $this->display_pages();
		echo "<br><br></form>";
	}
	function setcookie_rows($get_rows){
		setcookie("get_rows", $get_rows,time()+(3600*24*365*30),'/');
	}
}
?>

