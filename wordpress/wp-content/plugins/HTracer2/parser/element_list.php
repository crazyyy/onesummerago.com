<?php
	//4.0.1
	if($_SERVER['SERVER_NAME']=='htest.ru') //Если тестовый сервер - показывать все ошибки
		error_reporting(E_ALL);
	$GLOBALS['BP_ELEMENT_ID']=1;
	class BP_ElementsList extends BP_Array//Массива с тегами
	{
		public function Add(&$El)
		{
			if($El->BP_ID!==NULL)
			{
				if($this->Virtual)
					$this->Elements[$El->BP_ID]=&$El;
				else
					$this->Elements[$El->BP_ID]=$El;
			}
			else
			{
				$El->BP_ID=$GLOBALS['BP_ELEMENT_ID']++;
				if($this->Virtual)
					$this->Elements[$El->BP_ID]=&$El;
				else
					$this->Elements[$El->BP_ID]=$El;
			}
		}
		public function GetElementByID($ID)
		{
			$Res=NULL;
			foreach ($this->Elements as $El)
			{
				if(!$El->isTag)
					continue;
				$Res=$El->GetElementByID($ID);
				if($Res!==NULL)
					break;
			}
			return $Res;
		}
		public function GetElementByID_SameLevel($ID)
		{
			$Res=NULL;
			foreach ($this->Elements as $El)
			{
				if($El->isTag && $El->ID==$ID)
					return $El;
			}
			return NULL;
		}
		public function GetElementsByTagName($TagName)
		{
			$Res= new BP_ElementsList(NULL,true);
			foreach ($this->Elements as $El)
			{
				if(!$El->isTag)
					continue;
				if($Res->Have($El->BP_ID))
					continue;
				$Res->Merge($El->GetElementsByTagName($TagName));
			}
			return $Res;
		}
		public function GetElementsByTagName_SameLevel($TagName)
		{
			$Res= new BP_ElementsList(NULL,true);
			foreach ($this->Elements as $El)
			{
				if(!$El->isTag)
					continue;
				if($El->TagName===$TagName)
					$Res->Add($El);
			}
			return $Res;
		}
		public function GetElementsbyClassName($Class)
		{
			$Res= new BP_ElementsList(NULL,true);
			foreach ($this->Elements as $El)
			{
				if(!$El->isTag)
					continue;
				if($Res->Have($El->BP_ID))
					continue;
				$Res->Merge($El->GetElementsbyClassName($Class));
			}
			return $Res;			
		}
		public function GetElementsbyClassName_SameLevel($Class)
		{
			$Res= new BP_ElementsList(NULL,true);
			foreach ($this->Elements as $El)
			{
				if(!$El->isTag)
					continue;
				if(isset($El->Classes[$Class]))
					$Res->Add($El);
			}
			return $Res;
		}
		public function GetElementsbyClass($Class){$this->GetElementsbyClassName($Class);}
		
		
		public function ParseSelector($String)
		{
			$Res=Array();
			$Variants=explode(',',$String);
			foreach ($Variants as $n=>$Variant)
			{
				$Variant=trim($Variant).' ';
			
				$Res[$n]=Array();
				$str='';
			
				$lastCond=' ';
				$lastFree=true;
			
				$len=strlen($Variant);
				for($i=0;$i<$len;$i++)
				{
					$cur=$Variant{$i};
					if($cur=='.' || $cur=='#' || $cur==' ')
					{
						if($str)
						{	
							if(trim($str)=='')
								$lastFree=true;
							else
							{
								$Res[$n][]=Array($lastFree,$lastCond,$str);
								$str='';
								$lastFree=false;
							}
						}
						if(!$str && $cur==' ')
							$lastFree=true;
						$lastCond=$cur;
					}
					else
						$str=$str.$cur;
				}
			}
			return $Res;
		}
		public function Search($Selector)
		{
			$Selector=$this->ParseSelector(Selector);
			$Res= new BP_ElementsList(NULL,true);
		
			foreach($Selector as $Block)
			{
				$cRes=$this;
				if(!count($Block))
					$cRes=new BP_ElementsList(NULL,true);
				foreach($Block as $Cond)
				{
					$Free=$Cond[0];
					$Oper=$Cond[1];
					$Str=$Cond[2];
				
					if($Free)
					{
						if(trim($Oper)=='')
							$cRes=$cRes->GetElementsByTagName($Str);
						if(trim($Oper)=='.')
							$cRes=$cRes->GetElementsByClassName($Str);
						if(trim($Oper)=='#')
							$cRes=$cRes->GetElementByID($Str);
					}
					else
					{
						$cRes=$cRes->GetElementsByTagName_SameLevel($Str);
						if(trim($Oper)=='.')
							$cRes=$cRes->GetElementsByClassName_SameLevel($Str);
						if(trim($Oper)=='#')
							$cRes=$cRes->GetElementByID_SameLevel($Str);						
					}
					if($cRes===NULL)
						$cRes=BP_ElementsList(Array(),true);
					elseif(isset($cRes->TagName))
						$cRes=BP_ElementsList(Array($cRes),true);
					$Res->Merge($cRes);
				}
			}
		}
	};

?>