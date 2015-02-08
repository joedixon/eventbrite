<?php
	$res = '';
	
	$res .= '<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=organizer" class="submit" style="margin-bottom:20px">'.lang('new_organizer').'</a>';
	
	if(isset($organizers['no_results'])){
		$res .= '<h2>'.lang('no_organizers_setup').'</h2>';
	}
	else{
		$this->table->set_template($cp_table_template);
		$this->table->set_heading(lang('name'), lang('description'), lang('url'));
	
		if(isset($organizers)){
			foreach($organizers as $organizer){
				foreach($organizer as $row){
					$this->table->add_row(
						'<a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=organizer'.AMP.'id='.$row['id'].'">' . $row['name'] . '</a>',
						$row['description'],
						'<a href="' . $row['url'] . '">' . $row['url'] . '</a>'
					);
				}
			}
		}
		$res .= $this->table->generate();	
	}
		
	echo $res;
?>