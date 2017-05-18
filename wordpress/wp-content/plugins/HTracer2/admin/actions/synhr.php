			<h3><a href="#synhr"><?php echo ht_trans('Синхронизировать фильтры')?></a></h3>
			<div>
				<form method='post'>
					<?php echo ht_trans('Изменения в разделе фильтры подействуют не сразу, чтобы избежать перегрузки сервера, они применяются постепенно')?>.<br /><br />
					<?php echo ht_trans('Однако вы сможете применить изменения, нажав следующую кнопку')?>:<br /><br />
					<input type='hidden' name='waspost' value='1' />
					<input type='hidden' name='form' value='synhr' />
					<input id='synhr_filters_btn'
						   type='submit' 
						   value='<?php echo ht_trans('Поехали')?>' 
						   <?php if(!$GLOBALS['htracer_mysql']) echo "disabled='disabled'"; ?> 
					/>
					<span id='synhr_filters_txt'
					      style='color:red; <?php if($GLOBALS['htracer_mysql']) echo "display:none";?>'>
						<?php echo ht_trans('Эта возможность недопустима при храненинии информации в файлах, а не в MySQL')?>.
					</span>
				</form>
			</div>