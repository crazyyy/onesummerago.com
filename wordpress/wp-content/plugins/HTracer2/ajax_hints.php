<?php	
	$path=dirname(__FILE__);
	if(file_exists($path.'/HTracer.php'))
		include($path.'/HTracer.php');
	else
	{	
		$path=explode('/',$path);
		unset($path[count($path)-1]);
		$path=join('/',$path);
		if(file_exists($path.'/HTracer.php'))
			include($path.'/HTracer.php');
	}	
	if(strtolower($GLOBALS['htracer_encoding'])=='utf-8')
		header("Content-type: text/html;charset=UTF-8");
	else
		header("Content-type: text/html;charset={$GLOBALS['htracer_encoding']}");
	htracer_print_queries_like($_GET['q']);
?>