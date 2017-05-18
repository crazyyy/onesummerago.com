<?php // Для запуска требуется PHP 5 
	$GLOBALS['keysyn_not_coonect']=true;
	include_once("config.php");
	header ("Content-Type: text/html;charset=utf-8");
	//echo '<pre>';
	//print_r($_GET);
	//exit();
	$host  = array_key_exists('host', $_REQUEST)  ? $_REQUEST['host']  : '';
	$query = array_key_exists('query', $_REQUEST) ? $_REQUEST['query'] : 'машина';
	
	//echo $_REQUEST['query'].'<br />';
	//echo mb_convert_encoding(
	$query = mb_convert_encoding($query,'utf-8',mb_detect_encoding($query,array('utf-8','cp1251')));
	$esc   = htmlspecialchars($query);
	$ehost = htmlspecialchars($host);
	if($_REQUEST['host'])
		$search_tail = htmlspecialchars(" host:$ehost");
	$doc = "<?xml version='1.0' encoding='utf-8'?>
			<request>
				<query>$esc{$search_tail}</query>
				<page>$page</page>
			</request>";
	$context = stream_context_create(array(
		'http' => array(
			'method'=>"POST",
			'header'=>"Content-type: application/xml\r\n" .
					  "Content-length: " . strlen($doc),
			'content'=>$doc
		)
	));
	$response = file_get_contents($Globals['Yandex_XML_str'], true, $context);
	if($response && $_REQUEST['single'])
	{
		$xmldoc = new SimpleXMLElement($response);
		$found = $xmldoc->xpath("response/results/grouping/group/doc");
		foreach ($found as $item)
		{
			echo $item->url;
			exit();
		}
		exit();
	}
if(!$_GET['place']):
?>
	<html>
	<head>
		<link media="screen" rel="stylesheet" href="serp.css" />
		<meta name="robots" content="noindex,nofollow" />
	</head>
	<body><div id="mdiv">
<?php	
endif;
	if($response) 
	{
 		$xmldoc = new SimpleXMLElement($response);
		$error = $xmldoc->response->error;
		if (!$error) 
		{
			$found = $xmldoc->xpath("response/results/grouping/group/doc");
			if(!$_GET['place'])
				echo "<ol>";
			$i=0;	
			foreach ($found as $item) 
			{
				$i++;
				if($_GET['place'])
				{
					$U1=strtolower($item->url);
					$U2=strtolower($_GET['place']);
					if(strpos($U1,'http://www.'.$U2)===0
					 ||strpos($U1,'http://'.$U2)===0	
				  	 ||strpos($U1,'www.'.$U2)===0
					 ||strpos($U1,$U2)===0)
					{
						echo($i);
						exit;
					}
					continue;
				}
				echo strtolower($item->url).'<br />';
				echo "<li>";
				if(!$_GET['pid'])
					echo "<a href='{$item->url}'>" . highlight_words($item->title) . "</a>";
				else
					echo "<span onclick='seturl(\"{$item->url}\")' class='plink'>".highlight_words($item->title)."</span>";
				echo "<div class='passages'>";
				if ($item->passages) 
					foreach ($item->passages->passage as $passage) 
						echo highlight_words($passage)."<br/>";
				if(!$_GET['pid'])
					echo "<span class='url'>{$item->url}</span>";
				else
					echo "<span onclick='seturl(\"{$item->url}\")' class='purl'>{$item->url}</span>";
				echo "</div></li>\n";
			}
			if($_GET['place'])
			{
				if(!$i)
					die('1002 ');
				die('1001 ');
			}	
			echo "</ol>\n";
		}
		else
		{
			if($_GET['place'])
				die('1002 ');
			else
				echo "@%#$error\n";
		}
	}
	else
	{
		if($_GET['place'])
			die('1002 ');
		else
			echo "@%#Внутренняя ошибка сервера.\n";
	}
function highlight_words($node)
{
	$stripped = preg_replace('/<\/?(title|passage)[^>]*>/', '', $node->asXML());
	return str_replace('</hlword>', '</strong>', preg_replace('/<hlword[^>]*>/', '<strong>', $stripped));
}
?>
	</div><body>
</html>