<?php
class address
{
	public $id_address;
	public $id_customer;
	public $alias;
	public $company;
	public $first_name;
	public $last_name;
	public $address1;
	public $address2;
	public $city;
	public $postcode;
	public $phone;
	public $remark;
	public $error = FALSE;
	
	public function __construct($id = '')
	{
		if( $id != '')
		{
			$qs = dbQuery("SELECT * FROM tbl_address WHERE id_address = ".$id);
			if( dbNumRows($qs) == 1 )
			{
				$rs = dbFetchArray($qs);
				$this->id_address 	= $id;
				$this->id_customer 	= $rs['id_customer'];
				$this->alias				= $rs['alias'];
				$this->company		= $rs['company'];
				$this->first_name		= $rs['first_name'];
				$this->last_name		= $rs['last_name'];
				$this->address1		= $rs['address1'];
				$this->address2		= $rs['address2'];
				$this->city				= $rs['city'];
				$this->postcode		= $rs['postcode'];
				$this->phone			= $rs['phone'];
				$this->remark			= $rs['remark'];
			}
			else
			{
				$this->error = 'No address found';
			}
		}
	}
	
	public function insertAddress(array $ds)
	{
		$qs = "INSERT INTO tbl_address (id_customer, alias, company, first_name, last_name, address1, address2, city, postcode, phone, remark) VALUES ";
		$qs .= "(".$ds['id_customer'].", '".$ds['alias']."', '".$ds['company']."', '".$ds['first_name']."', '".$ds['last_name']."', '".$ds['address1']."', '".$ds['address2']."', '".$ds['city']."', '".$ds['postcode']."', '".$ds['phone']."', '".$ds['remark']."')";
		return dbQuery($qs);
	}
	
	public function updateAddress($id, array $ds)
	{
		$qs = "UPDATE tbl_address SET id_customer = ".$ds['id_customer'].",  alias = '".$ds['alias']."', company = '".$ds['company']."', first_name =  '".$ds['first_name']."', ";
		$qs .= "last_name = '".$ds['last_name']."', address1 = '".$ds['address1']."', address2 = '".$ds['address2']."', city = '".$ds['city']."', postcode = '".$ds['postcode']."', phone = '".$ds['phone']."', remark = '".$ds['remark']."'";
		$qs .= " WHERE id_address = ".$id;
		return dbQuery($qs);
	}
	
	public function deleteAddress($id)
	{
		return dbQuery("DELETE FROM tbl_address WHERE id_address = ".$id);	
	}
}/// end class

?>