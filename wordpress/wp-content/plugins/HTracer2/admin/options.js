	function update_cloud_css()
	{
		var nowrap=$('#cloud_css_nowrap').attr('checked');
		if(nowrap)
			nowrap='    white-space:nowrap;\n';
		else
			nowrap='';
		
		var center=$('#cloud_css_center').attr('checked');
		if(center)
			center='    text-align:center;\n';
		else
			center='';

		var color=$('#cloud_css_color').val().trim();
		if(color)
			color='    color:'+color+';\n';
			
		var width=$('#cloud_css_width').val().trim();
		if(width)
			width='    width:'+width+'px;\n';

		code='';
		
		if(center || width)
			code= '#cloud\n{\n'+center+width+'}\n';
		if(nowrap || color)
			code= code+ '#cloud a\n{\n'+nowrap+color+'}\n';
			
		$('#cloud_css_code').attr('hcontent','<pre>'+code+'</pre>');
		$('#cloud_css_style').html(code);
	}
	function show_cloud_sourche()
	{
		var code = '';
		var style=$('#htracer_cloud_style').val();
		if(!style)
			style='cloud';
		code='style='+ style +' ' + $('#htracer_cloud_links').val() +'/' + $('#htracer_cloud_links').val() * $('#htracer_cloud_randomize').val();
		if(style=='cloud')
		{
			code+='&minsize='+$('#htracer_cloud_min_size').val();
			code+='&maxsize='+$('#htracer_cloud_max_size').val();
		}
		$('.cloud_code_params').html(code);
		$("#cloud_code_dialog").dialog({ width: 800 });
	}
	$(function() {
		update_cloud_css();
		$("#htracer_cloud_style").change(function(){
			SetOptVisible('htracer_cloud_min_size',!$("#htracer_cloud_style").val());
			SetOptVisible('htracer_cloud_max_size',!$("#htracer_cloud_style").val());
		});
		$("#htracer_cloud_style").trigger('change');
		
		$.each($("#htracer_usp"), function() 
		{
			var name= $(this).attr('id').replace('htracer_','');
			 $(this).change(function(){
				window.setTimeout(function(){
					if($('#htracer_'+name).attr('checked'))
						$('#'+name+'_options').css('color','black');
					else
						$('#'+name+'_options').css('color','gray');
				},50);
			 });
			 $(this).trigger('change');
		});
		
		$.each($(".dhint"), function() 
		{
			$(this).click(function() {
				var id= '#'+$(this).attr('id');
				var did='#'+$(this).attr('id')+'_dialog';
				if(!$(id).attr('shown') || $(id).attr('shown')=='0')
				{
					$(did).css('display','block');
					$(id).attr('shown','1');
					$(id).html(HideOptions_str);
				}
				else
				{
					$(did).css('display','none');
					$(id).attr('shown','0');
					$(id).html(ShowOptions_str);
				}
				
			/*
				var w = $(this).attr('dwidth');
				if(!w)
					w=800;
				
				$(did).dialog({modal: true, resizable:false,minWidth:w,buttons:{"OK":function(){$(this).dialog( "close" );}},
					close: function(event, ui) { 
						$(id+' .tmp').html('');
						$.each($(did+' input, '+did+' select ,'+did+' textarea'), function() {
							if($(this).attr('name') && $(this).attr('name')!=''
							&&(!$(this).attr('type') || $(this).attr('type').toLowerCase()!='checkbox'|| $(this).attr('checked')))
							
							{
								$(id+' .tmp').append(
									'<input type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).val()+'" />'
								);
							}
						});
					}
				});
			*/
			});
		});	
	});	
	function CreateOptString(name)
	{
		var v =0;
		if($("#"+name).attr('checked'))
			v =1;
		return '&'+name +'='+ v;
	}
	function CreateOptStringSelect(name)
	{
		var v =$("#"+name).val();
		return '&'+name +'='+ v.trim();
	}
	var CheckSpeedTime=false;
	function CheckSpeed()
	{
		CheckSpeedTime=new Date();
		$('#speed_test_btn').attr('disabled',true);
		$('#speed_test_res').html('');
		
		Url='http://'+Domain+$('#speed_test_url').val();
		Url = Url	+ '?htracer_show_time='+AJAX_PASS
					+ CreateOptStringSelect("htracer_use_php_dom")
					+ CreateOptString("htracer_mysql_dont_create_tables")
					+ CreateOptString("htracer_mysql_close")
					+ CreateOptString("htracer_pconnect");	
		$.ajax({
			url: Url,
			success: function(data){
				var cur=new Date();
				var time= cur - CheckSpeedTime;
				data=data.split('<!--ht_show_time=');
				data=data[data.length-1];
				data=data.split('-->');
				data=data[0];
				$('#speed_test_btn').attr('disabled',false);
				data=Math.round(data) / 1000;
				time=Math.round(time) / 1000;
				var prc=Math.round(100* (data/time));
				var tmp_str = speed_test_prc_str.replace('%ftime%',data).replace('%time%',time);
				//
				$('#speed_test_res').html( tmp_str+' (<b>'+prc+'%</b>)<br /> <small>'+SpeedTestNoCash_str+'</small>');
			}
		});
		
	}
	var CheckQueryFiltersLastOption=false;
	var CheckQueryFiltersLastURL=false;
	function CheckQueryFilters()
	{
		window.setTimeout(function(){CheckQueryFilters0()},50);
	}
	function CheckQueryFilters0()
	{
		var Url = "ajax/test_query.php?q="
				+ encodeURIComponent($("#test_query").val()) 
				+ CreateOptString("htracer_trace_sex_filter")
				+ CreateOptString("htracer_trace_free_filter")
				+ CreateOptString("htracer_trace_download_filter")
				+ CreateOptString("htracer_trace_service_filter")
				+ CreateOptString("htracer_mats_filter")
				+ CreateOptString("htracer_symb_white_list")
				+ CreateOptString("htracer_numeric_filter")
				+ CreateOptString("htracer_not_ru_filter")
				+ CreateOptStringSelect("htracer_user_minus_words");
		if(Url==CheckQueryFiltersLastURL)
			return;
		CheckQueryFiltersLastURL=Url;
		
		$('#test_query_img').css('display','inline');
		$('#test_query_img').attr('src','images/loading.gif');
		$('#test_query_res').html('');
		if(CheckQueryFiltersLastOption)
			$('#'+CheckQueryFiltersLastOption).parent().parent().css('background-color','white');
		$.ajax({
			dataType: 'json',
			success: function(data){
				if(this.url!=CheckQueryFiltersLastURL)
					return;
				if(CheckQueryFiltersLastOption)
					$('#'+CheckQueryFiltersLastOption).parent().parent().css('background-color','white');
				$('#test_query_res').html('');
				if(data[1])
				{
					var str='<span class="hint" onclick=\'ShowHintDialog("","БФ -- Безусловный Фильтр. Такой фильтр нельзя изменить в настройках<br /><br />'
						+'УФ -- Условный Фильтр. Этот фильтр можно отключить в настройках. Соответсвующая настройка подсвечена серым фоном.")\'>';
					if(data[0])
					{
						CheckQueryFiltersLastOption=data[0];
						$('#'+data[0]).parent().parent().css('background-color','#F1F1F1');
						str=str+'УФ';
					}
					else
						str=str+'БФ';
					str=str+'</span>';
					
					str=str+'.'+data[1]+': ';
					str=str+data[2];
					$('#test_query_res').html(str);
					$('#test_query_img').attr('src','images/no.png');
				}
				else
					$('#test_query_img').attr('src','images/yes.png');
			},
			url: Url
		});
	}
	
	var is_mysql_ping_error=false;
	var is_mysql_host_error=false;
	
	function CheckMySQL()
	{
		$("#test_mysql_btn").attr("disabled",true);
		$("#test_mysql_btn").attr("value",checking_str);
		$("#test_mysql_res").html('');
		
		$.ajax({
			success: function(data)
			{
				$("#test_mysql_res").html(data);
				$("#test_mysql_btn").attr("value",check_str_tr);
				SetOpacity("#test_mysql_res",1);
				is_mysql_ping_error=(data.indexOf('>20:<')!=-1);
				is_mysql_host_error=(data.indexOf('host_error')!=-1);
				if(is_mysql_ping_error)
					SetOptVisible('htracer_mysql_ignore_mysql_ping',true);
				if(is_mysql_host_error)
					SetOptVisible('htracer_mysql_host',true);
					
					
			},
			url: "ajax/test_mysql.php?q=0"
				+ CreateOptStringSelect("htracer_mysql")
				+ CreateOptStringSelect("htracer_mysql_login")
				+ '&htracer_mysql_pass_s='+ $("#htracer_mysql_pass").val().trim()
				+ CreateOptStringSelect("htracer_mysql_dbname")
				+ CreateOptStringSelect("htracer_mysql_host")
				+ CreateOptString("htracer_mysql_ignore_mysql_ping")
		});
	}

	function RefreshCloud()
	{
		$("#test_cloud_btn").attr("disabled",true);
		$.ajax({
			success: function(data){
				data=data.split("<script").join("<"+"!--");
				data=data.split("<SCRIPT").join("<"+"!--");
				data=data.split("<Script").join("<"+"!--");
				data=data.split("</script>").join("-->");
				data=data.split("</SCRIPT>").join("-->");
				data=data.split("</Script>").join("-->");
				data=data.split("</script").join("-->");
				data=data.split("</SCRIPT").join("-->");
				data=data.split("</Script").join("-->");
				$("#cloud").html(data);
				$("#test_cloud_btn").attr("disabled",false);
			},
			url: "ajax/test_cloud.php?q=0"
				+ CreateOptStringSelect("htracer_cloud_links")
				+ CreateOptStringSelect("htracer_cloud_randomize")
				+ CreateOptStringSelect("htracer_cloud_min_size")
				+ CreateOptStringSelect("htracer_cloud_max_size")
				+ CreateOptStringSelect("htracer_cloud_style")
		});
	}

	


	var isLoading=true;
	function SetOptEnabled(name,val)
	{	
		$("#"+name).attr('disabled', !val);
		var color='gray';
		if(val)
			color='black';
		$("#"+name).parent().parent().css('color', color);
		
	}
	var OptionsDataStr='';

	function SetOptVisible(name,val,sparent)
	{	
		if(isLoading)
		{
			if(sparent)
			{
				if(!val)
					$("#"+name).parent().css('display', 'none');
				else
					$("#"+name).parent().css('display', '');
			}
			else
			{
				if(!val)
					$("#"+name).parent().parent().css('display', 'none');
				else
					$("#"+name).parent().parent().css('display', '');
			}
		}
		else if(sparent)
		{
			if(val)
				$("#"+name).parent().show(222);
			else
				$("#"+name).parent().hide(222);
		}
		else
		{
			if(val)
				$("#"+name).parent().parent().show(222);
			else
				$("#"+name).parent().parent().hide(222);
		}
	}
	var	in_trigers=false;
	function form_changed()
	{
		if(!in_trigers)
		{
			$("#submit_btn").attr('disabled',false);
			$("#submit_btn").attr('value',save_str_tr);
			setTimeout(function() {GetOptionsData();},100);
		}
	}
	function GetOptionsData()
	{	
		OptionsDataStr='';
		$.each($("#options_form input, #options_form select"),function(){
			var id=$(this).attr('id');
			if(id && $(this).attr('name') && id!='htracer_admin_pass' && id!='htracer_mysql_pass'
			&& (!$(this).attr('type') || $(this).attr('type').toLowerCase()!='submit'))
			{
				var val;	
				if($(this).attr('type') && $(this).attr('type').toLowerCase()=='checkbox')
				{
					if($(this).attr('checked'))
						val=1;
					else
						val=0;
				}
				else
				{
					val=$(this).val();
					if(!val)
						val='';
				}
				OptionsDataStr+=id+'='+val+"\n";
			}
		});
		$('#export_text').html(OptionsDataStr.trim());
	}
	function SetOpacity(el,opacity)
	{
		var pOpacity = opacity* 100;
		$(el).css('filter', ':progid:DXImageTransform.Microsoft.Alpha(opacity='+pOpacity+')');
		$(el).css('-moz-opacity', opacity);
		$(el).css('-khtml-opacity', opacity);
		$(el).css('opacity', opacity);
	}
	
	
	var is_prefix_detected=false;
	var is_host_detected=false;
	
	function RefreshBehaviorMetrics()
	{
		var val=$("#htracer_trace_grooping").val();
		val= (!val||val===0||val==='0'||val==='1'||val==='1');
		val= (val &&  $("#htracer_mysql").val()!=0);
		SetOptEnabled("htracer_trace_view_depth",val);
		SetOptEnabled("htracer_trace_use_targets",val);
		var d='none';
		if(val && $("#htracer_show_all_options").attr('checked') && $("#htracer_trace_use_targets").attr('checked'))
			d='block';
		$("#targrets").css('display',d);
	}
	$(function() {
		if($('#test_query').val().trim())
			CheckQueryFilters();
		$('#test_query,#htracer_trace_sex_filter,#htracer_trace_free_filter,#htracer_trace_download_filter,#htracer_mats_filter,#htracer_symb_white_list,#htracer_numeric_filter,#htracer_not_ru_filter,#htracer_user_minus_words').change(function(){CheckQueryFilters()});
		$('#test_query,#htracer_user_minus_words').keypress(function(){CheckQueryFilters()});
		
		SetOptVisible('htracer_context_links_selector',$("#hkey_insert_context_links").val()=='selector');
		$("#test_cloud_btn").trigger('click');
	
		$("#submit_btn").attr('disabled',true);
		$('#options_form input,#options_form textarea').keypress(function(){form_changed()});
		$('#options_form input,#options_form select,#options_form textarea').change(function(){form_changed()});
		$('#options_form').ajaxForm({ 
		        beforeSubmit:  function(){
				    $("#options_form input").attr('disabled',false);
				    $("#options_form select").attr('disabled',false);
					
					$("#submit_btn").attr('disabled',true);
					$("#submit_btn").attr('value',saving_str);
				},  // pre-submit callback 
				success:       function(){
					$("#submit_btn").attr('value',saved_str);
					Trigers();
					//$("#submit_btn").attr('disabled',true);
				}
       });
		$("#htracer_mysql").change(function() {
			RefreshBehaviorMetrics();
		});
		$("#htracer_mysql").keypress(function() {
			RefreshBehaviorMetrics();
		});



		$("#htracer_mysql").change(function() {
			RefreshBehaviorMetrics();
			var val=($("#htracer_mysql").val()!=0);
			SetOptEnabled('htracer_mysql_login',val);
			SetOptEnabled('htracer_mysql_pass',val);
			SetOptEnabled('htracer_mysql_dbname',val);
			SetOptEnabled('htracer_mysql_host',val);
			SetOptEnabled('htracer_mysql_prefix',val);
			SetOptEnabled('htracer_mysql_set_names',val);
			SetOptEnabled('htracer_mysql_ignore_mysql_ping',val);
			SetOptEnabled('htracer_mysql_dont_create_tables',val);
			SetOptEnabled('htracer_mysql_optimize_tables',val);
			SetOptEnabled('htracer_mysql_close',val);

			SetOptEnabled('htracer_trace_view_depth',val);
			SetOptEnabled('htracer_trace_use_targets',val);
			
			SetOptEnabled('htracer_trace_p1_url',val);
			SetOptEnabled('htracer_trace_p2_url',val);
			SetOptEnabled('htracer_trace_p3_url',val);
			SetOptEnabled('htracer_trace_p4_url',val);
			SetOptEnabled('htracer_trace_p5_url',val);
			
			SetOptEnabled('htracer_trace_p1_bonus',val);
			SetOptEnabled('htracer_trace_p2_bonus',val);
			SetOptEnabled('htracer_trace_p3_bonus',val);
			SetOptEnabled('htracer_trace_p4_bonus',val);
			SetOptEnabled('htracer_trace_p5_bonus',val);
			

			
			if(val)
			{
				$('#synhr_filters_btn').attr('disabled',false);
				$('#synhr_filters_txt').css('display', 'none');
			}
			else
			{
				$('#synhr_filters_btn').attr('disabled',true);
				$('#synhr_filters_txt').css('display', 'inline');
			}
		});
		
		$("#htracer_cash_days").change(function() {
			var val=($("#htracer_cash_days").val()!=0);
			SetOptEnabled('htracer_cash_use_gzip',val);
			SetOptEnabled('htracer_short_cash',val);
			SetOptEnabled('htracer_cash_save_full_pages',val);
		});


		$("#hkey_insert_context_links").change(function() {
			var val=($("#hkey_insert_context_links").val()!=0);
			SetOptEnabled('htracer_site_stop_words',val);
			SetOptEnabled('htracer_context_links_b',val);
			SetOptEnabled('htracer_clcore_size',val);
			SetOptEnabled('htracer_max_clinks',val);		
			SetOptEnabled('htracer_clinks_segment_lng',val);		
			SetOptVisible('htracer_context_links_selector',$("#hkey_insert_context_links").val()=='selector');		
		});
		
		
		$("#htracer_insert_img_alt").change(function() {
			var val=($("#htracer_insert_img_alt").attr('checked'));
			SetOptEnabled('htracer_img_alt_rewrite',val);
		});
		
		
		$("#htracer_insert_a_title").change(function() {
			var val=($("#htracer_insert_a_title").attr('checked'));
			SetOptEnabled('htracer_a_title_rewrite',val);
		});
		
		
		$("#htracer_insert_meta_keys").change(function() {
			var val=($("#htracer_insert_meta_keys").attr('checked'));
			SetOptEnabled('htracer_meta_keys_rewrite',val);
		});
		
		$("#htracer_trace_use_targets").change(function() {
			RefreshBehaviorMetrics();
		});
		$("#htracer_show_all_options").change(function() {
			var val=($("#htracer_show_all_options").attr('checked'));
			SetOptVisible('htracer_meta_keys_rewrite',val,true);
			//
			SetOptVisible('htracer_cloud_pre',val);
			SetOptVisible('htracer_cloud_post',val);

			SetOptVisible('htracer_img_alt_rewrite',val,true);
			SetOptVisible('htracer_a_title_rewrite',val,true);
			SetOptVisible('htracer_mysql_prefix',val||is_prefix_detected);
			SetOptVisible('htracer_mysql_host',val||is_host_detected||is_mysql_host_error);
			SetOptVisible('htracer_mysql_ignore_mysql_ping',val||is_mysql_ping_error);
			SetOptVisible('htracer_cash_use_gzip',val);
			SetOptVisible('htracer_cash_save_full_pages',val);
			SetOptVisible('htracer_site_stop_words',val);
			SetOptVisible('htracer_mysql_optimize_tables',val);
			SetOptVisible('htracer_mysql_close',val);
			SetOptVisible('htracer_only_night_update',val);
			SetOptVisible('htracer_pconnect',val);
			SetOptVisible('htracer_url_exceptions',val);
			SetOptVisible('htracer_ignored_get_params',val);
			
			SetOptVisible('htracer_premoderation',val);
			if(!val)
				$('#user_minus_words').css('display', 'none');
			else
				$('#user_minus_words').css('display', 'block');
				

			if(!is_ru_lang)
				SetOptVisible('htracer_encoding',val);
			SetOptVisible('ohr_3',val,true);
			SetOptVisible('ohr_4',val,true);
			
			

			SetOptVisible('htracer_trace',val);
			SetOptVisible('htracer_trace_download_filter',val);
			SetOptVisible('htracer_trace_service_filter',val);
			SetOptVisible('htracer_mats_filter',val);
			
			SetOptVisible('htracer_validate',val);
			SetOptVisible('htracer_symb_white_list',val);
			SetOptVisible('htracer_mysql_dont_create_tables',val);
			//SetOptVisible('htracer_admin_pass',val);
			
			SetOptVisible('htracer_cloud_style',val);
			SetOptVisible('htracer_clcore_size',val);
			SetOptVisible('htracer_max_clinks',val);		
			SetOptVisible('htracer_clinks_segment_lng',val);	
			
			SetOptVisible('htracer_trace_runaway',val);
			SetOptVisible('htracer_trace_view_depth',val);
			SetOptVisible('htracer_trace_use_targets',val);
			
			
			SetOptVisible('htracer_numeric_filter',val);
			SetOptVisible('htracer_not_ru_filter',val);
			
			
			$("#htracer_trace_use_targets").trigger('change');
		});
		Trigers();
		isLoading=false;
		$("#submit_btn").attr('disabled',true);
		GetOptionsData();
		
		$("#htracer_mysql,#htracer_mysql_login,#htracer_mysql_pass,#htracer_mysql_host"
		+",#htracer_mysql_dbname,#htracer_mysql_ignore_mysql_ping").change(function() {
			if(!in_trigers)
			{
				$("#test_mysql_btn").attr("disabled",false);
				SetOpacity("#test_mysql_res",0.3);
			}	
		});
		$("#htracer_mysql_login,#htracer_mysql_pass,#htracer_mysql_host"
		+",#htracer_mysql_dbname").keypress(function() {
			if(!in_trigers)
			{
				$("#test_mysql_btn").attr("disabled",false);
				SetOpacity("#test_mysql_res",0.3);
			}
		});
		if(allow_autodetect)
		{
			$.ajax({
				success: function(data,p1,p2){
					var ctype=p2.getResponseHeader('Content-type');
					if(ctype && $('#htracer_encoding'))
					{
						if(ctype.indexOf('1251')!=-1)
						{
							$('#htracer_encoding').val('windows-1251');
							$('#encoding_autodetect').html(encoding_auto_str+' Windows-1251 (CP1251).<br /><br />');
						}	
						else if(ctype.toLowerCase().indexOf('utf-8')!=-1||ctype.toLowerCase().indexOf('utf8')!=-1)
							$('#encoding_autodetect').html(encoding_auto_str+' UTF-8.<br /><br />');
					}
				},
				url: "../../"
			});
			$.ajax({
				url: "ajax/mysql_auto_detect.php",
				dataType: 'json',
				success: function(data)
				{
					if(data['engine'])
					{
						$('#mysql_autodetect_msg').html(mysql_auto_str.replace('%engine%',data['engine']));
						$('#htracer_mysql_login').val(data['user']);
						$('#htracer_mysql_pass').val(data['pass']);
						$('#htracer_mysql_dbname').val(data['db']);
						$('#htracer_mysql_host').val(data['host']);
						
						
						//SetOptVisible('htracer_mysql_host',val);
						if(data['host'] && data['host']!='localhost')
						{
							SetOptVisible('htracer_mysql_host',true);
							$('#htracer_mysql_host').val(data['host']);
							is_host_detected=true;
						}						
						if(data['prefix'])
						{	
							SetOptVisible('htracer_mysql_prefix',true);
							$('#htracer_mysql_prefix').val(data['prefix']);
							is_prefix_detected=true;
						}
					}
				}
			});
		}
		CheckMySQL();
	});
	function Trigers()
	{
		in_trigers=true;
		$("#htracer_cash_days").trigger('change');
		$("#htracer_mysql").trigger('change');
		$("#hkey_insert_context_links").trigger('change');
		$("#htracer_insert_img_alt").trigger('change');
		$("#htracer_insert_a_title").trigger('change');
		$("#htracer_insert_meta_keys").trigger('change');
		$("#htracer_show_all_options").trigger('change');
		$("#htracer_cloud_style").trigger('change');
		in_trigers=false;
	}
							
	$("#test_query").bind("keypress",
		function(e)
		{
			if((window.event && window.event.keyCode == 13)
			||(e && e.keyCode == 13))
			{
				$("#test_query_btn").trigger("click");
				return false;
			}
		}
	);	