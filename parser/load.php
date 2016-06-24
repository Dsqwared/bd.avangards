<?php

 if (!empty($_FILES)) {
 	// var_dump($_FILES);
	if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], dirname(__FILE__).'/pdf/baseparse.xps')) {
	    echo "OK";
	    system('./pdf/raze.sh');
	    system('php recognize.php');
	} else {
	    echo "ERROR";
	}

} else {

?>

<form method="post" enctype="multipart/form-data">
  Please choose a file: <input type="file" name="uploadFile"><br>
  <input type="submit" value="Upload File">
</form>

<?php } ?>