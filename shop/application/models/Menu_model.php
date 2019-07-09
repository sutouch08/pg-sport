<?php
class Menu_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getProductGroup()
  {
    $rs = $this->db->get('tbl_product_group');
    return $rs->result();
  }


  public function getCategory($level, $parent)
  {
    $rs = $this->db->where('level_depth', $level)->where('parent_id', $parent)->get('tbl_category');
    return $rs->result();
  }


  //----- ตรวจสอบว่าหมวดหมู่นี้มีหมวดหมู่ย่อยหรือไม่
  public function hasChild($id)
  {
    $rs = $this->db->where('parent_id', $id)->get('tbl_category');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }
} //--- End class

 ?>
