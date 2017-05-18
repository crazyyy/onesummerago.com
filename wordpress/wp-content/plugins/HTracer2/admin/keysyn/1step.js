			function fulltrim(str) 
			{
				str=str.replace(/(^[\s\xA0]+|[\s\xA0]+$)/g, '');
				var arr=str.split(' ');
				var arr2=[];
				for(var i=0; i<arr.length; i++)
				{
					if(arr[i]!=='')
						arr2.push(arr[i]);
				}
				str=arr2.join(' ');
				return str;
			}
			
			function isSameKey(key1,key2)
			{
				if(key1==key2)
					return true;
				if(!key1 || !key2)
					return false;
				key1=fulltrim(key1);
				key2=fulltrim(key2);
				if(key1==key2)
					return true;
				key1=key1.toLowerCase();
				key2=key2.toLowerCase();
				if(key1==key2)
					return true;
				key1=key1.split(' ').sort().join(' ');
				key2=key2.split(' ').sort().join(' ');
				if(key1==key2)
					return true;
				return false;
			}
			function getTextAreaStrings(id)
			{
				var Res=[];
				var ta=document.getElementById(id);
				var Res0=ta.innerHTML.split('\n');
				for(var i=0; i<Res0.length; i++)
				{
					var cur=fulltrim(Res0[i]);
					if(cur)
						Res.push(cur);	
				}
				return Res;
			}
			function addToTextArea(id,arr)
			{	
				var arr0=getTextAreaStrings(id);
				for(var i=0; i<arr.length; i++)
				{
					for(var j=0; j<arr0.length; j++)
						if(isSameKey(arr[i],arr0[j]))
							arr[i]=false;
				}
				for(var i=0; i<arr.length; i++)
					if(arr[i])
						arr0.push(arr[i]);
				document.getElementById(id).innerHTML=arr0.join('\n');
				document.getElementById(id).value=arr0.join('\n');
			}
			function gen_r(genVars,str,level)
			{// Рекурсивная функция
				if(!str)	
					str='';
				if(!level)	
					level=0;
				if(level == genVars.length)	
					return [fulltrim(str)];
				var Res=[];
				for(var i=0; i<genVars[level].length; i++)
				{	
					var Res2 = gen_r(genVars,str+genVars[level][i],level+1);
					for(var j=0; j<Res2.length; j++)
					{
						var was=false;
						if(level==0)
						{
							for(var k=0; k<Res2.length; k++)
							{
								if(isSameKey(Res2[j],Res[k]))
								{
									was=true;
									break;
								}
							}
						}
						if(!was)
							Res.push(Res2[j]);
					}
				}
				return Res;
			}
			function genkeypress(event)
			{	
				if(!event)
					event=window.event;
				if(event.keyCode!=13)
					document.getElementById('genbtn').disabled=false;
				else if(!document.getElementById('genbtn').disabled)
					genclick();
			}
			function genclick()
			{
				document.getElementById('genbtn').disabled=true;
				var genstr=document.getElementById('gen').value;
				genstr=' '+genstr+' ';
				if(genstr.split('{').length!=genstr.split('{').length)
				{
					alert("Число открывающихся фигурных скобок '{' не равно числу закрывающих '}' ");
					return;
				}
				if(genstr.split('{').length<2)
				{
					alert("Нет ни одной открывающей фигурной скобки!");
					return;
				}
				var genstr1=genstr.split('{');
				var genVars=[];
				for(var i=0; i<genstr1.length; i++)
				{
					var cur=genstr1[i].split('}');
					if(cur.length<2)
					{
						if(i==0)	
							genVars.push([genstr1[i]]);
						else
						{
							alert("Неправильный синтаксис. Синтаксис не поддерживает вложеность");
							return;
						}
					}
					else if(cur.length==2)
					{					
						genVars.push(cur[0].split('|'));
						genVars.push([cur[1]]);
					}
				}
				addToTextArea('genkeys',gen_r(genVars));
				document.getElementById('addbtn').disabled=false;
			}
			function addclick()
			{	
				addToTextArea('keys',getTextAreaStrings('genkeys'));
				document.getElementById('genkeys').innerHTML='';
				document.getElementById('genkeys').value='';
				document.getElementById('addbtn').disabled=true;
			}
