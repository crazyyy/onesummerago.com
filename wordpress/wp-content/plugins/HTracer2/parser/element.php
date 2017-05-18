<?php
	define('BP_TEXT','text');
	define('BP_CODE','code');
	define('BP_COMMENT','comment');
	define('BP_CDATA','cdata');
	define('BP_TAG','tag');
	define('BP_TAG_END','end');
	
	define('BP_TAG_SIMPLE','tag');
	define('BP_TAG_CLOSE','close');
	define('BP_TAG_SELFCLOSE','selfclose');
	
	class BP_TEXT
	{
		public $isTag=false;
		public $BP_ID=NULL;

		public $Parent=NULL;
		public $Type=BP_TEXT;//BP_TEXT BP_CODE BP_COMMENT BP_TAG_END
		public $Code='';
		
		
		public function __construct($Code, $Type=BP_TEXT, $Parent=NULL)
		{
			$this->Parent=$Parent;
			$this->Code=$Code;
			$this->Type=$Type;
		}
		public function print_r($level=0)
		{
			return;
			echo str_repeat('    ',$level);
			echo $this->Type.' ';
			echo '<small>'.str_replace(Array("\r","\n","\t",'<'),Array('\r','\n','\t','&lt;'),$this->Code)."</small>\n";	
		}
	}
	
	class BP_Element0 extends BP_Element_As_Container
	{
		public $LogicalLevel=-1;
		
		public $isTag=true;
		public $BP_ID=NULL;

		public $Parent=NULL;
		public $Type=BP_TAG_SIMPLE;//BP_TAG_SIMPLE BP_TAG_CLOSE BP_TAG_SELFCLOSE
		
		public $TagName=NULL;
		public $TagData=NULL;
		
		public $CloseTag=NULL;
		
		
		public $ID=NULL;
		public $Classes=Array();
		public $Attributes=Array();
		public $EndTag=false;

		public function is_tag()
		{
			return true;
		}

		public function print_r($level=0)
		{
			echo str_repeat('    ',$level);
			echo $this->TagName.' '.$this->LogicalLevel."\n";
			//echo '<small>'.str_replace(Array("\r","\n","\t",'<'),Array('\r','\n','\t','&lt;'),$this->Code)."</small>\n";	
			foreach($this->Elements as $Item)
				if(!is_string($Item))
					$Item->print_r($level+1);
				
		}
		
		public function GetElementsByTagName_SameLevel($TagName)
		{
			if($this->TagName==$TagName)
				return BP_ElementsList(Array($this),true);
			else
				return BP_ElementsList(Array(),true);
		}
		public function GetElementbyID_SameLevel($ID)
		{
			if($this->ID==$ID)
				return $this;
			else
				return NULL;	
		}
		public function GetElementsbyClassName_SameLevel($Class)
		{
			if(isset($this->Classes[$Class]))
				return BP_ElementsList(Array($this),true);
			else
				return BP_ElementsList(Array(),true);
		}
		
		public function Load($Code,$l=false)
		{
			if(!$l)
				$l=strlen($Code);
			
			$isEndTag=false;

			$this->TagName='';
			static $space_chars=array(' '=>1,"\n"=>1,"\t"=>1,"\r"=>1);
			$TagNameStart=false;
			$IsAutoEndTag=false;
			$TagNameEnd=$l;
			//preg_split("[\r|\n|\t| ]",$text,2);
			for($i=0;$i<$l;++$i)
			{
				$cur=$Code{$i};
				if(!isset($space_chars[$cur]))
				{
					if($TagNameStart===false)
					{
						if($cur==='/')
							$isEndTag=true;
						else
							$TagNameStart=$i;
					}
					elseif($cur==='/')
					{
						$IsAutoEndTag=true;
						$TagNameEnd=$i;
						break;
					}
				}
				elseif($TagNameStart!==false)
				{
					$TagNameEnd=$i;
					break;
				}
			}
			$this->TagName=strtolower(substr($Code, $TagNameStart, $TagNameEnd-$TagNameStart));
			$this->TagData=substr($Code, $TagNameEnd+1);
			if($isEndTag)
				$this->Type=BP_TAG_CLOSE;
			//return;
			$last='';
			if(!$isEndTag && !$IsAutoEndTag)
			{
				for($i=$l-1;$i>$TagNameEnd;--$i)
				{
					$cur=$Code{$i};
					if(!isset($space_chars[$cur]))
					{
						$IsAutoEndTag=($cur==='/');
						break;
					}
				}
			}
			if($IsAutoEndTag)
				$this->Type=BP_TAG_SELFCLOSE;
			
			//----------------------------
			
			
			static $LogicalLevels = Array
			(
				0=>Array('span'=>1,'b'=>1,'strong'=>1),//Инлайн
				1=>Array('div'=>1),//Блоки
				
				10=>Array('td'=>1,'th'=>1,'caption'=>1),//Ячейки
				11=>Array('tr'=>1),//строки
				12=>Array('tbody'=>1,'thead'=>1,'tfoot'=>1),//части текста
				15=>Array('table'=>1),//части
				
				101=>Array('body'=>1,'head'=>1),//части
				102=>Array('html'=>1,'xml'=>1),
			);
			foreach ($LogicalLevels as $Level =>$Tags)
			{
				if(isset($Tags[$this->TagName]))
				{
					$this->LogicalLevel=$Level;
					break;
				}
			}
			if($isEndTag)
				return;
		}
		public function __construct($Code=false, $Parent=NULL)
		{
			$this->Parent=$Parent;
			if($Code)
				$this->Load($Code);
		}
	};
	
	if(function_exists('interface_exists') && interface_exists("ArrayAccess"))
	{
		//Имплементация массива с интерфейсами
		class BP_Element extends BP_Element0 implements ArrayAccess 
		{
			public function offsetSet($offset, $value)  
			{ 
				if($offset===NULL)
					$this->Attributes[]=$value;
				else
					$this->Attributes[$offset]=$value;
			}
			public function offsetExists($offset){return isset($this->Attributes[$offset]);}
			public function offsetUnset($offset) {unset($this->Attributes[$offset]);}
			public function offsetGet($offset)   {return isset($this->Attributes[$offset]) ? $this->Attributes[$offset] : null;}
		};
	}
	else
	{
		class BP_Element extends BP_Element0{};
	}
	

?>