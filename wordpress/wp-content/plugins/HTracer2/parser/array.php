<?php
	if($_SERVER['SERVER_NAME']=='htest.ru') //Если тестовый сервер - показывать все ошибки
		error_reporting(E_ALL);
	
	class BP_Array0 //Имплементация массива
	{
			public $Elements = array();
			public $Virtual = false;//Если виртуальный, значит все элементы - ссылки

			public function __construct($array=NULL,$Virtual=false)
			{
				$this->Virtual=$Virtual;
				$this->Set($array);
			}
			public function Count()
			{
				return count($this->Elements);
			}

			public function Add(&$El,$Key=NULL)
			{
				if($Key===NULL)
				{
					if($this->Virtual)
						$this->Elements[]=&$El;
					else
						$this->Elements[]=$El;
				}
				else
				{
					if($this->Virtual)
						$this->Elements[$Key]=&$El;
					else
						$this->Elements[$Key]=$El;			
				}
			}
			public function Set($array=NULL)
			{
				if(is_object($array))
					$this->Elements = $array->Elements;
				elseif(is_array($array))
					$this->Elements = $array;
				else
					$this->Elements = Array();
			}
			public function Merge($Array)
			{
				if(is_object($Array) ||!is_array($Array))
					$Array=Array($Array);
				foreach($Array as $El)
				{
					if(is_object($El))
						$this->Elements=array_merge($this->Elements,$El->Elements);
					elseif(is_array($El))
						$this->Elements=array_merge($this->Elements,$El);
				}
			}
			public function Clear(){$this->Set(NULL);}
			public function Have($Key){return isset($this->Elements[$Key]);}
	};
	
	if(function_exists('interface_exists') && interface_exists("Iterator"))
	{
		//Имплементация массива с интерфейсами
		class BP_Array extends BP_Array0 implements Iterator,ArrayAccess 
		{
			//Iterator
			public function rewind()	{reset($this->Elements);}
			public function current()	{return current($this->Elements);}
			public function key() 		{return key($this->Elements);}
			public function next() 		{return next($this->Elements);}
			public function valid()
			{
				$key = key($this->Elements);
				return ($key !== NULL && $key !== FALSE);
			}
			//ArrayAccess
			public function offsetSet($offset, $value)  { $this->Add($value,$offset);}
			public function offsetExists($offset){return isset($this->Elements[$offset]);}
			public function offsetUnset($offset) {unset($this->Elements[$offset]);}
			public function offsetGet($offset)   {return isset($this->Elements[$offset]) ? $this->Elements[$offset] : null;}
		};
	}
	else
	{
		class BP_Array extends BP_Array0 {};
	}
?>