<?php // Конфиг Semantic Core Builder

//Яндекс XML
	$Globals['Yandex_XML_str']='http://xmlsearch.yandex.ru/xmlsearch?user=hkey-belousoff&key=03.108321577:795e1ebee00295d9bc56d44f9f50b206';

//Подключение к БД

//хост, логин и пароль 
	if(!$GLOBALS['keysyn_not_coonect'])
	{
		mysql_connect('localhost','root','')  
			or die("<h1>Open config.php and input username and password for MySQL</h1>");

//Имя базы данных!!!
		mysql_select_db('seotest') 
			or die(mysql_error().'<hr /> <h1>Open config.php and input MySQL database name</h1>');
		mysql_query("SET NAMES 'utf8'") or die ('_SET NAMES :'.mysql_error());
	}
	/**/
	
	
	/*
	mysql_connect('localhost','hkey_wpdb','123321')  
		or die("<h1>Open scb_funs.php and input username and password for MySQL</h1>");
	//Имя таблицы
	mysql_select_db('hkey_wpdb') 
		or die(mysql_error().'<hr /> <h1>Open scb_funs.php and input MySQL database name</h1>');
	mysql_query("SET NAMES 'utf8'") or die ('_SET NAMES :'.mysql_error());
	/**/

?>