<?php
	$res = '';
	
	$res .= '<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=venue" class="submit" style="margin-bottom:20px">'.lang('new_venue').'</a>';
	
	if(isset($venues['no_results'])){
		$res .= '<h2>'.lang('no_venues_setup').'</h2>';
	}
	else{
		$this->table->set_template($cp_table_template);
		$this->table->set_heading(lang('name'), lang('address'), lang('address_2'), lang('city'), lang('postal_code'), lang('region'), lang('country'), lang('country_code'), lang('latitude'), lang('longitude'));
		
		if(isset($venues)){
			foreach($venues as $venue){
				foreach($venue as $row){
					$this->table->add_row(
						'<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=venue'.AMP.'id='.$row['id'].'">' . $row['name'] . '</a>',
						$row['address'],
						$row['address_2'],
						$row['city'],
						$row['postal_code'],
						$row['region'],
						$row['country'],
						$row['country_code'],
						$row['latitude'],
						$row['longitude']
					);
				}
			}
		}
		$res .= $this->table->generate();
	}	
	echo $res;
?>