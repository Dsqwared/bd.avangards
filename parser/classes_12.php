<?php
require_once("Db.class.php");

/**
* parser class to do full work with bd
*/
class Parser
{
    private $db=null;
    private $logged=false;
    private $username='belik';
    private $password='123456789';
    function __construct($loginkvadrat=true)
    {
        $this->db = new Db();
        if ($loginkvadrat) {
	        $this->login();
	        $this->logged=$this->checklogin();
        }
    }
    private function Log($message,$type=0){
        $types=array('','[WARNING]','[>>>ERROR<<<]');
        echo(date('[D d H:i:s]').$types[$type].' - '.$message.PHP_EOL);
    }
    private function checklogin(){
        $this->Log('Checking if already logged...');
        $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,"http://www.kg-r.com/base-/");
            curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
            curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $content = curl_exec( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );
  
        if (preg_match('/<a id="login-button">/', $content)) {
            return false;
        } else if (preg_match('/href="\/logout">Выход/',$content)){
            return true;
        }
    }

    public function login(){
        if ($this->logged) return $this->Log('Alredy logged... skip');
        $this->Log('Login...');
        $ch = curl_init();

          $headers=array(
                        'Referer:http://www.kg-r.com/',
                        'X-Requested-With:XMLHttpRequest',
                );

        curl_setopt($ch, CURLOPT_URL,"http://www.kg-r.com/auth.php?admin=yes&user_login={$this->username}&password={$this->password}&rememberme=1");
        curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
        curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         
            $content = curl_exec( $ch );
                $err     = curl_errno( $ch );
                $errmsg  = curl_error( $ch );
                $header  = curl_getinfo( $ch );
                curl_close( $ch );

        if (preg_match('/com\/my\/#1504/',$content)){
                    $this->logged=true;
                } else {
                    $this->logged=false;
                        var_dump($header);
                        var_dump($errmsg);
                        var_dump($err);
                        var_dump($content);
                        exit();
                }
    }

    public function parse(){
        if (!$this->logged) return $this->Log('Login required!',2);

        $this->grep_data(1,1,1);
        $this->grep_data(2,1,1);
        $this->grep_data(3,1,1);
        $this->grep_data(4,1,1);
        $this->grep_data(5,1,1);
    }

    function grep_data($op=1,$mseg=1,$page=1){
        $ch = curl_init();

        $headers=array(
                            'Host:www.kg-r.com',
                            'Referer:http://www.kg-r.com/api/base?region=4&locality=3574&type=city&marketSegment=1&operation=1&newAds=0&page=0',
                            'X-Requested-With:XMLHttpRequest',
                    );
        $this->Log("Start loading...");
              

            curl_setopt($ch, CURLOPT_URL,"http://www.kg-r.com/api/base?region=4&locality=3574&type=city&marketSegment={$mseg}&operation={$op}&newAds=0&page=$page");
            curl_setopt($ch, CURLOPT_COOKIEFILE, "cookie.txt");
            curl_setopt($ch, CURLOPT_COOKIEJAR, "cookie.txt");
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
            // curl_setopt($ch, CURLOPT_POSTFIELDS,'_method=POST&data%5BUser%5D%5Bemail%5D=sx000%401-tn.com&data%5BUser%5D%5Bpassword%5D=NAQrdc4w2eoH4Tw0edJOoSZ7&data%5BUser%5D%5Bremember_me%5D=0');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

             
                $content = curl_exec( $ch );
                    $err     = curl_errno( $ch );
                    $errmsg  = curl_error( $ch );
                    $header  = curl_getinfo( $ch );
                    curl_close( $ch );

                            // var_dump($header);
                            // var_dump($errmsg);
                            // var_dump($err);
                            // var_dump($content);
                    if (strlen($content)<500) return $this->Log('Ends page',1);

                
        $this->Log("Loaded...");

                    $dcd=json_decode($content,true);
                    @unlink(dirname(__FILE__).'/grepped.json');
                    @unlink(dirname(__FILE__).'/decoded.json');
				//print_r($dcd);exit;
                    if ($dcd!=null) {
                        //file_put_contents('grepped.json', $dcd['ads']);
                        //exec('nodehack.js');
                        $this->sql_push($dcd['ads']);
                        $this->Log("sql...");

                        if ($page<2)
                            $this->grep_data($op,$mseg,$page+1);
                    } else $this->Log("Server sent bullshit...",1);
    }                               

    function sql_push($decoded){
       // $rfile=dirname(__FILE__).'/decoded.json';

       // if (file_exists($rfile)){
           // $decodedc=file_get_contents($rfile);
          //  $decoded=json_decode($decodedc,true);

                if ($decoded!=null) {
                    $this->Log('Loaded:'.count($decoded).'...');
					$base = 'http://kg-base.st0.kg-r.com/';
                    foreach ($decoded as $one) {
                            $id=$one['main']['id'];
                            $operation=$one['main']['operation'][0]['id'];
                            $marketSegment=$one['main']['marketSegment'][0]['id'];
                            $estateType=$one['main']['estateType'][0]['title'];
                            $date=strtotime($one['main']['insertDate'])+3600;
                            $region=$one['terrain']['region'][0]["title"];
                            $city=$one['terrain']['city'][0]["title"];
                            $adminDistrict=isset($one['terrain']['adminDistrict'][0])?$one['terrain']['adminDistrict'][0]["title"]:'---';
                            if (isset($one['terrain']['district'][0])){
                                if ($one['terrain']['district'][0] == "12 квартал") $one['terrain']['district'][0] = "12-й квартал";
                            }
                            $district=isset($one['terrain']['district'][0])?$one['terrain']['district'][0]["title"]:'---';
                            $street=isset($one['terrain']['street'][0])?$one['terrain']['street'][0]["title"]:'---';
                            $rooms=$one['params']['p1'][0];
							$area=$one['params']['p5'][0];
							$area_json=addslashes($one['params']['p5'][0].' / '.$one['params']['p6'][0].' / '.$one['params']['p7'][0]);
							$floor=$one['params']['p3'][0];
							$max_floor=$one['params']['p4'][0];
                            $price=$one['params']['price'];
                            $desc=$one['info']['text'];
                            $url=$one['info']['url'];
                            $phones=implode(' ',$one['info']['contacts']['phones']);
                            $flag=isset($one['info']["mark"][0])?$one['info']["mark"][0]['id']:0;
							$p_links = isset($one['info']['photo'])?$one['info']['photo']:null;
							
							$images = array();
							if(!empty($p_links) && is_array($p_links)) {
								foreach($p_links as $p_link) :
									$images[] = $base.$p_link;
								endforeach;
							}
							
							$photos_json = !empty($images) ? addslashes(implode(':::',$images)) : '';
							
							$sql="SELECT COUNT(*) as count from parsed WHERE id=:id AND price=:price";
							$this->db->bind("id",$id);
							$this->db->bind("price",$price);
								$sql.=" AND phones = '$phones'";
	
							 //$sql="SELECT * from parsed WHERE id=:id";
            				  //$this->db->bind("id",$id);

                        $seems=$this->db->query($sql);
                           
                        if ($seems[0]['count'] != 0)  {
						
						return $this->Log('Dublicate info!');
						} else {
							//print_r(addslashes($photos_json));exit;
                            // echo("$id $operation $estateType $street $rooms $phones $price ".PHP_EOL);
                            
                        $this->db->query("INSERT INTO parsed(id,frm,region,city,adistrict,district,street,rooms,floor,max_floor,area,area_json,operation,marketSegment,estateType,adDate,phones,price,text,url,images,flags) 
                        VALUES(:id,:frm,:region,:city,:adistrict,:district,:street,:rooms,:floor,:max_floor,:area,:area_json,:operation,:marketSegment,:estateType,:adDate,:phones,:price,:text,:url,:images,:flags) ON DUPLICATE KEY UPDATE adDate=VALUES(adDate)",
                              array(
                              'id'=>$id,
                              'frm'=>1,
                              'region'=>$region,
                              'city'=>$city,
                              'adistrict'=>$adminDistrict,
                              'district'=>$district,
                              'street'=>$street,
                              'rooms'=>$rooms,
							  'floor'=>$floor,
							  'max_floor'=>$max_floor,
							  'area'=>$area,
							  'area_json'=>$area_json,
                              'operation'=>$operation,
                              'marketSegment'=>$marketSegment,
                              'estateType'=>$estateType,
                              'adDate'=>$date,
                              'phones'=>$phones,
                              'price'=>$price,
                              'text'=>$desc,
                              'url'=>$url,
							  'images'=>$photos_json,
                              'flags'=>$flag,
                              ));
                            // var_dump($decoded);
							}
                    }
                } else $this->Log('Parsing error...',2);
       // } else $this->Log('Hack algorythm error...',2);
    }

   /* function sql_put_safe($id,$date,$price,$rooms,$operation,$city,$district,$street,$desc,$phones){
                            // $id=$one['main']['id'];
                            // $operation=$one['main']['operation'][0]['id'];
                            $marketSegment=1;
                            $estateType='---';
                            $date=strtotime($date);
                            $region='---';
                            // $city=$one['terrain']['city'][0]["title"];
                            $adminDistrict='---';
                            // $district='---';
                            // $street=isset($one['terrain']['street'][0])?$one['terrain']['street'][0]["title"]:'---';
                            // $rooms=$one['params']['p1'][0];
                            // $price=$one['params']['price'];
                            // $desc=$one['info']['text'];
                            $url='';
                            $flag=0;
							
                            // echo("$id $operation $estateType $street $rooms $phones $price ".PHP_EOL);

                         $sql="SELECT * from parsed WHERE price=:price";
							$this->db->bind("price",$price);
                         foreach ($phones as $key=>$phone) {
							$this->db->bind("phone".$key,"%$phone%");
							if ($key)
								$sql.=' AND phones LIKE :phone'.$key;
							else
								$sql.=' AND phones LIKE :phone'.$key;
                         }

                        $seems=$this->db->query($sql);
                           
                        if (count($seems)) return $this->Log('Dublicate info!');
                        // return $this->Log('Debug insert');
                            $phones=implode(' ',$phones);
                        $this->db->query("INSERT INTO parsed(id,frm,region,city,adistrict,district,street,rooms,operation,marketSegment,estateType,adDate,phones,price,text,url,flags) 
                        VALUES(:id,:frm,:region,:city,:adistrict,:district,:street,:rooms,:area,:area_json,:operation,:marketSegment,:estateType,:adDate,:phones,:price,:text,:url,:flags) ON DUPLICATE KEY UPDATE adDate=VALUES(adDate)",
                              array(
                              'id'=>$id,
                              'frm'=>2,
                              'region'=>$region,
                              'city'=>$city,
                              'adistrict'=>$adminDistrict,
                              'district'=>$district,
                              'street'=>$street,
                              'rooms'=>$rooms,
                              'operation'=>$operation,
                              'marketSegment'=>$marketSegment,
                              'estateType'=>$estateType,
                              'adDate'=>$date,
                              'phones'=>$phones,
                              'price'=>$price,
                              'text'=>$desc,
                              'url'=>$url,
                              'flags'=>$flag,
                              ));
                            // var_dump($decoded);
                 
    }*/
     function sql_mail_put_safe($id,$date,$price,$rooms,$operation,$city,$estate_type='',$floor=0,$max_floor=0,$district,$street,$desc,$phones,$flags=0,$area,$area_json){
                            // $id=$one['main']['id'];
                            // $operation=$one['main']['operation'][0]['id'];
                            $marketSegment=1;
                            $estateType=$estate_type;
                            $date=strtotime($date);
                            $region='Днепропетровская обл.';
                            // $city=$one['terrain']['city'][0]["title"];
                            $adminDistrict='---';
                            // $district='---';
                            // $street=isset($one['terrain']['street'][0])?$one['terrain']['street'][0]["title"]:'---';
                            // $rooms=$one['params']['p1'][0];
                            // $price=$one['params']['price'];
                            // $desc=$one['info']['text'];
                            $url='';
                            $flag=$flags;
                            // echo("$id $operation $estateType $street $rooms $phones $price ".PHP_EOL);
						 $phones=implode(' ',$phones);
						 $street1 = addslashes($street);
                         $sql="SELECT COUNT(*) as count from parsed WHERE price=:price AND rooms=:rooms";
              $this->db->bind("price",$price);
              $this->db->bind("rooms",$rooms);
							 $sql.=" AND phones = '$phones' AND street = '$street1'";

                        $seems=$this->db->query($sql);
                          // print_r($seems);exit;
                        if ($seems[0]['count'] != 0) { 
                          // var_dump($seems[0]['street']);
                          // var_dump($seems[0]['text']);
                          return $this->Log('Dublicate info!'); 
                        } else {
                        // return $this->Log('Debug insert');
                           
                        $this->db->query("INSERT INTO parsed(id,frm,region,city,adistrict,district,street,rooms,area,area_json,floor,max_floor,operation,marketSegment,estateType,adDate,phones,price,text,url,flags) 
                        VALUES(:id,:frm,:region,:city,:adistrict,:district,:street,:rooms,:area,:area_json,:floor,:max_floor,:operation,:marketSegment,:estateType,:adDate,:phones,:price,:text,:url,:flags) ON DUPLICATE KEY UPDATE adDate=VALUES(adDate)",
                              array(
                              'id'=>$id,
                              'frm'=>3,
                              'region'=>$region,
                              'city'=>$city,
                              'adistrict'=>$adminDistrict,
                              'district'=>$district,
                              'street'=>$street,
                              'rooms'=>$rooms,
							 'area'=>$area,
							  'area_json'=>$area_json,
							  'floor'=>$floor,
							  'max_floor'=>$max_floor,
                              'operation'=>$operation,
                              'marketSegment'=>$marketSegment,
                              'estateType'=>$estateType,
                              'adDate'=>$date,
                              'phones'=>$phones,
                              'price'=>$price,
                              'text'=>$desc,
                              'url'=>$url,
                              'flags'=>$flag,
                              ));
                            // var_dump($decoded);
							echo $city;
                 }
    }
	
	function sql_ray2_put_safe($id,$date,$price,$rooms,$operation,$city,$estateType,$floor=0,$max_floor=0,$district,$street,$desc,$phones,$flags=0,$area,$area_json,$url,$adminDistrict){
	
                            $marketSegment=1;
                            $date=strtotime($date);
                            $region='Днепропетровская обл.';
                            $flag=$flags;
 					$street1 = addslashes($street);
 				 $phones=implode(' ',$phones);
                         $sql="SELECT COUNT(*) as count from parsed WHERE price=:price AND rooms=:rooms";
              $this->db->bind("price",$price);
              $this->db->bind("rooms",$rooms);
			  $sql.=" AND phones = '$phones' AND street = '$street1'";

                        $seems=$this->db->query($sql);
                           
                        if ($seems[0]['count'] != 0) { 
                          // var_dump($seems[0]['street']);
                          // var_dump($seems[0]['text']);
                          return $this->Log('Dublicate info!'); 
                        } else {


                        // return $this->Log('Debug insert');
                            
                        $this->db->query("INSERT INTO parsed(id,frm,region,city,adistrict,district,street,rooms,area,area_json,floor,max_floor,operation,marketSegment,estateType,adDate,phones,price,text,url,flags) 
                        VALUES(:id,:frm,:region,:city,:adistrict,:district,:street,:rooms,:area,:area_json,:floor,:max_floor,:operation,:marketSegment,:estateType,:adDate,:phones,:price,:text,:url,:flags) ON DUPLICATE KEY UPDATE adDate=VALUES(adDate)",
                              array(
                              'id'=>$id,
                              'frm'=>2,
                              'region'=>$region,
                              'city'=>$city,
                              'adistrict'=>$adminDistrict,
                              'district'=>$district,
                              'street'=>$street,
                              'rooms'=>$rooms,
							 'area'=>$area,
							  'area_json'=>$area_json,
							  'floor'=>$floor,
							  'max_floor'=>$max_floor,
                              'operation'=>$operation,
                              'marketSegment'=>$marketSegment,
                              'estateType'=>$estateType,
                              'adDate'=>$date,
                              'phones'=>$phones,
                              'price'=>$price,
                              'text'=>$desc,
                              'url'=>$url,
                              'flags'=>$flag,
                              ));
                            // var_dump($decoded);
                 }
			}	
			 
		 function setWatched($post_id) {
			$date = date("Y-m-d H-i-s");
			 $this->db->query("UPDATE parsed SET `is_watched`=1,`time_watched`='$date' WHERE `id_mn`='$post_id'");
		}
		
    	 function setStats() {
		 	$stats_all = $this->db->query("SELECT COUNT(*) as count FROM parsed");
			$stats_all = $stats_all[0]['count'];
			$stats_city_dnepr = $this->db->query("SELECT COUNT(*) as count FROM parsed WHERE city='Днепропетровск'");
			$stats_city_dnepr = $stats_city_dnepr[0]['count'];
			$stats_city_dneprzh = $this->db->query("SELECT COUNT(*) as count FROM parsed WHERE city='Днепродзержинск'");
			$stats_city_dneprzh = $stats_city_dneprzh[0]['count'];
			$stats_city_novomos = $this->db->query("SELECT COUNT(*) as count FROM parsed WHERE city='Новомосковск'");
			$stats_city_novomos  = $stats_city_novomos[0]['count'];
			$stats_providers = $this->db->query("SELECT frm,COUNT(*) as count FROM parsed GROUP BY frm");
			$stats_providers = serialize($stats_providers);
			$stats_flats = $this->db->query("SELECT estateType,COUNT(*) as count FROM parsed GROUP BY estateType");
			$stats_flats = serialize($stats_flats);
			$stats_operations = $this->db->query("SELECT operation,COUNT(*) as count FROM parsed GROUP BY operation");
			$stats_operations = serialize($stats_operations);
			$date = date('Y-m-d H:i:s');
			$this->db->query("UPDATE stats SET `stats_all`='$stats_all',`stats_dnepropetrovsk`='$stats_city_dnepr',`stats_dneprodzerzhinsk`='$stats_city_dneprzh',`stats_novomoskovsk`='$stats_city_novomos',`stats_providers`='$stats_providers',`stats_flats`='$stats_flats',`stats_operations`='$stats_operations',`time`='$date' WHERE id=1");
		 }
		 
		  function getPhotosArchived($post_id) {

				$pathdir='tmp/'; // путь к папке, файлы которой будем архивировать
				$nameArhive = $pathdir.'archive/'.$post_id.'.zip'; //название архива
				
				$images = explode(',', $_GET['array']);
				$prep_images = [];
				
				foreach($images as $key=>$image) {
					$raw = file_get_contents($image);
					$fp = fopen('tmp/'.$post_id.'_'.$key.'.jpg','x');
					fwrite($fp, $raw);
					fclose($fp);
					$prep_images[] = 'tmp/'.$post_id.'_'.$key.'.jpg'; 
				}
				
				
				
				$zip = new ZipArchive; // класс для работы с архивами
				if ($zip -> open($nameArhive, ZipArchive::CREATE) === TRUE){ // создаем архив, если все прошло удачно продолжаем
					$dir = opendir($pathdir); // открываем папку с файлами
					while( $file = readdir($dir)){ // перебираем все файлы из нашей папки
							if (is_file($pathdir.$file) && in_array($pathdir.$file, $prep_images)){ // проверяем файл ли мы взяли из папки
								$zip -> addFile($pathdir.$file, $file); // и архивируем
								echo("Заархивирован: " . $pathdir.$file) , '<br/>';
							}
					}
					$zip -> close(); // закрываем архив.
					
					header("Location: $nameArhive");
					
					$dir = opendir($pathdir);
					while( $file = readdir($dir)){ // перебираем все файлы из нашей папки
							if (is_file($pathdir.$file) && in_array($pathdir.$file, $prep_images)){ // проверяем файл ли мы взяли из папки
									@unlink($pathdir.$file);
							}
					}

  					exit();
				}else{
					die ('Произошла ошибка при создании архива');
				}
		}  
		
		function grab_image($url,$saveto){
				$ch = curl_init ($url);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
				$raw=curl_exec($ch);
				curl_close ($ch);
				if(file_exists($saveto)){
					unlink($saveto);
				}
				$fp = fopen($saveto,'x');
				fwrite($fp, $raw);
				fclose($fp);
			}
}

if(isset($_POST['mode']) && $_POST['mode'] == 'watched') {
	$prs=new Parser(false);
	$prs->setWatched($_POST['id']);
}

if(isset($_GET['action']) && $_GET['action'] == 'archive') {
	$prs=new Parser(false);
	$prs->getPhotosArchived($_GET['id']);
}