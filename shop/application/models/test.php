<?php
class Test extends CI_Model
{
	public $invent;
	public $club;
	
	public function __construct()
	{
		parent::__construct();	
		$this->invent = $this->load->database('invent', true);
		$this->club = $this->load->database('club', true);
	}
	
	
	public function getClubOrder()
	{
		$rs = $this->club->limit(3)->get('tbl_order');
		return $rs->result();
	}
	
	public function getInventOrder()
	{
		$rs = $this->invent->limit(5)->get('tbl_order');
		return $rs->result();	
	}
	
}