<?php
	$res = '';

	$this->table->set_template($cp_table_template);
	$res .= '<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=ticket'.AMP.'eid='.$id.'" class="submit" style="margin-bottom:20px">'.lang('new_ticket').'</a>';
	
	if(isset($tickets)){
		$this->table->set_heading(lang('name'), lang('description'), lang('currency'), lang('price'), lang('quantity_available'));
		foreach($tickets as $ticket){
			$ticket_price = (isset($ticket['ticket']['price'])) ? $ticket['ticket']['price'] : lang('donation');
			$this->table->add_row('<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=ticket'.AMP.'id='.$ticket['ticket']['id'].AMP.'eid='.ee()->input->get('id').'">' . $ticket['ticket']['name'] . '</a>', $ticket['ticket']['description'], $ticket['ticket']['currency'], $ticket_price, $ticket['ticket']['quantity_available']);
		}
		$res .= $this->table->generate();
	}
	else{
		$res .= '<h2>'.lang('no_tickets').'</h2>';
	}
		
	echo $res;
?>