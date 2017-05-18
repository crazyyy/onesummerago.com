	function trim(str) {
		return str.replace(/(^[\s\xA0]+|[\s\xA0]+$)/g, '');
	}
	function TFilters() 
	{
		this.Items=[];
		this.Load=function()
		{
			for(N in KeyTree)
			{
				this.Add(N,true);
				this.Add(N,false);
			}
		}
		this.Add=function(iN, iIsPositive)
		{
			//for(var i=0; i<this.Items.length; i++)	
			//	if(this.Items[i].N==iN)
			//		this.Items[i].Deleted=true;
			this.Items.push(new TFilter(iN, iIsPositive,this));
		}
		this.PrintEnabled=function(Name)//Для дебага 
		{
			var pStr='';
			var mStr='';
			for(var i=0; i<this.Items.length; i++)	
			{
				if(this.Items[i].Deleted||!this.Items[i].Enabled)
					continue;
				var add=this.Items[i].Addon;
				if(!add)
					add="***";
				if(this.Items[i].IsPositive)
					pStr=pStr+'|'+add;
				else
					mStr=mStr+'|'+add;
			}
			alert(Name+'\n'+pStr+'\n-'+mStr);
		}
		this.ResToCount=function(CkMass)
		{//Возвращает число успешных применения фильтров
			var CkMass2 =this.ResToArray(CkMass.length);
			Count=0;
			for(var i=0; i<CkMass.length; i++)
				if(CkMass[i]==CkMass2[i])
					Count++;
			return Count;		
		}
		this.ResToArray=function(Count)
		{//Возвращает массив
			var CkMass=[];
			for(var i=0;i<Count; i++)
				CkMass.push(false);	
			for(var d=0; d<2; d++)
			{
				var positive=!d;
				for(var i=0; i<this.Items.length; i++)
				{	
					if(this.Items[i].Deleted||!this.Items[i].Enabled)
						continue;
					if(this.Items[i].IsPositive==positive)
						for(var j=0; j<this.Items[i].All.length; j++)
							CkMass[this.Items[i].All[j]]=positive;
				}
			}
			return CkMass;
		}
		this.GetEnabledFiltersCount=function()
		{
			var EnabledFiltersCount=0;
			for(var i=0; i<this.Items.length; i++)
				if(this.Items[i].Enabled)
					EnabledFiltersCount++;
			return  EnabledFiltersCount;
		}
		this.ToStr=function()
		{
			var Print=0;

			//Запоминаем текущее состояние
			var CkMass= GetCkMass();

			var els=$(".key_cb");
			var SelectedCount=0;
			for(var i=0;i<els.length; i++)
				if(els[i].checked)
					SelectedCount++;			
			for(var i=0; i<this.Items.length; i++)	
				this.Items[i].Enabled=true;
			//if(Print)
			//	this.PrintEnabled('x0');	
			for(var i=0; i<this.Items.length; i++)	
			{
				if(this.Items[i].Deleted)
					continue;
				//Минус фильтры мы проверяем на полное соответсвие	
				//Плюс фильтры мы проверяем на наличие одного соответсвия
				this.Items[i].Enabled=!this.Items[i].IsPositive;
				for(var j=0; j<this.Items[i].All.length; j++)
				{
					var N2=this.Items[i].All[j];
					if((this.Items[i].IsPositive && CkMass[N2])
					 ||(!this.Items[i].IsPositive && CkMass[N2]))
					 {
						this.Items[i].Enabled=this.Items[i].IsPositive;
						break;
					}
				}
				
				for(var j=0; j<this.Items[i].All.length; j++)
				{
					this.Items[i].Clear=true;
					var N2=this.Items[i].All[j];
					if(this.Items[i].IsPositive != CkMass[N2])
					{
						this.Items[i].Clear=false;
						break;
					}
				}
			}
			if(Print)
				this.PrintEnabled('x10');
			var Is0Enabled=false;//mainkey
			//Удаляем все фильтры у которых родитель того же направления Enabled
			for(var i=0; i<this.Items.length; i++)
			{
				if(this.Items[i].Deleted||!this.Items[i].Enabled)
					continue;
				if(this.Items[i].N==0)
					Is0Enabled=true;
				
				//continue;
	
				for(var j=0; j<this.Items.length; j++)
				{
					if(i==j||this.Items[j].Deleted||!this.Items[j].Enabled
					||this.Items[i].IsPositive!=this.Items[j].IsPositive
					||!this.Items[j].Clear)
						continue;
					var br2=false;	
					for(var k=0; k<this.Items[j].Childrens.length; k++)
					{
						if(this.Items[j].Childrens[k]==i)
						{
							//if(!this.Items[j].IsPositive)
							//	alert(this.Items[i].Addon+'<<'+this.Items[j].Addon);
							this.Items[i].Enabled=false;
							//br2=true;
							break;
						}
					}
					//if(br2)
					//	break;
				}
			}
			if(Print)
				this.PrintEnabled('x20');
				
			//Удаляем все фильтры которые не увеличивают число попаданий
			var sc0=this.ResToCount(CkMass);
			for(var d=0; d<=10; d++)//
			{
				//for(var i=this.Items.length-1; i>=0; i--)
				var	wasChange=false;
				for(var i=0; i<this.Items.length; i++)
				{//в обратную сторону, поскольку, вначале стоят более общие фильтры
					if(this.Items[i].Deleted||!this.Items[i].Enabled)
						continue;
					//пытаемся вырубить всех детей вместе			
					if(this.Items[i].N==1)
					{
						var sdft=this.Items[i].Text;
						var dsfst='asdas';
					}
					//Проверяем удаление этого кея изменит ли результат	
					this.Items[i].Enabled=false;	
					sc=this.ResToCount(CkMass);	
					this.Items[i].Enabled=true;

					if(sc0<=sc)//результат не ухудшился
					{
						wasChange=true;
						sc0=sc;
						// Теперь пытаемся отключить всех детей
						scR=this.Items[i].TryToDisableAllChildrens(CkMass,sc0);	
						if(scR)
							sc0=scR;
						else
							this.Items[i].Enabled=false;	
					}
				}
				if(!wasChange)
					break
			}
			if(Print)
				this.PrintEnabled('x30');

			//Если фильтров больше чем запросов

			if(this.GetEnabledFiltersCount()>=SelectedCount)
				for(var i=0; i<this.Items.length; i++)
					this.Items[i].Enabled=false;
			
			if(Print)
				this.PrintEnabled('x40');	

				
			/*
			if(EnabledCount>=SelectedCount)
			{
				var rStr='';
				for(var i=0;i<els.length; i++)
				{
					if(els[i].checked)
					{
						if(rStr)
							rStr=rStr+'|';
						rStr=rStr+'"'+els[i].value+'"';
					}
				}
				if(SelectedCount!=1)
					rStr='('+rStr+')';
				return rStr;
			}
			*/
			
			var PFilters=[];
			var NFilters=[];
			for(var i=0; i<this.Items.length; i++)
			{
				if(this.Items[i].Deleted||!this.Items[i].Enabled)
					continue;
				if(this.Items[i].IsPositive)
					PFilters.push(this.Items[i]);
				else
					NFilters.push(this.Items[i]);
			}
			//Теперь смотрим что у нас получилось
			var CkMass2= this.ResToArray(CkMass.length);
			var pVals=[];//Значения добавочных элементов
			var nVals=[];
			var pAddons=[];//Уточнения добавочных элементов
			var nAddons=[];
			var pVals0=[];//Добавочные у которых нет уточнений
			var nVals0=[];
			for(var i=0; i<CkMass.length; i++)			
			{	
				if(CkMass2[i]!=CkMass[i])
				{
					var cb=document.getElementById('key_cb_'+i);
					var val=cb.value;
					var add=$(cb).attr('addon');
					if(KeyTree[i] && KeyTree[i].length)
					{
						val='"'+val+'"';
						if(CkMass[i])
							pVals0.push(val);
						else
							nVals0.push(val);
					}
					else
					{	
						if(CkMass[i])
							pAddons.push(add);						
						else
							nAddons.push(add);						
					}
					if(CkMass[i])
						pVals.push(val);
					else
						nVals.push(val);
				}
			}
			var pStr='';
			if(PFilters.length==0)
			{
				if(pVals.length==0)
					pStr=''; 
				else if(pVals.length==1)
					pStr=pVals[0];
				else
					pStr='('+pVals.join('|')+')';
			}
			else if(PFilters.length==1)
			{
				pStr=PFilters[0].Text;
				if(pVals.length)
					pStr='('+pStr+'|'+pVals.join('|')+')';	
			}
			else 
			{
				var pStrText='';
				for(var i=0; i<PFilters.length; i++)
				{	
					if(PFilters[i].N==0)
						continue;
					if(pStr)
					{
						pStr=pStr+'|';
						pStrText=pStrText+'|';
					}
					pStr=pStr+PFilters[i].Addon;
					pStrText=pStrText+PFilters[i].Text;
				}
				if(!pVals0.length)
				{
					if(pAddons.length)
						pStr=pStr+'|'+pAddons.join('|');
					pStr=mainkey+' ('+pStr+')';
				}
				else
					pStr='('+pStrText+'|'+pVals.join('|')+')';
			}
			if(pStr=='')
				return 'Ничего не выбрано';
			var nStr='';
			for(var i=0; i<NFilters.length; i++)
			{
				var add =NFilters[i].Addon;
				if(add.indexOf(' ')!==-1)
					add='('+add+')';
				nStr=nStr+' -'+add;
			}
			for(var i=0; i<nAddons.length; i++)
			{
				var add=nAddons[i];
				if(add.indexOf(' ')!==-1)
					add='('+add+')';
				nStr=nStr+' -'+add;	
			}
			for(var i=0; i<nVals0.length; i++)
				nStr=nStr+' -'+nVals0[i];
				
			var Res=trim(pStr)+' '+trim(nStr);	
			Res=trim(Res);
			//alert(Res);
			return Res; 
		}
			
		
	}
	function TFilter(iN, iIsPositive, iBase) 
	{
		this.N=iN;
		this.Enabled=false;
		this.TmpEnabled=false;
		this.Deleted=false;
		this.Base=iBase;

		this.IsPositive=iIsPositive;
		
		this.All=[];//Дети и сам N
		this.All.push(this.N);
		this.Childrens  = [];	
		this.ChildrensA = [];	
		this.TryToDisableAllChildrens = function(CkMass, sc0)
		{	
			//ГЛючит чего-то
			return false;
			
			if(!this.Childrens.length)
				return false
			if(!this.IsPositive)
				return false;
			$Was=false;
			for(var i=0; i<this.Base.Items.length; i++)
			{	
				if(this.Base.Items[i].IsPositive==this.IsPositive
				 && this.ChildrensA[this.Base.Items[i].N])
				{
					$Was=true;
					this.Base.Items[i].TmpEnabled=this.Base.Items[i].Enabled;
					this.Base.Items[i].Enabled=false;
				}
			}
			if($Was)
				return false;
			sc=this.Base.ResToCount(CkMass);	
			if(sc0==sc)
				return sc; 
			for(var i=0; i<this.Base.Items.length; i++)
				if(this.Base.Items[i].IsPositive==this.IsPositive
				 && this.ChildrensA[this.Base.Items[i].N])
					this.Base.Items[i].Enabled=this.Base.Items[i].TmpEnabled;
			return false;
		}
		if(KeyTree[this.N])
		{
			for(var i=0; i<KeyTree[this.N].length; i++)	
			{
				this.All.push(KeyTree[this.N][i]);
				this.Childrens.push(KeyTree[this.N][i]);
				this.ChildrensA[KeyTree[this.N][i]]=1;
			}
		}
		this.Parents  = [];	
		this.ParentsA = [];	
		for(var i in KeyTree)
		{
			for(var j=0; j<KeyTree[i].length; j++)	
			{
				this.Parents.push(KeyTree[i][j]);
				this.ParentsA[KeyTree[i][j]]=1;
			}
		}	
		this.cb=document.getElementById('key_cb_'+this.N);
		this.Text=this.cb.value;
		this.Addon=$(this.cb).attr('addon');
	}
	var Filters=new TFilters();
	//
	function refresh_selection()
	{
		UndoBufer.inRefresh=true;
		refresh_selection0();
		
		
		
		UndoBufer.inRefresh=false;
		//setTimeout("refresh_selection0()",10);
	}
	function nextdialog()
	{
		if(!document.getElementById('savefrombtn')||
			document.getElementById('savefrombtn').disabled) 
			return true;
		return confirm('Вы не сохранили результаты! Вы действительно хотите продолжить?');
	}
	function refresh_selection0()
	{
		var rem='';
		var els=$(".key_cb");
		var clearCount=0;
		var Count=0;
		var selvalues=[];
		for(var i=0;i<els.length; i++)
		{
			var el=els[i];
			if(el.checked)
			{
				Count++;
				if(rem)
					rem+="\n";
				rem+=el.value;
				selvalues.push(el.value);
				clearCount+=parseInt($(el).attr('clearcount'));
			}
		}
		if(document.getElementById('s2_selected'))
			document.getElementById('s2_selected').value=selvalues.join('#');
		if(document.getElementById('savefrombtn'))
			document.getElementById('savefrombtn').disabled=false;
		document.getElementById('selkeys1').innerHTML=rem;
		document.getElementById('selkeys2').innerHTML=rem;
		if(!rem)
		{
			document.getElementById('oselkeys1').style.display='none';
			document.getElementById('oselkeys2').style.display='none'
		}
		else	
		{
			document.getElementById('oselkeys1').style.display='block';
			document.getElementById('oselkeys2').style.display='block'
		}
		//document.getElementById('selkeys2').focus();
		//document.getElementById('selkeys2').select();
			
		var fstr=Filters.ToStr();
		document.getElementById('fselkeys1').innerHTML=fstr;
		document.getElementById('fselkeys2').innerHTML=fstr;
		Count = Count+'';
		var last=Count[Count.length-1];
		var last2='';
		if(Count.length>1)
			last2=Count[Count.length-2];
		var KeyStr='ключевика';
		var Choose='Выбрано';
		if(last2=='1'|| parseInt(last) > 4||last=='0')	
			KeyStr='ключевиков';
		else if(last == '1')
		{
			KeyStr='ключевик';
			Choose='Выбран';
		}
		var info=Choose+' <b>'+Count+'</b> '+KeyStr;
		info=info+', с ожидаемым числом показов: <b>'+clearCount+'</b>.';
		
		document.getElementById('selkeys1info').innerHTML=info;
		document.getElementById('selkeys2info').innerHTML=info;
		
	}
	function key_span_click(event,N)
	{
		if(!event) 
			event = window.event;
		if(!event||!event.ctrlKey)
		{
			var t=document.getElementById('key_cb_'+N);
			if(t.checked)
				t.checked='';
			else
				t.checked='checked';
			refresh_selection();	
			UndoBufer.Save();
		}
	}
	function key_cb_click(event,N)
	{	
		if(!event) 
			event = window.event;
		if(!KeyTree[N]) //!event.ctrlKey
		{
			UndoBufer.Save();
			refresh_selection();
			return true;	
		}
		var t=document.getElementById('key_cb_'+N);
		var val='';
		if(t.checked)
		{
			if(N!=0)
				tFilters[N]=N;
			val='checked';
		}
		else if(N!=0)
			tFilters[N]=-N;
		for(var i=0;i<KeyTree[N].length; i++)
		{
			var j=KeyTree[N][i];
			document.getElementById('key_cb_'+j).checked=val;
		}
		UndoBufer.Save();
		refresh_selection();
		return true;
	}
	function GetCkMass()
	{
		var els=$(".key_cb");
		var CkMass=[];
		for(var i=0;i<els.length; i++)
			CkMass.push(false);	
		for(var i=0;i<els.length; i++)
			CkMass[$(els[i]).attr('ival')]=!(!(els[i].checked));
		return CkMass;
	}
	function TUndoBufer()
	{
		this.Items=[];
		this.Pos=-1;
		this.Max=-1;
		this.inRefresh=false;
		this.inUndo=false;
		
		this.Save=function()
		{
			if(!this.inRefresh && !this.inUndo)
			{
				this.Pos++;
				this.Max=this.Pos;
				var CkMass= GetCkMass();
				if(this.Items.length==this.Pos)
					this.Items.push(CkMass);
				else
					this.Items[this.Pos]=CkMass;
			}
		}
		this.Load=function()
		{
			var CkMass=this.Items[this.Pos];
			var els=$(".key_cb");
			for(var i=0;i<els.length; i++)
			{
				if(CkMass[$(els[i]).attr('ival')])
					els[i].checked='checked';
				else
					els[i].checked='';
			}
			refresh_selection();
		}
		this.Undo=function()
		{
			this.inUndo=true;
			if(this.Pos>0)
			{
				this.Pos--;
				this.Load();
			}
			this.inUndo=false;
		}
		this.Repeat=function()
		{
			this.inUndo=true;
			if(this.Pos<this.Max)
			{
				this.Pos++;
				this.Load();
			}
			this.inUndo=false;
		}
	}
	function body_onkeypress(e)
	{
		if(!e) 
			e = window.event;
		if(e.ctrlKey)//z=90 y=89
		{
			if(e.keyCode==90)//ctrl+z
				UndoBufer.Undo();
			else if(e.keyCode==89)//ctrl+y
				UndoBufer.Repeat();
		}
	}