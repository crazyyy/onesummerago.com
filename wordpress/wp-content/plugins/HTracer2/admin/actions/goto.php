		<h3><a href="#goto_page"><?php echo ht_trans('Перейти на страницу')?></a></h3>
		<div>
			<form action='page.php'>
				<input id='goto_page_input' type='text' name='url' value='/' spellcheck='false' /><br />
				<input type='submit' value='<?php echo ht_trans('Перейти')?>' />
			</form>
		</div>
		<h3><a href="#add_page"><?php echo ht_trans('Добавить страницу')?></a></h3>
		<div>
			<form action='page.php'>
				<input id='add_page_input' type='text' name='url' value='/' spellcheck='false' /> 
				<input type='hidden' name='add_page' value='1' /><br />
				<input type='submit' value='<?php echo ht_trans('Создать')?>' />
			</form>
		</div>
