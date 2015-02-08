<?php
	$res = '';

	if(isset($id)){
	$res .= form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=organizer'.AMP.'id='.$id);
	}
	else{
		$res .= form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=organizer');
	}
	$this->table->set_template($cp_table_template);
	
	
	$this->table->set_heading(lang('option'), lang('value'));
	
	$this->table->add_row(
		form_label(lang('name'), 'name'),
		form_input('name', $name)
	);
	
	$this->table->add_row(
		form_label(lang('description'), 'description'),
		form_textarea('description', $description)
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