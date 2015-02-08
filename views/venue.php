<?php
	$res = '';

	if(isset($id)){
	$res .= form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=venue'.AMP.'id='.$id);
	}
	else{
		$res .= form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=venue');
	}
	$this->table->set_template($cp_table_template);
	
	
	$this->table->set_heading(lang('option'), lang('value'));
	
	if(isset($organizers)){
		$this->table->add_row(
			form_label(lang('organizer_id'), 'organizer_id'),
			form_dropdown('organizer_id', $organizers)
		);
	}
	
	$this->table->add_row(
		form_label(lang('name'), 'name'),
		form_input('name', $name)
	);
	
	$this->table->add_row(
		form_label(lang('address'), 'address'),
		form_input('address', $address)
	);
	
	$this->table->add_row(
		form_label(lang('address_2'), 'address_2'),
		form_input('address_2', $address_2)
	);
	
	$this->table->add_row(
		form_label(lang('city'), 'city'),
		form_input('city', $city)
	);
	
	$this->table->add_row(
		form_label(lang('region'), 'region'),
		form_input('region', $region)
	);
	
	$this->table->add_row(
		form_label(lang('postal_code'), 'postal_code'),
		form_input('postal_code', $postal_code)
	);
	
	$this->table->add_row(
		form_label(lang('country_code'), 'country_code'),
		form_input('country_code', $country_code)
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