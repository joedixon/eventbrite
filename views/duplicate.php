<?php

	$res = '';

	$res .= form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=duplicate'.AMP.'id='.$id);
	
	$this->table->set_template($cp_table_template);
	
	$this->table->set_heading(lang('option'), lang('value'));
	
	$this->table->add_row(
		form_label(lang('title'), 'title'),
		form_input('title', $title, 'id="title"')
	);

	$res .= $this->table->generate();	

	$res .= form_submit(
		array(
			'name' => 'submit',
			'value' => lang('eventbrite_duplicate'),
			'class' => 'submit'
		)
	);
	$res .= form_close();
	
	echo $res;
?>