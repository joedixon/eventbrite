<?php
	$res = '';
	
	$res .= form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=settings');
	
	$this->table->set_template($cp_table_template);
	
	$this->table->set_heading(lang('option'), lang('value'));
	
	$this->table->add_row(
		form_label(lang('user_key'), 'user_key'),
		form_input('user_key', $user_key)
	);
	
	$res .= $this->table->generate();
	$res .= form_submit(
		array(
			'name' => 'submit',
			'value' => lang('eventbrite_save'),
			'class' => 'submit'
		)
	);
	$res .= form_close();
	echo $res;
?>