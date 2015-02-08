<?php
	echo "<script type='text/javascript' charset='utf-8' src='$theme_folder_url/js/colorpicker.js'></script>";
	echo "<script type='text/javascript' charset='utf-8' src='$theme_folder_url/js/timepicker.js'></script>";
	echo "<script type='text/javascript' charset='utf-8' src='$theme_folder_url/js/datepicker.js'></script>";
	echo "<link rel='stylesheet' href='$theme_folder_url/css/colorpicker.css' type='text/css' media='screen' />";
	echo "<link rel='stylesheet' href='$theme_folder_url/css/timepicker.css' type='text/css' media='screen' />";

	$res = '';

	if(isset($id)){
	$res .= form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=event'.AMP.'id='.$id);
	$res .= '<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=ticket'.AMP.'eid='.$id.'" class="submit" style="margin-bottom:20px">'.lang('new_ticket').'</a>';
	}
	else{
		$res .= form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=event');
	}
	
	$this->table->set_template($cp_table_template);
	
	$this->table->set_heading(lang('option'), lang('value'));
	
	$this->table->add_row(
		form_label(lang('title'), 'title'),
		form_input('title', $event['title'], 'id="title"')
	);
	
	$this->table->add_row(
		form_label(lang('description'), 'description'),
		form_textarea('description', $event['description'], 'id="description"')
	);
	
	$this->table->add_row(
		form_label(lang('start_date'), 'start_date'),
		'<p class="datepair">' . form_input('start_date', $event['start_date'], 'class="date start"') . form_input('start_time', $event['start_time'], 'class="time start"') . '</p>'
	);
	
	$this->table->add_row(
		form_label(lang('end_date'), 'end_date'),
		'<p class="datepair">' . form_input('end_date', $event['end_date'], 'class="date end"') . form_input('end_time', $event['end_time'], 'class="time end"') . '</p>'
	);
	
	$this->table->add_row(
		form_label(lang('timezone'), 'timezone'),
		form_dropdown('timezone', $timezones, $event['timezone'], 'id="timezone"')
	);
	
	$privacy_select = ($event['privacy'] == 'Public') ? 1 : 0;
	$this->table->add_row(
		form_label(lang('privacy'), 'privacy'),
		form_dropdown('privacy', $privacy, $privacy_select)
	);
	
	$this->table->add_row(
		form_label(lang('personalized_url'), 'personalized_url'),
		'<div><a href="' . $event['url'] . '">' . $event['url'] . '</a> - <a href="#" id="url_click">' .lang('edit') . '</a></div><div id="url" style="display:none">' .
		form_input('personalized_url', '', 'id="personalized_url"') . '</div>'
	);

	$venue = (isset($event['venue_id'])) ? $event['venue_id'] : $event['venue']['id'];
	$this->table->add_row(
		form_label(lang('venue_id'), 'venue_id'),
		form_dropdown('venue_id', $venues, $venue)
	);
	
	$organizer = (isset($event['organizer_id'])) ? $event['organizer_id'] : $event['organizer']['id'];
	$this->table->add_row(
		form_label(lang('organizer_id'), 'organizer_id'),
		form_dropdown('organizer_id', $organizers, $organizer)
	);
	
	$this->table->add_row(
		form_label(lang('capacity'), 'capacity'),
		form_input('capacity', $event['capacity'], 'id="capacity"')
	);
	$currency = (isset($event['tickets'][0]['ticket']['currency'])) ? $event['tickets'][0]['ticket']['currency'] : '';
	$this->table->add_row(
		form_label(lang('currency'), 'currency'),
		form_input('currency', $currency, 'id="currency" placeholder="' . lang('create_ticket') . '"')
	);
	
	$status_select = ($event['status'] == 'Draft') ? 'draft' : 'live';
	$this->table->add_row(
		form_label(lang('status'), 'status'),
		form_dropdown('status', $statuses, $event['status'])
	);
	
	$this->table->add_row(
		form_label(lang('custom_header'), 'custom_header'),
		form_textarea('custom_header', $event['custom_header'], 'id="custom_header"')
	);
	
	$this->table->add_row(
		form_label(lang('custom_footer'), 'custom_footer'),
		form_textarea('custom_footer', $event['custom_footer'], 'id="custom_footer"')
	);
	
	$this->table->add_row(
		form_label(lang('confirmation_page'), 'confirmation_page'),
		form_textarea('confirmation_page', $event['confirmation_page'], 'id="confirmation_page"')
	);
	
	$this->table->add_row(
		form_label(lang('confirmation_email'), 'confirmation_email'),
		form_textarea('confirmation_email', $event['confirmation_email'], 'id="confirmation_email"')
	);
	
	$this->table->add_row(
		form_label(lang('background_color'), 'background_color'),
		form_input('background_color', $event['background_color'], 'class="color-picker"')
	);
	
	$this->table->add_row(
		form_label(lang('text_color'), 'text_color'),
		form_input('text_color', $event['text_color'], 'class="color-picker"')
	);
	
	$this->table->add_row(
		form_label(lang('link_color'), 'link_color'),
		form_input('link_color', $event['link_color'], 'class="color-picker"')
	);
	
	$this->table->add_row(
		form_label(lang('title_text_color'), 'title_text_color'),
		form_input('title_text_color', $event['title_text_color'], 'class="color-picker"')
	);
	
	$this->table->add_row(
		form_label(lang('box_background_color'), 'box_background_color'),
		form_input('box_background_color', $event['box_background_color'], 'class="color-picker"')
	);
	
	$this->table->add_row(
		form_label(lang('box_text_color'), 'box_text_color'),
		form_input('box_text_color', $event['box_text_color'], 'class="color-picker"')
	);
	
	$this->table->add_row(
		form_label(lang('box_border_color'), 'box_border_color'),
		form_input('box_border_color', $event['box_border_color'], 'class="color-picker"')
	);
	
	$this->table->add_row(
		form_label(lang('box_header_background_color'), 'box_header_background_color'),
		form_input('box_header_background_color', $event['box_header_background_color'], 'class="color-picker"')
	);
	
	$this->table->add_row(
		form_label(lang('box_header_text_color'), 'box_header_text_color'),
		form_input('box_header_text_color', $event['box_header_text_color'], 'class="color-picker"')
	);

	$res .= $this->table->generate();	

	$res .= form_submit(
		array(
			'name' => 'submit',
			'value' => lang('eventbrite_create'),
			'class' => 'submit'
		)
	);
	$res .= form_close();
	$res .= "<script>
       	$(document).ready(function() {
       		$('#url_click').click(function(e){
       			e.preventDefault();
       			$('#url').slideToggle();
       		});
			$('input.color-picker').focus(function(){
				var input = this;
				$(this).ColorPicker({
					onSubmit: function(hsb, hex, rgb, el) {
						console.log(el);
						$(el).ColorPickerSetColor(hex);
						$(el).ColorPickerHide();
					},
					livePreview: true,
					onChange: function(hsb, hex, rgb){
						$(input).val(hex);
					},
					onBeforeShow: function () {
						$(this).ColorPickerSetColor(this.value);
					}
				}).bind('keyup', function(){
					$(this).ColorPickerSetColor(this.value);
				});
			})
		});</script>";
	echo $res;
?>