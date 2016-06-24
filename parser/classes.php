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
    private $password='avangard2008avangard';
    function __construct($loginkvadrat=true)
    {
        $this->db = new Db();
        if ($loginkvadrat) {
	        $this->login();
	        $this->logged=$this->checklogin();
        }
    }
    public function Log($message,$type=0){
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
        if ($this->logged) return $this->Log('Already logged... skip');
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
		
		$this->grep_data(1,2,1);
        $this->grep_data(2,2,1);
        $this->grep_data(3,2,1);
        $this->grep_data(4,2,1);
        $this->grep_data(5,2,1);
		
		$this->grep_data(1,3,1);
        $this->grep_data(2,3,1);
        $this->grep_data(3,3,1);
        $this->grep_data(4,3,1);
        $this->grep_data(5,3,1);
		
		$this->grep_data(1,4,1);
        $this->grep_data(2,4,1);
        $this->grep_data(3,4,1);
        $this->grep_data(4,4,1);
        $this->grep_data(5,4,1);
		
		$this->grep_data(1,5,1);
        $this->grep_data(2,5,1);
        $this->grep_data(3,5,1);
        $this->grep_data(4,5,1);
        $this->grep_data(5,5,1);
		
		
		
		
		$this->grep_data_suburb(1,1,1);
        $this->grep_data_suburb(2,1,1);
        $this->grep_data_suburb(3,1,1);
        $this->grep_data_suburb(4,1,1);
        $this->grep_data_suburb(5,1,1);
		
		$this->grep_data_suburb(1,2,1);
        $this->grep_data_suburb(2,2,1);
        $this->grep_data_suburb(3,2,1);
        $this->grep_data_suburb(4,2,1);
        $this->grep_data_suburb(5,2,1);
		
		$this->grep_data_suburb(1,3,1);
        $this->grep_data_suburb(2,3,1);
        $this->grep_data_suburb(3,3,1);
        $this->grep_data_suburb(4,3,1);
        $this->grep_data_suburb(5,3,1);
		
		$this->grep_data_suburb(1,4,1);
        $this->grep_data_suburb(2,4,1);
        $this->grep_data_suburb(3,4,1);
        $this->grep_data_suburb(4,4,1);
        $this->grep_data_suburb(5,4,1);
		
		$this->grep_data_suburb(1,5,1);
        $this->grep_data_suburb(2,5,1);
        $this->grep_data_suburb(3,5,1);
        $this->grep_data_suburb(4,5,1);
        $this->grep_data_suburb(5,5,1);
    }

    function grep_data($op=1,$mseg=1,$page=1){
        $ch = curl_init();

        $headers=array(
                            'Host:www.kg-r.com',
                            'Referer:http://www.kg-r.com/api/base?region=4&locality=3574&type=city&marketSegment=1&operation=1&newAds=0&page=0',
                            'X-Requested-With:XMLHttpRequest',
                    );
        $this->Log("Start loading...");
		
             // &date%5B0%5D=period&date%5B1%5D=0

            curl_setopt($ch, CURLOPT_URL,"http://www.kg-r.com/api/base?region=4&locality=3574&type=city&marketSegment={$mseg}&operation={$op}&newAds=0&page=$page&date[0]=period&date[1]=0");
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
	
	 function grep_data_suburb($op=1,$mseg=1,$page=1){
	 
	 //for($i=1;$i<=50;$i++) {
	 //$page = $i;
        $ch = curl_init();

        $headers=array(
                            'Host:www.kg-r.com',
                            'Referer:http://www.kg-r.com/api/base?region=4&locality=3574&type=suburb&marketSegment=1&operation=1&newAds=0&page=0',
                            'X-Requested-With:XMLHttpRequest',
                    );
        $this->Log("Start loading...");
		
             // &date%5B0%5D=period&date%5B1%5D=0

            curl_setopt($ch, CURLOPT_URL,"http://www.kg-r.com/api/base?region=4&locality=3574&type=suburb&marketSegment={$mseg}&operation={$op}&newAds=0&page=$page&date[0]=period&date[1]=0");
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
					
				//	}
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
							
                            
							
							if(!empty($one['terrain']['city'])) {
								$city=$one['terrain']['city'][0]["title"];
								$district=isset($one['terrain']['district'][0])?$one['terrain']['district'][0]["title"]:'---';
							} else {
								$city=$one['terrain']['town'][0]["title"];
								$district=isset($one['terrain']['area'][0]["title"])?$one['terrain']['area'][0]["title"]:'---';
							}
							
                            $adminDistrict=isset($one['terrain']['adminDistrict'][0])?$one['terrain']['adminDistrict'][0]["title"]:'---';
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
							
							$send_phone = $one['info']['contacts']['phones'][0];
							
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
							  
							
							//echo $last_id; echo $district;
							
							if($operation == 1 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && ($estateType == "Квартира" || $estateType == "Квартира в новострое")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
						
								if($seems[0]['count'] != 0) {
								echo "111";
									$this->sendSMSSOAP($last_id, $district, $send_phone);
								 }
							}  else if($operation == 2 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && $price >= 2000 && ($estateType == "Квартира" || $estateType == "Квартира в новострое" || $estateType == "Дом" || $estateType == "Часть дома")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts_rent` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
						
								if($seems[0]['count'] != 0) {
								echo "112";
									$this->sendSMSSOAPRent($last_id, $district, $send_phone);
								 }
							} else if($operation == 1 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && $price >= 25000 && ($estateType == "Дом" || $estateType == "Часть дома")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts_houses` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
							
								if($seems[0]['count'] != 0) {
								echo "113";
									$this->sendSMSSOAPHouse($last_id, $district, $send_phone);
								 }
							}
							}//if dublicate
                    }//foreach
                } else $this->Log('Parsing error...',2); // decoded
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
							
							if($estate_type == "Квартира-суточн") {
								$estateType = "Квартира";
								$operation = 3;
							} else {
								$estateType = $estate_type;
							}
                            
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
							//$send_phone = $phones[0];
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
							  
							  $send_phone = $phones[0];
							  
							  if($operation == 1 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && ($estateType == "Квартира" || $estateType == "Квартира в новострое")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
						
								if($seems[0]['count'] != 0) {
								echo "111";
									$this->sendSMSSOAP($last_id, $district, $send_phone);
								 }
							}  else if($operation == 2 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && $price >= 2000 && ($estateType == "Квартира" || $estateType == "Квартира в новострое" || $estateType == "Дом" || $estateType == "Часть дома")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts_rent` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
						
								if($seems[0]['count'] != 0) {
								echo "112";
									$this->sendSMSSOAPRent($last_id, $district, $send_phone);
								 }
							} else if($operation == 1 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && $price >= 25000 && ($estateType == "Дом" || $estateType == "Часть дома")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts_houses` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
							
								if($seems[0]['count'] != 0) {
								echo "113";
									$this->sendSMSSOAPHouse($last_id, $district, $send_phone);
								 }
							}
							  
                            // var_dump($decoded);
							echo $city;
                 }
    }
	
	function sql_ray2_put_safe($id,$date,$price,$rooms,$operation,$city,$estateType,$floor=0,$max_floor=0,$district,$street,$desc,$phones,$flags=0,$area,$area_json,$url,$adminDistrict,$photos_json){
	
							$send_phone = $phones[0];
                            $marketSegment=1;
                            $date=strtotime($date)-3600;
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
                            
                        $this->db->query("INSERT INTO parsed(id,frm,region,city,adistrict,district,street,rooms,area,area_json,floor,max_floor,operation,marketSegment,estateType,adDate,phones,price,text,url,images,flags) 
                        VALUES(:id,:frm,:region,:city,:adistrict,:district,:street,:rooms,:area,:area_json,:floor,:max_floor,:operation,:marketSegment,:estateType,:adDate,:phones,:price,:text,:url,:images,:flags) ON DUPLICATE KEY UPDATE adDate=VALUES(adDate)",
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
							  'images'=>$photos_json,
                              'flags'=>$flag,
                              ));
							  
							
						if($operation == 1 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && ($estateType == "Квартира" || $estateType == "Квартира в новострое")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
						
								if($seems[0]['count'] != 0) {
								echo "111";
									$this->sendSMSSOAP($last_id, $district, $send_phone);
								 }
							}  else if($operation == 2 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && $price >= 2000 && ($estateType == "Квартира" || $estateType == "Квартира в новострое" || $estateType == "Дом" || $estateType == "Часть дома")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts_rent` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
						
								if($seems[0]['count'] != 0) {
								echo "112";
									$this->sendSMSSOAPRent($last_id, $district, $send_phone);
								 }
							} else if($operation == 1 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && $price >= 25000 && ($estateType == "Дом" || $estateType == "Часть дома")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts_houses` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
							
								if($seems[0]['count'] != 0) {
								echo "113";
									$this->sendSMSSOAPHouse($last_id, $district, $send_phone);
								 }
							}
                            // var_dump($decoded);
                 }
			}	
			
		function sql_baza_broker_put_safe($id,$date,$price,$rooms,$operation,$city,$estateType,$floor=0,$max_floor=0,$district,$street,$desc,$phones,$flags=0,$area,$area_json,$url,$adminDistrict,$photos_json){
	
							$send_phone = $phones[0];
                            $marketSegment=1;
                            $date=strtotime($date)-3600;
                            $region='Днепропетровская обл.';
                            $flag=$flags;
 					$street1 = addslashes($street);
 				 $phones=implode(' ',$phones);
                         $sql="SELECT COUNT(*) as count from parsed WHERE price=:price AND rooms=:rooms";
              $this->db->bind("price",$price);
              $this->db->bind("rooms",$rooms);
			  $sql.=" AND phones = '$phones'";

                        $seems=$this->db->query($sql);
                           
                        if ($seems[0]['count'] != 0) { 
                          // var_dump($seems[0]['street']);
                          // var_dump($seems[0]['text']);
                          return $this->Log('Dublicate info!'); 
                        } else {


                        // return $this->Log('Debug insert');
                            
                        $this->db->query("INSERT INTO parsed(id,frm,region,city,adistrict,district,street,rooms,area,area_json,floor,max_floor,operation,marketSegment,estateType,adDate,phones,price,text,url,images,flags) 
                        VALUES(:id,:frm,:region,:city,:adistrict,:district,:street,:rooms,:area,:area_json,:floor,:max_floor,:operation,:marketSegment,:estateType,:adDate,:phones,:price,:text,:url,:images,:flags) ON DUPLICATE KEY UPDATE adDate=VALUES(adDate)",
                              array(
                              'id'=>$id,
                              'frm'=>4,
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
							  'images'=>$photos_json,
                              'flags'=>$flag,
                              ));
							  
							
							if($operation == 1 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && ($estateType == "Квартира" || $estateType == "Квартира в новострое")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
						
								if($seems[0]['count'] != 0) {
								echo "111";
									$this->sendSMSSOAP($last_id, $district, $send_phone);
								 }
							}  else if($operation == 2 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && $price >= 2000 && ($estateType == "Квартира" || $estateType == "Квартира в новострое" || $estateType == "Дом" || $estateType == "Часть дома")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts_rent` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
						
								if($seems[0]['count'] != 0) {
								echo "112";
									$this->sendSMSSOAPRent($last_id, $district, $send_phone);
								 }
							} else if($operation == 1 && $city=='Днепропетровск' && $flag !=6 && $flag !=8 && $price >= 25000 && ($estateType == "Дом" || $estateType == "Часть дома")) {
								$last_id = $this->db->lastInsertId();
								$sql="SELECT COUNT(*) as count FROM `sms_districts_houses` WHERE `district`='$district' AND `active`=1";
								$seems=$this->db->query($sql);
							
								if($seems[0]['count'] != 0) {
								echo "113";
									$this->sendSMSSOAPHouse($last_id, $district, $send_phone);
								 }
							}
                 }
			}	
			 
		 function setWatched($user_id,$post_id) {
			$date = date("Y-m-d H-i-s");
             $db_arr = array(
                 'post_id' => (int)$post_id,
                 'user_id' => (int)$user_id,
             );

			 //$this->db->query("UPDATE parsed SET `is_watched`=1,`time_watched`='$date' WHERE `id_mn`='$post_id'");
             $this->db->query("INSERT INTO watched_row  (post_id,user_id)
                            VALUES(:post_id,:user_id)",$db_arr);
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
			
		function sendwaitingSMS() {
			$sql="SELECT * FROM `sms_waiting_sending`";
			$result=$this->db->query($sql);
			if(!empty($result)) {
				foreach($result as $sms) :
					$this->sendSMSSOAP($sms['entry_id'],$sms['district'],$sms['receiver_phone']);
					$id = $sms['id'];
					$sql="DELETE FROM `sms_waiting_sending` WHERE id = '$id'";
					$result1=$this->db->query($sql);
				endforeach;
			}
		}
		
		function saveFilter($user_id,$name,$query) {

            $sql="SELECT * FROM `saved_filters` WHERE name = '$name'";
			$result=$this->db->query($sql);
            $db_arr = array(
                'user_id' => (int)$user_id,
                'name' => $name,
                'query' => $query,

            );

            if(empty($result)) {

                $result =  $this->db->query("INSERT INTO saved_filters  (user_id,name,query)
                            VALUES(:user_id,:name,:query)",$db_arr);


				/*$sql="INSERT INTO `saved_filters` (user_id,name,query) VALUES((int)$user_id,'$name','$query')";
             $result=$this->db->query($sql);*/
			}
		}
		
		function removeFilter($id) {
			$sql="DELETE FROM `saved_filters` WHERE id = '$id'";
			$result=$this->db->query($sql);
		}
			
		function sendSMSSOAP($id=null,$district=null,$phone=0506123470){
			//$date_from = date_parse('08:00:00');
			//$date_to = date_parse('21:00:00');
			$date_now = date_parse(date('H:i:s'));
			
			$sql="SELECT * FROM `sms_districts` main LEFT JOIN (SELECT id,manager as name,phone FROM `db_managers`) managers ON main.manager = managers.id WHERE district='$district'";
            $result_district=$this->db->query($sql);

			$manager_phone = $result_district[0]['phone'];
			$receiver_phone = $phone;
			
			if($date_now['hour'] > 8 && $date_now['hour']+1 < 21) {
				//header ('Content-type: text/html; charset=utf-8'); 
				$client = new SoapClient('http://turbosms.in.ua/api/wsdl.html', array('trace'=>1,'encoding'=>'UTF-8', 'connection_timeout'=>180, 'verifyhost'=>false)); 
				
				//print_r ($client->__getFunctions ());
				// Данные авторизации 
				$auth = array ( 
					'login' => 'avangards', 
					'password' => 'CTavngardsCRT2101smsSHTjh_SFRD' 
				); 
				//$district = iconv('cp1251', 'UTF-8', $district);
				$result = $client->__soapCall("Auth", array($auth));

				// Авторизируемся на сервере 
				//$result = $client->Auth($auth); 
				
				// Результат авторизации 
				//echo $result->AuthResult; 
				
				// Получаем количество доступных кредитов 
			//	$result = $client->GetCreditBalance(); 
			//	echo $result->GetCreditBalanceResult;
			
			//$text = 'Запись id='.$id.". Менеджер: ".$manager_phone;	
			//$text = 'Срочная продажа Вашей недвижимости в срок,оценка,оформление!'.$manager_phone;
            $text = 'Предлагаем профессиональную помощь в продаже Вашей квартиры!'.$manager_phone;
			//$text = iconv('windows-1251', 'utf-8', $text);
			$name = $result_district[0]['name'];
			
			$sms = array( 
				'sender' => 'AVANGARD AN', 
				'destination' => '+38'.$receiver_phone, 
				'text' => $text 
				);
			
			$result = $client->__soapCall("SendSMS", array($sms));
			$sms_id = $result->SendSMSResult->ResultArray[0];
			
			
			$sql="INSERT INTO `sms_sent_stat`(object_id,phone,manager,status,sms_id) VALUES('$id','$receiver_phone','$name','0','$sms_id')";
            $result=$this->db->query($sql);	
			
			} else {
				$sql="INSERT INTO `sms_waiting_sending`(entry_id,receiver_phone,manager_phone,district) VALUES('$id','$receiver_phone','$manager_phone','$district')";
           		 $result=$this->db->query($sql);	
				}
				
			}
			
		function sendSMSSOAPHouse($id=null,$district=null,$phone=0506123470){
			//$date_from = date_parse('08:00:00');
			//$date_to = date_parse('21:00:00');
			$date_now = date_parse(date('H:i:s'));
			
			$sql="SELECT * FROM `sms_districts_houses` main LEFT JOIN (SELECT id,manager as name,phone FROM `db_managers`) managers ON main.manager = managers.id WHERE district='$district'";
            $result_district=$this->db->query($sql);

			$manager_phone = $result_district[0]['phone'];
			$receiver_phone = $phone;
			
			if($date_now['hour'] > 8 && $date_now['hour']+1 < 21) {
				header ('Content-type: text/html; charset=utf-8'); 
				$client = new SoapClient('http://turbosms.in.ua/api/wsdl.html', array('trace'=>1,'encoding'=>'UTF-8', 'connection_timeout'=>180, 'verifyhost'=>false)); 
				
				//print_r ($client->__getFunctions ());
				// Данные авторизации 
				$auth = array ( 
					'login' => 'avangards', 
					'password' => 'CTavngardsCRT2101smsSHTjh_SFRD' 
				); 
				//$district = iconv('cp1251', 'UTF-8', $district);
				$result = $client->__soapCall("Auth", array($auth));

				// Авторизируемся на сервере 
				//$result = $client->Auth($auth); 
				
				// Результат авторизации 
				//echo $result->AuthResult; 
				
				// Получаем количество доступных кредитов 
			//	$result = $client->GetCreditBalance(); 
			//	echo $result->GetCreditBalanceResult;
			
			//$text = 'Запись id='.$id.". Менеджер: ".$manager_phone;	
			//$text = 'Есть потенциальный покупатель на дом в вашем районе!'.$manager_phone;
            $text = 'Предлагаем профессиональную помощь в продаже Вашего дома!'.$manager_phone;
			//$text = iconv('windows-1251', 'utf-8', $text);
			$name = $result_district[0]['name'];
			
			$sms = array( 
				'sender' => 'AVANGARD AN', 
				'destination' => '+38'.$receiver_phone, 
				'text' => $text 
				);
			
			$result = $client->__soapCall("SendSMS", array($sms));
			$sms_id = $result->SendSMSResult->ResultArray[0];
			
			
			$sql="INSERT INTO `sms_sent_stat`(object_id,phone,manager,status,sms_id) VALUES('$id','$receiver_phone','$name','0','$sms_id')";
            $result=$this->db->query($sql);	
			
			} else {
				$sql="INSERT INTO `sms_waiting_sending`(entry_id,receiver_phone,manager_phone,district) VALUES('$id','$receiver_phone','$manager_phone','$district')";
           		 $result=$this->db->query($sql);	
				}
				
			}
			
		function sendSMSSOAPRent($id=null,$district=null,$phone=0506123470){
			//$date_from = date_parse('08:00:00');
			//$date_to = date_parse('21:00:00');
			$date_now = date_parse(date('H:i:s'));
			
			$sql="SELECT * FROM `sms_districts_rent` main LEFT JOIN (SELECT id,manager as name,phone FROM `db_managers`) managers ON main.manager = managers.id WHERE district='$district'";
            $result_district=$this->db->query($sql);

			$manager_phone = $result_district[0]['phone'];
			$receiver_phone = $phone;
			
			if($date_now['hour'] > 8 && $date_now['hour']+1 < 21) {
				header ('Content-type: text/html; charset=utf-8'); 
				$client = new SoapClient('http://turbosms.in.ua/api/wsdl.html', array('trace'=>1,'encoding'=>'UTF-8', 'connection_timeout'=>180, 'verifyhost'=>false)); 
				
				//print_r ($client->__getFunctions ());
				// Данные авторизации 
				$auth = array ( 
					'login' => 'avangards', 
					'password' => 'CTavngardsCRT2101smsSHTjh_SFRD' 
				); 
				//$district = iconv('cp1251', 'UTF-8', $district);
				$result = $client->__soapCall("Auth", array($auth));

				// Авторизируемся на сервере 
				//$result = $client->Auth($auth); 
				
				// Результат авторизации 
				//echo $result->AuthResult; 
				
				// Получаем количество доступных кредитов 
			//	$result = $client->GetCreditBalance(); 
			//	echo $result->GetCreditBalanceResult;
			
			//$text = 'Запись id='.$id.". Менеджер: ".$manager_phone;	
			$text = 'Поможем сдать Вашу недвижимость платежеспособным клиентам!'.$manager_phone;
			//$text = iconv('windows-1251', 'utf-8', $text);
			$name = $result_district[0]['name'];
			
			$sms = array( 
				'sender' => 'AVANGARD AN', 
				'destination' => '+38'.$receiver_phone, 
				'text' => $text 
				);
			
			$result = $client->__soapCall("SendSMS", array($sms));
			$sms_id = $result->SendSMSResult->ResultArray[0];
			
			
			$sql="INSERT INTO `sms_sent_stat`(object_id,phone,manager,status,sms_id) VALUES('$id','$receiver_phone','$name','0','$sms_id')";
            $result=$this->db->query($sql);	
			
			} else {
				$sql="INSERT INTO `sms_waiting_sending`(entry_id,receiver_phone,manager_phone,district) VALUES('$id','$receiver_phone','$manager_phone','$district')";
           		 $result=$this->db->query($sql);	
				}
				
			}
}

if(isset($_POST['mode']) && $_POST['mode'] == 'watched') {
	$prs=new Parser(false);
	$prs->setWatched($_POST['user_id'],$_POST['id']);
}

if(isset($_GET['action']) && $_GET['action'] == 'archive') {
	$prs=new Parser(false);
	$prs->getPhotosArchived($_GET['id']);
}

if(isset($_GET['action']) && $_GET['action'] == 'sms') {
	$prs=new Parser(false);
	//$prs->sendSMSSOAP();
}

if(isset($_POST['mode']) && $_POST['mode'] == 'saveurl') {

	$prs=new Parser(false);
	$prs->saveFilter($_POST['userid'],$_POST['name'],$_POST['query']);
}

if(isset($_POST['mode']) && $_POST['mode'] == 'removeurl') {
	$prs=new Parser(false);
	$prs->removeFilter($_POST['id']);
}