	var all_keys_index=[];
	function ReloadKeyColor(t,id)
	{
		//try{
		if($(t).attr("checked"))
		{
			$('#In_'+id).css("color","black");
			$('#In_'+id).css("text-decoration","none");
		}
		else
		{
			$('#In_'+id).css("text-decoration","line-through");
			$('#In_'+id).css("color","gray");
		}
	}
	var was_keys_chahged=false;
	var addKeyIndex=0;
	
	function addKeyRow(arr,tr_class,offset)
	{
		$('.dataTables_empty').css('display','none');
		$('.dataTables_empty').parent().css('display','none');
		
		var auto_offset=$('#keys_table tbody tr').length%2;
		offset=offset|auto_offset;
		if(offset===false)
			offset=auto_offset;
		var odd='odd';
		var res=1;
		if(offset%2)
		{
			res=0;
			odd='even';
		}	
		var tr_html=
			'<tr class="'+odd+' '+tr_class+'">'+
				'<td class="sc_td">'+
					arr[0]+
				'</td>'+
				'<td class="key_td">'+
					arr[1]+
				'</td>'+
				'<td class="out_td">'+
					arr[2]+
				'</td>'+
				'<td class="eva_td">'+
					arr[3]+
				'</td>'+
				'<td class="cl_td">'+
					arr[4]+
				'</td>';
		if(!single_page_url)
		{
			tr_html+=		
				'<td class="url_td">'+
					arr[5]+
				'</td>';
		}
		tr_html+=	
			'</tr>';
		$('#keys_table tbody').append(tr_html);
		addKeyIndex++;
		return res;
	}
	window.isJSOptChanging=false;
	var isOutChanged=[];
	window.SanAjaxRequestID=0;
	window.UrlAjaxRequestID=0;

	function ht_DoUrlRequest(id)
	{
		window.UrlAjaxRequestID=id;
		var key=$("#new_In_"+id).val();
		key+=' site:'+ Domain;
		key=encodeURIComponent(key);
		$.getJSON("http://ajax.googleapis.com/ajax/services/search/web?v=1.0&callback=ht_load_url_parsed_google&q="+key+"&context=?",function(data){});
	}
	function str_replace(search, replace, subject){return subject.split(search).join(replace);}

	function ht_load_url_parsed_google(func,data)
	{	
		if(data && data.results && data.results.length && data.results[0])
		{
			var res=data.results[0].unescapedUrl
				.replace('http://'+Domain+'/','/')
				.replace('http://www.'+Domain+'/','/');
				
			window.isJSOptChanging=true;
			$("#new_URL_"+window.UrlAjaxRequestID).val(res);
			window.isJSOptChanging=false;
		}
	}
	function suggest_parse_type_changed()
	{
		update_all_keys_index();
		if($('#suggest_parse_type').val()!='input')
		{
			$('#suggest_parse_input').css('display','none');
			$('#suggest_parse_btn'  ).css('display','inline');
		}
		else
		{			
			$('#suggest_parse_input').css('display','inline');
			$('#suggest_parse_btn'  ).css('display','none');
		}
		$('#progressbar'  		).css('display','none');
		$('#progressbar_label'	).css('display','none');
		$('#suggest_void_div'  	).css('display','none');

		
		$('#suggest_td2 ul').html('');
		recalc_suggest_count();

		$('#add_sugested_keys_btn').css('display','none');
	}
	var parse_suggests_from_table_tasks_count=0;
	var parse_suggests_from_table_tasks_finished=0;

	function parse_suggests_from_table()
	{
		$('#suggest_td2').attr('valign','top');	
		if(!$('#keys_table .key_in').length)
		{
			alert('No keys in table!');
			return;
		}
		$('#suggest_td2 ul').html('');
		recalc_suggest_count();
		$('#progressbar').css('display','block');
		$('#suggest_void_div').css('display','none');		
		$('#progressbar_label').css('display','block');

		$("#progressbar").progressbar({value:0});
		
		parse_suggests_from_table_tasks_count=0;
		parse_suggests_from_table_tasks_finished=0;
		
		$('#add_sugested_keys_btn').css('display','none');
		$('#suggest_td2').attr('valign','middle');
		update_all_keys_index();
		$.each($('#keys_table .key_in'),function()
		{
			if($('#suggest_aparse_cb').attr('checked'))
				make_suggest_ajax_request_shutle($(this).val());
			else
			{
				make_suggest_ajax_request($(this).val(),suggest_parse_str_change_index);
				make_suggest_ajax_request($(this).val()+' ',suggest_parse_str_change_index);
			}
		});
	}
	
	function parse_suggests_from_table_end()
	{
		
		$('#suggest_td2').attr('valign','top');
		$('#add_sugested_keys_btn').css('display','block');
		$('#progressbar').css('display','none');
		$('#suggest_void_div').css('display','none');
		$('#progressbar_label').css('display','none');
	}
	function add_sugested_keys()
	{
		$.each($('#suggest_td2 .add_sugested_key'),function()
		{
			if($(this).attr('checked'))
			{
				addKey($(this).attr('value'));
			}
		});
		$('#suggest_td2 #suggest_void_div').html(suggest_add_msg); 
		$('#suggest_td2 #suggest_void_div').css('display','block');
		$('#suggest_td2 #progressbar').css('display','none');
		$('#suggest_td2 #progressbar').css('display','none');
		$('#suggest_td2 #progressbar_label').css('display','none');
		$('#add_sugested_keys_btn').css('display','none');
		$('#suggest_td2 ul').css('display','none');
		$('#suggest_td2').attr('valign','middle');	
	}
	function update_all_keys_index()
	{	
		all_keys_index=[];
		$.each($('.key_in'),function()
		{
			var t=$(this).val();
			all_keys_index.push(t.trim());
		});
		var t=0;
		t++;
	}
	var suggest_parse_str_change_index=0;
	var suggest_parse_str_change_last='';
	var suggest_parse_str_change_index0=0;

	
	function suggest_parse_str_change(is_cb)
	{
		suggest_parse_str_change_index0++;
		setTimeout(function() {suggest_parse_str_change_inner(suggest_parse_str_change_index0,is_cb)},100);	
	}
	var hide_next_ac=false;
	function make_suggest_ajax_request(key,ci)
	{
		parse_suggests_from_table_tasks_count++;
		var cdata={'term':key,'showfirstkey':1};
		if($('#suggest_aparse_stem').attr('checked'))
			cdata={'term':key,'showfirstkey':1,'stem':1};
		$.ajax({
			url: "ajax/google_suggest.php",
			data:cdata,
			dataType:'json',
			success: function(arr)
			{
				if(ci==suggest_parse_str_change_index)
				{
					//alert(arr);
					$.each(arr,function(){
						addKeyToSuggest(this.label);
					});
				}
				parse_suggests_from_table_tasks_finished++;
				if($('#suggest_parse_type').val()!='input')
				{
					$("#progressbar").progressbar("value",(parse_suggests_from_table_tasks_finished/parse_suggests_from_table_tasks_count * 100));
					$('#progressbar_label').html(parse_suggests_from_table_tasks_finished+"/"+parse_suggests_from_table_tasks_count);
					if(parse_suggests_from_table_tasks_finished==parse_suggests_from_table_tasks_count)
						parse_suggests_from_table_end();
				}
			}
		});
	}
	function make_suggest_ajax_request_shutle(key)
	{
		key=key.trim().replace('  ',' ').replace('  ',' ').replace('  ',' ').replace('  ',' ');
		var was_queries=[];
		//was_queries.push(key);
		var words=key.split(' ');
		for(var t=0;t<100;t++)
		{//Теперь генерируем комбинации
			if(t)
			{
				for(//Перемешиваем слова
					var j, x, i = words.length; i;
					j = parseInt(Math.random() * i),
					x = words[--i], words[i] = words[j], words[j] = x
				);
			}
			key=words.join(' ').trim().replace('  ',' ').replace('  ',' ').replace('  ',' ');
			if($.inArray(key,was_queries)==-1)
			{
				was_queries.push(key);
				make_suggest_ajax_request(key+' ',suggest_parse_str_change_index);
				make_suggest_ajax_request(key,suggest_parse_str_change_index);
				if(words.length==2 && was_queries.length==2)
					break;
			}
		}
	}
	function suggest_parse_str_change_inner(index0,is_cb)
	{
		if($("#suggest_aparse_cb").attr("checked"))
		{
			$("#suggest_aparse_stem").attr("disabled",false);
			$("#suggest_aparse_stem_hint").css("color",'black');
		}
		else
		{
			$("#suggest_aparse_stem").attr("disabled",true);
			$("#suggest_aparse_stem_hint").css("color",'gray');
		}
		if(index0!=suggest_parse_str_change_index0
		|| $('#suggest_parse_type').val()!='input')
			return;
		if(is_cb && !$('#suggest_aparse_cb').attr('checked'))
			$('#suggest_parse_str').autocomplete("search");
		//alert(1);
		var str = $('#suggest_parse_str').val() + $('#suggest_aparse_cb').attr('checked');
		if(suggest_parse_str_change_last==str)
			return;
		else
			suggest_parse_str_change_last=str;
		//alert(3);
	
		var val= $('#suggest_parse_str').val().trim().indexOf(' ')==-1;
		var color='black'; 	
		if(val)
			var color='gray'; 	
		//$('#suggest_aparse_hint').css('color',color);
		$('#suggest_td2 ul').html('');
		recalc_suggest_count();
		update_all_keys_index();
		suggest_parse_str_change_index++;
		var key =$('#suggest_parse_str').val();
		if($('#suggest_aparse_cb').attr('checked'))
			make_suggest_ajax_request_shutle(key);
	}
	$(function(){
		$('#suggest_parse_str').change(function(){suggest_parse_str_change();});
		$('#suggest_parse_str').keypress(function(){suggest_parse_str_change();});
		
		suggest_parse_type_changed();
		suggest_parse_str_change();
		
		$('#suggest_parse_str').autocomplete({
			source: 'ajax/google_suggest.php',
			open:function(event,ui){
				if(hide_next_ac)
					$('.ui-autocomplete').css('display','none');
				hide_next_ac=false;
				if(!$('#suggest_aparse_cb').attr('checked'))
				{
					var ul=$('#suggest_td2 ul');
					$.each($('.ui-autocomplete li a'),function()
					{
						addKeyToSuggest($(this).html().trim());
					});
				}
			},
			/*focus:function(event,ui){},*/
			select: function(event,ui){
				setTimeout(function() {
					$('#suggest_parse_str').autocomplete("search");
					//suggest_parse_str_change();
				},100);
			}
		});
	});
	function recalc_suggest_count()
	{
		$('#sugested_keys_count').html('('+$('.add_sugested_key[checked="checked"]').length+')');
	}
	function addKeyToSuggest(key)
	{
		if($.inArray(key,all_keys_index)==-1
		&& key.indexOf("\\")==-1)
		{
			var ul=$('#suggest_td2 ul');
			ul.html(ul.html() 
			+ '<li>'
				+'<input class="add_sugested_key" type="checkbox" checked="checked" value="'+key+'" onchange="recalc_suggest_count()" />'
				+key
			+'</li>');
			all_keys_index.push(key);	
			$('#add_sugested_keys_btn').css('display','block');
			ul.css('display','block');
			recalc_suggest_count();
		}
	}
	
	function UpdateZebra(Selector)
	{
		var trs=$(Selector+' tr');
		trs.removeClass("odd even");
		var j=0;
		for(var i=0;i<trs.length;i++)
		{
			if(j%2)
				$(trs[i]).addClass('even');
			else
				$(trs[i]).addClass('odd');
			if($(trs[i]).css('display')!='none')
				j++;		
			//alert($(trs[i]).css('display'));
		}
		
	}
	var was_ulinks_chahged=false;
	var addULink_Index=0;
	function UpdateULinksSave()
	{
		if(was_ulinks_chahged)
		{
			$("#u_submit_btn").attr('disabled',false);
			$("#u_submit_btn").attr('value',save_str_tr);
		}
		else
			$("#u_submit_btn").attr('disabled',true);
	}
	function ULinksChanged()
	{
		was_ulinks_chahged=true;
		UpdateULinksSave();
	}
	function addUlinksRow(arr)
	{
		var tr_html=
			'<tr>'+
				'<td class="u_key_td">'+arr[0]+'</td>'+
				'<td class="u_url_td">'+arr[1]+'</td>'+
				'<td class="u_don_td">'+arr[2]+'</td>'+
				'<td class="u_act_td">'+arr[3]+'</td>'+
			'</tr>';
		$('#ulinks_table tbody').append(tr_html);	
	}
	var ULink_KeyChanged=[];
	var ULink_UrlChanged=[];
	var ULink_AutoChange=false;
	function ULink_in_array(needle, arr) 
	{
	    for (key in arr)
	        if (haystack[key] === needle)
				return true;
	    return false;
	}
	
	
	var ULink_New_Key_Changed_ID=false;
	var ULink_New_Key_Changed_Last='';
	function ULink_New_Key_Changed(i)
	{
		if(!ULink_AutoChange)
			ULink_KeyChanged.push(i);
		if(ULink_New_Key_Changed_ID)
			window.clearTimeout(ULink_New_Key_Changed_ID);
		if(!ULink_in_array(ULink_UrlChanged,i))
		{
			ULink_New_Key_Changed_ID= window.setTimeout(function() 
			{
				window.ULink_New_Key_Changed_ID=false;
				var tkey =$('#u_tr_'+i+'_key').val();
				if(tkey && tkey!=window.ULink_New_Key_Changed_Last)
				{
					window.ULink_New_Key_Changed_Last=tkey;
					ULinks_DetectURL(i);
				}
			}, 400);
		}
		ULinksChanged();
	}
	
	function ULinks_DetectURL(i,t)
	{
		if(!t)
			t=0;
		var tkey =$('#u_tr_'+i+'_key').val();
		$.ajax({
			url: "ajax/find_url_by_key.php",
			type:'POST',
			data:{'key':tkey,'t':0},
			success: function(text)
			{	
				if(text)
				{
					window.ULink_AutoChange=true;
					$('#u_tr_'+i+'_url').val(text);
					window.ULink_AutoChange=false;
				}
			}
		});
	}
	
	var ULink_New_URL_Changed_ID=false;
	var ULink_New_URL_Changed_Last='';
	function ULink_New_URL_Changed(i,par)
	{
		if(!ULink_AutoChange)
			ULink_UrlChanged.push(i);
		if(ULink_New_URL_Changed_ID && !par)
			window.clearTimeout(ULink_New_URL_Changed_ID);
		if(!ULink_in_array(ULink_KeyChanged,i))
		{
			ULink_New_URL_Changed_ID= window.setTimeout(function() 
				{
					window.ULink_New_URL_Changed_ID=false;
					var turl =$('#u_tr_'+i+'_url').val();
					if(turl && (turl+'dsf__sdf'+i)!=window.ULink_New_URL_Changed_Last)
					{
						window.ULink_New_URL_Changed_Last=turl+'dsf__sdf'+i;
						ULinks_DetectKey(i);
					}
				}, 400);
		}
		ULinksChanged();
	}
	
	function ULinks_DetectKey(i)
	{
		var turl =$('#u_tr_'+i+'_url').val();
		var furl=turl.toLowerCase();
		if(furl.indexOf('http://')!==0)
		{
			if(furl.indexOf(Domain)===0||furl.indexOf('www.'+Domain)===0)
				furl='http://'+Domain+turl;
			else
				furl='http://'+Domain+turl;
		}
		else
			furl=turl;
			
		//Пытаемся получить из базы
		$.ajax({
			url: "ajax/find_url_key.php",
			type:'POST',
			data:{'url':turl,'db':1},
			success: function(text)
			{
				if(text)
				{
					window.ULink_AutoChange=true;
					$('#u_tr_'+i+'_key').val(text);
					window.ULink_AutoChange=false;
				}
				else
				{
					//Если в базе нет - парсим титл страницы
					$.ajax(
					{
						url: furl,
						dataType:'xml',
						success: function(data)
						{
							var val  =$(data).find('title').text();
							var TitleSpace0=TitleSpace;
							var vals=val.split(TitleSpace);
							if(vals.length<2)
							{
								if(TitleSpace=='<<') TitleSpace0='&lt;&lt;';
								else if(TitleSpace=='<') TitleSpace0='&lt;';
								else if(TitleSpace=='&lt;&lt;') TitleSpace0='<<';
								else if(TitleSpace=='&lt;') TitleSpace0='<';
								else if(TitleSpace=='>>') TitleSpace0='&qt;&qt;';
								else if(TitleSpace=='>') TitleSpace0='&qt;';
								else if(TitleSpace=='&qt;&qt;') TitleSpace0='>>';
								else if(TitleSpace=='&qt;') TitleSpace0='>';
								else if(TitleSpace=='&laquo;') TitleSpace0='«';
								else if(TitleSpace=='«') TitleSpace0='&laquo;';
								else if(TitleSpace=='&raquo;') TitleSpace0='»';
								else if(TitleSpace=='»') TitleSpace0='&raquo;';
								
								vals=val.split(TitleSpace0);	
							}
							var l=0;
							if(TitleOrder=='ltr' && vals.length)
								l = vals.length-1;
							window.ULink_AutoChange=true;	
							$('#u_tr_'+i+'_key').val(vals[l]);									
							window.ULink_AutoChange=false;
						}
					});
				}
			}
		});	
	}
	function addULink(key,url,donor,after,lid)
	{
		if(!key)
			key='';
		if(!url)
			url='';
		if(!donor)
			donor='';
		addULink_Index++;
		var tr_class='new_tr';
		var id='u_tr_'+addULink_Index;
		if(!lid)
			lid='new_'+addULink_Index;
																	
								
		var tr_html=
			'<tr class="'+tr_class+'" id="'+id+'">'+
				'<td class="u_key_td">'+
					'<input class="u_key" name="new_Key_' +addULink_Index+'" id="'+id+'_key" value="'+key+'"   name="'+lid+'_key" onchange="ULink_New_Key_Changed('+addULink_Index+')" onkeypress="ULink_New_Key_Changed('+addULink_Index+')" />'+
				'</td>'+
				'<td class="u_url_td">'+
					'<input class="u_url" name="new_aURL_'+addULink_Index+'" id="'+id+'_url" value="'+url+'"   name="'+lid+'_url" onchange="ULink_New_URL_Changed('+addULink_Index+');" onkeypress="ULink_New_URL_Changed('+addULink_Index+');" />'+
				'</td>'+
				'<td class="u_don_td">'+
					'<input class="u_don" name="new_Don_' +addULink_Index+'" id="'+id+'_don" value="'+donor+'" name="'+lid+'_don" onchange="ULinksChanged();" onkeypress="ULinksChanged();" />'+
				'</td>'+
				'<td class="u_act_td">'+
					'<img onclick="ulink_delete(this);" alt="D" src="images/delete.gif" style="cursor:pointer" />&nbsp;&nbsp;'+
					'<img onclick="ulink_clone(this);" alt="C"  src="images/clone.gif"  style="cursor:pointer" />'+
				'</td>'+
			'</tr>';	
		if(!after)
			$('#ulinks_table tbody').append(tr_html);	
		else
			$(after).after(tr_html);	
		UpdateZebra('#ulinks_table tbody');
		$("#"+id+"_key").autocomplete({source: 'ajax/google_suggest.php'});
		$("#"+id+"_url").autocomplete({source: 'ajax/pages_autocomplete.php'});
		$("#"+id+"_don").autocomplete({source: 'ajax/pages_autocomplete.php'});
		$('#ulinks_table .dataTables_empty').css('display','none');
		if(url!=='' && key==='')
			ULink_New_URL_Changed(addULink_Index,true);
		
	}
	function ULinksFromCsv_parse()
	{
        var limit = 10000;
		var code=$('#ul_csv_code').val().trim().split("\n",limit);
		var sep=$('#ul_csv_comma').val();
		if(sep=="\\t")
			sep="\t";
		sep_type=false;
		if(sep.trim())
			sep=sep.trim();

		if(sep[0]=='Y'||sep[0]=='y'||sep[0]=='G'||sep[0]=='g')
		{
			if(sep[0]=='G'||sep[0]=='g')
				sep_type='G';
			else
				sep_type='Y';
			if(sep.length=1)
				sep='~~sssssssssssssss~~';
			else
			{
				sep[0]=' ';
				sep=sep.trim();
			}
		}
		if(sep==='')
			sep='~~sssssssssssssss~~';
		var out=[];
		var ns= '_hear_must_be_sep_';
		for(var i=0;i<code.length;i++)
		{
			var tmp=[];
			var cur=code[i];
			
			if(sep=='A'||sep=='a')
			{
				var low=cur.toLowerCase();
				var s=low.indexOf('href=');
				if(s!==-1)
				{	
					s = s + ('href=').length;
					var e=-1;
					if(cur[s]==="\"")
						e=low.indexOf("\"",s+1);
					else if(cur[s]==='\'')
						e=low.indexOf('\'',s+1);
					else
					{
						e=low.indexOf(' ',s+1);
						if(e===-1||e>low.indexOf('>',s+1))
							e=low.indexOf('>',s+1);
						s--;
					}
					if(e!==-1)
					{
						var href = cur.substr(s+1,e-s-1).trim();
						tmp.push(href);
						s=low.indexOf('>',e);
						e=low.indexOf('<',s);
						if(s!==-1&& e!==-1)
							tmp.push(cur.substr(s+1,e-s-1).trim());
					}
				}
			}
			else
			{
				if(ns.indexOf(sep)==-1)
				{// Поскольку кавычки могут экранировать разделитель
					cur=cur.split('"',100);
					var arr=[];
					for(var j=0;j<cur.length;j++)
					{
						var t=cur[j];
						if(j%2)
							t=t.split(sep).join(ns);
				
						arr.push(t);
					}
					cur=arr.join('"');
				}
				cur=cur.split(sep,100);
				
				for(var j=0;j<cur.length;j++)
				{
					var cell=cur[j];
					if(ns.indexOf(sep)==-1)
						cell=cell.split(ns).join(sep);
					cell=cell.trim();
					if(cell[0]=="\"" && cell[cell.length-1]=="\"")
						cell=cell.substr(1,cell.length-2);
					tmp.push(cell);
				}
			}
			if(sep_type)
				tmp.push(sep_type);
			out.push(tmp);
		}
		return out;
	}
	function ULinksFromCsv_reload()
	{
		window.setTimeout(function(){
			$("#ul_csv_table").empty();
			var sep=$('#ul_csv_comma').val();
			var code=ULinksFromCsv_parse(4);
			if(code.length<2)
			{
				$("#ul_csv_btn").attr('disabled',true);
				return;
			}
			for(var i=0;i<code.length;i++)
			{
				if(i==3)
					break;
				var cur=code[i];
				if(i==0)
				{
					var row= "<tr>";
					for(var j=0;j<cur.length;j++)
					{
						var ksel='';
						var usel='';
						var dsel='';
						var sel=" selected='selected' ";
						if(j==cur.length-1 && (sep[0]=='Y'||sep[0]=='y'||sep[0]=='G'||sep[0]=='g'))
							dsel=sel;
						if(sep=='A'||sep=='a')
						{
							if(j==1)
								ksel=sel;
							if(j==0)
								usel=sel;
						}
							
						
						row=row+ "<td>"+
								"<select class='ul_csv_role' style='width:auto; font-weight:bold'>"+
									"<option value='0'>SELECT</option>"+
									"<option value='1' "+ksel+">Anchor</option>"+
									"<option value='2' "+usel+">Acceptor</option>"+
									"<option value='3' "+dsel+">Donor</option>"+
								"</select></td>";
					}
					row=row+ "</tr>";
					$("#ul_csv_table").append(row);
				}
				var row= "<tr class='ul_csv_row'>";
				for(var j=0;j<cur.length;j++)
				{
					row=row+ 
						"<td>" + 
							(cur[j].split('&').join('&amp;').split('<').join('&lt')) +
						"</td>";
				}
				row=row+ "</tr>";
				$("#ul_csv_table").append(row);
				
				//  var chars = Array("&", "<", ">", '"', "'");
				//var replacements = Array("&amp;", "&lt;", "&gt;", "&quot;", "'");
   
			}
			$("#ul_csv_btn").attr('disabled',false);
			//alert();
		},100);
	}
	
	function ULinksFromCsv_Import()
	{
		var roles0=$("#ul_csv_table .ul_csv_role")
		var roles=[];
		roles[1]=false;
		roles[2]=false;
		roles[3]=false;
		
		var wasAcc=false;
		for(var i=0;i<roles0.length;i++)
		{
			var role=($(roles0[i]).val()) * 1;
			if(role==2)
				wasAcc=true;
			roles[role]=i;
		}
		if(!wasAcc)
		{
			alert('Acceptor column required');
			return;
		}
		
		var code=ULinksFromCsv_parse();

		for(var i=0;i<code.length;i++)
		{
			var cur=code[i];
			var key='';
			var url='';
			var don='';

			if(roles[1]!==false)
				key=cur[roles[1]];
			if(roles[2]!==false)
				url=cur[roles[2]];
			if(roles[3]!==false)
				don=cur[roles[3]];
			addULink(key,url,don);
		}
		$("#ul_csv_dialog").dialog('close');
	}
	//column
	
	function ULinksFromCsv()
	{
		$("#ul_csv_dialog").dialog({modal: true, resizable:false,width:800, height:500});
	}
	function ULinksFromTbl()
	{
	
		var trs=$("#keys_table tbody tr");
		$was=false;
		$("#ul_table_dialog_content").html(" "); 
		for(var i=0;i<trs.length; i++)
		{
			var tr=$(trs[i]);
			if(!tr.find(".out_td input:first-child").val())
				continue;
			$was=true;
			var code="<tr>"+
				"<td width='15px'><input type='checkbox' checked='checked' class='ulft_cb' /></td>"+
				"<td><input class='ulft_key' value='"+tr.find(".out_td input:first-child").val()+"' /></td>"+
				"<td><input class='ulft_url' value='"+tr.find(".url_td input").val()+"' /></td>"+
			"</tr>";
			$("#ul_table_dialog_content").append(code);
		}
		if(!$was)
			$("#ul_table_dialog_content").append('Table is empty');
		 
		$("#ul_table_dialog").dialog({modal: true, resizable:false,width:900});
	}
	function ULinksFromTbl_Load()
	{
		var trs=$("#ul_table_dialog_content tr");
		for(var i=0;i<trs.length; i++)
		{
			var tr=$(trs[i]);
			if(tr.find(".ulft_cb").attr("checked")=="checked")
					addULink(tr.find(".ulft_key").val(),tr.find(".ulft_url").val());		
		}
		$("#ul_table_dialog").dialog('close');
		was_ulinks_chahged=true;
		UpdateULinksSave();
	}
	function ulink_clone(e)
	{
		if (!e) e = window.event;
		e=$(e).parent().parent();
		addULink(
			e.find(".u_key").val(),
			e.find(".u_url").val(),
			e.find(".u_don").val(),
			e
		);
		was_ulinks_chahged=true;
		UpdateULinksSave();
	}
	function ulink_delete(e)
	{
		if (!e) e = window.event;
		
		e=$(e).parent().parent();
		e.find(".u_key").val('__deleted__');
		e.css('display','none');
		UpdateZebra('#ulinks_table tbody');
		was_ulinks_chahged=true;
		UpdateULinksSave();
	}
	function addKey(key,eva)
	{
		if(!key)
			key='';
		if(!eva)
			eva='1';

		var i=addKeyIndex;
		isOutChanged[i]=false;
		var w='300px';
		var sc=' ';
	
		if(!single_page_url)
			w='250px';
		else
			sc=' spellcheck="false" ';
			
		addKeyRow([
			'<input name="new_Status_'+i+'" value="1" type="checkbox" checked="checked" />',
			'<input class="key_in" name="new_In_'+i+'"  id="new_In_'+i+'"  onchange="form_changed()" onkeypress="form_changed()" type="text" value="'+key+'" style="width:'+w+'" '+sc+' />',
			'<input name="new_Out_'+i+'" id="new_Out_'+i+'" type="text" value="" />',
			'<input name="new_Eva_'+i+'" type="text" value="'+eva+'" />',
			'<input name="new_ShowInCLinks_'+i+'" value="1" type="checkbox" checked="checked" />',
			'<input name="new_URL_'+i+'" id="new_URL_'+i+'" type="text" value="/" />'
		],'new_key_tr');
		if(key)
		{
			form_changed();
			was_keys_chahged=true;
		}
		setTimeout(function() {
			if(!key)
				try{document.getElementById("new_In_"+i).focus();}catch(e){}
			else
			{
				$.ajax({
					url: "ajax/sanitarize.php",
					type:'POST',
					data:{'str':key},
					success: function(text)
					{
						window.isJSOptChanging=true;
						$("#new_Out_"+i).val(text);
						window.isJSOptChanging=false;
					}
				});
			}
			
			$("#new_In_"+i).keypress(function()
			{
				was_keys_chahged=true;
				window.SanAjaxRequestID++;
				var ID=window.SanAjaxRequestID;
				if(!$("#new_Out_"+i).val())
					isOutChanged[i]=false;
				if(!isOutChanged[i] && $("#new_In_"+i).val())
				{
					setTimeout(function() 
					{
						if(ID!=window.SanAjaxRequestID)
							return;
						ht_DoUrlRequest(i);	
						$.ajax({
							url: "ajax/sanitarize.php",
							/*context: document.body,*/
							type:'POST',
							data:{'str':$("#new_In_"+i).val()},
							success: function(text)
							{
								if(ID!=window.SanAjaxRequestID)
									return;
								window.isJSOptChanging=true;
								$("#new_Out_"+i).val(text);
								window.isJSOptChanging=false;
							}
						});
					},300);
				}
			});
			$("#new_URL_"+i).autocomplete({source: 'ajax/pages_autocomplete.php'});
			$("#new_In_" +i).autocomplete({source: 'ajax/google_suggest.php'});
			
			$("#new_Out_"+i).change(function(){
				if(!window.isJSOptChanging)
					isOutChanged[i]=true;
			});
			$("#new_URL_"+i).change(function(){
				if(!window.isJSOptChanging)
					isOutChanged[i]=true;
			});
		},10);
	}
	
	function form_changed()
	{
		$("#submit_btn").attr('disabled',false);
		$("#submit_btn").attr('value',save_str_tr);
	}
	function table_drawn()
	{
		$('#active_keys input').keypress(function(){form_changed()});
		$('#active_keys input').change(function(){form_changed()});
		//alert(111);
		//key_in.val
		//ws_parse_str
		if($('#ws_parse_str').length && $('#ws_parse_str').val().trim()=='')
		{
			setTimeout(function() 
			{
				if(!$('.key_in').length)
				{	
					$('#suggest_parse_type').val('input');
					suggest_parse_type_changed();
					setTimeout(function(){suggest_parse_type_changed();},100);
				}
				$.each($('.key_in'),function()
				{
					if($('#ws_parse_str').val().trim()=='')
						$('#ws_parse_str').val($(this).val().trim());
				});
			},100);
		}
		if($('#keys_table_filter input').val())
			$('td.dataTables_empty').html($('#keys_table td.dataTables_empty').html()
				+', <span style="color:gray">filter= <i>'+$('#keys_table_filter input').val())+'</i></span>';

		
		//$('#active_keys select').change(function(){form_changed()});
	}
	$(function() {
		UpdateULinksSave();
		$("#submit_btn").attr('disabled',true);
		
		$('#ulinks_form').ajaxForm(
		{
			beforeSubmit:  function(arr)
			{
				$("#u_submit_btn").attr('disabled',true);
				$("#u_submit_btn").attr('value',saving_str);
				arr.push({'name': 'ajax', 'value': '1'});
			},   
			dataType:'json',
			success:function(responseText)
			{
				$("#ulinks_form .new_tr").remove();
				
				for(var i=0;i<responseText.aaData.length;i++)
					addUlinksRow(responseText.aaData[i]);

				was_ulinks_chahged=false;
				$("#u_submit_btn").attr('value',saved_str);
				$("#ulinks_form .new_tr").remove();
			}
		});
		$('#active_keys').ajaxForm(
		{
			beforeSubmit:  function(arr)
			{
				$("#submit_btn").attr('disabled',true);
				$("#submit_btn").attr('value',saving_str);
				arr.push({'name': 'ajax', 'value': '1'});
			},   
			dataType:'json',
			success:function(responseText)
			{
				//alert(1);
				$("#keys_table tbody .new_key_tr").remove();//Удаляем добавленые только что строкт
				var res=($('#keys_table tbody tr').length-$("#keys_table tbody .new_key_tr").length%2);
				for(var i=0;i<responseText.aaData.length;i++)
					res=addKeyRow(responseText.aaData[i],'patch_tr',res);
				was_keys_chahged=false;
				addKeyIndex=0;
				//$("#submit_btn").attr('disabled',false);
				$("#submit_btn").attr('value',saved_str);
				$("#keys_table tbody .new_key_tr").remove();
				table_drawn();
			}
		});
		$('#export_form').ajaxForm(
		{
			beforeSubmit:  function(arr)
			{
				$("#export_btn").attr('disabled',true);
				$("#export_btn").attr('value',loading_str);
				arr.push({'name': 'ajax', 'value': '1'});
			},   
			success:function(responseText)
			{
				$("#export_btn").attr('value',exportg_str_tr);
				$("#export_btn").attr('disabled',false);
				var data=responseText;
				data=data.split("<script").join("<"+"!--");
				data=data.split("<SCRIPT").join("<"+"!--");
				data=data.split("<Script").join("<"+"!--");
				data=data.split("</script>").join("-->");
				data=data.split("</SCRIPT>").join("-->");
				data=data.split("</Script>").join("-->");
				data=data.split("</script").join("-->");
				data=data.split("</SCRIPT").join("-->");
				data=data.split("</Script").join("-->");

				$("#logos_td").html(data);
				$("#logos_td").attr('align','left');
			}
		});
		var aoColumnDefs=
		[
				/*{ "asSorting": [ "asc" ], "aTargets": [ 1, 2, 5]},*/
				{ "bSortable": false, "aTargets": [4]},
				{ "sSortDataType": "dom-text", "aTargets": [2,3]},
				{ "sType": "numeric", "aTargets": [3]},
				
				
				{ "sWidth": "22px", "aTargets": [4]},
				{ "sWidth": "45px", "aTargets": [0]},
				{ "sWidth": "47px", "aTargets": [3]},
				
				{ "sClass": "sc_td", "aTargets": [ 0 ]},
				{ "sClass": "key_td", "aTargets": [ 1 ]},
				{ "sClass": "out_td", "aTargets": [ 2 ]},
				{ "sClass": "eva_td", "aTargets": [ 3 ]},
				{ "sClass": "cl_td", "aTargets": [ 4 ]}
		];
		
		if(!single_page_url)
		{	
			aoColumnDefs.push({ "sClass": "url_td", "aTargets": [ 5 ]});
			aoColumnDefs.push({ "sWidth": "200px",  "aTargets": [ 5 ]});
			aoColumnDefs.push({ "sWidth": "200px",  "aTargets": [ 2 ]});
			aoColumnDefs.push({ "sWidth": "300px",  "aTargets": [ 1 ]});
		}
		else
		{
			aoColumnDefs.push({ "sWidth": "300px",  "aTargets": [ 2 ]});
			aoColumnDefs.push({ "sWidth": "310px",  "aTargets": [ 1 ]});
		}
		var sAjaxSource='ajax/keys.php';
		if(single_page_url)
			sAjaxSource='ajax/keys.php?url='+encodeURIComponent(single_page_url);
		if(is_ru_lang)
		{
			$('#keys_table').dataTable({
				'bJQueryUI': true,
				"bStateSave": true,
				//'fnDrawCallback':function() {alert(22)}, 
				'sPaginationType': 'full_numbers',
				"aaSorting": [[ 3, "desc" ]],
			
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": sAjaxSource,

				'fnPreDrawCallback':function() {
					if(was_keys_chahged && !confirm(not_save_confirm))
						return false;
					was_keys_chahged=false;
				},
				'fnDrawCallback':function() {
					was_keys_chahged=false;
					table_drawn();
				},
				"aoColumnDefs": aoColumnDefs,
				
			
				"oLanguage":{
					"sProcessing":   "Подождите...",
					"sLengthMenu":   "Показать _MENU_ ключевиков",
					"sZeroRecords":  "Ключевиков нет",
					"sInfo":         "Ключевики с _START_ до _END_ из _TOTAL_",
					"sInfoEmpty":    "Ключевики с 0 до 0 из 0",
					"sInfoFiltered": "(отфильтровано из _MAX_ ключевиков)",
					"sInfoPostFix":  "",
					"sSearch":       "Поиск:",
					"sUrl":          "",
					"oPaginate": {
						"sFirst": "Первая",
						"sPrevious": "Пред.",
						"sNext": "След.",
						"sLast": "Последняя"
					}
				}
			});
		}
		else
		{
			$('#keys_table').dataTable({
				'bJQueryUI': true,
				"bStateSave": true,
				//'fnDrawCallback':function() {alert(22)}, 
				'sPaginationType': 'full_numbers',
				"aaSorting": [[ 3, "desc" ]],
			
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": sAjaxSource,

				'fnPreDrawCallback':function() {
					if(was_keys_chahged && !confirm(not_save_confirm))
						return false;
					was_keys_chahged=false;
				},
				'fnDrawCallback':function() {
					was_keys_chahged=false;
					table_drawn();
				},
				"aoColumnDefs": aoColumnDefs
			});
		}
		var ULinksColumnDefs=
		[
				{ "bSortable": false, "aTargets": [3]},
				{ "sSortDataType": "dom-text", "aTargets": [0,1,2]},
				
				{ "sWidth": "200px", "aTargets": [0]},
				{ "sWidth": "165px", "aTargets": [1]},
				{ "sWidth": "120px", "aTargets": [2]},
				{ "sWidth": "50px", "aTargets": [3]},
				
				{ "sClass": "u_key_td", "aTargets": [ 0 ]},
				{ "sClass": "u_url_td", "aTargets": [ 1 ]},
				{ "sClass": "u_don_td", "aTargets": [ 2 ]},
				{ "sClass": "u_act_td", "aTargets": [ 3 ]}
		];
		
		if(is_ru_lang)
		{
			$('#ulinks_table').dataTable({
				'bJQueryUI': true,
				"bStateSave": true,
				//'fnDrawCallback':function() {alert(22)}, 
				'sPaginationType': 'full_numbers',
				"aaSorting": [[ 1, "desc" ]],
			
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": 'ajax/ulinks.php',//

				'fnPreDrawCallback':function() {
				
					if(was_ulinks_chahged && !confirm(not_save_confirm))
						return false;
					was_ulinks_chahged=false;
					UpdateULinksSave();
				},
				'fnDrawCallback':function() {
					was_ulinks_chahged=false;
					UpdateULinksSave();
				},
				"aoColumnDefs": ULinksColumnDefs,
				
			
				"oLanguage":{
					"sProcessing":   "Подождите...",
					"sLengthMenu":   "Показать _MENU_ ссылок",
					"sZeroRecords":  "Ссылок нет",
					"sInfo":         "Ссылки с _START_ до _END_ из _TOTAL_",
					"sInfoEmpty":    "Ссылки с 0 до 0 из 0",
					"sInfoFiltered": "(отфильтровано из _MAX_ ссылок)",
					"sInfoPostFix":  "",
					"sSearch":       "Поиск:",
					"sUrl":          "",
					"oPaginate": {
						"sFirst": "Первая",
						"sPrevious": "Пред.",
						"sNext": "След.",
						"sLast": "Последняя"
					}
				}
			});
		}
		else
		{
			$('#ulinks_table').dataTable({
				'bJQueryUI': true,
				"bStateSave": true,
				//'fnDrawCallback':function() {alert(22)}, 
				'sPaginationType': 'full_numbers',
				"aaSorting": [[ 1, "desc" ]],
			
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": 'ajax/ulinks.php',

				'fnPreDrawCallback':function() {
					if(was_ulinks_chahged && !confirm(not_save_confirm))
						return false;
					was_ulinks_chahged=false;
					UpdateULinksSave();
				},
				'fnDrawCallback':function() {
					was_ulinks_chahged=false;
					UpdateULinksSave();
				},
				"aoColumnDefs": ULinksColumnDefs
			});
		}
		$('#keys_table').css('width','100%');
		
		
	});
	$(function(){
		if($('#export_dest').val()=='sape')
		{
			$("#b_needtop").css('display','none');
			$("#b_budget").css('display','none');
			$("#needtop").css('display','none');
			$("#budget").css('display','none');
			$("#s_rub").css('display','none');
		}
		else
		{
			$("#b_needtop").css('display','inline');
			$("#b_budget").css('display','inline');
			$("#needtop").css('display','inline');
			$("#budget").css('display','inline');
			$("#s_rub").css('display','inline');
		}
		$('#export_dest').change(function (){
			$('#sape_img').removeClass('selected');
			$('#rookee_img').removeClass('selected');
			$('#webeffector_img').removeClass('selected');
			$('#seopult_img').removeClass('selected');
									
			$('#'+$('#export_dest').val()+'_img').addClass('selected');

			if($('#export_dest').val()=='sape')
			{
				$("#b_needtop").hide(333);
				$("#b_budget").hide(333);
				$("#needtop").hide(333);
				$("#budget").hide(333);
				$("#s_rub").hide(333);
			}
			else
			{
				$("#b_needtop").show(333);
				$("#b_budget").show(333);
				$("#needtop").show(333);
				$("#budget").show(333);
				$("#s_rub").show(333);
			}
		});
		$("#export_dest").trigger('change');
		$(function() {
			if(is_ru_lang)
			{
				$("#keys_table_filter label").click(function()
				{ 
					if(!single_page_url)
					{
						ShowHintDialog('',
							'Поиск производится по полям "Ключ", "Написание" и "Страница"<br /><br />'+
							'По умолчанию при поиске ищуться включения. Например, если вы наберете "<span style="color:gray">одесс</span>", то найдутся все ключи содержащие эту строку<br /><br />'+
							'Чтобы найти четкие соответствия перед запросом поставьте равно: "<span style="color:gray">=одесса</span>" или "<span style="color:gray">=/page.html</span>"<br /><br />'+
							'Чтобы найти строки начинающиеся с заданной поставьте перед запросом символ меньше: "<span style="color:gray">&lt;одесс</span>" или "<span style="color:gray">&lt;/dir/</span>"<br /><br />'+
							'Чтобы найти строки заканчивающися заданной поставьте перед запросом символ больше: "<span style="color:gray">>одесс</span>" или "<span style="color:gray">>/dir/</span>"<br /><br />'
						);
					}
					else
					{
						ShowHintDialog('',
							'Поиск производится по полям "Ключ" и "Написание"<br /><br />'+
							'По умолчанию при поиске ищуться включения. Например, если вы наберете "<span style="color:gray">одесс</span>", то найдутся все ключи содержащие эту строку<br /><br />'+
							'Чтобы найти четкие соответствия перед запросом поставьте равно: "<span style="color:gray">=одесса</span>"<br /><br />'+
							'Чтобы найти строки начинающиеся с заданной поставьте перед запросом символ меньше: "<span style="color:gray">&lt;одесс</span>"<br /><br />'+
							'Чтобы найти строки заканчивающися заданной поставьте перед запросом символ больше: "<span style="color:gray">>одесс</span>"<br /><br />'
						);				
					}
				});
			}
			$("#keys_table_filter label").css('cursor','help');
			$("#keys_table_filter input").css('cursor','edit');
			$("#keys_table_filter input").click( function(){ 
				return false;				
			});
			if(!single_page_url)
			{
				$("#keys_table_filter input").autocomplete(
				{
					source: 'ajax/pages_autocomplete.php',
					search: function(event, ui) 
					{
						return $("#keys_table_filter input").val().length>=2
							&& ($("#keys_table_filter input").val().substr(0,2)=='=/'
							|| $("#keys_table_filter input").val().substr(0,2)=='</');
					}
				});
			}
		});
	});

//=======================WS=============================
	function recalc_ws_count()
	{
		$('#ws_keys_count').html('('+$('.add_ws_key[checked="checked"]').length+')');
	}
	var parse_ws_tasks_count=0;
	var parse_ws_finished=0;
	function ws_k_change()
	{
		setTimeout(function() 
		{
			var val = $('#ws_k_input').val().trim().replace(',','.');
			var k=parseFloat(val);
			if(!k)
				k=parseFloat(val.replace('.',','));
			if(k)
			{
				$.each($('.add_ws_key_eva'), function()
				{
					$(this).val(Math.round(k * $(this).attr('eva')));
				});
			}
		},100);
	}
	$(function()
	{
		$("#ws_parse_str").bind("keypress",
			function(e)
			{
				if((window.event && window.event.keyCode == 13)
				||(e && e.keyCode == 13))
				{
					parse_ws();
					return false;
				}
			}
		);
	});
	
	function add_ws_keys()
	{
		$.each($('#ws_td2 tbody tr'),function()
		{
			//alert(1);
			var lst=$(this).children();
			var checked=false;
			var text='';
			var eva=0;
			for(var i=0;i<lst.length;i++)
			{
				var cur = $(lst[i]);
				if(cur.children('.add_ws_key') && cur.children('.add_ws_key').length)	
				{
					checked=cur.children('.add_ws_key').attr('checked');
					text=cur.children('.add_ws_key').attr('value');
				}
				if(cur.children('.add_ws_key_eva') && cur.children('.add_ws_key_eva').length)	
				{
					eva=cur.children('.add_ws_key_eva').attr('value');
				}
			}
			if(checked && text && eva)
				addKey(text,eva);
		});
		$('#ws_td2 #ws_void_div').html(suggest_add_msg);
		$('#ws_td2 #ws_void_div').css('display','block');
		$('#ws_td2 #ws_table_div').css('display','none');
		$('#ws_td2 #ws_wait_div').css('display','none');
		$('#ws_td2 #add_ws_keys_btn').css('display','none');
		$('#ws_td2').attr('valign','middle');
	}
	
	function parse_ws()
	{
		if(!$('#ws_parse_str').val())
		{
			alert('Введите запрос');
			return;
		}
		$('#ws_parse_btn').attr('disabled',true);
		
		$('#ws_td2 tbody').html('');
		parse_ws_tasks_count=0;
		parse_ws_finished=0;
		
		$('#ws_table_div').css('display','none');
		$('#add_ws_keys_btn').css('display','none');
		
		$('#ws_td2').attr('valign','middle');
		$('#ws_void_div').css('display','none');
		$('#ws_wait_div').css('display','block');
		
		
		update_all_keys_index();
		$.ajax({
			url: "ajax/ws.php",
			data:{'q':$('#ws_parse_str').val()},
			dataType:'json',
			success: function(arr)
			{	
				update_all_keys_index();
				$('#ws_parse_btn').attr('disabled',false);
				
				for(var i=0;i<arr.length;i++)
				{
					var cur=arr[i];
					if($.inArray(cur['key'],all_keys_index)==-1)
					{
						$('#ws_td2 tbody').append(
							'<tr>'+
								'<td><input type="checkbox" checked="checked" class="add_ws_key" value="'+cur['key']+'" onchange="recalc_ws_count()" /></td>'+
								'<td>'+cur['key']+'</td>'+
								'<td>'+cur['eva']+'</td>'+
								'<td><input type="text" class="add_ws_key_eva" eva="'+cur['eva']+'" size="3" value="'+cur['eva']+'" /></td>'+
							'</tr>'
						);
					}
				}
				$('#ws_k_input').val('1.0');
				$('#ws_td2').attr('valign', 'top');
				$('#ws_wait_div').css('display','none');
				$('#ws_table_div').css('display','block');
				$('#add_ws_keys_btn').css('display','block');
				recalc_ws_count();
			}
		});
	}
//=================================Экспорт: оптимизация проекта============================================================================
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
var project_optimization_count=0;
var project_optimization_cur=0;
var project_optimization_deleted=0;

var ya_cash=[];
var ya_cash_arr=[];
var project_optimization_need_top=3;
function project_optimization_apply_pos(position,canchor,carr)
{
	ya_cash[canchor]=position;
	project_optimization_cur++;
	$('#export_optimize_processbar').progressbar("value",100*(project_optimization_cur/project_optimization_count));
	$('#export_optimize_cur_str').html(project_optimization_cur);
	if(project_optimization_need_top<position)
	{
		if($('#export_data_textarea').val().trim())
		$('#export_data_textarea').val($('#export_data_textarea').val()+"\n");
		$('#export_data_textarea').val(
				$('#export_data_textarea').val()
				//+canchor
				+carr.join("\n")
				+"\n"
		);
	}
	else
	{
		project_optimization_deleted++;
		$('#export_optimize_deleted').html(project_optimization_deleted);
	}
}
$.ajax({
	dataType:'jsonp',
	url:"http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=PHP",
	success:function(data)
	{
		//alert(data);
	}
});
var cur_ajax_object_num=0;
var cur_ajax_object_arr=[];
var was_banned_time=false;

function start_project_optimization_inner()
{
	project_optimization_need_top=parseInt($('#ya_min_pos').val());
	cur_ajax_object_num=0;
	cur_ajax_object_arr=[];

	project_optimization_cur=0;
	project_optimization_deleted=0;
	$('#export_optimize_options').css('display','none');
	$('#export_optimize_results').css('display','block');
	$('#export_optimize_processbar').progressbar({value: 0});
	var data0=$('#export_data_textarea').val().split('\n');
	var data1=[];
	for (var i=0;i<data0.length;i++)
	{
		var cur=data0[i].trim();
		if(cur && cur!='')
			data1.push(cur);
	}
	var lastAnhor='';
	var count=0;
	var arr=[];
	$('#export_data_textarea').val('');
	for(var i=0;i<data1.length;i++)
	{
		var cur=data1[i];
		var anchor=GetAnchorFromLink(cur);
		if((anchor!=lastAnhor && i!=0)||i==data1.length-1) 
		{
			count++;
			arr.push(cur);
			ya_cash_arr[anchor]=arr.concat();
			if($('#export_optimize_se').val()=='yandex')
			{
				$.ajax(
				{
					url: '../keysyn/yaparse.php?query='+encodeURIComponent(anchor)+'&place='+Domain,
					dataType:'text',
					tanchor:anchor,
					tarr:arr.concat(),
					success: function(position) 
					{
						position=parseInt(position);
						var canchor=this.tanchor;
						var carr=this.tarr;
						project_optimization_apply_pos(position,canchor,carr);
					}	
				});
			}
			else
			{
				//cur_ajax_object_num=0;
				cur_ajax_object_arr.push(
				//$.ajax(
				{
					dataType:'jsonp',
					url:'http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q='//&callback=tcb&context=hh'
						+encodeURIComponent(anchor),
					tanchor:anchor+'',
					tarr:arr.concat(),
					success: function(data) 
					{
						var position=102;
						var canchor=this.tanchor;
						var carr=this.tarr;
						var time = 0;
						if(data && !data.responseData)
							data.responseData=data;

						if(data && data.responseData && data.responseData.results)
							time=0;
						else if(/*data===null||*/
						(data && (data.responseStatus==='403' || data.responseStatus===403)))
							time=30000;	
						if(data && data.responseData && data.responseData.results 
						&& data.responseData.results.length && data.responseData.results[0])
						{
							for(var i=0;i<data.responseData.results.length;i++)
							{
								var turl=data.responseData.results[0].url.trim();
								if(turl.indexOf('http://'+Domain)===0||turl.indexOf('http://www.'+Domain)===0)
								{
									position=i+1;
									break;
								}
							}
						}		
						if(window.was_banned_time)
						{
							time=0;
							window.was_banned_time=false;
						}
						if(time)
						{
							$('#google_ban').css('display','inline');
							window.was_banned_time=true;
							$('#google_ban_sec').html(30);
							setTimeout(function(){inc_ban_sec()},1000);
						}	
						else
							$('#google_ban').css('display','none');

						project_optimization_apply_pos(position,canchor,carr);
						window.cur_ajax_object_num++;
						if(window.cur_ajax_object_num<window.cur_ajax_object_arr.length)
						{
							setTimeout(function()
								{$.ajax(window.cur_ajax_object_arr[window.cur_ajax_object_num]);},
								time + 300 + Math.random()%600);
						}
						else
							$('#google_ban').css('display','none');

					}
				});
				if(i==data1.length-1)
				{
					$.ajax(cur_ajax_object_arr[0]);
				}
			}
			arr=[];
		}
		else
			arr.push(cur);
		lastAnhor=anchor;
	}
	$('#export_optimize_count').html(count);
	project_optimization_count=count;
}	
function tcb(fun,data)
{
	window.cur_ajax_object_arr[window.cur_ajax_object_num].success(data);
	//window[fun](data);	
}
function inc_ban_sec()
{
	var sec=parseInt($('#google_ban_sec').html());
	if(sec>0)
	{
		sec--;
		$('#google_ban_sec').html(sec);
		setTimeout(function(){inc_ban_sec()},1000);
	}
}
function start_project_optimization()
{	
	if($('#export_optimize_se').val()=='yandex')
	{
		$.ajax({
			url: '../keysyn/yaparse.php?query='+encodeURIComponent('тест'),
			success: function(str) 
			{
				if(str.indexOf('@%#')!=-1)
				{
					alert(
						'You must enter Yandex.XML api key in keysyn/config.php\n'+
						'Yandex.XML error:\n '+str.replace(/(<([^>]+)>)/ig,"").trim()
					);
				}
				else
					start_project_optimization_inner();
			}
		});
	}
	else
		start_project_optimization_inner();
}

//
