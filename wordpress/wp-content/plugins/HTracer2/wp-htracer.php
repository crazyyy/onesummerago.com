<?php
/*
Plugin Name: HTracer
Description: SEO plugin
Author: Hkey
Version: 3.4.2
*/
$GLOBALS['htracer_is_wp_plugin']=true;

$GLOBALS['htracer_trace']=false;
$GLOBALS['htracer_mysql']=true;

include_once(dirname(__FILE__).'/HTracer.php');
class WP_HTracer
{
	function WP_HTracer()
	{
		$this->read_ini();
		$charset=get_bloginfo('charset');
		if($charset && trim($charset))
			$GLOBALS['htracer_encoding']=strtolower($charset);
			
		add_action('init', array(&$this, 'init_action'));
		add_action('admin_menu', array(&$this, 'admin_menu'));//Добавляем опции в админку
		add_filter('the_content', array(&$this, 'the_content_filter'),100);
		add_filter('wp_list_categories', array(&$this, 'wp_list_filter'),100);
		add_filter('wp_list_pages', array(&$this, 'wp_list_filter'),100);
		add_filter('widget_text', array(&$this, 'widget_text'),100);
		//widget_text
		add_filter('aioseop_keywords', array(&$this, 'aioseop_keywords_filter'));
	}
	function widget_text($Text,$Inst=false)
	{
		if(function_exists('htracer_insert_clouds'))
			return htracer_insert_clouds($Text);
		return $Text;
	}

	function init_action()
	{	
		if(!$this->options['not_trace'])
			HTracer::AddQuery();
			
		if($this->options['insert_in_all'])
		{
			$GLOBALS['insert_keywords_where']='a_title+img_alt+meta_keys';
			$GLOBALS['insert_keywords_params']=$this->options['insert_in_all_pars'];
			ob_start('insert_keywords_cb');
		}
		//if($this->options['test_mode'])
		//	$GLOBALS['htracer_test']=true;
	}
	function admin_menu()
	{
		add_options_page('HTracer', 'HTracer', 8,'HTracer', array(&$this, 'admin'));
	}
	function wp_head_action()
	{
		if($this->options['add_meta_keywords'])
			the_meta_keys_tag();
	}
	function aioseop_keywords_filter($content)
	{
		$keys='';
		if($this->options['add_meta_keywords'])
			$keys=get_meta_keywords();
		if($content && $keys)
			$content.=', ';
		return $content.$keys;
	}
	function cloud_filter($content)
	{
		if($this->options['replace_cloud'])
			$content=get_keys_cloud($this->options['replace_cloud_pars']);
		return $content;
	}
	function wp_list_filter($content)
	{
		if($this->options['insert_in_list'])
			$content=insert_keywords($content,'a_title',$this->options['insert_in_list_pars']);
		return $content;
	}
function close_dangling_tags($html){
  #put all opened tags into an array
  preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU",$html,$result);
  $openedtags=$result[1];
 
  #put all closed tags into an array
  preg_match_all("#</([a-z]+)>#iU",$html,$result);
  $closedtags=$result[1];
  $len_opened = count($openedtags);
  # all tags are closed
  if(count($closedtags) == $len_opened){
    return $html;
  }
 
  $openedtags = array_reverse($openedtags);
  # close tags
  for($i=0;$i < $len_opened;$i++) {
    if (!in_array($openedtags[$i],$closedtags)){
      $html .= '</'.$openedtags[$i].'>';
    } else {
      unset($closedtags[array_search($openedtags[$i],$closedtags)]);
    }
  }
  return $html;
}

	function the_content_filter($content)
	{
		if($this->options['insert_in_content'])
			$content=insert_keywords($content,'a_title+img_alt',$this->options['insert_in_list_pars']);
		if(is_single() && $this->options['clinks'])	
			$content=hkey_insert_context_links($content);
		if(is_single() && $this->options['add_to_content_end'])	
		{
			$content=str_replace('^','&*+hrs+*',$content);//экранируем символ ^
			$content=str_replace(array('<br />','<br/>','<br>'),array('^','^','^'),$content);//заменяем БР на ^ 
			$content=rtrim($content,'^'); // удаляем брейкеты с конца
			$content=str_replace('^','<br />',$content);//востанавливаем БР 
			$content=str_replace('&*+hrs+*','^',$content);//деэкранируем символ ^
			$content=$content.'<br />'.get_keys_cloud($this->options['add_to_content_end_pars']);
		}
		return $content;
	}
	function OutInput($Name,$Type='checkbox')
	{
		$Value=$this->options[$Name];
		if(!function_exists('get_magic_quotes_gpc')||!get_magic_quotes_gpc())
			$Value=addslashes($Value);
		if($Type=='checkbox')
		{
			if(isset($this->options[$Name]) && $this->options[$Name])
				echo "<input name='$Name' id='$Name' type='$Type' checked='checked' value='1' />";
			else
				echo "<input name='$Name' id='$Name' type='$Type' value='1' />";
		}
		else
			echo "<input name='$Name' id='$Name' type='$Type' value='$Value' size='55' />";
	}
	static function GetHURL()
	{
		$hURL= $_SERVER['REQUEST_URI'];
		$hURL=explode('/wp-admin/',$hURL,2);
		if(count($hURL)===1)
			$hURL=explode("\\wp-admin\\",$hURL,2);
		if(count($hURL)===1)
			$hURL='';
		else
			$hURL=$hURL[0];
		$hURL='http://'.$_SERVER['SERVER_NAME'].$hURL.'/'.'wp-content/plugins/HTracer/admin/options.php';
		 //;
		$aCS=ht_calc_wp_key();
		$hURL.='?wp_akey='.$aCS;
		return $hURL;
	}
	function admin()
	{
		if($_POST['was_wp_options_post'])
			$this->LoadFromPost();
		$tabSelected=0;
		if(isset($_POST['was_ht_admin_post_tmp'])||isset($_POST['was_ht_admin_post'])
		||isset($_POST['change_query_weigth']) ||isset($_POST['ht_page']))
			$tabSelected=2;
		elseif($_POST['was_ht_import_post']||$_POST['ht_in_csv_import'])
			$tabSelected=3;
		elseif($_POST['was_ht_export_post'])
			$tabSelected=4;
			
			

		$hURL=WP_HTracer::GetHURL();
		?>
		<script type="text/javascript">
			function SetAdvField(id,val)
			{
				if(!val)
					jQuery('#'+id).css('display','none');
				else
					jQuery('#'+id).css('display','inline');
			}
			function SetAdvFields()
			{
				window.setTimeout(function(){SetAdvFields0();});
			}
			function SetAdvFields0()
			{
				try{
					var val = jQuery('#show_all_options').attr('checked');	
					SetAdvField('insert_in_list_pars',val);
					SetAdvField('insert_in_content_pars',val);
					SetAdvField('add_to_content_end_pars',val);
					SetAdvField('replace_cloud_pars',val);
					SetAdvField('insert_in_all_pars',val);
					SetAdvField('insert_in_all_td',val);

					SetAdvField('pars_str',val);
					SetAdvField('not_trace_span',val);
					SetAdvField('not_trace_ptd',val);
				}catch(e){}
			}
			jQuery(document).ready(function(){
				SetAdvFields();
			});
		</script>
		<style>
			#options, #options td
			{
				font-size: 12pt;
			}
			#options input[type='checkbox']
			{
				margin-bottom:5px;
			}
		</style>
		<div class="wrap">
			<div id='options' style='padding: 20px; '>
				<div id="icon-options-general" class="icon32"><br></div>
				<h2><?php echo __("HTracer options", 'HTracer'); ?></h2>

				
				<form method="post">
					<input type="hidden" name='waspost' value='1' />
					<input type="hidden" name='was_wp_options_post' value='1' />
					<table>
						<tr><td colspan="2"><input type='checkbox' id='show_all_options' onclick='SetAdvFields()' /><?php echo __("Show all options", 'HTracer'); ?></td></tr>
						<tr><td colspan="2"><br /></td></tr>
						<tr><td colspan="2"><?php $this->OutInput('add_meta_keywords');?> <?php echo __('Add Meta Keywords', 'HTracer'); ?> <i>(<?php echo __('requires', 'HTracer'); ?>  <a href="http://wordpress.org/extend/plugins/all-in-one-seo-pack/">All In One SEO Pack</a>)</i></td></tr>
						<tr><td></td><td><b id='pars_str'><?php echo __("Parameters", 'HTracer'); ?>:</b></td></tr>
						<tr><td><?php $this->OutInput('insert_in_list');?>    							<?php echo __('Insert link titles in lists of categories and pages', 'HTracer'); ?></td><td><?php $this->OutInput('insert_in_list_pars'		,'text');?></td></tr>
						<tr><td><?php $this->OutInput('insert_in_content');?> 							<?php echo __('Insert link titles and img alts in post content', 'HTracer'); ?>	</td><td><?php $this->OutInput('insert_in_content_pars'	,'text');?></td></tr>
						<tr><td><?php $this->OutInput('replace_cloud');?>	  							<?php echo __('Replace default WP Cloud with HTracer`s cloud', 'HTracer'); ?>    			</td><td><?php $this->OutInput('replace_cloud_pars'		,'text');?></td></tr>
						<tr><td id='insert_in_all_td'><?php $this->OutInput('insert_in_all');?>	  		<?php echo __('Insert links titles and img alts everywhere', 'HTracer'); ?> 		</td><td><?php $this->OutInput('insert_in_all_pars'		,'text');?></td></tr>
						<tr><td><?php $this->OutInput('add_to_content_end');?>							<?php echo __('Add a list of links at the end of post', 'HTracer'); ?>							</td><td><?php $this->OutInput('add_to_content_end_pars'	,'text');?></td></tr>
						<tr><td colspan="2"><?php $this->OutInput('clinks');?> 							<?php echo __('Insert context links', 'HTracer'); ?>								</td></tr>
						<tr><td colspan="2" id='not_trace_ptd'><br /></td></tr>
						<tr><td colspan="2"><span id='not_trace_span'><?php $this->OutInput('not_trace');?> <?php echo __('Do not remember visits', 'HTracer'); ?>	</span></td></tr>
					</table>
					<br />
					<input type="submit" class='button-primary' value='<?php echo __('Save options', 'HTracer'); ?>' /> 
				</form>					
				<br /><br />
				<span style="font-size:12pt;"><a href='<?php echo $hURL;?>'><?php echo __('More options', 'HTracer'); ?></a></span>
			</div>	
			
		</div>
		<script type="text/javascript">
		/* <![CDATA[ */
			jQuery(document).ready(function(){
				jQuery('html,body').scrollTop(0);
			});	
		/* ]]> */
		</script>
			
<?php
		HTracer::CreateTables();
		$this->write_ini();		
		//echo '<br><br><H1>Просмотр и редактирование запросов</H1>';	
		//include(dirname(__FILE__).'/admin.php');
		//echo '<br><br><H1>Импорт запросов</H1>';	
		//include(dirname(__FILE__).'/import.php');
?>
		
<?php
	}
	function LoadFromPost()
	{
		if(!$_POST['waspost'])
			return;
		$this->options=Array();
		
		foreach($_POST as $key => $Value)
		{
			$Value=stripslashes($Value);
			$this->options[$key]=$Value;
		}
	}
	function read_ini($path=false)
	{
		if($this->options && count($this->options))
			return;
		if(!$path)
			$path=dirname(__FILE__).'/wp_options.ini';
		$assoc_array=@parse_ini_file($path,true);
		foreach ($assoc_array as $key => $item) 
		{
			if (is_array($item))
				foreach ($item as $key2 => $item2) 
					$assoc_array[$key][$key2]=stripslashes($item2);
			else	
				$assoc_array[$key]= stripslashes($item);
		}
		$this->options=$assoc_array;
	}
	function write_ini($path=false, $assoc_array=false) 
	{	
		if(!$path)
			$path=dirname(__FILE__).'/wp_options.ini';
		if(!$assoc_array)	
			$assoc_array=$this->options;
		foreach ($assoc_array as $key => $item) 
		{
			if (is_array($item)) 
			{
				$content .= "\n[$key]\n";
				foreach ($item as $key2 => $item2) 
				{
					$item2=addslashes($item2);
					$content .= "$key2 = \"$item2\"\n";
				}
			}	 
			else 
			{
				$item=addslashes($item);
				$content .= "$key = \"$item\"\n";
			}
		}
		$handle = fopen($path, 'w');
		if (!$handle||!fwrite($handle, $content))
			return false;
		fclose($handle);
		return true;
	}
};
$WP_HTracer= new WP_HTracer();



/// ВИДЖЕТЫ


if(!file_exists('disable_widgets'))
{
	if (version_compare(PHP_VERSION, '5.3.0') >= 0)
	{
		include('wp_php_5_3.php');
	}
	elseif(class_exists('WP_Widget'))
	{
		class HTracer_WP_Widget extends WP_Widget {}
	}
}
if(class_exists('HTracer_WP_Widget'))
{
	class HTracer_Cloud_Widget extends HTracer_WP_Widget 
	{
		public function __construct() 
		{
			parent::__construct(
				'hcloud', // Base ID
				__("HTracer`s Cloud", 'HTracer'), // Name
				array('description' => __("Display HTracer`s semantic cloud", 'HTracer')) // Args
			);
		}

		public function widget( $args, $instance ) 
		{
			extract($args);
			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;
			if(function_exists('get_keys_cloud'))
				echo get_keys_cloud();
			else
				echo "HTracer is not installed";
			echo $after_widget;
		}
		public function update( $new_instance, $old_instance ) 
		{
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			return $instance;
		}
		public function form( $instance ) 
		{
			$hURL=WP_HTracer::GetHURL().'#tabs-cloud';
			if (isset($instance['title'])) 
				$title = $instance['title'];
			else 
				$title = __('Cloud', 'HTracer');
			?>
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>
				<p>
					<a href="<?php echo $hURL?>"><?php echo __('Configure cloud', 'HTracer'); ?></a>
				</p>
			<?php 
		}
	}
	class HTracer_Links_Widget extends HTracer_WP_Widget 
	{
		public function __construct() 
		{
			parent::__construct(
				'hlinks', // Base ID
				__("HTracer`s Links", 'HTracer'), // Name
				array('description' => __("Display HTracer`s links block", 'HTracer')) // Args
			);
		}

		public function widget( $args, $instance ) 
		{
			extract($args);
			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;
			$rand=trim($instance['randomnicity']);
			if($rand*1<0)
				$rand=1;
			$from=trim($instance['count'])*$rand;
			$count=trim($instance['count']);
			$offset=trim($instance['offset']);
			if($offset==='auto')
			{
				$offset=0;
				if(isset($GLOBALS['htracer_cloud_links']) && isset($GLOBALS['htracer_cloud_randomize']))
					$offset=$GLOBALS['htracer_cloud_links']*$GLOBALS['htracer_cloud_randomize'];
			}
			$offset=round($offset);
			if($offset<0)
				$offset=0;

			echo $offset;
			if(function_exists('get_keys_cloud'))
				echo get_keys_cloud("style=ul_list {$count}/$from&offset=".$offset);
			else
				echo "HTracer is not installed";
			echo $after_widget;
		}
		public function update( $new_instance, $old_instance ) 
		{
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['count'] = strip_tags( $new_instance['count'] );
			$instance['randomnicity'] = strip_tags( $new_instance['randomnicity'] );
			$instance['offset'] = strip_tags( $new_instance['offset'] );
			
			return $instance;
		}
		public function form( $instance ) 
		{
			$title = __('See also', 'HTracer');
			$count = 10;
			$randomnicity = 1;
			$offset = 0;
			
			if (isset($instance['title'])) 
				$title = $instance['title'];
			if (isset($instance['count'])) 
				$count = $instance['count'];
			if (isset($instance['randomnicity'])) 
				$randomnicity = $instance['randomnicity'];
			if (isset($instance['offset'])) 
				$offset = $instance['offset'];
				
				
			?>
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php echo __('Count', 'HTracer'); ?>:</label> 
					<input size="3" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'randomnicity' ); ?>"><?php echo __('Randomnicity', 'HTracer'); ?>:</label> 
					<input size="3" id="<?php echo $this->get_field_id( 'randomnicity' ); ?>" name="<?php echo $this->get_field_name( 'randomnicity' ); ?>" type="text" value="<?php echo esc_attr( $randomnicity ); ?>" /> <?php echo __('From 1 to 5', 'HTracer'); ?>
				</p>
				<p>
					<label for="<?php echo $this->get_field_id( 'offset' ); ?>"><?php echo __('Offset', 'HTracer'); ?>:</label> 
					<input size="3" id="<?php echo $this->get_field_id( 'offset' ); ?>" name="<?php echo $this->get_field_name( 'offset' ); ?>" type="text" value="<?php echo esc_attr( $offset ); ?>" />
					
				<p>				
					<br />
					<?php echo __('Offset avoid duplication of links in the cloud and link block');?>.<br /><br />
					<?php echo __('If you dont HTracer`s use cloud, set offset=0');?>.<br /><br />
					<?php echo __('If you use HTracer`s cloud widget, set offset=auto');?><br /><br />
					<?php echo __('If you call the_keys_cloud() without params, set offset=auto');?><br /><br />
					<?php echo __('If you call the_keys_cloud("style=10/20"), set offset=20');?>.<br /><br />
				</p>
				
			<?php 
		}
	} 
	class HTracer_ULinks_Widget extends HTracer_WP_Widget 
	{
		public function __construct() 
		{
			parent::__construct(
				'hulinks', // Base ID
				__("HTracer`s ULinks", 'HTracer'), // Name
				array('description' => __("Display HTracer`s unical links block", 'HTracer')) // Args
			);
		}

		public function widget( $args, $instance ) 
		{
			extract($args);
			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;
			if(function_exists('get_keys_cloud'))
				echo get_keys_cloud();
			else
				echo "HTracer is not installed";
			echo $after_widget;
		}
		public function update( $new_instance, $old_instance ) 
		{
			$instance = array();
			$instance['title'] = strip_tags( $new_instance['title'] );
			return $instance;
		}
		public function form( $instance ) 
		{
			$title = __('See also', 'HTracer');
			$count = 1;
			if (isset($instance['title'])) 
				$title = $instance['title'];
			?>
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>
			<?php 
		}
	} 
	add_action('widgets_init', 'htracer_widgets_init',1);
	function htracer_widgets_init()
	{
		try 
		{
			register_widget('HTracer_Cloud_Widget');
			register_widget('HTracer_Links_Widget');
			if($GLOBALS['htracer_ulink_plugin'])
				register_widget('HTracer_ULinks_Widget');
		} catch (Exception $e) {}
	}
}
?>