<?php 
class octabook_general {

	function oct_price_format($amount) {
		$return_price ='';
		
		if(get_option('octabook_currency_symbol_position')=='B') { $return_price .= '<i>'.get_option('octabook_currency_symbol').'</i>'; }		
			if(get_option('octabook_price_format_comma_separator')=='Y') { 
				$return_price .= number_format($amount,get_option('octabook_price_format_decimal_places'),".",','); 
			} else {
				$return_price .= number_format($amount,get_option('octabook_price_format_decimal_places'),".",''); 
			}		
		if(get_option('octabook_currency_symbol_position')=='A') { $return_price .= '<i>'.get_option('octabook_currency_symbol').'</i>'; }
							
		return $return_price;					
	}			
	function oct_price_format_for_pdf($amount) {		$return_price ='';		if(get_option('octabook_currency_symbol_position')=='B') { $return_price .= iconv('UTF-8', 'windows-1252', get_option('octabook_currency_symbol')); }					if(get_option('octabook_price_format_comma_separator')=='Y') { 				$return_price .= number_format($amount,get_option('octabook_price_format_decimal_places'),".",','); 			} else {				$return_price .= number_format($amount,get_option('octabook_price_format_decimal_places'),".",''); 			}						if(get_option('octabook_currency_symbol_position')=='A') { $return_price .= get_option('octabook_currency_symbol'); }									return $return_price;						}		
	
	function oct_price_format_without_currency_symbol($amount) {
		$return_price ='';
			if(get_option('octabook_price_format_comma_separator')=='Y') { 
				$return_price .= number_format($amount,get_option('octabook_price_format_decimal_places'),".",','); 
			} else {
				$return_price .= number_format($amount,get_option('octabook_price_format_decimal_places'),".",''); 
			}	
		return $return_price;	
	}
	
	function convertToHoursMins($time, $format = '%02d:%02d') {
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
	}

	
}