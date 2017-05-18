	var was_pages_changed=false;
	$(function() {
	
		if(is_ru_lang)
		{
			//alert(1);
			$('#pages_table').dataTable({
				'bJQueryUI': true,
				"bStateSave": true,
				
				//'fnDrawCallback':function() {alert(22)}, 
				'sPaginationType': 'full_numbers',
				"aaSorting": [[ 1, "desc" ]],
				
				
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": 'ajax/pages.php',
				'fnPreDrawCallback':function() {
					if(was_pages_changed && !confirm(not_save_confirm))
						return false;
					was_pages_changed=false;
				},
				'fnDrawCallback':function() {
					was_pages_changed=false;
					table_drawn();
				},
				"aoColumnDefs": [
					{ "bSortable": false, "aTargets": [4,5]},
					{"sWidth": "50px", "aTargets": [1]},
					{ "sClass": "page_td", "aTargets": [ 0 ]},
					{ "sClass": "peva_td", "aTargets": [ 1 ]},
					{ "sClass": "k1_td", "aTargets": [ 2 ]},
					{ "sClass": "k2_td", "aTargets": [ 3 ]},
					{ "sClass": "cloud_td", "aTargets": [ 4 ]},
					{ "sClass": "titles_td", "aTargets": [ 5 ]}
				],
				"oLanguage":{
					"sProcessing":   "Подождите...",
					"sLengthMenu":   "Показать _MENU_ страниц",
					"sZeroRecords":  "Страниц нет",
					"sInfo":         "Страницы с _START_ до _END_ из _TOTAL_",
					"sInfoEmpty":    "Страницы с 0 до 0 из 0",
					"sInfoFiltered": "(отфильтровано из _MAX_ страниц)",
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
		{//alert(22);
			$('#pages_table').dataTable({
				'bJQueryUI': true,
				"bStateSave": true,
				//'fnDrawCallback':function() {alert(22)}, 
				'sPaginationType': 'full_numbers',
				"aaSorting": [[ 1, "desc" ]],
			
				"bProcessing": true,
				"bServerSide": true,
				"sAjaxSource": 'ajax/pages.php',
				'fnPreDrawCallback':function() {
					if(was_pages_changed && !confirm(not_save_confirm))
						return false;
					was_pages_changed=false;
				},
				'fnDrawCallback':function() {
					was_pages_changed=false;
					table_drawn();
				},
				"aoColumnDefs": [
					{ "bSortable": false, "aTargets": [4,5]},
					{"sWidth": "50px", "aTargets": [1]},
					{ "sClass": "page_td", "aTargets": [ 0 ]},
					{ "sClass": "peva_td", "aTargets": [ 1 ]},
					{ "sClass": "k1_td", "aTargets": [ 2 ]},
					{ "sClass": "k2_td", "aTargets": [ 3 ]},
					{ "sClass": "cloud_td", "aTargets": [ 4 ]},
					{ "sClass": "titles_td", "aTargets": [ 5 ]}
				]
			});
		}
		$('#pages_table').css('width','100%');
		$('.cloud_th, .titles_th').css('width','auto');		
		if(!is_ru_lang)
			$('.peva_td').css('width','55px');
			
		if(is_ru_lang)
		{
			$("#pages_table_filter label").click(function(){ 
				ShowHintDialog('',
						'Поиск проходит в полях "URL", "Первый ключевик" и "Второй ключевик"<br /><br />'+
						'По умолчанию при поиске ищуться включения. Например, если вы наберете "<span style="color:gray">одесс</span>", то найдутся все ключи содержащие эту строку<br /><br />'+
						'Чтобы найти четкие соответствия перед запросом поставьте равно: "<span style="color:gray">=одесса</span>" или "<span style="color:gray">=/page.html</span>"<br /><br />'+
						'Чтобы найти строки начинающиеся с заданной поставьте перед запросом символ меньше: "<span style="color:gray">&lt;одесс</span>" или "<span style="color:gray">&lt;/dir/</span>"<br /><br />'+
						'Чтобы найти строки заканчивающися заданной поставьте перед запросом символ больше: "<span style="color:gray">>одесс</span>" или "<span style="color:gray">>/dir/</span>"<br /><br />'
				);
			});
		}
		$("#pages_table_filter input").click( function(){ 
			return false;				
		});
		$("#pages_table_filter input").autocomplete(
		{
			source: 'ajax/pages_autocomplete.php',
			search: function(event, ui) 
			{
				return $("#pages_table_filter input").val().length>=2
					&& ($("#pages_table_filter input").val().substr(0,2)=='=/'
					 || $("#pages_table_filter input").val().substr(0,2)=='</');
			}
		});
		$("#submit_btn").attr('disabled',true);
		
		$('#pages_form').ajaxForm(
		{
			beforeSubmit:  function(arr)
			{
				$("#submit_btn").attr('disabled',true);
				$("#submit_btn").attr('value',saving_str);
				arr.push({'name': 'ajax', 'value': '1'});
			},   
			//dataType:'json',
			success:function(responseText)
			{
				$("#submit_btn").attr('value',saved_str);
				table_drawn();
				was_pages_changed=false;
			}
		});
	});
		
	function form_changed()
	{
		was_pages_changed=true;
		$("#submit_btn").attr('disabled',false);
		$("#submit_btn").attr('value',save_str_tr);
	}
	function table_drawn()
	{
		$('#pages_table input').keypress(function(){form_changed()});
		$('#pages_table input').change(function(){form_changed()});
		$.each($('#pages_table .page_main_keys'),function(){
			$(this).autocomplete({source: 'ajax/keys_autocomplete.php?san_sugest=1&page='+$(this).attr('page')});
		});
		if($('#pages_table_filter input').val())
			$('td.dataTables_empty').html($('#pages_table td.dataTables_empty').html()
				+', <span style="color:gray">filter: <i>'+$('#pages_table_filter input').val())+'</i></span>';
	}