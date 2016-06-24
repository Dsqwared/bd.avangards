<?php 



require("Db.class.php");
$db = new Db();

header('Content-Type: application/json');

if (isset($_GET['id'])){

	$id=$_GET['id'];
	
	if (isset($_GET['message'])){

		   $db->query("INSERT INTO comments(id,id_mn,date,author,message) 
                        VALUES(NULL,:id,NULL,:author,:message)",
                              array(
                              'id'=>$id,
                              'author'=>isset($_COOKIE['bd_user'])?$_COOKIE['bd_user']:'anonym',
                              'message'=>$_GET['message'],
                              ));
		   echo json_encode(array('code'=>0));
		   exit();
	}



	
	$sql="SELECT date,author,message FROM comments WHERE id_mn=:id_mn ORDER BY date";

	$db->bind("id_mn",$id);
	
	$comments = $db->query($sql);

	echo json_encode($comments);
	exit();
}
echo('[]');






 ?>