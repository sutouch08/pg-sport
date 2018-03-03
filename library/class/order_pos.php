<?php
class order_pos
{
  public $id_order_pos;
	public $reference;
  public $id_shop;
	public $id_customer;
	public $id_employee;
	public $id_payment = 1;  //--- 1 = cash 2 = credit card
  public $pos_no; //--- หมายเลขประจำเครื่อง
  public $status = 1; //--- 1 = normal, 2 = pause, 2 = cancled
  public $is_paid = 0;
  public $order_amount = 0; //--- ยอดเงินที่ต้ยอดที่ต้องชำระ
  public $received_amount = 0; //--- รับเงินมา
  public $change_amount = 0; //--- เงินทอน
  public $date_add;
  public $date_upd;
  public $total_product = 0;
  public $total_qty = 0;
  public $total_amount = 0;
  public $error;


  public function __construct($id='')
  {
    if($id != '' OR $id !== FALSE)
    {
      $this->getData($id);
    }
  }



  public function getData($id)
  {
    $qs = dbQuery("SELECT * FROM tbl_order_pos WHERE id_order_pos = '".$id."'");
    if(dbNumRows($qs) == 1)
    {
      $ds = dbFetchArray($qs);
      foreach($ds as $key => $value)
      {
        $this->$key = $value;
      }

      $this->total_product = $this->getTotalProduct($id);
      $this->total_qty = $this->getTotalQty($id);
      $this->total_amount = $this->getTotalOrder($id);
    }
  }


  public function getDataByReference($reference)
  {
    $qs = dbQuery("SELECT * FROM tbl_order_pos WHERE reference = '".$reference."'");
    if(dbNumRows($qs) == 1)
    {
      $ds = dbFetchArray($qs);
      foreach($ds as $key => $value)
      {
        $this->$key = $value;
      }
    }
  }



  public function getId($reference)
  {
    $sc = FALSE;
    $qs = dbQuery("SELECT id_order_pos FROM tbl_order_pos WHERE reference = '".$reference."'");
    if(dbNumRows($qs) == 1)
    {
      list($sc) = dbFetchArray($qs);
    }

    return $sc;
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

      $qs = dbQuery("INSERT INTO tbl_order_pos (".$fields.") VALUES (".$values.")");
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




  public function update($id, array $ds = array())
  {
    $sc = FALSE;
    if(!empty($ds))
    {
      $set = "";
      $i = 1;
      foreach($ds as $field => $value)
      {
        $set .= $i == 1 ? $field." = '".$value."'" : ", ".$field." = '".$value."'";
        $i++;
      }

      $sc = dbQuery("UPDATE tbl_order_pos SET ".$set." WHERE id_order_pos = ".$id);
      if($sc === FALSE)
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

      $qs = dbQuery("INSERT INTO tbl_order_pos_detail (".$fields.") VALUES (".$values.")");
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


  public function updateDetail($id, array $ds = array())
  {
    $sc = FALSE;
    if(!empty($ds))
    {
      $set = "";
      $i = 1;
      foreach($ds as $field => $value)
      {
        $set .= $i == 1 ? $field." = '".$value."'" : ", ".$field." = '".$value."'";
        $i++;
      }

      $sc = dbQuery("UPDATE tbl_order_pos_detail SET ".$set." WHERE id_order_pos_detail = ".$id);
      if($sc === FALSE)
      {
        $this->error = dbError();
      }
    }

    return $sc;
  }



  public function deleteDetail($id)
  {
    return dbQuery("DELETE FROM tbl_order_pos_detail WHERE id_order_pos_detail = ".$id);
  }


  public function getDetailOrder($id)
  {
    return dbQuery("SELECT * FROM tbl_order_pos_detail WHERE id_order_pos = '".$id."'");
  }




  public function getDetails($id)
  {
    return dbQuery("SELECT * FROM tbl_order_pos_detail WHERE id_order_pos = '".$id."'");
  }




  public function getDetail($id)
  {
    $sc = FALSE;
    $qs = dbQuery("SELECT * FROM tbl_order_pos_detail WHERE id_order_pos_detail = '".$id."'");
    if(dbNumRows($qs) == 1)
    {
      $sc = dbFetchObject($qs);
    }

    return $sc;
  }




  public function getNotSaveId()
  {
    $sc = FALSE;
    $qs = dbQuery("SELECT id_order_pos FROM tbl_order_pos WHERE status = 0");
    if(dbNumRows($qs) > 0)
    {
      list($sc) = dbFetchArray($qs);
    }

    return $sc;
  }



  public function getExistsDetail($id_order, $id_pa, $price, $pdisc, $adisc)
  {
    $sc  = FALSE;
    $qr  = "SELECT * FROM tbl_order_pos_detail ";
    $qr .= "WHERE id_order_pos = '".$id_order."' ";
    $qr .= "AND id_product_attribute = '".$id_pa."' ";
    $qr .= "AND price = '".$price."' ";
    $qr .= "AND pdisc = '".$pdisc."' AND adisc = '".$adisc."' ";

    $qs = dbQuery($qr);

    if(dbNumRows($qs) == 1)
    {
      $sc = dbFetchObject($qs);
    }

    return $sc;
  }





  public function getTotalProduct($id)
  {
    $qty = 0;
    $qs = dbQuery("SELECT count(id_order_pos_detail) FROM tbl_order_pos_detail WHERE id_order_pos = '".$id."'");
    if(dbNumRows($qs) == 1)
    {
      list($qty) = dbFetchArray($qs);
    }
    return $qty;
  }






  public function getTotalQty($id)
  {
    $qty = 0;
    $qs = dbQuery("SELECT SUM(qty) FROM tbl_order_pos_detail WHERE id_order_pos = '".$id."'");
    if(dbNumRows($qs) == 1)
    {
      list($qty) = dbFetchArray($qs);
    }

    return is_null($qty) ? 0 : $qty;
  }



  public function getTotalOrder($id)
  {
    $amount = 0;
    $qs = dbQuery("SELECT SUM(total_amount) AS amount FROM tbl_order_pos_detail WHERE id_order_pos = '".$id."'");
    if(dbNumRows($qs) == 1)
    {
      list($amount) = dbFetchArray($qs);
    }

    return is_null($amount) ? 0 : $amount;
  }



  public function pauseBill($id)
  {
    return dbQuery("UPDATE tbl_order_pos SET status = 2 WHERE id_order_pos = ".$id);
  }


  public function getPauseList()
  {
    return dbQuery("SELECT * FROM tbl_order_pos WHERE status = 2");
  }

  //-----------------  New Reference --------------//
	public function getNewReference()
	{
		$Y		= date('y');
		$M		= date('m');
		$prefix = getConfig('PREFIX_POS_BILL');
		$runDigit = getConfig('RUN_DIGIT'); //--- รันเลขที่เอกสารกี่หลัก
		$preRef = $prefix . '-' . $Y . $M;
		$qs = dbQuery("SELECT MAX(reference) AS reference FROM tbl_order_pos WHERE reference LIKE '".$preRef."%' ORDER BY reference DESC");
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




  public function updateOrderAmount($id)
  {
    $amount = $this->getTotalOrder($id);
    $qr = "UPDATE tbl_order_pos SET order_amount = ".$amount." WHERE id_order_pos = ".$id;

    return dbQuery($qr);
  }




  public function sold(array $ds = array())
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

      $sc = dbQuery("INSERT INTO tbl_order_detail_sold (".$fields.") VALUES (".$values.")");
    }

    return $sc;
  }




}
 ?>
