<?php
class Login_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function getUser($user, $pass)
	{
		$rs = $this->db->where("user_name", $user)->where("password", $pass)->get("tbl_user");
		if($rs->num_rows() == 1 )
		{
			return $rs->row();
		}
		else
		{
			return false;
		}
	}
	
	public function loged($id_user)
	{
		return $this->db->where("id", $id_user)->update("tbl_user", array("last_login" => NOW()));	
	}
	
	public function update_cart($id_c, $id_customer)
	{
		return $this->db->where("id_customer", $id_c)->update("tbl_cart", array("id_customer" => $id_customer));	
	}
	
	
	
}/// end class

?>