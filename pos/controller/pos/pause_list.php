<?php
$cs = new order_pos();
$qs = $cs->getPauseList();
$sc = array();
if(dbNumRows($qs) > 0)
{
  while($rs = dbFetchObject($qs))
  {
    $arr = array('id' => $rs->id_order_pos, 'reference' => $rs->reference);
    array_push($sc, $arr);
  }
}
else
{
  $arr = array('nodata' => 'nodata');
  array_push($sc, $arr);
}

echo json_encode($sc);

 ?>
