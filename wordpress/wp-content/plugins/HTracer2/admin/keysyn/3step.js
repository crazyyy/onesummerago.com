var FunDeeep=0;//Нужен для защиты от ложных срабатываний onchange
function trim(str) {
	return str.replace(/(^[\s\xA0]+|[\s\xA0]+$)/g, '');
}
function url_changed(ID,By)
{
	if(FunDeeep)
		return;
	FunDeeep++;
	if(!By)
		By='user';
	Keys.getKeyById(ID).Changed(By);
	FunDeeep--;
}
function inherit_changed(ID)
{
	if(FunDeeep)
		return;
	FunDeeep++;
	Keys.getKeyById(ID).InheritChanded();
	FunDeeep--;
}
var lastKID=-1;
function YaBox(query,KID)
{	
	lastKID=KID;
	$.colorbox({
		href:"yaparse.php?host="
			+encodeURIComponent(Domain)
			+"&query="+encodeURIComponent(query)
			+"&pid=1",
		onOpen:function()    { SetBoxTop(); },
		onLoad:function()    { SetBoxTop(); },
		onComplete:function(){ SetBoxTop(); }	
	});
};
function GoogleCallback (func, data) 
{
	//alert(func);
	//alert(data);
	//var ul = document.createElement("ul");
	var str='';
	$.each(data.results, function(i, val)
	{
		var on='onclick="seturl(\''+val.url+'\')"';
		str=str+
			'<li>'
				+'<span '+on+' class="plink">'+val.title+"</span>"
				+'<div class="passages">'
					+val.content
					+'<br>'
					+'<span '+on+' class="url">'+val.url+'</span>'
				+'</div>'
			+'</li>';
	});//
	if(!str)
		str='<div id="mdiv"><b>Google ничего не выдал</b></div>';
	else
		str='<div id="mdiv"><ol>'+str+'</ol></div>';
	$.colorbox({
		html:str,
		onOpen:function()    { SetBoxTop(); },
		onLoad:function()    { SetBoxTop(); },
		onComplete:function(){ SetBoxTop(); }
	});
}
function SetBoxTop()
{
	var top=parseInt(getBodyScrollTop_ff()) + 100;
	document.getElementById('colorbox').style.top=top+"px";
}
function getBodyScrollTop_ff()  
{  
     return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);  
} 
function GBox(query,KID)
{
	lastKID=KID;
	var q=encodeURIComponent(query+' site:'+Domain);
	$.getJSON("http://ajax.googleapis.com/ajax/services/search/web?v=1.0&callback=GoogleCallback&q="+q+"&context=?",function(data){});
	//$.getJSON("http://ajax.googleapis.com/ajax/services/search/web?v=1.0&key=your-key&callback=GoogleCallback&q='+q+'&context=?",function(data){});
}
function seturl(url,By)
{	
	FunDeeep++;
	if(document.getElementById('kid_'+lastKID+'_url'))
		document.getElementById('kid_'+lastKID+'_url').value=url;
	else
		document.getElementById('Q_'+lastKID+'_URL').value=url;
	if(!By)
		By='user';
	try{
		Keys.getKeyById(lastKID).Changed(By);
	}catch(e){};
	$.colorbox.close();
	FunDeeep--;
}
function TKey(iID,iKey,iClear,iDirty,iURL,iSetBy,iInherit,iChildrensIDs) 
{
	this.ID=iID+0;
	this.Key=iKey;
	this.Clear=iClear+0;
	this.Dirty=iDirty+0;
	this.URL=iURL;
	this.SetBy=iSetBy;
	this.Inherit=iInherit+0;
	this.ChildrensIDs=iChildrensIDs;
	this.Childrens=[];
	this.InheritChanded=function()
	{
		var cb=document.getElementById('kid_'+this.ID+'_inherit');
		if(cb.checked)//=(this.Inherit>0);
			this.Inherit=cb.value+0;
		else
			this.Inherit=0;
	}
	this.Changed=function(By)
	{		
		FunDeeep++;
		this.SetBy=By;
		this.Inherit=0;
		var T=document.getElementById('kid_'+this.ID+'_url').value;
		T=trim(T);
		var T0=T;
		if(T.indexOf('http://'+Domain+'/')===0)
			T=T.replace('http://'+Domain+'/','');
		else if(T.indexOf('http://'+Domain)===0)
			T=T.replace('http://'+Domain,'');
		else if(T.indexOf(Domain+'/')===0)
			T=T.replace(Domain+'/','');
		else  if(T.indexOf(Domain)===0)
			T=T.replace(Domain,'');
		if(!T && T0)	
			T='/';
		else if(T[0]!='/')
			T='/'+T;
		this.URL=T;
		
		this.Synhr();
		for(var i=0;i<this.Childrens.length;i++)
		{
			var child=this.Childrens[i];
			if(!child.URL||child.Inherit>=this.Dirty)
			{
				child.Inherit=this.Dirty;
				child.URL=this.URL;
				child.SetBy=By;
				child.Synhr();
			}
		}
		Keys.CheckURLs();
		FunDeeep--;
	}
	this.Synhr=function()
	{
		FunDeeep++;
		document.getElementById('kid_'+this.ID+'_url').value=this.URL;
		document.getElementById('kid_'+this.ID+'_setby').value=this.SetBy;
		if(this.URL)
			document.getElementById('kid_'+this.ID+'_link').href='http://'+Domain+this.URL;
		else
			document.getElementById('kid_'+this.ID+'_link').href='http://'+Domain+'/';
		var span=document.getElementById('kid_'+this.ID+'_setby_span');
		if(this.SetBy=='none'||!this.SetBy)
			span.innerHTML='нет';
		else if(this.SetBy=='user')
			span.innerHTML='пользователем';
		else 
			span.innerHTML='скриптом';
		var cb=document.getElementById('kid_'+this.ID+'_inherit');
		cb.value=this.Inherit;
		cb.checked=(this.Inherit>0);
		FunDeeep--;
	}
}
/*
	$.ajax(
	{
		url: 'ajax/test.html',
		dataType:'text'
		success: function(data) 
		{
			alert(data);
			//$('.result').html(data);
			//alert('Load was performed.');
		}
	});
*/
function TKeys()
{
	this.Items=[];
	this.IdToKeys=[];
	this.CheckURLsCash={};
	this.CheckURLsErrors={};
	
	this.getKeyById=function(ID){return this.IdToKeys[ID+''];}
	this.CheckURLs=function()
	{
		//alert('0');
		this.CheckURLsErrors={};
		for(var i=0;i<this.Items.length;i++)
		{
			var key=this.Items[i];
			if(key.URL===''||key.URL==='/')
				continue;
			if(!this.CheckURLsCash[key.URL])
			{	
				this.CheckURLsCash[key.URL]!='1000';//Маркируем что мы проверяем
				var URL=key.URL;
				if(key.URL.indexOf('http://')==-1)
				{
					URL='http://'+Domain;
					if(key.URL[0]!='/')
						URL=URL+'/';
					URL=URL+key.URL;	
				}
				$.ajax(
				{
					url:'http://htracer.ru/keysyn/sub_browser.php?status=1'
						+'&url='+encodeURIComponent(URL),
					url0:key.URL,
					url2:URL,	
					dataType:'text',
					success: function(data) 
					{
						Keys.CheckURLsCash[this.url0]=data;
						if(data!='200')	
							Keys.CheckURLsErrors[this.url0]=data;
						Keys.RefreshErrors();
					}
				});
			}
			else if(this.CheckURLsCash[key.URL]!='200'||this.CheckURLsCash[key.URL]!='1000')
				this.CheckURLsErrors[key.URL]=this.CheckURLsCash[key.URL];
		}
		this.RefreshErrors();
	}
	this.RefreshErrors=function()
	{
		//alert(this.CheckURLsErrors.length);
		var Str='';
		//$.each(this.CheckURLsErrors, function(url)
		for(var url in Keys.CheckURLsErrors)
		{
			var code  = Keys.CheckURLsErrors[url];
			if(code==200||code=='')
				return;
			var addon='';
			if(url.indexOf('http://')===-1)
			{
				addon='http://'+Domain;
				if(url[0]!='/')
					addon=addon+'/'	
			}
			var code2 ='';
			if(code=='301')
				code2='Перемещено окончательно (редирект)';
			else if(code=='302')
				code2='Найдено (редирект)';
			else if(code=='303')
				code2='Смотреть другое (редирект)';
			else if(code=='304')
				code2='Не изменялось';
			else if(code=='305')
				code2='Использовать прокси';
			else if(code=='307')
				code2='Временное перенаправление (Temporary Redirect)';
			else if(code=='400')
				code2='Плохой запрос';
			else if(code=='401')
				code2='Требуется авторизация';
			else if(code=='402')
				code2='Требуется оплата (!!!????)';
			else if(code=='403')
				code2='Запрещено';
			else if(code=='404')
				code2='Страница не найдена';	
			else if(code=='405')
				code2='Метод не применим (метод HEAD)';
			else if(code=='408')
				code2='Request Timeout';
			else if(code=='500')
				code2='Внутренняя ошибка сервера';
			else if(code=='501')
				code2='Не реализовано (метод HEAD)';
			else if(code=='503')
				code2='Сервис недоступен';
			else if(code=='504')
				code2='Шлюз не отвечает';	
			Str=Str
				+'<tr>'
					+'<td>'
						+'<a href="'+addon+url+'"><small>'+addon+'</small>'+url+'</b></a>'
					+'</td>'
					+'<td>'+code+'</td>'
					+'<td>'+code2+'</td>'
				+'</tr>';
		}//);
		if(Str)
			Str=
				'<table><caption>HTTP ошибки</caption>'
					+'<tr>'
						+'<th>Страница</th>'
						+'<th>Код</th>'
						+'<th>Описание</th>'
					+'</tr>'
					+Str
				+'</table>';
		document.getElementById('errors').innerHTML=Str;
	}
	this.Add=function(iID,iKey,iClear,iDirty,iURL,iSetBy,iInherit,iChildrensIDs) 
	{
		var key=new TKey(iID,iKey,iClear,iDirty,iURL,iSetBy,iInherit,iChildrensIDs); 
		this.Items.push(key);
		this.IdToKeys[iID+'']=key;
	}
	this.Init=function()
	{//Запускаеться после окончания добавления
/*		var t1=[];
		t1['xx']=1;
		t1['/x/s']=1;
		var t2={};
		t2['xx']=1;
		t2['/x/s']=1;
		var d;
		d++;*/
		for(var i=0;i<this.Items.length;i++)
		{
			var key=this.Items[i];
			key.Childrens=[];
			for(var j=0;j<key.ChildrensIDs.length;j++)
				key.Childrens.push(this.getKeyById(key.ChildrensIDs[j]));
		}
	}
	this.Synhr=function()
	{//Синхронизирует представление с данными
		FunDeeep++;
		for(var i=0;i<this.Items.length;i++)
			this.Items[i].Synhr();
		this.CheckURLs();
		FunDeeep--;
	}
}
function Expand(ID)
{
	var div=document.getElementById('expand_div_'+ID);
	if(div.style.display=='none')
		div.style.display='block';
	else
		div.style.display='none';
}

