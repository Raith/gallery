<?php
	if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
		$fileTmpPath = $_FILES["image"]["tmp_name"];
		$fileName = $_FILES["image"]["name"];
		$fileNameCmps = explode(".", $fileName);
		$fileExtension = strtolower(end($fileNameCmps));
		$allowedfileExtensions = array("jpg", "jpeg", "gif", "png", "tif", "tiff", "bmp", "ico");
		
		if (in_array($fileExtension, $allowedfileExtensions)) {
			
			$newFileName = substr(md5(time() . $fileName), 0, 16);
		
			for ($i = 0; $i < strlen($newFileName); $i++) {
				if (mt_rand() % 2 == 0) {
					$newFileName[$i] = strtoupper($newFileName[$i]);
				} 
			}

			$newFileName = $newFileName . '.' . $fileExtension;
			$uploadFileDir = "img/";
			$dest_path = $uploadFileDir . $newFileName;
 
			if(move_uploaded_file($fileTmpPath, $dest_path)) {
				echo json_encode($newFileName);
			}
		}
	}

?>