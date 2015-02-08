<?php echo "<link rel='stylesheet' href='$theme_folder_url/css/layout.css' type='text/css' media='screen' />";?>
<div class="wrapper">
	<div class="dash_stats">
		<?= '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=events"><h2>' . lang('live_events') . '</h2></a>'; ?>
		<span><?= $live_total ?></span>
	</div>
	<div class="dash_stats">
		<?= '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=events"><h2>' . lang('draft_events') . '</h2></a>'; ?>
		<span><?= $draft_total ?></span>
	</div>
	<div class="dash_stats">
		<?= '<a href="'.BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=events"><h2>' . lang('completed_events') . '</h2></a>'; ?>
		<span><?= $completed_total ?></span>
	</div>
	<div class="dash_stats">
		<h2><?= lang('live_events_list'); ?></h2>
		<?php if(isset($live)){
			echo '<table class="mainTable">';
			echo '<tr><th>' . lang('title') . '</th><th>' . lang('start_date') . '</th></tr>';
			foreach($live as $event){
				echo '<tr><td><a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=event'.AMP.'id='.$event['event']['id'].'">' . $event['event']['title'] . '</a></td><td>' . $event['event']['start_date'] . '</td></tr>';
			}
			echo '</table>';
		} else{
			echo '<h2>' . lang('no_live_events') . '</h2>'; 
		} ?>
	</div>
	<div class="dash_stats">
		<h2><?= lang('draft_events_list'); ?></h2>
		<?php if(isset($draft)){
			echo '<table class="mainTable">';
			echo '<tr><th>' . lang('title') . '</th><th>' . lang('start_date') . '</th></tr>';
			foreach($draft as $event){
				echo '<tr><td><a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=event'.AMP.'id='.$event['event']['id'].'">' . $event['event']['title'] . '</a></td><td>' . $event['event']['start_date'] . '</td></tr>';
			}
			echo '</table>';
		} else{
			echo '<h2>' . lang('no_draft_events') . '</h2>';
		} ?>
	</div>
	<div class="dash_stats">
		<h2><?= lang('completed_events_list'); ?></h2>
		<?php if(isset($completed)){
			echo '<table class="mainTable">';
			echo '<tr><th>' . lang('title') . '</th><th>' . lang('start_date') . '</th></tr>';
			foreach($completed as $event){
				echo '<tr><td><a href="' . BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=event'.AMP.'id='.$event['event']['id'].'">' . $event['event']['title'] . '</a></td><td>' . $event['event']['start_date'] . '</td></tr>';
			}
			echo '</table>';
		} else{
			echo '<h2>' . lang('no_completed_events') . '</h2>';
		} ?>
	</div>
</div>