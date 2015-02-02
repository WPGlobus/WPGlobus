<?php

/**
»де€ така€ 
»спользовать фильтр pre_insert_term ( taxonomy.php:2769 )
на входе получаем $term
который может быть вида
- {:en}WPGlobusQA category_name EN{:}{:ru}WPGlobusQA category_name RU{:}
- WPGlobusQA category_name EN
- WPGlobusQA category_name RU

term 
1-ый случай найдЄтс€ по name
2-ой случай найдЄтс€ по slug
3-ий случай не найдЄтс€ и будет создан новый term

наш SELECT найдет и вернЄт по части name его полную версию 

¬озникает друга€ проблема
можно передать в качестве term например - WPGlobus и ожидать, что будет создан новый терм, но он не создатьс€
поскольку будет найден как часть существующего терма.
«начит надо SELECT Ќаписать так, чтобы искалось в €зыковых метках 
{:zz}WPGLobus{:}
[:zz]WPGLobus
<!--:zz-->WPGLobus<!--:-->
либо вообще без меток 'WPGLobus' , но тогда больше ничего в поле name не должно быть.

кроме того в SELECT надо подключить таблицу wp_term_taxonomy чтобы провер€ть $taxonomy.

≈ще один плюс этого способа вижу в независимости от типа €зыковых меток,
если ранее был qT с метками [:zz]  то должно работать 

*/

add_filter( 'pre_insert_term', 'wpglobus_insert_term', 10, 2 );
function wpglobus_insert_term( $term, $taxonomy ) {
		
	//error_log($term);	
	//error_log($taxonomy);
	
	global $wpdb;
	
	$sql = "SELECT name FROM wp_terms WHERE name LIKE '%$term%'";
	
	$var = $wpdb->get_var( $sql );
	
	// error_log('name : ' . $var);
	
	return $var;
}
//wp_set_object_terms( 546, array('WPGlobusQA category_name RU'), 'category'); 
