<?php 
//session_start();
set_time_limit(600);
ini_set('memory_limit', '384M');
require("classes.php");
$db = new Db('avangards_settings.ini.php');

$ch_array = array(
	'"',
	'&',
	'>',
	'<',
	'\''
);

$ch_array_rep = array(
	'&quot;',
	'&amp;',
	'&gt;',
	'&lt;',
	'&apos;'
);

/*$db_managers = array(
	'Валерий Алексеенко' => 'valeraavangard.est.ua',
	'Алина Багаева' => 'alinabagaeva.est.ua',
	'Виталий Бурназов' => 'vitos.est.ua',
	'Роман Бондаренко' => 'roman-avangard.est.ua',
	'Елена Бурлаенко' => 'elenbyr.est.ua',
	'Сергей Вязмин' => 'vyazmin.est.ua',
	'Иван Войников' => 'ivoynikov.est.ua',
	'Владимир Васниковский' => 'v675654247.est.ua',
	'Роман Василишин' => 'roman-avangard2015.est.ua',
	'Дмитрий Громов' => 'dmetr.est.ua',
	'Виктория Гондарь' => 'viktoriagh.est.ua',
	'Ирина Дублянская' => 'dtelegina8686.est.ua',
	'Ольга Диденко' => 'avangardolga.est.ua',
	'Любовь Долгих' => 'lubanik.est.ua',
	'Алина Дубовенко' => 'alinchik.est.ua',
	'Петр Загородний' => 'petrzavangard.est.ua',
	'Наталья Исаенко' => 'nat1963.est.ua',
	'Олег Касаткин' => 'olegavangard2517.est.ua',
	'Дмитрий Котляров' => 'avdmdmitro.est.ua',
	'Татьяна Клюйкова' => 'andrtan2005.est.ua',
	'Дмитрий Корнилов' => 'dimitrius.est.ua',
	'Дмитрий Кулаков' => 'dimanagiant.est.ua',
	'Людмила Курнос' => 'ludakurnos.est.ua',
	'Елена Кравченко' => 'elena-avangard.est.ua',
	'Оксана Кучер' => 'ksuha2121.est.ua'
	'Михаил Макий' => 'makiymm.est.ua',
	'Дмитрий Мамонов' => 'mamonavangard.est.ua',
	'Владимир Мельниченко' => 'vladimir1983avangard.est.ua',
	'Руслан Мищенко' => 'politra.est.ua',
	'Евгений Муравьёв' => 'evgesha.est.ua',
	'Наталья Медынская' => 'kvitka.est.ua',
	'Марина Носань' => 'nosan.est.ua',
	'Ксения Нонь' => 'ksyusha199321.est.ua',
	'Владислав Нежнов' => 'vv2013.est.ua',
	'Олег Пятак' => 'olegpyatak1970.est.ua',
	'Вячеслав Педенко' => 'vipestate.est.ua',
	'Сергей Подковенко' => 'podkova.est.ua',
	'Алина Руруа' => 'alina-rurua.est.ua',
	'Александр Свердлин' => 'm0674016622.est.ua',
	'Валерий Скороход' => 'svu1964.est.ua',
	'Максим Седой' => 'mmaxavangard.est.ua',
	'Лана Холодная' => 'holodnayalana.est.ua',
	'Виталий Чубук' => 'chapion.est.ua',
	'Светлана Чинчевая' => 'svetik1820.est.ua',
	'Яна Яковенко' => 'yana-milka.est.ua'
);*/

$db_managers = array(
	'hk' => '123123123',
	'Алексеенко' => 'valeraavangard',
	'Багаева' => 'alinabagaeva',
	'Бурназов' => 'vitos',
	'Бондаренко' => 'roman-avangard',
	'Бурлаенко' => 'elenbyr',
	'Вязмин' => 'vyazmin',
	'Войников' => 'ivoynikov',
	'Васниковский' => 'v675654247',
	'василишин' => 'roman-avangard2015',
	'Громов' => 'dmetr',
	'Гондарь' => 'viktoriagh',
	'Дублянская' => 'dtelegina8686',
	'Диденко' => 'avangardolga',
	'Долгих' => 'lubanik',
	'Дубовенко' => 'alinchik',
	'Загородний' => 'petrzavangard',
	'Исаенко' => 'nat1963',
	'Касаткин' => 'olegavangard2517',
	'Котляров' => 'avdmdmitro',
	'Клюйкова' => 'andrtan2005',
	'Корнилов' => 'dimitrius',
	'Кулаков' => 'dimanagiant',
	'Курнос' => 'ludakurnos',
	'Кравченко' => 'elena-avangard',
	'Кучер' => 'ksuha2121',
	'Макий' => 'makiymm',
	'Мамонов' => 'mamonavangard',
	'Мельниченко' => 'vladimir1983avangard',
	'Мищенко' => 'politra',
	'Муравьёв' => 'evgesha',
	'Медынская' => 'kvitka',
	'Носань' => 'nosan',
	'Нонь' => 'ksyusha199321',
	'Нежнов' => 'vv2013',
	'Пятак' => 'olegpyatak1970',
	'Педенко' => 'vipestate',
	'Подковенко' => 'podkova',
	'Руруа' => 'alina-rurua',
	'Свердлин' => 'm0674016622',
	'Скороход' => 'svu1964',
	'Седой' => 'mmaxavangard',
	'Холодная' => 'holodnayalana',
	'Чубук' => 'chapion',
	'Чинчевая' => 'svetik1820',
	'Яковенко' => 'yana-milka'
);


$districts = array(
			'Амур-Нижнеднепровский (АНД)',
			'Воронцова',
			'Калиновая',
			'Левобережный-1',
			'Левобережный-2',
			'Отечественная',
			'Передовая',
			'Правда',
			'Фрунзенский',
			'Героев Сталинграда',
			'поселек Мирный',
			'Лоцманская каменка',
			'Нагорный',
			'Победа 1-3',
			'Победа 4-6',
			'Парус 1-2',
			'Подстанция',
			'Поселек южный',
			'Сокол 1-2',
			'Индустриальный',
			'Клочко',
			'Левобережный-3',
			'Самаровка',
			'Юбилейное',
			'ЖД Вокзал',
			'проспект Кирова',
			'проспект Пушкина',
			'Красногвардейский',
			'12 квартал',
			'Корея',
			'Краснополье',
			'Титова',
			'Шляховка',
			'Ленинский',
			'Диевка',
			'Западный',
			'Коммунар',
			'Новые кайдаки',
			'Парус 1-2',
			'Петровского',
			'Сухачевка',
			'Таромское',
			'Одинковка',
			'Приднепровск',
			'Рыбальск',
			'Гагарина',
			'Рабочая',
			'Тополь1-3',
			'Солнечный'
			);

$districts1 = array(
			'Амур-Нижнеднепровский',
			'Амур-Нижнеднепровский',
			'Амур-Нижнеднепровский',
			'Индустриальный', 
			'Индустриальный',
			'Амур-Нижнеднепровский',
			'Амур-Нижнеднепровский',
			'Амур-Нижнеднепровский',
			'Амур-Нижнеднепровский',
			'Бабушкинский',
			'Бабушкинский', 
			'Жовтневый',
			'Жовтневый',
			'Жовтневый',
			'Жовтневый',
			'Ленинский',
			'Жовтневый',
			'Жовтневый',
			'Жовтневый',
			'Индустриальный',
			'Амур-Нижнеднепровский',
			'Индустриальный',
			'Самарский',
			'Индустриальный',
			'Кировский',
			'Кировский',
			'Пушкина',
			'Красногвардейский',
			'Бабушкинский',
			'Бабушкинский',
			'Красногвардейский',
			'Красногвардейский',
			'Красногвардейский',
			'Ленинский',
			'Ленинский',
			'Ленинский',
			'Ленинский',
			'Ленинский',
			'Ленинский',
			'Ленинский',
			'Ленинский',
			'Ленинский',
			'Самарский',
			'Самарский',
			'Самарский',
			'Жовтневый',
			'Красногвардейский',
			'Бабушкинский',
			'Амур-Нижнеднепровский'
			);

$sql1 = "SELECT * FROM `wp_terms`";
$terms = $db->query($sql1);

$sqlu = "SELECT * FROM `wp_users`";
$users = $db->query($sqlu);



$terms1 = array();
foreach($terms as $key=>$term) {
	$terms1[$term['term_id']] = $term;
}

$users1 = array();
foreach($users as $key=>$user) {
	$users1[$user['ID']] = $user;
}


$posts_count_sql = "SELECT COUNT(*) as count FROM `wp_posts` WHERE post_status='publish' AND post_type='estate_property'";
$posts_count = $db->query($posts_count_sql);
$p_count = $posts_count[0]['count'];



$step = 100;
$pages = ceil($p_count/$step);
print_r($posts_count);
echo "Pages";
print_r($pages);
$date = date('c',time());
$all_count = 0;

		$xml = '<?xml version="1.0" encoding="UTF-8"?>';
		$xml .= '<realty-feed xmlns="https://webmaster.yandex.ua/schemas/feed/realty/2010-06">';
		$xml .= '<generation-date>'.$date.'</generation-date>';
		
		for($i=0;$i<$pages;$i++) {
			$start = $i * $step;
			//$finish = $start + $step;
			$sql_posts = "SELECT * FROM `wp_posts` WHERE post_status='publish' AND post_type='estate_property'  ORDER BY ID ASC LIMIT $start, $step";
			$posts = $db->query($sql_posts); 
			echo count($posts);
				foreach($posts as $post) :
					$id = $post['ID'];
					$sgl_meta = "SELECT meta_value,meta_key FROM `wp_postmeta` WHERE post_id='$id'";
					$metas = $db->query($sgl_meta);
					$sgl_terms = "SELECT * FROM `wp_term_relationships` WHERE object_id='$id' AND ((`term_taxonomy_id`>55 AND `term_taxonomy_id`<77) OR `term_taxonomy_id`=132 OR `term_taxonomy_id`=133)";
					$terms_rel = $db->query($sgl_terms);
					$sgl_terms_13 = "SELECT * FROM `wp_term_relationships` WHERE object_id='$id' AND `term_taxonomy_id`=13";
					$terms_rel_13 = $db->query($sgl_terms_13);
					//echo "1111";print_r($terms_rel);
					//exit;
					$metas1 = array();
					foreach($metas as $key=>$meta) {
						$metas1[$meta['meta_key']] = $meta;
					}
					
					$post_author = $post['post_author'];
					$sqlus = "SELECT * FROM `wp_usermeta` WHERE user_id='$post_author'";
					$users_meta = $db->query($sqlus);
					
					$users_meta1 = array();
					foreach($users_meta as $key=>$user_meta1) {
						$users_meta1[$user_meta1['meta_key']] = $user_meta1;
					}
										
					if($metas1['prop_action_category']['meta_value'] != -1 && isset($terms1[$metas1['prop_category']['meta_value']]['name']) && $metas1['property_county']['meta_value'] != 'none' && $metas1['property_area']['meta_value'] != 'none' && $metas1['property_city']['meta_value'] != 'none' && $metas1['property_city']['meta_value'] != 'all') {
						$xml .= '<offer internal-id="'.$id.'">';
						
						//if(array_key_exists($users_meta1['last_name']['meta_value'],$db_managers)) {
							$xml .= '<estausername>'.$users_meta1['nickname']['meta_value'].'</estausername>';
						//}
						
						$type = !empty($terms_rel) ? $terms1[$terms_rel[0]['term_taxonomy_id']]['name'] : $terms1[$metas1['prop_category']['meta_value']]['name'];
						$new = false;
						if($type == 'Квартиры' || !empty($terms_rel_13)) {
							$type = 'квартира';
						} else if($type == 'Квартиры в новострое') {
						 	$type = 'квартира';
							$new = true;
						} else if($type == 'Дача (новострой)') {
						 	$type = 'дача';
						} else if($type == 'Дом (новострой)') {
						 	$type = 'дом';
						} else if($type == 'Недострой') {
							$type = 'Недострой';
						 	//$type = 'часть дома';
						} else if($type == 'Коммерческая недвижимость') {
						 	$type = 'дом';
						} else if($type == 'Земля') {
						 	$type = 'земельный участок';
						}
						
						$action_type = mb_strtolower($terms1[$metas1['prop_action_category']['meta_value']]['name'],'UTF-8');
						$xml .=	'<type>'.$action_type.'</type>';
						$xml .=	'<property-type>жилая</property-type>';
						$xml .=	'<category>'.mb_strtolower($type,'UTF-8').'</category>';
						
						$xml .=	'<url>'.$post['guid'].'</url>';
      					$xml .=	'<creation-date>'.date('c',strtotime($post['post_modified_gmt'])).'</creation-date>';
						
						$xml .=	 '<location>';
							$xml .=	'<country>Украина</country>';
							$xml .=	'<region>Днепропетровская область</region>';
							//$xml .=	'<region>'.$metas1['property_county']['meta_value'].'</region>';
							
							$d_key = array_search($metas1['property_area']['meta_value'],$districts);
							$distr = ($d_key !== FALSE)? $districts1[$d_key] : $metas1['property_area']['meta_value'];
							//$distr = $metas1['property_area']['meta_value'];
							$xml .=	'<locality-name>'.$metas1['property_city']['meta_value'].'</locality-name>';
							if($metas1['property_area']['meta_value'] != 'all') {
								$xml .=	'<sub-locality-name>'.$distr.'</sub-locality-name>';
								}
							$xml .=	'<address>улица '.$metas1['property_address']['meta_value'].'</address>';
						$xml .=	'</location>';
						
							$xml .=	'<sales-agent>';
							if(isset($users1[$post_author])) {
								$xml .=	'<name>'.$users_meta1['first_name']['meta_value'].' '.$users_meta1['last_name']['meta_value'].'</name>';
								$xml .=	'<phone>+38'.$users_meta1['mobile']['meta_value'].'</phone>';
								}
								$xml .=	'<category>агентство</category>';
								$xml .=	'<organization>Агентство недвижимости AVANGARD</organization>';
								$xml .=	'<url>http://www.avangards.com.ua</url>';
								if(isset($users1[$post_author])) {
									$xml .=	'<email>'.$users1[$post_author]['user_email'].'</email>';
								}
							$xml .=	'</sales-agent>';
							$xml .=	'<price>';
									$xml .=	'<value>'.intval($metas1['property_price']['meta_value']).'</value>';
									
									$currency = $action_type == 'аренда' ? 'UAH' : 'USD';
									$xml .=	'<currency>'.$currency .'</currency>';
							$xml .=	'</price>';
							
							if(!empty($metas1['attachid']['meta_value'])) {
							$imgs = explode(',',$metas1['attachid']['meta_value']);
								foreach($imgs as $val):
									if(!empty($val)) {
										$sqlim= "SELECT * FROM `wp_postmeta` WHERE post_id='$val' AND meta_key='_wp_attached_file'";
										$img = $db->query($sqlim);
										if(!empty($img)) {
											$xml .=	'<image>http://www.avangards.com.ua/wp-content/uploads/'.$img[0]['meta_value'].'</image>';
											}
									}
								endforeach;
							}
							
							$descr = str_replace($ch_array, $ch_array_rep, $metas1['description']['meta_value']);
								$xml .=	'<description>'.$descr.'</description>';
								
								if(!empty($metas1['property_size']['meta_value'])) {
								$xml .=	'<area>';
									 $xml .= '<value>'.$metas1['property_size']['meta_value'].'</value>';
									 $xml .= '<unit>кв.м</unit>';
								$xml .=	'</area>';
								}
								if(!empty($metas1['property_lot_size']['meta_value'])) {
								$xml .=	'<living-space>';
									 $xml .= '<value>'.$metas1['property_lot_size']['meta_value'].'</value>';
									 $xml .= '<unit>кв.м</unit>';
								$xml .=	'</living-space>';
								}
								if(!empty($metas1['property_rooms']['meta_value'])) {
								$xml .=	'<kitchen-space>';
									 $xml .= '<value>'.$metas1['property_rooms']['meta_value'].'</value>';
									 $xml .= '<unit>кв.м</unit>';
								$xml .=	'</kitchen-space>';
								}
								if(!empty($metas1['property_bedrooms']['meta_value'])) {
									$xml .=	'<rooms>'.$metas1['property_bedrooms']['meta_value'].'</rooms>';
									}
								if(!empty($metas1['property_bathrooms']['meta_value'])) {
									$xml .=	'<floor>'.$metas1['property_bathrooms']['meta_value'].'</floor>';
									}
								if(!empty($metas1['property_etagnost']['meta_value'])) {
       								$xml .=	'<floors-total>'.$metas1['property_etagnost']['meta_value'].'</floors-total>';
								}
									if(!empty($metas1['property_seria']['meta_value'])) {				
										$xml .=	'<building-type>'.$metas1['property_seria']['meta_value'].'</building-type>';
									}

													
						$xml .= '</offer>';
						$all_count = $all_count + 1;
										
					}
				endforeach;
			}
   			

		$xml .= '</realty-feed>';
//print_r($xml);

$fp1 = fopen('archive/feed.xml', 'w');
fwrite($fp1,$xml);
fclose($fp1);


echo $all_count;
echo "<br>".$i;
/*print_r($post);

$objects1 = array();
foreach($objects as $key=>$object) {
	$objects1[$object['meta_key']] = $object;
}


print_r($objects1);
*/

print_r($terms1);


print_r($users1);

echo "1";
?>