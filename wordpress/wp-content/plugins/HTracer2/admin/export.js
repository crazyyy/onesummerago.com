function GetAnchorFromLink(Link)
{
	var a=Link.split('>');
	if(a.length<3)
		return false;
	a=a[3];
	if(a.indexOf('<')!==-1)
		a=a.split('<');
	else
		a=a.split('&lt;');
	return a[0];
}
var ya_cash=[];
var g_cash=[];
var cur_pos=0;
var strs;
var res_strs;
var del_count=0;
var ya_requests=0;

function out_positions()
{
	var positions='';
	for(key in ya_cash)
	{	
		var pos=ya_cash[key];
		var len=50-key.length;
		for(var i=0;i<len;i++)
			key+=' ';
		if(pos===1001||pos==='1001' ||pos==='1001 ')
			pos='>10';
		if(pos===1002||pos==='1002' ||pos==='1002 ')
			pos='No Data <small>check Yandex.XML limit</small>';	
		//positions+='<tr><td>'+key+'</td><td>'+pos+'</td></tr>';
		positions+=key+'   '+pos+'\n';
	}
	//positions='<table>'+positions+'</table>';
	positions='<pre>'+positions+'</pre>';
	document.getElementById('positions').innerHTML=positions;
}
function start_optimization()
{
	
/*
	document.getElementById('opt_btn').disabled=true;
	document.getElementById('optproject').style.display='block';
	
	del_count=0;
	strs=document.getElementById('ta').value;
	if(!strs)
		strs=document.getElementById('ta').innerHTML;
	strs=strs.split("\n");	
	strs.push('');
	document.getElementById('all').innerHTML=strs.length;
	cur_pos=0;
	res_strs='';
	optimization_next();
*/
}
function optimization_next()
{
	//alert('optimization_next');
	if(strs.length>=cur_pos)
	{
		out_positions();
		document.getElementById('calc').innerHTML=cur_pos;
		document.getElementById('del').innerHTML=del_count;
		document.getElementById('ya_req').innerHTML=ya_requests;
	}
	if(strs.length<=cur_pos)
	{
		if(document.getElementById('opt_btn').disabled)
			alert('Обработка закончена!!!');
		document.getElementById('opt_btn').disabled=false;
		return;
	}
	else
	{
		var Link=strs[cur_pos];
		var Anchor=GetAnchorFromLink(Link);
		if(!Anchor||Anchor===false||Anchor===''||Anchor==='false')
		{
			AddCurString();
			cur_pos++;
			optimization_next();
		}
		if(ya_cash[Anchor])
			ApplyYaPosition(ya_cash[Anchor]);
		else
		{
			ya_requests++;
			jQuery.ajax(
			{
				url: '../keysyn/yaparse.php?query='+encodeURIComponent(Anchor)+'&place='+domain,
				dataType:'text',
				lanhor:Anchor,
				success: function(position) 
				{
					ya_cash[this.lanhor]=position;
					ApplyYaPosition(position);
				}	
			});
		}
	}
}
function AddCurString()
{
	//alert('AddCurString');
	var Link=strs[cur_pos];
	if(typeof Link!='undefined')
	{
		res_strs+="\n"+Link;
		document.getElementById('ta_opt').innerHTML=res_strs;
		document.getElementById('ta_opt').value=res_strs;
	}
}
function ApplyYaPosition(position)
{
	//alert('ApplyYaPosition');
	position=parseInt(position);
	var min_pos=parseInt(document.getElementById('ya_min_pos').value);
	if(position>min_pos)
		AddCurString();
	else
		del_count++;
	cur_pos++;
	optimization_next();
}