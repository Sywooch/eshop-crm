<?php
namespace app\components;

use yii\base\Component;
use yii\base\Exception;
use yii\caching\Cache;
use yii\db\Connection;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Config
 *
 * @author Abhimanyu Saharan <abhimanyu@teamvulcans.com>
 * @package abhimanyu\config\components
 */
class Tools extends Component
{
	public static function format_phone($phone = '', $convert = false, $trim = true)
	{
	    // If we have not entered a phone number just return empty
	    if (empty($phone)) {
	        return '';
	    }
	 	
	 	if(substr($phone, 0, 1) == '7') $phone = substr_replace($phone, '8', 0, 1);
	 	elseif(substr($phone, 0, 2) == '+7') $phone = substr_replace($phone, '8', 0, 2);
	 	elseif(substr($phone, 0, 2) != '+7' and substr($phone, 0, 1) != '7' and substr($phone, 0, 1) != '8') $phone = '8'.$phone;
	 	
	    // Strip out any extra characters that we do not need only keep letters and numbers
	    $phone = preg_replace("/[^0-9A-Za-z]/", "", $phone);
	 
	    // Do we want to convert phone numbers with letters to their number equivalent?
	    // Samples are: 1-800-TERMINIX, 1-800-FLOWERS, 1-800-Petmeds
	    if ($convert == true) {
	        $replace = array('2'=>array('a','b','c'),
	                 '3'=>array('d','e','f'),
	                     '4'=>array('g','h','i'),
	                 '5'=>array('j','k','l'),
	                                 '6'=>array('m','n','o'),
	                 '7'=>array('p','q','r','s'),
	                 '8'=>array('t','u','v'), '9'=>array('w','x','y','z'));
	 
	        // Replace each letter with a number
	        // Notice this is case insensitive with the str_ireplace instead of str_replace 
	        foreach($replace as $digit=>$letters) {
	            $phone = str_ireplace($letters, $digit, $phone);
	        }
	    }
	 
	    // If we have a number longer than 11 digits cut the string down to only 11
	    // This is also only ran if we want to limit only to 11 characters
	    if ($trim == true && strlen($phone)>11) {
	        $phone = substr($phone,  0, 11);
	    } 
	 
	    // Perform phone number formatting here
	    if (strlen($phone) == 7) {
	        return preg_replace("/([0-9a-zA-Z])([0-9a-zA-Z])/", "$1$2", $phone);
	    } elseif (strlen($phone) == 10) {
	        return preg_replace("/([0-9a-zA-Z])([0-9a-zA-Z])([0-9a-zA-Z])/", "$1$2$3", $phone);
	    } elseif (strlen($phone) == 11) {
	        return preg_replace("/([0-9a-zA-Z])([0-9a-zA-Z])([0-9a-zA-Z])([0-9a-zA-Z])/", "$1$2$3$4", $phone);
	    }
	 
	    // Return original phone if not 7, 10 or 11 digits long
	    return $phone;
	}
	/**
	* 
	* @param undefined $var
	* @param undefined $arr
	* @param undefined $name
	* 
	* @return
	*/
	public static function processData($var, &$arr = false, $name = false) {
		if($var !==false and isset($var) and !is_null($var) and !empty($var) and substr($var, 0, 1) != '0') $var = htmlspecialchars($var);
		else return false;
		if($arr !==false and $name !==false)
			$arr[$name] = $var;
	}
	/**
	* 
	* @param undefined $arr
	* 
	* @return
	*/
	public static function joinString($arr = array()){
		$return = false;
		
		if(count($arr)<1) return $return;
		
		$addr = [];		
		
		foreach($arr as $ar) {
			if(!is_null($ar) and !empty($ar)) $addr[] = $ar;
		}
		$array_empty = array('');
		$addr = array_diff($addr, $array_empty);
		if(count($addr) >0) {
			$return = implode(', ', $addr);
		}
		return $return;
	}
	
	/** 
	* @param undefined $num
	* @param undefined $translite
	* 
	* @return	
	* 
	* Пример использования 
	* echo num2str('2133.93');
	* 	
	* Возвращает сумму прописью
	*/
	public static function num2str($num,$translite=null){
		$defaultTranslite = array(
			'null' => 'ноль',
			'a1' => array(1=>'один',2=>'два',3=>'три',4=>'четыре',5=>'пять',6=>'шесть',7=>'семь',8=>'восемь',9=>'девять'),
			'a2' => array(1=>'одна',2=>'две',3=>'три',4=>'четыре',5=>'пять',6=>'шесть',7=>'семь',8=>'восемь',9=>'девять'),
			'a10' => array(0=>'десять',1=>'одиннадцать',2=>'двенадцать',3=>'тринадцать',4=>'четырнадцать',5=>'пятнадцать',6=>'шестнадцать',7=>'семнадцать',8=>'восемнадцать',9=>'девятнадцать'),
			'a20' => array(2=>'двадцать',3=>'тридцать',4=>'сорок',5=>'пятьдесят',6=>'шестьдесят',7=>'семьдесят',8=>'восемьдесят',9=>'девяносто'),
			'a100' => array(1=>'сто',2=>'двести',3=>'триста',4=>'четыреста',5=>'пятьсот',6=>'шестьсот',7=>'семьсот',8=>'восемьсот',9=>'девятьсот'),
			'uc' => array('копейка', 'копейки', 'копеек'),
			'ur' => array('рубль', 'рубля', 'рублей'),
			'u3' => array('тысяча', 'тысячи', 'тысяч'),
			'u2' => array('миллион', 'миллиона', 'миллионов'),
			'u1' => array('миллиард', 'миллиарда', 'миллиардов'),
		);
		
		$translite = is_null($translite) ? $defaultTranslite : $translite;
		
		list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
		$out = array();
		if (intval($rub) > 0) {
		
			// Разбиваем число по три символа
			$cRub = str_split($rub,3); 
			
			foreach($cRub as $uk=>$v) {
				if (!intval($v)) continue; 
				list($i1,$i2,$i3) = array_map('intval',str_split($v,1)); 

				$out[] = isset($translite['a100'][$i1]) ? $translite['a100'][$i1] : ''; // 1xx-9xx
				$ax = ($uk+1 == 3) ? 'a2' : 'a1';
				if ($i2 > 1) 
					$out[]= $translite['a20'][$i2].' '.$translite[$ax][$i3]; // 20-99
				else 
					$out[]= $i2 > 0 ? $translite['a10'][$i3] : $translite[$ax][$i3]; // 10-19 | 1-9
				
				if (count($cRub) > $uk+1){
					$uName = $translite['u'.($uk+1)]; 
					$out[]= self::morph($v,$uName);
				}
			} 
		}
		else $out[] = $translite['null']; 
		
		// Дописываем название "рубли"
		$out[] = self::morph(intval($rub),$translite['ur']); // rub
		// Дописываем название "копейка"
		$out[] = $kop.' '.self::morph($kop,$translite['uc']); // kop
		
		// Объединяем маcсив в строку
		$str = join(' ',$out); 
		
		// Удаляем лишние пробелы и возвращаем результат
		return trim(preg_replace('/ {2,}/', ' ', $str)); 
	}

	/**
	 * Склоняем словоформу
	 */
	public static function morph($number, $titles) {
		$cases = array (2, 0, 1, 1, 1, 2);
		return $titles[ ($number%100>4 && $number%100<20)? 2 : $cases[min($number%10, 5)] ];
	}
	
	// Функция получения кода ссылки из индекса 
	public static function dec2link($id) {
		if(is_string($id)) $id = base_convert($id, 16, 10);
	    $digits='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
	    $link=''; 
	    do { 
	        $dig=$id%62; 
	        $link=$digits[$dig].$link; 
	        $id=floor($id/62); 
	    } while($id!=0); 
	    return $link; 
	}
	
	// Функция получения индекса из кода ссылки 
	public static function link2dec($link) { 
	    $digits=Array('0'=>0,  '1'=>1,  '2'=>2,  '3'=>3,  '4'=>4,  '5'=>5,  '6'=>6, 
	                  '7'=>7,  '8'=>8,  '9'=>9,  'a'=>10, 'b'=>11, 'c'=>12, 'd'=>13, 
	                  'e'=>14, 'f'=>15, 'g'=>16, 'h'=>17, 'i'=>18, 'j'=>19, 'k'=>20, 
	                  'l'=>21, 'm'=>22, 'n'=>23, 'o'=>24, 'p'=>25, 'q'=>26, 'r'=>27, 
	                  's'=>28, 't'=>29, 'u'=>30, 'v'=>31, 'w'=>32, 'x'=>33, 'y'=>34, 
	                  'z'=>35, 'A'=>36, 'B'=>37, 'C'=>38, 'D'=>39, 'E'=>40, 'F'=>41, 
	                  'G'=>42, 'H'=>43, 'I'=>44, 'J'=>45, 'K'=>46, 'L'=>47, 'M'=>48, 
	                  'N'=>49, 'O'=>50, 'P'=>51, 'Q'=>52, 'R'=>53, 'S'=>54, 'T'=>55, 
	                  'U'=>56, 'V'=>57, 'W'=>58, 'X'=>59, 'Y'=>60, 'Z'=>61); 
	    $id=0; 
	    for ($i=0; $i<strlen($link); $i++) { 
	        $id+=$digits[$link[(strlen($link)-$i-1)]]*pow(62,$i); 
	    } 
	    return $id; 
	}
	
	//short link based on time()
	public static function shortString() {
		return self::dec2link(str_shuffle(time()));
	}
	
	public static function wordwrapaddress($s, &$firstLine, &$secondLine, &$thirdLine) {
		$address = $s;
		
		$firstLineLen = 50;//34;
		$secondLineLen = 42;//38;
		$thirdLineLen = 30;//26;
	
		// first line
		if (mb_strlen($address) < $firstLineLen) 
		{
			$firstLine = $address;
			return;
		}
		
		//$firstLine = utf8_wordwrap($address, $firstLineLen, "\n", true);
		
		$firstLine = mb_substr($address, 0, $firstLineLen);
		$address = mb_substr($address, $firstLineLen);

		//second line
		if (mb_strlen($address) < $secondLineLen) 
		{
			$secondLine = $address;
			return;
		}
		
		$secondLine = mb_substr($address, 0, $secondLineLen);
		$address = mb_substr($address, $secondLineLen);
		
		//third line
		$thirdLine = mb_substr($address, 0);
	}
	
	public static function parse_url_if_valid($url)
	{
	    // Массив с компонентами URL, сгенерированный функцией parse_url()
	    $arUrl = parse_url($url);
	    // Возвращаемое значение. По умолчанию будет считать наш URL некорректным.
	    $ret = null;

	    // Если не был указан протокол, или
	    // указанный протокол некорректен для url
	    if (!array_key_exists("scheme", $arUrl)
	            || !in_array($arUrl["scheme"], array("http", "https")))
	        // Задаем протокол по умолчанию - http
	        $arUrl["scheme"] = "http";

	    // Если функция parse_url смогла определить host
	    if (array_key_exists("host", $arUrl) &&
	            !empty($arUrl["host"]))
	        // Собираем конечное значение url
	        $ret = sprintf("%s://%s%s", $arUrl["scheme"],
	                        $arUrl["host"], $arUrl["path"]);

	    // Если значение хоста не определено
	    // (обычно так бывает, если не указан протокол),
	    // Проверяем $arUrl["path"] на соответствие шаблона URL.
	    else if (preg_match("/^\w+\.[\w\.]+(\/.*)?$/", $arUrl["path"]))
	        // Собираем URL
	        $ret = sprintf("%s://%s", $arUrl["scheme"], $arUrl["path"]);

	    // Если url валидный и передана строка параметров запроса
	    if ($ret && empty($ret["query"]))
	        $ret .= sprintf("?%s", $arUrl["query"]);

	    return $ret;
	}
	
	public static function is_url($in){ 
	    /*$w = "a-z0-9"; 
	    $url_pattern = "#( 
	    (?:f|ht)tps?://(?:www.)? 
	    (?:[$w\\-.]+/?\\.[a-z]{2,4})/? 
	    (?:[$w\\-./\\#]+)? 
	    (?:\\?[$w\\-&=;\\#]+)? 
	    )#xi";*/
	    $url_pattern = '/(https?:\/\/)?(www\.)?([-а-яa-zёЁцушщхъфырэчстью0-9_\.]{2,}\.)(рф|[a-z]{2,6})((\/[-а-яёЁцушщхъфырэчстьюa-z0-9_]{1,})?\/?([a-z0-9_-]{2,}\.[a-z]{2,6})?(\?[a-z0-9_]{2,}=[-0-9]{1,})?((\&[a-z0-9_]{2,}=[-0-9]{1,}){1,})?)/i';

	    $a = preg_match($url_pattern,$in); 
	    return $a; 
	}
	
	public static function art($art) {
		//if(strtoupper(substr($art, 0,2)) == 'NF') $art = str_ireplace("NF", "HL", $art);
		//if(stripos($art, 'hl-') !==false) $art = str_ireplace("hl-", "HL", $art);
		if(substr($art, 0,2) == 'b.') $art = str_ireplace("b.", "", $art);
		if(stripos($art, 'nf600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl 600') !==false) $art = 'HL600';
		elseif(stripos($art, 'hl 900') !==false) $art = 'HL900';
		elseif(stripos($art, 'hl900') !==false) $art = 'HL900';
		elseif(stripos($art, 'hl 2200') !==false) $art = 'HL2200';
		elseif(stripos($art, 'hl2200') !==false) $art = 'HL2200';
		elseif(stripos($art, 'hl-2200') !==false) $art = 'HL2200';
		elseif(stripos($art, 'hl4000') !==false) $art = 'HL4000';
		elseif(stripos($art, 'hl-720') !==false) $art = 'HL720';
		elseif(stripos($art, 'hl720') !==false) $art = 'HL720';
		elseif(stripos($art, 'hl-t700') !==false) $art = 'HLT700';
		elseif(stripos($art, 'hlt700') !==false) $art = 'HLT700';
		elseif(stripos($art, 'hl-855') !==false) $art = 'HL855';
		elseif(stripos($art, 'hl855') !==false) $art = 'HL855';
		elseif(stripos($art, 'hl29') !==false) $art = 'HL29';
		elseif(stripos($art, 'hl-29') !==false) $art = 'HL29';
		elseif(stripos($art, 'hl-39') !==false) $art = 'HL39';
		elseif(stripos($art, 'hl39') !==false) $art = 'HL39';
		elseif(stripos($art, 'pf900') !==false) $art = 'PF900';
		elseif(stripos($art, 'pf901') !==false) $art = 'PF901';
		elseif(stripos($art, 'pf902') !==false) $art = 'PF902';
		elseif(stripos($art, 'pf903') !==false) $art = 'PF903';
		elseif(stripos($art, 'pf904') !==false) $art = 'PF904';
		elseif(stripos($art, 'pf-02') !==false) $art = 'PF02';
		elseif(stripos($art, 'pf02') !==false) $art = 'PF02';
		elseif(stripos($art, 'pf-03') !==false) $art = 'PF03';
		elseif(stripos($art, 'pf-04') !==false) $art = 'PF04';
		elseif(stripos($art, 'pf-05') !==false) $art = 'PF05';
		elseif(stripos($art, 'pf-07') !==false) $art = 'PF07';
		elseif(stripos($art, 'pf-09') !==false) $art = 'PF09';
		elseif(stripos($art, 'hl-t6') !==false) $art = 'HLT6';
		elseif(stripos($art, 'hlt') !==false) $art = 'HLT1';
		elseif(stripos($art, 'hl-t') !==false) $art = 'HLT1';		
		elseif(stripos($art, 'hl-100') !==false) $art = 'HLT100';
		elseif(stripos($art, 'hl-101d') !==false) $art = 'HLT101D';
		elseif(stripos($art, 'hl-102d') !==false) $art = 'HLT102D';
		elseif(stripos($art, 'hl170') !==false) $art = 'HL170';
		elseif(stripos($art, 'nf170') !==false) $art = 'HL170';
		elseif(stripos($art, 'hl300') !==false) $art = 'HL300';
		elseif(stripos($art, 'hl500') !==false) $art = 'HL500';
		elseif(stripos($art, 'nf500') !==false) $art = 'HL500';
		elseif(stripos($art, 'hl-500') !==false) $art = 'HL500';
		elseif(stripos($art, 'nf-500') !==false) $art = 'HL500';		
		elseif(stripos($art, 'hl-87') !==false) $art = 'HLT87';
		elseif(stripos($art, 'hl-12s') !==false) $art = 'HL12S';
		elseif(stripos($art, 'g85') !==false) $art = 'G85';
		elseif(stripos($art, '85g') !==false) $art = 'G85';
		elseif(stripos($art, 'hl-p') !==false) $art = 'HLP1';
		elseif(mb_stripos($art, 'фонарь-дубинка') !==false) $art = 'HLP1';
		elseif(mb_stripos($art, 'охотник') !==false or mb_stripos($art, 'комплект') !==false) $art = 'komplekt';
		elseif(stripos($art, 'liion') !==false) $art = 'A5800';
		elseif(stripos($art, '18650') !==false and stripos($art, '5200') !==false) $art = 'A5200';
		elseif(stripos($art, '18650') !==false and stripos($art, '5800') !==false) $art = 'A5800';
		elseif(stripos($art, 'A5200') !==false) $art = 'A5200';
		elseif(stripos($art, 'A5800') !==false) $art = 'A5800';
		elseif(stripos($art, 'A2400') !==false) $art = 'A2400';
		elseif(stripos($art, 'video3in1') !==false) $art = 'gps-ve-450r';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9x40') !==false) $art = 'gamo3-9x40';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9-40') !==false) $art = 'gamo3-9x40';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9x32') !==false) $art = 'gamo3-9x32';
		elseif(stripos($art, 'gamo') !==false and stripos($art, '3') !==false and stripos($art, '9-32') !==false) $art = 'gamo3-9x32';
		elseif(stripos($art, 'Bushnell') !==false and stripos($art, '3') !==false and stripos($art, '9x40') !==false) $art = 'bushnell3-9x40';
		elseif(stripos($art, 'Bushnell') !==false and stripos($art, '3') !==false and stripos($art, '9x32') !==false) $art = 'bushnell3-9x32';
		elseif(stripos($art, 'OPTIK-B3') !==false and stripos($art, '3') !==false and stripos($art, '9x40') !==false) $art = 'bushnell3-9x40';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'alpen10-60x60';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') !==false and stripos($art, '50x50') !==false) $art = 'alpen10-50x50';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') !==false and stripos($art, '70x70') !==false) $art = 'alpen10-70x70';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') ===false and stripos($art, '60x60') !==false) $art = 'alpen60x60';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') ===false and stripos($art, '50x50') !==false) $art = 'alpen50x50';
		elseif(stripos($art, 'alpen') !==false and stripos($art, '10') ===false and stripos($art, '70x70') !==false) $art = 'alpen70x70';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'bresser10-60x60';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '50x50') !==false) $art = 'bresser10-50x50';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '70x70') !==false) $art = 'bresser10-70x70';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') !==false and stripos($art, '90x80') !==false) $art = 'bresser10-90x80';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') ===false and stripos($art, '60x60') !==false) $art = 'bresser60x60';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') ===false and stripos($art, '50x50') !==false) $art = 'bresser50x50';
		elseif(stripos($art, 'bresser') !==false and stripos($art, '10') ===false and stripos($art, '70x70') !==false) $art = 'bresser70x70';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'bushnell10-60x60';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') !==false and stripos($art, '50x50') !==false) $art = 'bushnell10-50x50';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') !==false and stripos($art, '70x70') !==false) $art = 'bushnell10-70x70';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') ===false and stripos($art, '60x60') !==false) $art = 'bushnell60x60';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') ===false and stripos($art, '50x50') !==false) $art = 'bushnell50x50';
		elseif(stripos($art, 'bushnell') !==false and stripos($art, '10') ===false and stripos($art, '70x70') !==false) $art = 'bushnell70x70';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') !==false and stripos($art, '90x80') !==false) $art = 'nikon10-90x80';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') !==false and stripos($art, '90*80') !==false) $art = 'nikon10-90x80';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') !==false and stripos($art, '60x60') !==false) $art = 'nikon10-60x60';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') ===false and stripos($art, '28x40') !==false) $art = 'nikon28x40';
		elseif((stripos($art, 'bino-n') !==false or stripos($art, 'nikon') !==false) and stripos($art, '10') ===false and stripos($art, '70x70') !==false) $art = 'nikon70x70';
		//elseif(stripos($art, 'N83250-1') !==false) $art = 'nikon8-32x50';
		elseif(stripos($art, 'N83250') !==false) $art = 'nikon8-32x50';
		elseif(stripos($art, 'p5050') !==false) $art = 'poisk50x50';
		elseif(stripos($art, 'n1850') !==false) $art = 'nikon18x50';
		elseif(stripos($art, 'b2050') !==false) $art = 'baigish20x50';
		elseif(stripos($art, 'b1050') !==false) $art = 'baigish10x50';
		elseif(stripos($art, 'b1650') !==false) $art = 'baigish16x50';
		elseif(stripos($art, 'b2840') !==false) $art = 'baigish28x40';
		elseif(stripos($art, 'n2050') !==false) $art = 'nikon20x50';
		elseif(stripos($art, 'n750') !==false) $art = 'nikon7x50';
		elseif(stripos($art, 'n1042') !==false) $art = 'nikon10x42';
		elseif(stripos($art, 'BAIGISH') !==false and stripos($art, '10') ===false and stripos($art, 'af') !==false and stripos($art, '70x70') !==false) $art = 'baigish70x70';
		elseif(stripos($art, 'bino-b70x70') !==false) $art = 'baigish70x70';
		elseif(stripos($art, 'b50x50') !==false) $art = 'baigish50x50';
		elseif(stripos($art, 'bino-b32x40') !==false) $art = 'baigish32x40';
		elseif(stripos($art, 'b30x50') !==false) $art = 'baigish30x50';
		elseif(stripos($art, 'bino-b20x50') !==false) $art = 'baigish20x50';
		elseif(stripos($art, 'bino-b20x40') !==false) $art = 'baigish20x40';
		elseif(stripos($art, 'bino-b10x40') !==false) $art = 'baigish10x40';
		elseif(stripos($art, 'bino-b10-90x80') !==false) $art = 'baigish10-90x80';
		elseif(stripos($art, 'BINO-BRECCER70X70') !==false) $art = 'breaker70x70';
		elseif(stripos($art, 'BINO-BRECCER50X50') !==false) $art = 'breaker50x50';
		elseif(stripos($art, 'BINO-BREAKER70X70') !==false) $art = 'breaker70x70';
		elseif(stripos($art, 'LEAPERS') !==false and stripos($art, '6') !==false and stripos($art, '24x50') !==false) $art = 'leapers6-24x50';
		elseif(stripos($art, 'fnp') !==false) $art = 'FNP';
		elseif(stripos($art, 'kompas') !==false) $art = 'KOMPASS';
		elseif(stripos($art, 'upsell-mltt') !==false) $art = 'MLTT';
		elseif(stripos($art, 'Gerber_bearg_113') !==false) $art = 'GERBER_BG';
		elseif(stripos($art, 'optik-c1x35') !==false) $art = 'GAMO1X35';
		elseif(stripos($art, 'monikul-bres35x95') !==false) $art = 'BRESSER35X95';
		elseif(stripos($art, 'upsell-zaryadnik') !==false) $art = 'ZARYADNIK';
				
		elseif(stripos($art, 'MONIKUL-BUSH95X52') !==false) $art = 'bushnell95x52';
		else $art = $art;
		return $art;
	}

	
}