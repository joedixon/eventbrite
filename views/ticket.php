<?php
echo "<script type='text/javascript' charset='utf-8' src='$theme_folder_url/js/timepicker.js'></script>";
echo "<script type='text/javascript' charset='utf-8' src='$theme_folder_url/js/datepicker.js'></script>";
echo "<link rel='stylesheet' href='$theme_folder_url/css/timepicker.css' type='text/css' media='screen' />";
	$res = '';

	if(isset($id)){
	$res .= form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=ticket'.AMP.'id='.$id.AMP.'eid='.$eid);
	}
	else{
		$res .= form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=ticket'.AMP.'eid='.$eid);
	}
	$this->table->set_template($cp_table_template);
	
	
	$this->table->set_heading(lang('option'), lang('value'));
	
	if(isset($events)){
		$this->table->add_row(
			form_label(lang('event_id'), 'event_id'),
			form_dropdown('event_id', $events)
		);
	}
	
	$this->table->add_row(
		form_label(lang('is_donation'), 'is_donation'),
		form_dropdown('is_donation', array(0 => lang('fixed_price'), 1 => lang('is_donation')), $type)
	);
	
	$this->table->add_row(
		form_label(lang('name'), 'name'),
		form_input('name', $name)
	);
	
	$this->table->add_row(
		form_label(lang('description'), 'description'),
		form_textarea('description', $description)
	);
	
	if(isset($price)){
		$this->table->add_row(
		form_label(lang('price'), 'price'),
		form_input('price', $price)
	);
	}
	
	$this->table->add_row(
		form_label(lang('quantity_available'), 'quantity_available'),
		form_input('quantity_available', $quantity_available)
	);
	
	$this->table->add_row(
		form_label(lang('start_date'), 'start_date'),
		'<p class="datepair">' . form_input('start_date', $start_date, 'class="date start"') . form_input('start_time', $start_time, 'class="time start"') . '</p>'
	);
	
	$this->table->add_row(
		form_label(lang('end_date'), 'end_date'),
		'<p class="datepair">' . form_input('end_date', $end_date, 'class="date end"') . form_input('end_time', $end_time, 'class="time end"') . '</p>'
	);
	
	$this->table->add_row(
		form_label(lang('min'), 'min'),
		form_input('min', $min)
	);
	
	$this->table->add_row(
		form_label(lang('max'), 'max'),
		form_input('max', $max)
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