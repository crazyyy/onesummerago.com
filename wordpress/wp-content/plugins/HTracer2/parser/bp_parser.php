<?php
		function BP_JoinRanges(&$Ranges,$i,$end,$Count)
		{
			if(strpos($Ranges[$i],$end,3)!==false)
				return;
			for($j=$i+1;$j<$Count; ++$j)
			{	
				$Range=$Ranges[$j];
				$Ranges[$i].='<'.$Range;
				$Ranges[$j]='';
				if(strpos($Range,$end)!==false)
					break;
			}
		}
		function BP_Parse2($Code)
		{
		
			$len=strlen($Code);
			$SpecCode=str_replace('*','_', $Code);
			$SpecCode=str_replace(Array('"',"'",'>'),Array("*","*","*"), $SpecCode);
			
			
			$space_chars=array(' '=>1,"\n"=>1,"\t"=>1,"\r"=>1);
			$space_chars2=array(' '=>1,"\n"=>1,"\t"=>1,"\r"=>1,'='=>1);
			$in_sk=false;
			$in_tag=false;
			$wasName=false;
			$aName='';

			//Комменты, Сидату, Скрипты, Стили
			$Ranges=Array();
			$Count=0;
			

			$TagPrototype=Array('type'=>'tag','attr'=>Array(),'name'=>'');
			$Tag=$TagPrototype;
			//echo ' <br />';

			for($i=0;$i<$len;++$i)
			{
				//echo ' 0 ';
				if(!$in_tag)	
				{
					//echo ' 10 ';
					
					$i2=strpos($Code,'<',$i);
					if($i2===false)	
						$i2=$len-1;
					//if($i!==$i2)	
					//	$Ranges[$Count++]=substr($Code,$i,$i2-$i);
					$in_tag=true;
					$wasName=false;
					//if($i!==0)
					//	$Ranges[$Count++]=$Tag;
					$i=$i2;
					//$Tag=$TagPrototype;
				}
				elseif($in_tag)
				{
					//echo ' 20 ';
					if($in_sk)
					{
						//echo ' 30 ';

						$i2=strpos($Code,$in_sk,$i);
						if($i2===false)	
							$i2=$len-1;
						//if($aName)
						//	$Tag['attr'][$aName]['val']=substr($Code,$i,$i2-$i);
						//else
						//	$Tag['attr'][]=Array('val'=>substr($Code,$i,$i2-$i),'sk'=>$Code{$i-1});
						$i=$i2;
						$in_sk=false;
					}
					else
					{
					
						$i2=strpos($SpecCode,'*',$i);
						if($i2===false)	
						{
							//echo ' 41 ';
							$i2=$len-1;
						}

						$start=false;
						for($j=$i;$j<$i2;$j++)
						{
							if(isset($space_chars2[$Code{$j}]))
							{
								continue;
								if($wasName)
								if($start!==false)
								{
									$aName=strtolower(substr($Code,$start,$j-$start));
									if($wasName)
										$Tag['attr'][$aName]=Array('sk'=>$Code[$i2]);
									else
									{
										$Tag['name']=$aName;
										$aName=false;
									}
									$start=false;
								}
							}
							else
								$start=$j;
						}
						//=explode(' ',substr($Code,$i,$i2-$i));
						$i=$i2;
						//echo strlen($SpecCode).'/'.$i.'/'.$len.'/'.$Code[$i];
						if($Code{$i}==='>')
						{
							$in_tag=false;
							//echo ' 50 ';
						}
						else
							$in_sk=$Code{$i};
					}
				}
			}
			//echo '=='.$Count;
			return $Ranges;
		}
		
		function BP_Parse($Code)
		{
			if($Code==='')
				return;
			//isset почти вдвое быстрее чем (s=='x'||s=='X') 
			//Эти массивы нужны для оптимизации поскорости
			static $space_chars=array(' '=>1,"\n"=>1,"\t"=>1,"\r"=>1);
			static $S_Char=array('S'=>1,"s"=>1);
			static $C_Char=array('C'=>1,"c"=>1);
			static $R_Char=array('R'=>1,"r"=>1);
			static $I_Char=array('I'=>1,"i"=>1);
			static $P_Char=array('P'=>1,"p"=>1);
			static $T_Char=array('T'=>1,"t"=>1);
			static $Y_Char=array('Y'=>1,"y"=>1);
			static $L_Char=array('L'=>1,"l"=>1);
			static $E_Char=array('E'=>1,"e"=>1);
			
			static $AutoEndsTags = Array('!doctype'=>1,'hr'=>1,'br'=>1,'img'=>1,'meta'=>1);
			
			
			$Code=explode('<',$Code);

			$Ranges=Array();
			$count=count($Code);
			$TagPrototype=Array('type'=>'tag','items'=>Array(),'code'=>'');
			$CommentPrototype=Array('type'=>'comment','code'=>'');
			$CDataPrototype=Array('type'=>'comment','code'=>'');
			$CODEPrototype=Array('type'=>'code','code'=>'');
			
			$RN=0;
			for($i=0;$i<$count;++$i)
			{	
				$sep='>';
				$Type1='tag';
				$Type2='text';
				
				$Range=$Code[$i];
				if($Range==='')
					continue;
				if($i===0)
				{
					$Ranges[$RN]=$Range;
					$RN++;
					continue;
				}
				if(isset($Range{2}) && $Range{0}==='!' && $Range{1}==='-' && $Range{2}==='-')
				{
					BP_JoinRanges($Code,$i,'-->',$count);
					$sep='>';
					$Type1='comment';
				}
				elseif(isset($Range{4})
				&& isset($S_Char[$Range{0}])
				&& isset($T_Char[$Range{1}])
				&& isset($Y_Char[$Range{2}])
				&& isset($L_Char[$Range{3}])
				&& isset($E_Char[$Range{4}]))
				{//Смотрим есть ли закрывашка у тега /> 
					$len=strlen($Range);
					$was_sl=false;
					$was_sl=false;
					$len=strpos($Range,">");
					for($j=$len-1;$j>0;--$j)
					{
						$cur=$Range{$j};
						if($cur=='/')
						{
							$was_sl=true;
							break;
						}
						if(!isset($space_chars[$cur]))
							break;
					}
					if(!$was_sl)
					{//ищем конец тега скрипт
						for($j=$i+1;$j<$count; ++$j)
						{	
							$Range2=$Code[$j];
							if(isset($Range2{5}) && $Range2{0}=='/'//</style 
							&& isset($S_Char[$Range2{1}])
							&& isset($T_Char[$Range2{2}])
							&& isset($Y_Char[$Range2{3}])
							&& isset($L_Char[$Range2{4}])
							&& isset($E_Char[$Range2{5}]))	
								break;
							else
							{
								$Code[$i].='<'.$Range2;
								$Code[$j]='';
							}
						}
					}
					$Type2='code';
				}
				elseif(isset($Range{5})//<script 
				&& isset($S_Char[$Range{0}])
				&& isset($C_Char[$Range{1}])
				&& isset($R_Char[$Range{2}])
				&& isset($I_Char[$Range{3}])
				&& isset($P_Char[$Range{4}])
				&& isset($T_Char[$Range{5}]))
				{//Смотрим есть ли закрывашка у тега /> 
					$len=strlen($Range);
					$was_sl=false;
					for($j=$len-1;$j>0;--$j)
					{
						$cur=$Range{$j};
						if($cur=='/')
						{
							$was_sl=true;
							break;
						}
						if(!isset($space_chars[$cur]))
							break;
					}
					if(!$was_sl)
					{//ищем конец тега скрипт
						for($j=$i+1;$j<$count; ++$j)
						{
							$Range2=$Code[$j];						
							if(isset($Range2{6}) && $Range2{0}=='/'//</script 
							&& isset($S_Char[$Range2{1}])
							&& isset($C_Char[$Range2{2}])
							&& isset($R_Char[$Range2{3}])
							&& isset($I_Char[$Range2{4}])
							&& isset($P_Char[$Range2{5}])
							&& isset($T_Char[$Range2{6}]))	
								break;
							else
							{
								$Code[$i].='<'.$Range2;
								$Code[$j]='';
							}
						}
					}
					$Type2='code';
				}
				elseif(isset($Range{7}) && $Range{0}==='!' && $Range{1}==='[' 
				&& $Range{2}==='C' && $Range{3}==='D' && $Range{4}==='A' && $Range{5}==='T' && $Range{6}==='A' && $Range{7}==='[')
				{//<![CDATA[
					BP_JoinRanges($Code,$i,']]>',$count);
					$Type1='cdata';
					$sep=']]>';
				}
				$Range=explode($sep,$Range,2);
				if($Type1==='tag')
				{
					//BP_Label1
					/*
					$ch=false;
					$Range=$Code[$i];
					$len=strlen($Range);
					$in_sk=false;
					for($j=0;$j<$len;$j++)
					{
						$cur=$Range[$j];
					}
					*/
					/*
					if(substr_count($Range[0],'"')%2||substr_count($Range[0],"'")%2)
					{//мы столкнулись с ошибкой быстрого разбора
						$Range=$Code[$i];
						$len=strlen($Range);
						$in_sk=false;
						for($j=0;$j<$len;$j++)
						{
							if($Range{$i}===$ch)
								$in_sk!=$in_sk;
							elseif(!$in_sk && $Range{$i}==='>')
							{
								
							}
						}
						//goto BP_Label1
					}
					*/
					
					//$Ranges[$RN]=$TagPrototype;
					//$T=$Range[0];
					//if($T[0]!=='/')
					//^[a-zа-я0-9_]{5,20}$
					//	$res=preg_split("[\r|\n|\t| ]",$Range[0]);
				}
				elseif($Type1==='comment')
					$Ranges[$RN]=$CommentPrototype;
				elseif($Type1==='cdata')
					$Ranges[$RN]=$CDataPrototype;
				$Ranges[$RN]['code']=$Range[0];
				$RN++;
				if(isset($Range[1]) && $Range[1]!=='')
				{
					if($Type2==='text')
						$Ranges[$RN]=$Range[1];
					else
					{
						$Ranges[$RN]=$CODEPrototype;
						$Ranges[$RN]['code']=$Range[1];
					}
					$RN++;
				}
			}
			return;
			//if(!isset($GLOBALS['printed']))
			//	print_r($Ranges);
			//$GLOBALS['printed']=true;
			//return;
			

			$CurParent=$this;
			$Levels=Array();
			$BP_ID=1;
			
			
			$count=count($Code);
			static $TagCash=Array();
			//foreach($Ranges as $k=>$Data)
			//return;
		
			$Tag = new BP_Element();
			for($k=0;$k<$count;++$k)
			{
				$Range=$Ranges[$k];
				if(is_string($Range))
				{
					if($CurParent->TagName==='script'||$CurParent->TagName==='style')
						$Cur=new BP_TEXT($Code, BP_CODE, $CurParent);
					else
						$CurParent->Elements[]=$Range;
					continue;
				}
				list($Type, $Code)=$Range;
				
				if($Type==BP_TAG)
				{	
					$Tag->Load($Code);
					//continue;
					$Tag->Parent=$CurParent;

					if($Tag->Type==BP_TAG_CLOSE)
					{
						$Added=false;
						
						if($Tag->TagName==$CurParent->TagName && count($Levels)!==0)
						{//Тег опознан
							$CurParent->CloseTag=$Code;
							unset($Levels[count($Levels)-1]);
							
							if(count($Levels)===0)
								$CurParent=&$this;
							else
								$CurParent=&$Levels[count($Levels)-1];
							$Added=true;
						}
						elseif($Tag->LogicalLevel!==-1)
						{
							$Spec=false;
							$c=count($Levels);
							for($i=$c-1;$i>0;$i--)
							{
								$Par=$Levels[$i];
								if($Tag->TagName==$Par->TagName)
								{
									$Spec=true;
									break;
								}
								if($Par->LogicalLevel>$Tag->LogicalLevel && 
								($Tag->LogicalLevel!=1||$Par->LogicalLevel>15))//Div может включать в себя Table
								{
									break;
								}
							}
							if($Spec)
							{
								for($j=$c-1;$j>=$i+1;$j--)
									unset($Levels[count($Levels)-1]);
								$CurParent->CloseTag=$Code;
								$Added=true;
								unset($Levels[count($Levels)-1]);
								$CurParent=&$Levels[count($Levels)-1];
							}
						}
						if(!$Added)
						{
							$Cur=new BP_TEXT($Code, BP_TAG_CLOSE, $CurParent);
							$Cur->BP_ID=$GLOBALS['BP_ELEMENT_ID']++;
							$CurParent->Elements[]=$Cur;
						}
						
					}
					else
					{
						$Spec=false;
						if($Tag->LogicalLevel>=10)
						{//Закрываем все теги данного логического уровня
						//если у нас td (LogicalLevel=10), то он закроет все до прошлого td включительно, 
						//либо table, tr, tbody не включительно... 
							$c=count($Levels);
							for($i=$c-1;$i>0;$i--)
							{
								$Par=$Levels[$i];
								if($Par->LogicalLevel===$Tag->LogicalLevel)
								{
									$Spec=true;
									break;
								}
								elseif($Par->LogicalLevel>$Tag->LogicalLevel||$Par->LogicalLevel===-1||
								($Par->LogicalLevel!==10 && $Par->LogicalLevel===1))
								{
									$i++;
									break;
								}
								else//($Par->LogicalLevel<$Tag->LogicalLevel)
									$Spec=true;
							}
							if($Spec)
							{
								for($j=$c-1;$j>=$i;$j--)
									unset($Levels[count($Levels)-1]);
								if(count($Levels)===0)
									$CurParent=&$this;
								else
									$CurParent=&$Levels[count($Levels)-1];
							}
						}
						$CurParent->AddChild($Tag);
						//$CurParent->Elements[]=$Tag;
						if($Tag->Type==BP_TAG_SIMPLE && !isset($AutoEndsTags[$Tag->TagName]))
						{
							//TR закрывает TD сверху игнорируя все кроме tr и tbody и table
							$Levels[count($Levels)]=&$CurParent->Elements[count($CurParent->Elements)-1];
							$CurParent=&$Levels[count($Levels)-1];
						}
						$Tag = new BP_Element();
					}
				}
				else
				{
					//continue;
					$Cur=new BP_TEXT($Code, $Type, $CurParent);
					$Cur->BP_ID=$GLOBALS['BP_ELEMENT_ID']++;
					$CurParent->Elements[]=$Cur;
				}
			}
		}
		
?>		