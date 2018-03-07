<?php
function setToken($token)
{
	setcookie("file_download_token", $token, time() +3600,"/");
}


 ?>
