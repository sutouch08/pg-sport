<?php
class return_order
{
  public $id;
  public $reference;
  public $order_code;
  public $id_customer;
  public $id_employee;
  public $isCancle = 0;
  public $date_add;
  public $date_upd;
  public $remark;
  public $error;

  public function __construct($id = ''){
    if($id != '' || $id !== FALSE)
    {
      $this->getData($id);
    }
  }


  public function getData($id)
  {
    $qs = dbQuery("SELECT * FROM tbl_return_order WHERE id = '".$id."'");
    if(dbNumRows($qs) == 1)
    {
      $ds = dbFetchArray($qs);
      foreach($ds as $key => $value)
      {
        $this->$key = $value;
      }
    }
  }




  public function getDataByReference($reference)
  {
    $qs = dbQuery("SELECT * FROM tbl_return_order WHERE reference = '".$reference."'");
    if(dbNumRows($qs) == 1)
    {
      $ds = dbFetchArray($qs);
      foreach($ds as $key => $value)
      {
        $this->$key = $value;
      }
    }
  }



  public function add(array $ds = array())
  {
    $sc = FALSE;
    if(!empty($ds))
    {
      $fields = "";
      $values = "";
      $i = 1;
      foreach($ds as $field => $value)
      {
        $fields .= $i == 1 ? $field : ", ".$field;
        $values .= $i == 1 ? "'".$value."'" : ", '".$value."'";
        $i++;
      }

      $qs = dbQuery("INSERT INTO tbl_return_order (".$fields.") VALUES (".$values.")");
      if($qs === TRUE)
      {
        $sc = dbInsertId();
      }
      else
      {
        $this->error = dbError();
      }
    }

    return $sc;
  }



  public function addDetail(array $ds = array())
  {
    $sc = FALSE;
    if(!empty($ds))
    {
      $fields = "";
      $values = "";
      $i = 1;
      foreach($ds as $field => $value)
      {
        $fields .= $i == 1 ? $field : ", ".$field;
        $values .= $i == 1 ? "'".$value."'" : ", '".$value."'";
        $i++;
      }

      $sc = dbQuery("INSERT INTO tbl_return_order_detail (".$fields.") VALUES (".$values.")");
    }

    return $sc;
  }




  public function getDetails($id)
  {
    return dbQuery("SELECT * FROM tbl_return_order_detail WHERE id_return_order = '".$id."'");
  }


  public function getSumQty($id)
  {
    $qs = dbQuery("SELECT SUM(qty) FROM tbl_return_order_detail WHERE id_return_order = '".$id."'");
    list($qty) = dbFetchArray($qs);

    return is_null($qty) ? 0 : $qty;
  }


  public function cancelDetail($id)
  {
    return dbQuery("UPDATE tbl_return_order_detail SET isCancle = 1 WHERE id = ".$id);
  }


  //-----------------  New Reference --------------//
	public function getNewReference($date='')
	{
    $date = $date == '' ? date('Y-m-d') : $date;
		$Y		= date('y');
		$M		= date('m');
		$prefix = getConfig('PREFIX_RETURN');
		$runDigit = getConfig('RUN_DIGIT'); //--- รันเลขที่เอกสารกี่หลัก
		$preRef = $prefix . '-' . $Y . $M;
		$qs = dbQuery("SELECT MAX(reference) AS reference FROM tbl_return_order WHERE reference LIKE '".$preRef."%' ORDER BY reference DESC");
		list( $ref ) = dbFetchArray($qs);
		if( ! is_null( $ref ) )
		{
			$runNo = mb_substr($ref, ($runDigit*-1), NULL, 'UTF-8') + 1;
			$reference = $prefix . '-' . $Y . $M . sprintf('%0'.$runDigit.'d', $runNo);
		}
		else
		{
			$reference = $prefix . '-' . $Y . $M . sprintf('%0'.$runDigit.'d', '001');
		}
		return $reference;
	}

}

?>
