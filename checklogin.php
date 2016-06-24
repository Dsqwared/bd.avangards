<?php 


function checklogin($login=null,$pass=null){
  require_once("Db.class.php");
  $db = new Db('parser/avangards_settings.ini.php');
	$login = isset($_GET['user']) ? $_GET['user'] : null;
    $user_id = str_replace("user", "", $login);
	$pass = isset($_GET['pass']) ? $_GET['pass'] : null;
    $valid_users=array('admin'=>'mich87',
      //'user1'=>'g46u3h',
      //'user2'=>'h4hgro',
      //'user3'=>'n453h32',
      //'user4'=>'m257bv2',
      //'user5'=>'5c325xx4kjasdjhjaghsdasd',
      );

  if (isset($_COOKIE['bd_usr']) && (isset($_GET['user'])))
  { if ($_COOKIE['bd_usr'] != $_GET['user']) {
     setcookie('bd_usr', '', 1);
     setcookie('bd_usr_hash', '', 1);
      header("Location: http://".$_SERVER['SERVER_NAME']."/".$_SERVER['REQUEST_URI']);
    exit();

  }
  }


  if(!isset($_COOKIE['bd_usr'])){
    if ($login==null||$pass==null) return false;


    $sql = "SELECT u.user_pass, u.user_nicename from `wp_users` as u WHERE u.id=$user_id";

    $sql_controll = $db->query($sql);

    $user_pass_bd = $sql_controll[0]['user_pass'];
    $user_login_bd = $sql_controll[0]['user_nicename'];

    echo $user_pass_bd."<br><br>";
    echo $pass."<br><br>";

    if (isset($pass) && ($pass == $user_pass_bd)) {
      setcookie("bd_usr", $login);
      setcookie("bd_usr_hash", sha1($login.'sAuLt!G$#'.date('d')));
      return true;
    } else return false;
  // 86400 = 1 day
  } else {
    if(!isset($_COOKIE['bd_usr_hash'])) return false;

    $hash=$_COOKIE['bd_usr_hash'];
    $rez=($hash==sha1($_COOKIE['bd_usr'].'sAuLt!G$#'.date('d')));
    if (!$rez) {setcookie('bd_usr','',time()-300); }
    return $rez;
  }
  


}