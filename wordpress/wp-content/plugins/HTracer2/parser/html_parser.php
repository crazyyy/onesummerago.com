<?php
	if($_SERVER['SERVER_NAME']=='htest.ru') //Если тестовый сервер - показывать все ошибки
		error_reporting(E_ALL);

	$dn=dirname(__FILE__);
	//include($dn.'/array.php');
	//include($dn.'/element_list.php');
	//include($dn.'/as_container.php');
	//include($dn.'/element.php');
	//include($dn.'/document.php');
	
	include($dn.'/simple_html_dom.php');
	
	
	include($dn.'/bp_parser.php');
	
	
	echo '<pre>';

	$CODE="
		<meta http-equiv='content-type' content='text/html; charset=cp1251' />
		<html>
			<head></head>
			<body> 
				<div></div>
				текст &mdash;
			</body>
		</html>
	";
	$CODE=file_get_contents("test3.html");
	$dom = new DOMDocument('1.0', 'cp1251');
	$dom->substituteEntities = false;
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = false;
	
	
   // libxml_use_internal_errors(TRUE);
    $dom->loadHTML($CODE);
   // libxml_clear_errors();
	
	echo htmlspecialchars($dom->saveHTML());
	
	echo '<hr />';
	 
	 exit();
		
	
	echo '1='.strpos('asd d','x').' ';
	echo '2='.strpos('asd d','x',2);
	
	print_r(BP_Parse2($CODE));
	
	echo '<hr />';
	
	//$CODE=file_get_contents("http://www.google.com/");
	//file_put_contents("test2.html",$CODE);
	
	$CODE=file_get_contents("test.html");
	
	$N=100;


	$start=microtime(true);
	for($i=0;$i<$N;$i++)
		BP_Parse($CODE);
	echo "\n my:".(microtime(true)-$start);
	
	$start=microtime(true);
	for($i=0;$i<$N;$i++)
		BP_Parse2($CODE);
	echo "\n my2:".(microtime(true)-$start);
	
	
	$start=microtime(true);
	for($i=0;$i<$N;$i++)
	{
		$dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        libxml_use_internal_errors(TRUE);
		$CODE.=' ';
        $dom->loadHTML($CODE);
        libxml_clear_errors();
		//$as=$dom->GetElementsByTagName('a');
		//foreach ($dom->getElementsByTagName('a') as $row)
		//	$f=$row->GetAttribute('href');
	}
	echo "\n dom:".(microtime(true)-$start);
	//foreach($html->find('a') as $element) 
    //echo $element->href .' ('. $element->innertext. ')<br>';
	
	//*
	$start=microtime(true);
	for($i=0;$i<$N;$i++)
	{
		$dom = new ht_simple_html_dom(null, true, false);
		$dom->load($CODE, true, false);
		//foreach($dom->find('a') as $element)
		//	$f=$element->href;
	}
	echo "\n simple:".(microtime(true)-$start);
/**/


	echo '<hr />';	
	$T=true;
	
	$Range="style /  
.dsfaq_qa_block{ border-top: 1px solid #aaaaaa; margin-top: 20px; }>
.dsfaq_ol_quest{ }
.dsfaq_ol_quest li{ }
.dsfaq_ol_quest li a{ }
.dsfaq_quest_title{ font-weight: bold; }
.dsfaq_quest{ }
.dsfaq_answer_title{ font-weight: bold; }
.dsfaq_answer{ border: 1px solid #f0f0f0; padding: 5px 5px 5px 5px; }
.dsfaq_tools{ text-align: right; font-size: smaller; }
.dsfaq_copyright{ display: block; text-align: right; font-size: smaller; }
";
	$cur="x";
	$S_Char=array('S'=>1,"s"=>1);

	$Arr=array();
	$ArrTmp=array();
	
	class Cl
	{
		public $v1=0;
		public $v2=Array();
		public $v3=Array();
		public function Load()
		{
		}
		public function __construct()
		{
			$this->Load();
		}

	}
	$El=new Cl();
	
	$Range2='asdasd';
	$start=microtime(true);
	
	$r=false;
	$text="11111111\n 22222222222 \r 3333333333 \t 444444444444   5555555555";
		
	//<f x='if(1<2){}'>
	//если в теге число ' или " нечетно, нужно найти объеденить до след. символа ' или "
	
	

	
	for($i=0;$i<10000;$i++)
	{
		substr_count($text,'"');
		substr_count($text,"'");

		//$text2=str_replace(Array(' ',"\t","\r","\n"),' ',$text);
		//$res=explode(' ',$text2);
	}
	echo "\n replace =".(microtime(true)-$start);
	$Arr=array();
	$start=microtime(true);
	static $space_chars=array(' '=>1,"\n"=>1,"\t"=>1,"\r"=>1);

	$t=Array('v1'=>0,'v2'=>Array(),'v3'=>Array());
	for($i=0;$i<10000;$i++)
	{
		count_chars($text, 1);

		//$res=preg_split('/\s{1,999}/',$text);
	}
	echo "\n {} =".(microtime(true)-$start);
	echo "<hr>";
	
	$t=Array('v1'=>0,'v2'=>Array(),'v3'=>Array());
	$t2=$t;
	
	$t['v1']='x';
	echo ' '.$t['v1'].' '.$t2['v1'];
	
	//print_r();
	echo "<hr>";
	print_r(preg_split('/\s{1,999}/',$text));
	echo "<hr>";
	
	print_r(preg_split("['|\"|>]","111>222'333"));
	
/*	
	$X=new BP_Document0("
		<html>
			<!--<tag2>asdas</tag2>dasd-->
			<!--asdasdasd-->
			<head></head>
			<!--<tag1>asdasdasd-->
			<body> 
				Text
				<script>
					Code
				</script>
			</body>
			<!--<tag2>asdas</tag2>dasd-->
		</html>
	");
	*/
	class par
	{
		function f2()
		{
			echo 'f2';
		}
		function f1()
		{
			echo 'f1';
			$this->f2();
		}
	}
	class child extends par
	{
		function f2()
		{
			echo 'f3';
		}
	}
	$p= new child;
	$p->f1();
?>