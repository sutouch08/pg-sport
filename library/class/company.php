<?php 
//require_once SRV_ROOT."library/config.php";
//require_once SRV_ROOT."library/database.php";
//require SRV_ROOT."library/functions.php";
class company{
	public $name;
	public $full_name;
	public $phone;
	public $address;
	public $post_code;
	public $email;
	public $product_new;
	public $tax_id;
	public $fax;
	public $email_to_neworder;//---emailเมื่อมีการสั่งออร์เดอร์
	public function __construct(){
		list($name) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_NAME'"));
		list($full_name) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_FULL_NAME'"));
		list($phone) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_PHONE'"));
		list($fax) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_FAX_NUMBER'"));
		list($address) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_ADDRESS'"));
		list($post_code) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_POST_CODE'"));
		list($email) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_EMAIL'"));
		list($product_new) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'NEW_PRODUCT_DATE'"));
		list($default_active) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'DEFAULT_NEW_CUSSTOMER'"));
		list($tax_id) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_TAX_ID'"));
		list($email_to_neworder) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'EMAIL_TO_NEW_ORDER'"));
		$this->full_name = $full_name;
		$this->name = $name;
		$this->phone = $phone;
		$this->fax = $fax;
		$this->address = $address;
		$this->post_code = $post_code;
		$this->tax_id = $tax_id;
		$this->email = $email;
		$this->product_new = $product_new;
		$this->default_active = $default_active;
		$this->email_to_neworder = $email_to_neworder;
		}
	public function setCompany($name, $phone, $address, $email, $product_new)
	{
		$this->name = $name;
		$this->phone = $phone;
		$this->address = $address;
		$this->email = $email;
		$this->product_new = $product_new;
		dbQuery("UPDATE tbl_config SET value = '". $this->name."WHERE config_name = 'COMPANY_NAME'");
		dbQuery("UPDATE tbl_config SET value = '". $this->phone."WHERE config_name = 'COMPANY_PHONE'");
		dbQuery("UPDATE tbl_config SET value = '". $this->address."WHERE config_name = 'COMPANY_ADDRESS'");
		dbQuery("UPDATE tbl_config SET value = '". $this->product_new."WHERE config_name = 'NEW_PRODUCT_DATE'");
	}
	public function getCompany()
	{
		list($name) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_NAME'"));
		list($phone) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_PHONE'"));
		list($address) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_ADDRESS'"));
		list($email) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'COMPANY_EMAIL'"));
		list($product_new) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'NEW_PRODUCT_DATE'"));
		list($default_active) = dbFetchArray(dbQuery("SELECT value FROM tbl_config WHERE config_name = 'DEFAULT_NEW_CUSSTOMER'"));
		$this->name = $name;
		$this->phone = $phone;
		$this->address = $address;
		$this->email = $email;
		$this->product_new = $product_new;
		$this->default_active = $default_active;
	}
}




?>