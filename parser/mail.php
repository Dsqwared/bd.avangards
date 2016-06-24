<?php 
set_time_limit(0);
require("classes.php");

$prefix = 'archive/';

function loadfile($filename){

	if (!file_exists($filename)) return false;

	echo("found: $filename");

	$prs=new Parser(false);

	$csv = array_map('str_getcsv', file($filename));

	foreach ($csv as $key => $value) {

		if (preg_match('/ПК\w+-\s(\w+)/u', $value[0],$matches)){
			// var_dump($matches[1]);
			// var_dump('-------------------');
			// var_dump($value[13]*1000);
			// var_dump($value[19]);
			// exit();
			
			$phones=array($value[14]);
			if ($value[15]!='') $phones[]=$value[15];

			// var_dump($phones);
			$area = intval($value[9]);
			$area_json = intval($value[9]).' / '.intval($value[10]).' / '.intval($value[11]);
			$flags=0;
			if ($value[18]!='Владелец') $flags=8;
			$prs->sql_mail_put_safe($matches[1],$value[16],$value[13]*1000,$value[3],1,'Днепропетровск','Квартира',$value[5],$value[6],$value[1],$value[2],$value[19],$phones,$flags,$area,$area_json);
			
			// function sql_mail_put_safe($id,$date,$price,$rooms,$operation,$city,$district,$street,$desc,$phones){
			// var_dump('-------------------');
		} else if (preg_match('/АК\w+-\s(\w+)/u', $value[0],$matches)){
			
		//print_r('11');print_r($value);exit;
			$phones=array($value[10]);
			if ($value[11]!='') $phones[]=$value[11];

			$area = '';
			$area_json = '';
			// var_dump($phones);
			$flags=0;
			if ($value[13]!='Владелец') $flags=8;
			$prs->sql_mail_put_safe($matches[1],$value[12],$value[9],$value[4],2,'Днепропетровск',$value[3],$value[5],$value[6],$value[1],$value[2],$value[14],$phones,$flags,$area,$area_json);
			} else if (preg_match('/ПН\w+-\s(\w+)/u', $value[0],$matches)){
			$phones=array($value[12]);
			if ($value[13]!='') $phones[]=$value[13];

			$area = $value[5];
			$area_json = $area.'/0/0';
			// var_dump($phones);
			$flags=0;
			if ($value[16]!='Владелец') $flags=8;
			$date = $value[14];
			$price = floatval($value[11]) * 1000;
			$rooms = 0;
			$operation = 1;
			$estate_type = $value[3];
			$floor = 0;
			$max_floor = $value[6];
			$district = $value[1];
			$street = $value[2];
			$sq = floatval($value[4])*100;
			$desc = $value[17]. "Площадь участка: ".$sq." соток";
			$prs->sql_mail_put_safe($matches[1],$date,$price,$rooms,$operation,'Днепропетровск',$estate_type,$floor,$max_floor,$district,$street,$desc,$phones,$flags,$area,$area_json);
			}
		}

}
 
/* connect to gmail */
$hostname = '{imap.rambler.ru:993/imap/ssl/novalidate-cert}INBOX';
$username = 'dp1-avangard@rambler.ru';
$password = 'avangard';
//$inbox = imap_open('{imap.rambler.ru:993/imap/ssl}INBOX',$username, $password); // dsqwared этот вариант работает
/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to email: ' . imap_last_error());

/* grab emails */
//$emails = imap_search($inbox, 'UNSEEN');
$date = date("d-F-Y", strtotime("today"));
$emails = imap_search($inbox, "FROM admin@nedvig.net.ua ON \"$date\"");

/* if emails are returned, cycle through each... */
if($emails) {

  /* begin output var */
  $output = '';

  /* put the newest emails on top */
  rsort($emails);


    foreach($emails as $email_number) {

    /* get information specific to this email */
    $overview = imap_fetch_overview($inbox,$email_number,0);
    $message = imap_fetchbody($inbox,$email_number,2); //,FT_PEEK
    $structure = imap_fetchstructure($inbox,$email_number);



    // var_dump($overview);

     $attachments = array();
       if(isset($structure->parts) && count($structure->parts)) {
         for($i = 0; $i < count($structure->parts); $i++) {
           $attachments[$i] = array(
              'is_attachment' => false,
              'filename' => '',
              'name' => '',
              'attachment' => '');

           if($structure->parts[$i]->ifdparameters) {
             foreach($structure->parts[$i]->dparameters as $object) {
               if(strtolower($object->attribute) == 'filename') {
                 $attachments[$i]['is_attachment'] = true;
                 $attachments[$i]['filename'] = $object->value;
               }
             }
           }

           if($structure->parts[$i]->ifparameters) {
             foreach($structure->parts[$i]->parameters as $object) {
               if(strtolower($object->attribute) == 'name') {
                 $attachments[$i]['is_attachment'] = true;
                 $attachments[$i]['name'] = $object->value;

               }
             }

           }


           if($attachments[$i]['is_attachment']) {
             $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);
             if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
               $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
             }
             elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
               $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
             }
           }             
         } // for($i = 0; $i < count($structure->parts); $i++)
       } // if(isset($structure->parts) && count($structure->parts))




    if(count($attachments)!=0){

    	$file='';
        foreach($attachments as $at){

            if($at['is_attachment'] == 1){
				if (preg_match('/\.xls/',$at['filename'])){
					//var_dump($prefix.$at['filename'], $at['attachment']);
							file_put_contents($file=$prefix.$at['filename'], $at['attachment']);


				}
			}
			
		if ($file!=''){
			// echo "$file";
			system("ssconvert $file $file.csv");
			sleep(10);
			loadfile("$file.csv");
			// system("rm -rf $file & rm -rf $file.csv");
		}
	  }	
  }

 // echo $output;
	} 
}

/* close the connection */
imap_close($inbox);

?>
