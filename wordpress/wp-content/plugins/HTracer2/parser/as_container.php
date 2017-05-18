<?php
	class BP_Element_As_Container extends BP_ElementsList
	{// Представление тега как контейнера других элементов
		//public $Elements = array(); //Первого уровня
		public $parent=NULL;
		
		public $All = array(); //Индексы
		public $byTagName = array();//Индексы
		public $byClassName = array();//Индексы

		public function GetElementsByTagName($TagName)
		{
			if(isset($this->byTagName[$TagName]))
				return $this->byTagName[$TagName];
			else
				return new BP_ElementsList(NULL,false);
		}
		public function GetElementsbyClassName($Class)
		{
			if(isset($this->byClassName[$Class]))
				return $this->byClassName[$Class];
			else
				return new BP_ElementsList(NULL,false);
		}	
		public function GetElementbyID($ID)
		{
			if(isset($this->All[$ID]))
				return $this->All[$ID];
			else	
				return NULL;
		}
		public function AddChild($Tag,$FirstLevel=true)
		{
			if($FirstLevel)
				$this->Elements[]=$Tag;
				
			if(!isset($Tag->Attributes))
				return;
				
			if($Tag->ID!==NULL)
				$this->All[$Tag->ID]=&$this->Elements[count($this->Elements)-1];
			else
				$this->All[]=&$this->Elements[count($this->Elements)-1];

			if($Tag->TagName!==NULL)
			{
				if(!isset($this->byTagName[$Tag->TagName]))
					$this->byTagName[$Tag->TagName]=new BP_ElementsList(NULL,false);
				$this->byTagName[$Tag->TagName]->Elements[]=&$this->Elements[count($this->Elements)-1];	
			}
			foreach ($Tag->Classes as $Class)
			{
				if(!isset($this->byClassName[$Class]))
					$this->byClassName[$Class]=new BP_ElementsList(NULL,false);
				$this->byClassName[$Class]->Elements[]=&$this->Elements[count($this->Elements)-1];	
			}
			if($this->parent!=NULL)
				$parent->AddChild(&$Tag,false);
		}
	};
	
?>