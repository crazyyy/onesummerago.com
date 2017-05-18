<?php
	header("Pragma: no-cache");
	header("Content-type: text/html;charset=UTF-8");
	$GLOBALS['htracer_admin_page']='ajax_test_mysql';
	error_reporting(E_ERROR | E_PARSE);
	include_once('../../HTracer.php');
	error_reporting(E_ERROR | E_PARSE);
	include_once('../functions.php');
	
	function AcceptGlobal($Name)
	{
		$_GET[$Name]=trim($_GET[$Name]);
		if(isset($_GET[$Name]) && $_GET[$Name]!='******')
			$GLOBALS[$Name]=$_GET[$Name];
	}
//	

	AcceptGlobal("htracer_mysql");
	AcceptGlobal("htracer_mysql_login");
	//AcceptGlobal("htracer_mysql_pass");
	if(isset($_GET['htracer_mysql_pass_s']) && $_GET['htracer_mysql_pass_s']!='******')
		$GLOBALS['htracer_mysql_pass']=$_GET['htracer_mysql_pass_s'];
		
	AcceptGlobal("htracer_mysql_dbname");
	AcceptGlobal("htracer_mysql_host");
	AcceptGlobal("htracer_mysql_ignore_mysql_ping");

	$res=hkey_check_connection_to_mysql();
//	$res=20;
//	if($res!=0 && $res!=10)
	echo "<span style='display: none'>$res:</span> ";
	
	if($res==0)
		echo "&nbsp;&nbsp;&nbsp;&nbsp;<span style='color:green'>".ht_trans('Доступы верны')."</span>";
	elseif($res==10)
		echo "&nbsp;&nbsp;&nbsp;&nbsp;<span style='color:blue'>".ht_trans('Данные записываются в файлы').".</span>
			<span class='hint' onclick='ShowHintDialog(0,
				 \"".ht_trans('При записи данных в файлы скорость работы HTracer значительно ниже чем при записи в MySQL')
				 .".<br /><br /> ".ht_trans('Также в этом случае недоступны некоторые возможности HTracer').". \");'>".ht_trans('Подробнее')."...</span>";
	elseif($res==1010)
		echo "<br /><br /><span style='color:blue'>".ht_trans('Возможно, имя БД HTracer и сайта не совпадают').".</span>
			<span class='hint' onclick='ShowHintDialog(0,
				 \"".ht_trans('Если HTracer работает нормально -- вы можете проигнорировать это предупреждение')
				 ."<br /><br />".ht_trans('Это может привести к ошибкам работы HTracer')
				 .". <br /><br />".ht_trans('Например, когда вместо сайта выводится пустая страница при работе с MySQL, но при этом админка отображается нормально')
				 ."<br /><br />".ht_trans('Если вы решили проигнорировать это предупреждение, то убедитесь, в том что')." `".ht_trans('Использовать MySQL')."`=`".ht_trans('Форсировать')."`'  \");'>".ht_trans('Подробнее')."...</span>";
	else
	{
		echo "<span style='color:red'>";
		if($res==15)
			echo "&nbsp;&nbsp;&nbsp;&nbsp;".ht_trans('Сервер не поддерживает MySQL').".</span>";
		elseif($res==20)
			echo "<br /><br />".ht_trans('Возможно, поможет включение игнорирования mysql_ping').".</span>";
		elseif($res==30)
			echo "&nbsp;&nbsp;&nbsp;&nbsp;".ht_trans('Имя пользователя MySQL пустое').".</span>";
		elseif($res==40)
			echo "<br /><br />".ht_trans('Имя пользователя, пароль или хост MySQL не верны').".</span>";
		elseif($res==50)
			echo "&nbsp;&nbsp;&nbsp;&nbsp;".ht_trans('Имя базы данных MySQL пустое').".</span>";
		elseif($res==60)
			echo str_replace(Array('%BDName%','%User%'),Array($GLOBALS["htracer_mysql_dbname"],$GLOBALS["htracer_mysql_login"]),
				"<br /><br />".ht_trans("Базы данных MySQL с именем '%BDName%' не существует, либо у пользователя '%User%' нет к ней прав доступа").".</span>");
		$e=mysql_error();
		$host_error=false;
		if(strpos($e,'getaddrinfo failed')!==false)
		{
			echo '<br /> <i>'.ht_trans('Вероятно, хост MySQL задан не верно').'</i><br />';
			$host_error=true;
		}
		elseif(strpos($e,'server through socket')!==false)
		{
			$oh=$GLOBALS['htracer_mysql_host'];
			$GLOBALS['htracer_mysql_host']='127.0.0.1';
			if($oh!='localhost' || hkey_check_connection_to_mysql())
				echo '<br /> <i>'.ht_trans('Вероятно, хост MySQL задан не верно либо сервер MySQL не запущен').'</i><br />';
			else
				echo '<br /> <i>'.ht_trans('Выставьте хост MySQL = 127.0.0.1').'</i><br />';
			$GLOBALS['htracer_mysql_host']=$oh;
			$host_error=true;
		}	
		elseif(strpos($e,'ccess denied')!==false)
			echo '<br /> <i>'.ht_trans('Вероятно, имя пользователя MySQL или его пароль задан не верно').'</i><br />';
		elseif(strpos($e,'nknown database')!==false)
			echo '<br /> <i>'.ht_trans('Вероятно, имя БД задано не верно').'</i><br />';
		if($host_error)
			echo "<span style='display: none'>host_error</span>";
		if($res==40||$res==60)
		{
			$hint_content='<pre>'.$e.'</pre>';
			$hint_content=str_replace("\n",'\n',$hint_content);
			$hint_content=str_replace("\r",'',$hint_content);
			$hint_content=str_replace("'",'`',$hint_content);
			$hint_content="'$hint_content'";
			//function ShowHintDialog(title,content)
			echo ' <span class="hint" onclick="ShowHintDialog(0,'.
				$hint_content.
				');">'.ht_trans('Подробнее').'...</span>';
		}
	}
	//echo $res;
?>