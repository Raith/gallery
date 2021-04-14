<?php
	$data = file_get_contents("php://input");	

	echo $data;

	if(!empty($data)){	

		$filename = "imgur";
		$uploadFolder = "upload";

		$file = fopen($uploadFolder . "/" . $filename, "a");
		fwrite($file, $data . "\r\n");
		fclose($file);
	}
?>