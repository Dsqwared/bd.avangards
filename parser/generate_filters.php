<?php 
session_start();
set_time_limit(0);
require("classes.php");
$db = new Db();

	$exclude_array = array(
		'Днепродзержинск',
  		'Днепропетровский',
		'Кривой Рог',
		'Район области'
	);

$sqls = array (
	"SELECT district FROM parsed WHERE operation = 1 AND city = 'Днепропетровск' GROUP BY district",
	"SELECT district FROM parsed WHERE operation = 2 AND city = 'Днепропетровск' GROUP BY district",
	"SELECT district FROM parsed WHERE operation = 3 AND city = 'Днепропетровск' GROUP BY district",
	"SELECT district FROM parsed WHERE operation = 4 AND city = 'Днепропетровск' GROUP BY district",
	"SELECT district FROM parsed WHERE operation = 5 AND city = 'Днепропетровск' GROUP BY district"
);

$index = 1;
	$php = '<?php $districts=array(); ';
foreach($sqls as $sql) {
$rent_districts = $db->query($sql);

$arr_f = array();

	foreach($rent_districts as $district) :
		$check = ckeckDistrictsRules($district['district']);
			if($check[1] != 0) {
				$f_district = $check[0];
			} else {
				$f_district = $district['district'];
			}
		if(!in_array($f_district, $arr_f) && !in_array($f_district, $exclude_array)) {
			$arr_f[] = $f_district;
			}
	//print_r($district);	
	endforeach;


	$php .=  ' $districts['.$index.'] = ' . var_export($arr_f, TRUE) .';';
print_r($arr_f);

 $index++;
}
	$php .= '?>';
file_put_contents('districts.php', $php);


//generate types
$sqls = array (
	"SELECT estateType FROM parsed WHERE operation = 1 AND city = 'Днепропетровск' GROUP BY estateType",
	"SELECT estateType FROM parsed WHERE operation = 2 AND city = 'Днепропетровск' GROUP BY estateType",
	"SELECT estateType FROM parsed WHERE operation = 3 AND city = 'Днепропетровск' GROUP BY estateType",
	"SELECT estateType FROM parsed WHERE operation = 4 AND city = 'Днепропетровск' GROUP BY estateType",
	"SELECT estateType FROM parsed WHERE operation = 5 AND city = 'Днепропетровск' GROUP BY estateType"
);

$index = 1;
$index3 = 1;
	$php = '<?php $estateType=array(); ';
foreach($sqls as $sql) {
$rent_estateType = $db->query($sql);

$arr_f = array();


	foreach($rent_estateType as $estateType) :
		if(!empty($estateType['estateType'])) {
			$index1 = getOrderIndex($estateType['estateType']);
			if($index1  == 0) {
					$index1 = 100+$index3;
					$index3 = $index3 + 1;
				}
				
				$arr_f[$index1] = $estateType['estateType'];
			}
	//print_r($district);	
	endforeach;
	ksort($arr_f);

	$php .=  ' $estateType['.$index.'] = ' . var_export($arr_f, TRUE) .';';
print_r($arr_f);

 $index++;
}
	$php .= '?>';
file_put_contents('estateType.php', $php);


function getOrderIndex($estate) {
$index = 0;
	switch($estate) {
		case 'БазаОтдыха':
			$index = 14;
			break;
		case 'Гараж':
			$index = 17;
			break;
		case 'Гостиница':
			$index = 13;
			break;
		case 'Дача':
			$index = 6;
			break;
		case 'Дача (новострой)':
			$index = 7;
			break;
		case 'Дачный участок':
			$index = 11;
			break;
		case 'Дом':
			$index = 3;
			break;
		case 'Дом (новострой)':
			$index = 4;
			break;
		case 'Здание':
			$index = 10;
			break;
		case 'Кафе(ресторан)':
			$index = 18;
			break;
		case 'Квартира':
			$index = 1;
			break;
		case 'Квартира в новострое':
			$index = 2;
			break;
		case 'Киоск(павильон)':
			$index = 33;
			break;
		case 'Коммерческий участок':
			$index = 16;
			break;
		case 'Комната':
			$index = 11;
			break;
		case 'Магазин':
			$index = 12;
			break;
		case 'Недострой':
			$index = 20;
			break;
		case 'Объект автосервиса':
			$index = 21;
			break;
		case 'Объект здоровья':
			$index = 22;
			break;
		case 'Объект красоты':
			$index = 23;
			break;
		case 'Объект отдыха':
			$index = 24;
			break;
		case 'Объект питания':
			$index = 25;
			break;
		case 'Объект производства':
			$index = 26;
			break;
		case 'Объект промышленности ':
			$index = 27;
			break;
		case 'Офис':
			$index = 8;
			break;
		case 'Офис(помещ,здание)':
			$index = 9;
			break;
		case 'Паркинг':
			$index = 28;
			break;
		case 'Помещение':
			$index = 29;
			break;
		case 'Сельхоз участок':
			$index = 30;
			break;
		case 'Склад':
			$index = 31;
			break;
		case 'Склад(производ,база)':
			$index = 31;
			break;
		case 'Торговая площадь':
			$index = 15;
			break;
		case 'Участок под жилье':
			$index = 19;
			break;
		case 'Часть дома':
			$index = 5;
			break;
		case 'Объект спорта':
			$index = 32;
			break;
		case 'Комната с подсе':
			$index = 33;
			break;
		
		
	}
	return $index;
}



function ckeckDistrictsRules($district) {

$d = 0;
$comp = 0;
	switch($district) {
		case 'Вокзал ж/д':
			$d = 'Вокзал';
			$comp = 1;
		break;
		case 'Воронцова пр.':
			$d = 'Воронцова';
			$comp = 1;
		break;
		case 'Гагарина пр.':
			$d = 'Гагарина';
			$comp = 1;
		break;
		case 'Калиновая ул':
			$d = 'Калиновая';
			$comp = 1;
		break;
		case 'Кирова пр.':
			$d = 'Кирова';
			$comp = 1;
		break;
		case 'Клочко-6':
			$d = 'Кирова';
			$comp = 1;
		break;
		case 'Косиора ул.':
			$d = 'Косиора';
			$comp = 1;
		break;
		case 'Левобережный 1,2,3':
			$d = 'Левобережный';
			$comp = 1;
		break;
		case 'Левобережный-1':
			$d = 'Левобережный';
			$comp = 1;
		break;
		case 'Левобережный-2':
			$d = 'Левобережный';
			$comp = 1;
		break;
		case 'Левобережный-3':
			$d = 'Левобережный';
			$comp = 1;
		break;
		case 'Мирный пос.':
			$d = 'Мирный';
			$comp = 1;
		break;
		case 'Парус 1,2':
			$d = 'Парус';
			$comp = 1;
		break;
		case 'Петровского пр.':
			$d = 'Петровского';
			$comp = 1;
		break;
		case 'Петровского-Западный':
			$d = 'Петровского';
			$comp = 1;
		break;
		case 'Победа 1,2,3':
			$d = 'Победа';
			$comp = 1;
		break;
		case 'Победа 4,5,6':
			$d = 'Победа';
			$comp = 1;
		break;
		case 'Победа-1':
			$d = 'Победа';
			$comp = 1;
		break;
		case 'Победа-2':
			$d = 'Победа';
			$comp = 1;
		break;
		case 'Победа-3':
			$d = 'Победа';
			$comp = 1;
		break;
		case 'Победа-4':
			$d = 'Победа';
			$comp = 1;
		break;
		case 'Победа-5':
			$d = 'Победа';
			$comp = 1;
		break;
		case 'Победа-6':
			$d = 'Победа';
			$comp = 1;
		break;
		case 'Приднепровск-Северный':
			$d = 'Приднепровск';
			$comp = 1;
		break;
		case 'Пушкина пр.':
			$d = 'Пушкина';
			$comp = 1;
		break;
		case 'Рабочая ул':
			$d = 'Рабочая';
			$comp = 1;
		break;
		case 'Сокол 1,2':
			$d = 'Сокол';
			$comp = 1;
		break;
		case 'Сокол-1':
			$d = 'Сокол';
			$comp = 1;
		break;
		case 'Сокол-2':
			$d = 'Сокол';
			$comp = 1;
		break;
		case 'Солнечный-Воронцова':
			$d = 'Солнечный';
			$comp = 1;
		break;
		case 'Титова ул.':
			$d = 'Титова';
			$comp = 1;
		break;
		case 'Тополь 1,2,3':
			$d = 'Тополь';
			$comp = 1;
		break;
		case 'Тополь-1':
			$d = 'Тополь';
			$comp = 1;
		break;
		case 'Тополь-2':
			$d = 'Тополь';
			$comp = 1;
		break;
		case 'Тополь-3':
			$d = 'Тополь';
			$comp = 1;
		break;
		case 'Тополь-Сокол':
			$d = 'Тополь';
			$comp = 1;
		break;
		case 'Фрунзенский 1,2':
			$d = 'Фрунзенский';
			$comp = 1;
		break;
		case 'Фрунзенский-1':
			$d = 'Фрунзенский';
			$comp = 1;
		break;
		case 'Фрунзенский-2':
			$d = 'Фрунзенский';
			$comp = 1;
		break;
		case 'Центральный':
			$d = 'Центр';
			$comp = 1;
		break;
		case 'Лоц-Каменка':
			$d = 'Лоцманская Каменка';
			$comp = 1;
		break;
		case 'Рабочая ул.':
			$d = 'Рабочая';
			$comp = 1;
		break;
		case 'Шевченко пос.':
			$d = 'Шевченко';
			$comp = 1;
		break;
		case 'Калиновая ул.':
			$d = 'Калиновая';
			$comp = 1;
		break;
		
		
	}
	
	return array($d,$comp);
}



?>