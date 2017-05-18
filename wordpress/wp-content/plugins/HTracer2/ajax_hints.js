		var searchHintsCash=[];
		var searchHintsLastQuery='';
		var searchHintsInput=false;
		var seachHintsRequest;
		var is_hint_clicked=false;
		function initSearchHints(InputID)
		{	
			var input;	
			var isIE=false;
			try{isIE=(navigator.userAgent.indexOf('IE')!==-1);}catch(e){}
			searchHintsCash['']=document.getElementById('search_hints_inner').innerHTML;
			AddOnClickToHints();
			if(!InputID)
				InputID='search_input';
			if(typeof( mixed_var ) == 'object')
				input=InputID;
			else
				input=document.getElementById(InputID);
			searchHintsInput=input;	
			var hints=document.getElementById('search_hints');
			var delta=0;
			var delta2=0;
			try{
				delta1=parseInt(hints.style.paddingLeft)+parseInt(hints.style.paddingRight);
			}catch(e){}
			if(!delta1)
			{
				if(isIE)
					delta1=2;
				else
					delta1=4;
			}
			try{
				delta2=parseInt(input.style.borderLeftWidth)+parseInt(input.style.borderRightWidth);
			}catch(e){}
			if(!delta2)
				delta2=20;
			try{
				hints.style.width=(parseInt(input.offsetWidth) - delta1 +2)+'px';
			}catch(e){}
			try{
				hints.style.left=parseInt(input.offsetLeft)+'px';
			}catch(e){}
			//hints.style.width=parseInt(input.offsetWidth);
			
			input.onfocus=function(){is_hint_clicked=false; reloadSearchHints()};
			input.onkeypress=function(){setTimeout("reloadSearchHints()",10)};
			input.onblur=function(x,y,z){setTimeout("hideSearchHints()",10)};
			reloadSearchHints(true);
		}
		function AddOnClickToHints()
		{
			var inner=document.getElementById('search_hints_inner');//.innerHTML;
			var links=inner.getElementsByTagName('a');
			for(var i=0;i<links.length;i++)
			{
				var link=links[i];
				link.onclick=function(){is_hint_clicked=true;}
				link.onfocus=function(){is_hint_clicked=true;}
			}
		}
		function reloadSearchHints(dontshow)
		{
			if(!dontshow)
				document.getElementById('search_hints').style.display='block';
			if(seachHintsRequest)
			{
				try{seachHintsRequest.abort()}catch(e){}
				try{seachHintsRequest.onreadystatechange=function(){}}catch(e){}
			}
			searchHintsLastQuery=searchHintsInput.value;
			document.getElementById('search_hints_inner').innerHTML='';
			if(searchHintsCash[searchHintsLastQuery])
			{
				document.getElementById('search_hints_inner').innerHTML=searchHintsCash[searchHintsLastQuery];
				AddOnClickToHints();
			}
			else
				searchHintsDoRequest(searchHintsLastQuery);
		}
		function hideSearchHints()
		{
			
			if(!is_hint_clicked)
				document.getElementById('search_hints').style.display='none';
		}
		function searchHintsDoRequest(str)
		{
			var url= searchHintsURL+'?q='+encodeURIComponent(str);
			seachHintsRequest = null;
			if (window.XMLHttpRequest) 
				{try {seachHintsRequest = new XMLHttpRequest();} catch (e){}}
			else if (window.ActiveXObject) 
			{
				try {seachHintsRequest = new ActiveXObject('Msxml2.XMLHTTP');} catch (e)
				{try {seachHintsRequest = new ActiveXObject('Microsoft.XMLHTTP');} catch (e){}}
			}
			if (seachHintsRequest) 
			{       
				seachHintsRequest.open("GET", url, true);
				seachHintsRequest.onreadystatechange = function() {seachHintsRequestChange()};
				seachHintsRequest.send(null);
			}
		}
		function seachHintsRequestChange()
		{
			if((seachHintsRequest.readyState == 3 || seachHintsRequest.readyState == 4)
			&&  seachHintsRequest.status == 200 ) 
			{
				document.getElementById('search_hints_inner').innerHTML=seachHintsRequest.responseText;
				AddOnClickToHints();
				searchHintsCash[searchHintsLastQuery]=seachHintsRequest.responseText;
			} 
		}