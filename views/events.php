<?php
	$res = '';
	
	$res .= '<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=event" class="submit" style="margin:0 20px 20px 0">'.lang('new_event').'</a>';
	
	if(isset($no_results)){
		$res .= '<h2>'.lang('no_events_setup').'</h2>';
	}
	else{
		$this->table->set_template($cp_table_template);
		$this->table->set_heading(lang('title'), lang('description'), lang('start_date'), lang('end_date'), lang('privacy'), lang('venue'), lang('organizer'), lang('tickets'), lang('attendees'), lang('status'), lang('duplicate'));
		
		if(isset($events)){
			foreach($events as $row){
				$quantity_available  = $quantity_sold = 0;
				if(isset($row['tickets'])){
					foreach($row['tickets'] as $ticket){
						$quantity_available += $ticket['ticket']['quantity_available'];
						$quantity_sold += $ticket['ticket']['quantity_sold'];
					}
					$quantity_available = '<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=tickets'.AMP.'id='.$row['id'].'">' . $quantity_available . '</a>';
					if($quantity_sold > 0){
						$quantity_sold = '<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=list_attendees'.AMP.'id='.$row['id'].'">' . $quantity_sold . '</a>';
					}
				}
				if($row['status'] == 'Completed'){
					$this->table->add_row(
						$row['title'],
						$row['description'],
						$row['start_date'],
						$row['end_date'],
						$row['privacy'],
						'<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=venue'.AMP.'id='.$row['venue']['id'].'">' . $row['venue']['name'] . '</a>',
						'<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=organizer'.AMP.'id='.$row['organizer']['id'].'">' . $row['organizer']['name'] . '</a>',
						$quantity_available,
						$quantity_sold,
						$row['status'],
						'<a class="duplicate" href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=duplicate'.AMP.'id='.$row['id'].'">' . lang('duplicate') . '</a>'
					);
				}
				else{
					$this->table->add_row(
						'<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=event'.AMP.'id='.$row['id'].'">' . $row['title'] . '</a>',
						$row['description'],
						$row['start_date'],
						$row['end_date'],
						$row['privacy'],
						'<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=venue'.AMP.'id='.$row['venue']['id'].'">' . $row['venue']['name'] . '</a>',
						'<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=organizer'.AMP.'id='.$row['organizer']['id'].'">' . $row['organizer']['name'] . '</a>',
						$quantity_available,
						$quantity_sold,
						$row['status'],
						'<a class="duplicate" href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=duplicate'.AMP.'id='.$row['id'].'">' . lang('duplicate') . '</a>'
					);
				}
			}
		}
		$res .= $this->table->generate();
	}

	echo $res;
?>