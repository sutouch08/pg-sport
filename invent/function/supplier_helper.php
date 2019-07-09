<?php
function getSuplierIn($txt)
{
  $in = '0';
  $qr = "SELECT id FROM tbl_supplier WHERE code LIKE '%".$txt."%' OR name LIKE '%".$txt."%' ";
  $qs = dbQuery($qr);

  while($rs = dbFetchObject($qs))
  {
    $in .= ', '.$rs->id;
  }

  return $in;
}


function getPoInBySupplierIn($id_supplier_in)
{
  $in = '0';
  $qr = "SELECT id_po FROM tbl_po WHERE id_supplier IN(".$id_supplier_in.")";
  $qs = dbQuery($qr);

  while($rs = dbFetchObject($qs))
  {
    $in .= ', '.$rs->id_po;
  }

  return $in;
}


function getSupplierNameByPoId($id_po)
{
  $qr  = "SELECT sp.name FROM tbl_po AS po ";
  $qr .= "JOIN tbl_supplier AS sp ON po.id_supplier = sp.id ";
  $qr .= "WHERE po.id_po = ".$id_po." ";

  $qs = dbQuery($qr);
  if(dbNumRows($qs) == 1)
  {
    $rs = dbFetchObject($qs);
    return $rs->name;
  }

  return "Not found!";
}





 ?>
