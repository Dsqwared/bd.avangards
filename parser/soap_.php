<?php 
session_start();
set_time_limit(0);
require("classes.php");
$db = new Db();
$prs=new Parser(false);


$client=new SoapClient("http://ray-2.com/Services/FlatsService.svc?wsdl");
$time_client=new SoapClient("http://ray-2.com/Services/TimeService.svc?wsdl");
//$client1=new SoapClient("http://ray-2.com/Services/ImagesService.svc?wsdl");
$client3=new SoapClient("http://ray-2.com/Services/SecurityService.svc?wsdl");
$client_houses=new SoapClient("http://ray-2.com/Services/HousesService.svc?wsdl");

$auth = array('login'=>'zaderey','password'=>'6521971');
unset($_SESSION['key']);
if(!isset($_SESSION['key'])) {

$options = $db->query("SELECT option_value FROM `options` WHERE `option_name` = 'ray2_last_datetime'");
$time = $time_client->__soapCall("GetCurrentTime", array());
$current_time = $time->GetCurrentTimeResult;
$from_date = $options[0]['option_value'];

setNewTime($db, $current_time);

$response3 = $client3->__soapCall("Login", array($auth));
		
if(isset($response3->LoginResult->Roles->Role->AgencyId)) {
	$agency = array('sessionId'=>$response3->LoginResult->SessionId,'agencyId'=>$response3->LoginResult->Roles->Role->AgencyId);
	// print_r($agency);
	$session = $response3->LoginResult->SessionId;
	$session_arr = array('sessionId'=>$session);
		$client3->__soapCall("SetAgency", array($agency));
		$_SESSION['key'] = $session;
		$loc_arrays = getLocationsArrays($session_arr);	
		$vars = array('sessionId'=>$_SESSION['key'], 'fromDate'=>$from_date);
		$vars1 = array('sessionId'=>$_SESSION['key'], 'flatid'=>'736604');
		$flats = $client->__soapCall("GetByDateRange", array($vars));
		$houses = $client_houses->__soapCall("GetByDateRange", array($vars));
		foreach($houses->GetByDateRangeResult->House as $v) {

			$time_c = explode('.', str_replace('T', ' ', $v->CONT_DATE));
			$date_cp = date('Y-m-d H:i:s', (strtotime($time_c[0])+10800));
			
			if(isset($v->PriceRent))  { 
			 	 $price = $v->PriceRent;
			} else if(isset($v->CENA)) { 
				$price = $v->CENA; 
			} else { 
				$price = '';
			} 
			
			if(isset($v->ForRent)) {
				$operat = 2;
				} else if(isset($v->ForSale)) {
				$operat = 1;
				}
			$max_floor = isset($v->ETAZHEY) ? $v->ETAZHEY : '---'; 
			$floor = $max_floor;
			$rooms = isset($v->KOMNAT) ? $v->KOMNAT : 0;
			$city = isset($v->City) ? $loc_arrays['cities'][$v->City] : '---';
			$estate_type = 'Дом'; 
			$adistrict = isset($v->District) ? $loc_arrays['districts'][$v->District] : '---'; 
			$street = isset($v->Street) ? $loc_arrays['streets'][$v->Street] : '---';
			
			$phones = array(isset($v->KONTTEL1) ? $v->KONTTEL1 : '');
			if(isset($v->KONTTEL2)) { $phones[] = $v->KONTTEL2; }
			if(isset($v->KONTTEL3)) { $phones[] = $v->KONTTEL3; }
			$area = isset($v->SALL) ? $v->SALL : '---';
			$area_json = isset($v->SALL) ? $v->SALL.' / ' : ' / ';
			$area_json .= isset($v->SZHIL) ? $v->SZHIL.' / ' : '0 / '; 
			$area_json .= isset($v->SKITCHEN) ? $v->SKITCHEN : '0';
			$district = isset($v->Zone) ? $loc_arrays['zones'][$v->Zone] : '---'; //Район district
			$url = isset($v->Url) ? $v->Url : '';
			$desc = "Дом находится в городе $city, район $district ($adistrict). Ссылка $url";
		
			$prs->sql_ray2_put_safe('', $date_cp, $price, $rooms, $operat, $city, $estate_type, $floor, $max_floor, $district, $street, $desc, $phones, 0, $area, $area_json, $url, $adistrict);
			echo $date_cp;
		}	
		
		foreach($flats->GetByDateRangeResult->Flat as $v) {
		//print_r($v);
			$time_c = explode('.', str_replace('T', ' ', $v->CONT_DATE));
			$date_cp = date('Y-m-d H:i:s', (strtotime($time_c[0])+10800));
			
			//echo $date_cp;exit;
			
			if(isset($v->CenaRentMonth))  { 
			 	 $price = $v->CenaRentMonth;
				 $operat = 2;
			} else if(isset($v->CENA)) { 
				$price = $v->CENA; 
				$operat = 1;
			} else {
				$operat = 1;
				$price = 0; 
			}
			
			$floor = isset($v->ETAZH) ? $v->ETAZH : '0';
			$max_floor = isset($v->ETAZHEY) ? $v->ETAZHEY : '0'; 
			$rooms = isset($v->KOMNAT) ? $v->KOMNAT : 0;
			$city = isset($v->City) ? $loc_arrays['cities'][$v->City] : '---';
			$estate_type = 'Квартира'; 
			$adistrict = isset($v->District) ? $loc_arrays['districts'][$v->District] : '---'; 
			$street = isset($v->Street) ? $loc_arrays['streets'][$v->Street] : '---';
			
			$phones = array(isset($v->KONTTEL1) ? $v->KONTTEL1 : '');
			if(isset($v->KONTTEL2)) { $phones[] = $v->KONTTEL2; }
			if(isset($v->KONTTEL3)) { $phones[] = $v->KONTTEL3; }
			$area = isset($v->SALL) ? $v->SALL : '---';
			$area_json = isset($v->SALL) ? $v->SALL.' / ' : ' / ';
			$area_json .= isset($v->SZHIL) ? $v->SZHIL.' / ' : '0 / '; 
			$area_json .= isset($v->SKITCHEN) ? $v->SKITCHEN : '0';
			$district = isset($v->Zone) ? $loc_arrays['zones'][$v->Zone] : '---'; //Район district
			$url = isset($v->Url) ? $v->Url : '';
			$desc = "Квартира находится в городе $city, район $district ($adistrict). Ссылка $url";
		
			$prs->sql_ray2_put_safe('', $date_cp, $price, $rooms, $operat, $city, $estate_type, $floor, $max_floor, $district, $street, $desc, $phones, 0, $area, $area_json, $url, $adistrict);
			echo $date_cp;
		}	 
	}
}				

function getLocationsArrays($session_arr) {

$locations = new SoapClient("http://ray-2.com/Services/LocationsService.svc?wsdl");

	//$countries_array = $locations->__soapCall("GetCountries", array($session_arr));
	$regions_array = $locations->__soapCall("GetRegions", array($session_arr));
	$regions = array();
		foreach($regions_array->GetRegionsResult->Region as $val) :
			$regions[$val->Id] = $val->Name;
		endforeach;
	//$areas_array = $locations->__soapCall("GetAreas", array($session_arr));
	$districts_array = $locations->__soapCall("GetDistricts", array($session_arr));
	$districts = array();
		foreach($districts_array->GetDistrictsResult->District as $val) :
			$districts[$val->Id] = $val->Name;
		endforeach;
	$cities_array = $locations->__soapCall("GetCities", array($session_arr));
	$cities = array();
		foreach($cities_array->GetCitiesResult->City as $val) :
			$cities[$val->Id] = $val->Name;
		endforeach;
	$zones_array = $locations->__soapCall("GetZones", array($session_arr));
	$zones = array();
	foreach($zones_array->GetZonesResult->Zone as $val) :
			$zones[$val->Id] = $val->Name;
		endforeach;
	$streets_array = $locations->__soapCall("GetStreets", array($session_arr));
	$streets = array();
	foreach($streets_array->GetStreetsResult->Street as $val) :
			$streets[$val->Id] = $val->Name;
		endforeach;

	//print_r($regions);//-1 id to fit index

	return array(
			'regions' => $regions,
			'cities' => $cities,
			'districts' => $districts,
			'zones' => $zones,
			'streets' => $streets
			);
}

function setNewTime($db, $time) {
	$db->query("UPDATE`options` SET `option_value` = '$time' WHERE `option_name` = 'ray2_last_datetime'");
}





/* Flat {
 guid AgencyId;
 string AgencyName;
 int Area;
 int BALKON;
 int BRONDOOR;
 int BTI;
 int CABLETV;
 double CENA;
 dateTime CLOSE2DATE;
 dateTime CLOSE_DATE;
 int COKOL;
 int COMMERCIAL;
 int CONDITION;
 dateTime CONT_DATE;
 int COUNTG;
 int COUNTW;
 int CenaRentMonth;
 ObjectChangeTracker ChangeTracker;
 int City;
 int Class;
 int Country;
 int DOCUMENTS;
 int DistanceToSea;
 int District;
 int ELECTRO;
 string EMail;
 dateTime ENTER_DATE;
 int ETAZH;
 int ETAZHEY;
 int FINISH;
 int FOND;
 ArrayOfFlatAgentPhone FlatAgentPhones;
 ArrayOfFlatAssoc FlatAssocs;
 boolean ForRent;
 boolean ForSale;
 int Furniture;
 double H_POTOLKA;
 string House;
 int ISOKOMNAT;
 int Id;
 ArrayOfImage Images;
 int ImagesCount;
 boolean IsPublishedOnSite;
 int KAFEL;
 string KODPODJEZD;
 int KOLONKA;
 int KOMNAT;
 int KONSYERJ;
 string KONTLITCO;
 string KONTTEL1;
 string KONTTEL2;
 string KONTTEL3;
 int KORPUS;
 int KVARTIRA;
 int LIFT;
 int LODGIYA;
 int MATERIAL;
 boolean NoPosr;
 string Notes;
 int OKNA;
 int OKNADVOR;
 int OKNARED;
 int OKNA_TIP;
 string ORIENTIR;
 int OTDVHOD;
 int Orientirs;
 int PARKET;
 int PECHKA;
 int PEREK;
 int PEREPLAN;
 int PHONE;
 int POGREB;
 dateTime PREDL_DATE;
 int ParentId;
 int Parking;
 int Planirovka;
 int PriceSquareMeter;
 string PublisherTexts;
 int REDLINE;
 string REMARKS;
 string REMARKS1;
 double REQ_ID;
 int RESHETKI;
 int ROOF;
 int Refrigerator;
 int Region;
 guid RoleId;
 double SALL;
 int SANTEHNIKA;
 int SANUZEL;
 int SIGNAL;
 double SKITCHEN;
 int SOSTOYANIE;
 double SZHIL;
 string Source;
 int SourceType;
 int Status;
 int Street;
 int SubwayStation;
 int TAMBUR;
 int TIPDOMA;
 int TORG;
 int TRUBA;
 int TV;
 int TehEtazh;
 int UGLOVAYA;
 string Url;
 guid UserId;
 int VODA;
 base64Binary Version;
 int WashMachine;
 int Zone;
 ID Id;
 IDREF Ref;
 */
?>