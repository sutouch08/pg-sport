<?php
class printer
{
public $page;	
public $total_page		= 1;
public $current_page	= 1;
public $page_width 	= 200;
public $page_height	= 282;
public $content_width	= 190;
public $row				= 20;
public $header_rows = 4;
public $sub_total_row	= 2;
public $footer_row		= 4;
public $ex_row			= 0;
public $total_row		= 16;
public $row_height 	= 10;
public $font_size 		= 14;
public $title				= "";
public $title_size 		= "h4";
public $content_border = 2;
public $pattern			= array();
public $footer			= true;

public $header_row	= array();

public $sub_header	= "";
private $loader = "<script>	
						var load_time;  function load_in(){ $('#xloader').modal('show'); console.log('load_in'); var time = 0; 
						load_time = window.setInterval(function(){ if(time < 90){ time++; }else{ time += 0.01; } $('#preloader').css('width', time+'%');}, 1000); }
						function load_out(){ $('#xloader').modal('hide'); window.clearInterval(load_time); $('#preloader').css('width', '0%'); console.log('load_out'); }
						</script>";

public function __construct()
{
	return true;
}

public function config(array $data)
{
	foreach($data as $key=>$val)
	{
		$this->$key = $val;	
	}
	if(!$this->footer)
	{
		$this->row += $this->footer_row;
		$this->footer_row = 0;	
	}
	$this->row -= ($this->sub_total_row + $this->ex_row + $this->header_rows);
	$this->total_page = ceil($this->total_row/$this->row);
	return true;
}

public function doc_header($pageTitle = 'print pages')
{
	$header = "";
	$header .= "<!DOCTYPE html>";
	$header .= "	<html>";
	$header .= "<head>";
	$header .= "	<meta charset='utf-8'>";
	$header .= "	<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
	$header .= "	<link rel='icon' href='../favicon.ico' type='image/x-icon' />";
	$header .= "	<title>". $pageTitle ."</title>";
	$header .= "	<link href='".WEB_ROOT."library/css/bootstrap.css' rel='stylesheet'>";
	$header .= "	<link href='".WEB_ROOT."library/css/font-awesome.css' rel='stylesheet'>";
	$header .= "	<link href='".WEB_ROOT."library/css/bootflat.min.css' rel='stylesheet'>";
	$header .= "<link href='".WEB_ROOT."library/css/jquery-ui-1.10.4.custom.min.css' rel='stylesheet'  />";
	$header .= "<script src='".WEB_ROOT."library/js/jquery.min.js'></script>";
	$header .= "<script src='".WEB_ROOT."library/js/jquery-ui-1.10.4.custom.min.js'></script>";
	$header .= "<script src='".WEB_ROOT."library/js/bootstrap.min.js'></script> ";
	$header .= "	<style> .page_layout{ border: solid 1px #AAA; border-radius:5px; 	} @media print{ 	.page_layout{ border: none; } } 	</style>";
	$header .= $this->loader;
	$header .= "	</head>";
	$header .= "	<body>";
	$header .= "	<div class='modal fade' id='xloader' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true' data-backdrop='static'>";
	$header .= "	<div class='modal-dialog' style='width:150px; background-color:transparent;' >";
	$header .= "	<div class='modal-content'>";
	$header .= "	<div class='modal-body'>";
	$header .= "	<div style='width:100%; height:150px; padding-top:25px;'>";
	$header .= "	<div style='width:100%;  text-align:center; margin-bottom:10px;'><i class='fa fa-spinner fa-4x fa-pulse' style='color:#069; display:block;'></i>	</div>";
	$header .= "	<div style='width:100%; height:10px; background-color:#CCC;'></div>";
	$header .= "	<div id='preloader' style='margin-top:-10px; height:10px; width:1%; background-color:#09F;'></div>";
	$header .= "	<div style='width:100%;  text-align:center; margin-top:15px; font-size:12px;'><span><strong>Loading....</strong></span></div>";
	$header .= "	</div></div></div></div></div> "; // modal fade;
	$header .= "	<script> load_in(); </script>";
	$header .= "	<div class='hidden-print' style='margin-top:10px; padding-bottom:10px; padding-right:5mm; width:200mm; margin-left:auto; margin-right:auto; text-align:right'>";
	$header .= "	<button class='btn btn-primary' onclick='print()'><i class='fa fa-print'></i>&nbspพิมพ์</button>";
	$header .= "	</div><div style='width:100%'>";	
	return $header;
}

public function add_title($title)
{
	$this->title = $title;
}

public function set_pattern($pattern) //// กำหนดรูปแบบ CSS ให้กับ td 
{
	$this->pattern = $pattern;	
}

public function print_sub_total(array $data)
{
	$rs = "<table class='table' style='margin-bottom:0px;'>";
	foreach($data as $value)
	{	
		foreach($value as $val)
		{
			$rs .= "<tr style='height:".$this->row_height."mm; line-height:".$this->row_height."mm;'>";
			$rs .= $val;
			$rs .= "</tr>";
		}	
	}
	$rs .= "</table>";
	return $rs;
}

public function add_subheader($sub_header)
{
	$this->sub_header = $this->thead($sub_header);
}

public function thead(array $dataset)
{
	$thead	= "<table class='table' style='margin-bottom:-2px;'>"; 
	$thead 	.= "<thead>";
	$thead	.= "<tr style='height:".$this->row_height."mm; line-height:".$this->row_height."mm; font-size:10px;'>";
	foreach($dataset as $data)
	{
		$value 	= $data[0];
		$css		= $data[1];
		$thead 	.= "<th style='".$css."'>".$value."</th>";
	}
	$thead	.= "</tr>";
	$thead 	.= "</thead>";
	return $thead;
}

public function doc_footer()
{
	return "</div><script>$(window).load(function(){ load_out(); });</script></body></html>";
}

public function add_header(array $data)
{
	$i = 0;
	foreach($data as $label => $value)
	{	
		$this->header_row[$i] = array($label => $value);
		$i++;
	}
	return true;
}

public function print_header()
{
	$rd = $this->header_row;
	$r = count($rd);
	$height = ($this->header_rows * $this->row_height) +1;
	$i	= 0;
	$header = "<div style='width:".$this->content_width."mm; min-height:".$height."mm; margin:auto; margin-bottom:2mm; border:solid 2px #ccc; border-radius: 10px;' >";
	while(	$i<$r)
	{
		foreach($rd[$i] as $label => $value)
		{
			$header .= "<div style='width:50%; min-height:10mm; line-height:10mm; float:left; padding-left:10px; '>".$label." : ".$value."</div>";
		}
		$i++;
	}
	$header .= "</div>";
	return $header;
}

public function add_content($data)
{
	$content = "<div style='width:".$this->content_width."mm; margin:auto; margin-bottom:2mm; border:solid 2px #ccc; border-radius: 10px;' >";
	$content .= $data;
	$content .="</div>";
	return $content;
}

public function page_start()
{
	$page_break = "page-break-after:always;";
	if($this->current_page == $this->total_page)
	{
		$page_break = ""; 
	}
	return "<div class='page_layout' style='width:".$this->page_width."mm; padding-top:5mm; height:".$this->page_height."mm; margin:auto; ".$page_break."'>"; //// page start
}
public function page_end()
{
	return "</div><div class='hidden-print' style='height: 5mm; width:".$this->page_width."'></div>";	
}

public function top_page()
{
	$top = "";
	$top .= "<div style='width:".$this->content_width."mm; height:".$this->row_height."mm; margin:auto; margin-bottom:2mm;'>"; //// top start
	$top .= "<div style='width:80%; line-height:".$this->row_height."mm; float:left'><".$this->title_size." style='margin:0px;'>".$this->title."</".$this->title_size."></div>";
	$top .= "<div style='width:20%; line-height:".$this->row_height."mm; float:left; text-align:right;'><span style='position:relative; bottom: 0mm;'>หน้า ".$this->current_page."/".$this->total_page."</span></div>";
	$top .= "</div>"; /// top end;
	if( $this->header_rows )
	{
		$top .= $this->print_header();
	}
	return $top;
}

public function content_start()
{
	$height = ($this->row + $this->sub_total_row+1) * $this->row_height+2;
	$border = $this->content_border == 0 ? '' : "border:solid 2px #ccc;";
	return  "<div style='width:".$this->content_width."mm; height:".$height."mm; margin:auto; margin-bottom:2mm; ".$border." border-radius: 10px;'>";
}

public function content_end()
{
	return "</div>";	
}

public function print_row($data)
{
	$row = "<tr style='font-size:".$this->font_size."px; height:".$this->row_height."mm;'>";
	$pattern = $this->pattern;
	if(count($pattern) == 0 )
	{
		$c = count($data);
		while($c>0)
		{
			array_push($pattern, "");
			$c--;
		}
	}
	foreach($data as $n=>$value)
	{
		$row .= "<td style='".$pattern[$n]."'>".$value."</td>";
	}
	$row .= "</tr>";
	return $row;
}
public function table_start()
{
	return $this->sub_header;	
}

public function table_end()
{
	return "</table>";	
}


public function set_footer(array $data)
{
	if(!$this->footer)
	{
		return false;
	}else{
		$c = count($data);
		$box_width = 100/$c;	
		$height = $this->footer_row * $this->row_height;
		$row1 = $this->row_height;
		$row2 = 5;
		$row4 = 8;
		$row3 = $height - ($row1+$row2+$row4) - 2;
		$footer = "<div style='width:190mm; height:".$height."mm; margin:auto;'>";
		foreach($data as $n=>$value)
		{
			$footer .="<div style='width:".$box_width."%; height:".$height."mm; text-align:center; float:left;'>";
			$footer .="<span style='width:100%; height:".$row1."mm; text-align:center;'>".$value[0]."</span>";
			$footer .="<div style='width:100%; height:".($this->footer_row - 1)* $this->row_height."mm; text-align:center; border: solid 2px #ccc; padding-left:10px; padding-right:10px; border-radius:10px;'>";
			$footer .="<span style='width:100%; height: ".$row2."mm; text-align:center;font-size:8px; float:left;'>".$value[1]."</span>";
			$footer .="<span style='width:100%; height: ".$row3."mm; text-align:center; padding-left:5px; padding-right:5px; border-bottom:dotted 1px #ccc; float:left; padding:10px;'></span>";
			$footer .="<span style='width:100%; height: ".$row4."mm; text-align:center; float:left; padding-top: 10px;'>".$value[2]."</span>";
			$footer .="</div>";
			$footer .="</div>";
		}
		$footer .="</div>";
		$this->footer = $footer;
	}
}

public function print_barcode($barcode, $css = "")
{
	if($css == ""){ $css = "width: 100px;"; }
	return "<img src='".WEB_ROOT."library/class/barcode/barcode.php?text=".$barcode."' style='".$css."' />";
}

}//// end class
?>