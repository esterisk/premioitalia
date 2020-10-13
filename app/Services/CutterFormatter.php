<?php
namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Lang;
use \DateInterval;

class CutterFormatter {

	static function period($string) {
			if (preg_match('|(\d\d\d\d)-06-S|',$string, $m)) return trans('cutter.first').' '.trans('cutter.semester').' '.$m[1];
		elseif (preg_match('|(\d\d\d\d)-12-S|',$string, $m)) return trans('cutter.second').' '.trans('cutter.semester').' '.$m[1];
		elseif (preg_match('|(\d\d\d\d)-03-Q|',$string, $m)) return trans('cutter.first').' '.trans('cutter.quarter').' '.$m[1];
		elseif (preg_match('|(\d\d\d\d)-06-Q|',$string, $m)) return trans('cutter.second').' '.trans('cutter.quarter').' '.$m[1];
		elseif (preg_match('|(\d\d\d\d)-09-Q|',$string, $m)) return trans('cutter.third').' '.trans('cutter.quarter').' '.$m[1];
		elseif (preg_match('|(\d\d\d\d)-12-Q|',$string, $m)) return '4'.trans('cutter.nth').' '.trans('cutter.quarter').' '.$m[1];
		elseif (preg_match('|(\d\d\d\d)-12-Y|',$string, $m)) return trans('cutter.year')." ".$m[1];
		elseif (preg_match('|(\d\d\d\d)-(\d\d)|',$string, $m)) {
			$date = new Carbon($m[0]."-15");
			switch (Lang::getLocale()) {
				case 'it': setlocale(LC_TIME, 'it_IT'); break;
				default: setlocale(LC_TIME, 'en_US'); break;
			}
			return $date->formatLocalized('%B %G');
		}
		else return trans('cutter.period_total');
	}
	
	public static function slug($string) {
		$slug = Str::slug(preg_replace("/[‘’'“”–—•…]/",'-',$string));
		return $slug ? $slug : '-';
	}
	
	public static function number($number, $decimals = false, $tryLess = false) {
		if ($decimals === false) $tryLess = true;
		$comma = Lang::getLocale() == 'it' ? ',' : '.';
		$thsep = Lang::getLocale() == 'it' ? '.' : ',';
		if ($tryLess) {
			for ($i = 0; $i < $decimals; $i++) {
				if ($number == round($number, $i)) {
					$decimals = $i; 
					break;
				}
			}
		}		
		return number_format($number, $decimals, $comma, $thsep);
	}
	
	public static function dateAdd($date, $days) {
		$date = new Carbon($date);
		$interval = new DateInterval('P'.$days.'D');
		return $date->add($interval);
	}

	public static function dateSub($date, $days) {
		$date = new Carbon($date);
		$interval = new DateInterval('P'.$days.'D');
		return $date->sub($interval);
	}

	public static function date($date, $format) {

		if (!$date) return '';
		if ($date == 'now' || $date == 'today') $date = date('Y-m-d H:i:s');
		if ($date == '0000-00-00' || $date == '0000-00-00 00:00:00') return '';

		if (!is_object($date) || !($date instanceof Carbon)) {
			$date = new Carbon($date);
		}
		
		switch ($format) {
			case 'Y-m-d': $format = '%G-%m-%d'; break; 
			case 'd/m/Y': case 'd/m/y': case 'dmy': $format = (Lang::getLocale() == 'en' ? '%m/%d/%G' : '%d/%m/%G'); break; 
			case 'd M': $format = (Lang::getLocale() == 'en' ? '%B %e' : '%e %B'); break;
			case 'd M Y': $format = (Lang::getLocale() == 'en' ? '%B %e, %G' : '%e %B %G'); break;
			case 'd/m-R': 
				if ($date->format('Y-m-d') == date('Y-m-d')) return Lang::get('cutter.today');
				else $format = (Lang::getLocale() == 'en' ? '%m/%d' : '%d/%m'); 
				if ($date->format('Y') != date('Y')) $format .= '/%G';
				break;
		}
		
		switch (Lang::getLocale()) {
			case 'it': setlocale(LC_TIME, 'it_IT'); break;
			default: setlocale(LC_TIME, 'en_US'); break;
		}
		
		$string = $date->formatLocalized($format);
		if (Lang::getLocale() == 'it') $string = strtolower($string);
		return $string;
	}

	public static function zurl($input) {
		return urlencode(rtrim(strtr(base64_encode(gzdeflate(json_encode($input), 9)), '+/', '-_'), '='));
	}
	
	public static function unzurl($input) {
		return json_decode(gzinflate(base64_decode(strtr(urldecode($input), '-_', '+/'))),true);
	}

	static function euro($number, $zero = true, $spanize = false) {
		$number = round($number, 2);
		if ($number == 0 && !$zero) return '';
		if ($number == 0 && $zero !== true) return $zero;
		$meno = '';
		if ($number < 0) { $meno = '- '; $number = -$number; $class = 'negative-number'; } else { $class = 'positive-number'; }
		
		$result = '&euro;&nbsp;'.$meno.CutterFormatter::number($number,2);
		if ($spanize) return '<span class="'.$class.'">'.$result.'</span>';
		else return $result;
	}
	
	static function percent($number, $zero = true) {
		$number = round($number, 2);
		if ($number == 0 && !$zero) return '';
		if ($number == 0 && $zero !== true) return $zero;
		$meno = '';
		if ($number < 0) { $meno = '- '; $number = -$number; }
		return $meno.CutterFormatter::number($number,2,true).'%';
	}
	
	static function parseDate($date, $locale = false)
	{
		if (!$locale) $locale = Lang::getLocale();
		if ($locale == 'it') {
			$date = str_replace('/', '-', $date);
		}
		$t = strtotime($date);
		if (!$t) return false;
		else return date('Y-m-d', $t);
	}
	
	static function selectLabels($options, $lang)
	{
		$newOptions = [];
		foreach ($options as $option) $newOptions[$option] = Lang::get($lang.'.'.$option);
		return $newOptions;
	}

}
