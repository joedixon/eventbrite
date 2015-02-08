<?php
	$res = '';

	$this->table->set_template($cp_table_template);

	if(isset($attendees)){
		$this->table->set_heading(lang('name'), lang('email'), lang('created'), lang('order_id'), lang('order_type'), lang('amount_paid'), lang('currency'), lang('quantity'));
		foreach($attendees as $attendee){
			$this->table->add_row($attendee['attendee']['first_name'] . ' ' . $attendee['attendee']['last_name'], $attendee['attendee']['email'], $attendee['attendee']['created'], $attendee['attendee']['order_id'], $attendee['attendee']['order_type'], $attendee['attendee']['amount_paid'], $attendee['attendee']['currency'], $attendee['attendee']['quantity']);
		}
		$res .= $this->table->generate();
	}
	else{
		$res .= '<h2>'.lang('no_attendees').'</h2>';
	}
		
	echo $res;
?>