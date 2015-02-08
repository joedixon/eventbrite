<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// include config file
include(PATH_THIRD.'eventbrite_ee/config.php');


class Eventbrite_ee_mcp {
	
	private $authentication_tokens;

	/**
	 * Shortcut to module URL
	 *
	 * @access private
	 * @var string
	 */
	private $mod_url;
										
	/**
 	 * Array of permissions
 	 * @var array
	 */
	private $privacy;

	/**
	 * Array of event statuses
	 * @var array
	 */
	private $statuses;

	function __construct() {

		$this->mod_url = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module='.EVENTBRITE_PACKAGE;

		$this->privacy = array(
							'0' => 'Private',
							'1' => 'Public'
						);

		$this->statuses = array(
							'draft' => 'Draft',
							'live' => 'Live'
						);

		$this->authentication_tokens = $this->_get_settings();
		$this->authentication_tokens['app_key'] = EVENTBRITE_APP_KEY;

		ee()->load->library('Eventbrite', $this->authentication_tokens);
		ee()->load->library('table');
	}

	//Dashboard
	//List users, organizers, events etc
	function index()
	{
		$this->_nav('eventbrite');
		$events = ee()->eventbrite->user_list_events(array('only_display' => 'id,title,start_date,status'));
		
		$vars['completed_total'] = 0;
		$vars['live_total'] = 0;
		$vars['draft_total'] = 0;
		$vars['theme_folder_url'] = URL_THIRD_THEMES.'eventbrite';
		if(!isset($events['error'])){
			foreach($events as $row){
				foreach($row as $event){
					switch ($event['event']['status']){
						case 'Live':
						$vars['live_total']++;
						if(!isset($vars['live'][2])){
							$vars['live'][] = $event;
						}
						break;

						case 'Started':
						$vars['live_total']++;
						if(!isset($vars['live'][2])){
							$vars['live'][] = $event;
						}
						break;

						case 'Draft':
						$vars['draft_total']++;
						if(!isset($vars['draft'][2])){
							$vars['draft'][] = $event;
						}
						break;

						case 'Completed':
						$vars['completed_total']++;
						if(!isset($vars['completed'][2])){
							$vars['completed'][] = $event;
						}
						break;
					}
				}
			}
		}

		return ee()->load->view('index', $vars, TRUE);
	}

	function settings()
	{
		$user_key = ee()->input->post('user_key');

		// Prep the array
		$vars = array('user_key' => $user_key);

		if(ee()->input->post('submit')){
			ee()->db->empty_table('eventbrite_settings');
			ee()->db->insert('eventbrite_settings', $vars);
			ee()->session->set_flashdata('message_success', lang('eventbrite_settings_updated'));
			ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=settings');
		}

		//Get what is stored in the db
		$result = ee()->db->get('eventbrite_settings');

		//If we have a result
		if($result->num_rows() > 0){
			$vars = $result->row_array();
		}

		//Set the nav
		$this->_nav('settings');

		// Load the view
		return ee()->load->view('settings', $vars, TRUE);

	}	
	
	/**
	 * List all organizers associated with authenticated user
	 * @return string
	 */
	function organizers()
	{	
		//Get the organizer data
		$vars['organizers'] = $this->_get_organizers();

		//Load the navigation
		$this->_nav('eventbrite_organizers');

		//Got the details, lets load the view
		return ee()->load->view('organizers', $vars, TRUE);
	}
	
	/**
	 * CRUD for organizers
	 * @return string
	 */
	function organizer()
	{	
		//Set the initial empty array for the view
		$vars = array(
			'name' => '',
			'description' => ''
		);
		
		//Has the form been submitted?
		if(ee()->input->post('submit')){
			
			//Has the user submitted an name and description?
			if(ee()->input->post('name') && ee()->input->post('description')){
				
				//Is this an existing organizer with id?
				if(ee()->input->get_post('id')){
					
					//If they exist, use EB API to update them
					$update = ee()->eventbrite->organizer_update(array('id' => ee()->input->get('id'), 'name' => ee()->input->post('name'), 'description' => ee()->input->post('description')));
					
					//Have we got errors? Pass to error handler
					if(isset($update['error'])){
						$this->_error_handler($update);
					}
					
					//No errors? Set flashdata and redirect
					else{
						ee()->session->set_flashdata('message_success', lang('eventbrite_organizer_saved'));
						ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=organizers');
					}
				}
				
				//If no ID, we'll create a new one
				else{
					
					//Use EB API to create new organizer
					$new = ee()->eventbrite->organizer_new(array('name' => ee()->input->post('name'), 'description' => ee()->input->post('description')));
					
					//Have we got errors? Pass to error handler
					if(isset($new['error'])){
						$this->_error_handler($new);
					}
					
					//No errors? Set flashdata and redirect
					else{
						ee()->session->set_flashdata('message_success', lang('eventbrite_organizer_saved'));
	ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=organizers');
					}
				}
			}
			
			//No name and description? Throw an error
			else{
				return show_error(lang('eventbrite_missing_name_desc'));
			}
		}
		
		//Form not submitted
		else{
			
			//If the ID has been passed, we can grab the currently saved data
			if(ee()->input->get('id')){
				
				//Use EB API to grab data based on ID
				$organizer = ee()->eventbrite->organizer_get(array('id' => ee()->input->get('id')));
				
				//If we have errors, pass to error handler
				if(isset($organizer['error'])){
					$this->_error_handler($organizer);
				}
				
				//If not, prep data for view file
				else{
					$vars['name'] = $organizer['organizer'][0]['name'];
					$vars['description'] = $organizer['organizer'][0]['description'];
					$vars['id'] = ee()->input->get_post('id');
				}
			}
		}
		//Load the navigation
		$this->_nav('eventbrite_organizer');
		//Finally load the view...
		return ee()->load->view('organizer', $vars, TRUE);
	}
	
	function venues()
	{
		//Get venue data
		$vars['venues'] = $this->_get_venues();

		//Set the navigation
		$this->_nav('eventbrite_venues');
		
		//Got the details, lets load the view
		return ee()->load->view('venues', $vars, TRUE);
	}
	
	function venue()
	{	
		//Set the initial empty array for the view
		$vars = array(
			'name' => '',
			'address' => '',
			'address_2' => '',
			'city' => '',
			'region' => '',
			'postal_code' => '',
			'country_code' => '',
		);
		
		//Has the form been submitted?
		if(ee()->input->post('submit')){
			
			//Is this an existing organizer with id?
			if(ee()->input->get_post('id')){
				
				//If they exist, use EB API to update them
				$update = ee()->eventbrite->venue_update(array('id' => ee()->input->get('id'), 'name' => ee()->input->post('name'), 'address' => ee()->input->post('address'), 'address_2' => ee()->input->post('address_2'), 'city' => ee()->input->post('city'), 'region' => ee()->input->post('region'), 'postal_code' => ee()->input->post('postal_code'), 'country_code' => ee()->input->post('country_code')));
				
				//Have we got errors? Pass to error handler
				if(isset($update['error'])){
					$this->_error_handler($update);
				}
				
				//No errors? Set flashdata and redirect
				else{
					ee()->session->set_flashdata('message_success', lang('eventbrite_venue_saved'));
ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=venues');
				}
			}
			
			//If no ID, we'll create a new one
			else{
				
				//Use EB API to create new venue
				$new = ee()->eventbrite->venue_new(array('organizer_id' => ee()->input->post('organizer_id'), 'name' => ee()->input->post('name'), 'address' => ee()->input->post('address'), 'address_2' => ee()->input->post('address_2'), 'city' => ee()->input->post('city'), 'region' => ee()->input->post('region'), 'postal_code' => ee()->input->post('postal_code'), 'country_code' => ee()->input->post('country_code')));
				
				//Have we got errors? Pass to error handler
				if(isset($new['error'])){
					$this->_error_handler($new);
				}
				
				//No errors? Set flashdata and redirect
				else{
					ee()->session->set_flashdata('message_success', lang('eventbrite_venue_saved'));
ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=venues');
				}
			}
		}
		
		//Form not submitted
		else{
			
			//If the ID has been passed, we can grab the currently saved data
			if(ee()->input->get('id')){
				
				//Use EB API to grab data based on ID
				$venue = ee()->eventbrite->venue_get(array('id' => ee()->input->get('id')));
				
				//If we have errors, pass to error handler
				if(isset($venue['error'])){
					$this->_error_handler($venue);
				}
				
				//If not, prep data for view file
				else{
					$vars = $venue['venue'][0];					
					$vars['id'] = ee()->input->get_post('id');
				}
			}
			else{
				//Organizers
				$vars['organizers'] = $this->_get_organizers_array();
			}
		}

		//Set the nav
		$this->_nav('eventbrite_venue');

		//Finally load the view...
		return ee()->load->view('venue', $vars, TRUE);
	}
	
	function events()
	{
		//Use EB API to list all the venues with current credentials
		$events = ee()->eventbrite->user_list_events();
		
		//If there are no events, let the view file know
		if(isset($events['error'])){
			$this->_error_handler($events);
		}
		
		//If there are venues, prep the array for the view file
		else{
			foreach($events as $event){
				foreach($event as $row){
					$vars['events'][] = $row['event'];
				}
			}
		}
		//Load the navigation
		$this->_nav('eventbrite_events');

		if(!isset($vars)){
			$vars['no_results'] = TRUE;
		}

		//Got the details, lets load the view
		return ee()->load->view('events', $vars, TRUE);
	}
	
	function event()
	{	
		//Set the initial empty array for the view
		$vars['event'] = array(
			'title' => ee()->input->post('title'),
			'description' => ee()->input->post('description'),
			'start_date' => ee()->input->post('start_date') . ' ' . ee()->input->post('start_time'),
			'end_date' => ee()->input->post('end_date') . ' ' . ee()->input->post('end_time'),
			'timezone' => ee()->input->post('timezone'),
			'privacy' => ee()->input->post('privacy'),
			'url' => str_replace('http://', '', ee()->input->post('personalized_url')),
			'venue_id' => ee()->input->post('venue_id'),
			'organizer_id' => ee()->input->post('organizer_id'),
			'capacity' => ee()->input->post('capacity'),
			'currency' => ee()->input->post('currency'),
			'status' => ee()->input->post('status'),
			'custom_header' => ee()->input->post('custom_header'),
			'custom_footer' => ee()->input->post('custom_footer'),
			'confirmation_page' => ee()->input->post('confirmation_page'),
			'confirmation_email' => ee()->input->post('confirmation_email'),
			'background_color' => ee()->input->post('background_color'),
			'text_color' => ee()->input->post('text_color'),
			'link_color' => ee()->input->post('link_color'),
			'title_text_color' => ee()->input->post('title_text_color'),
			'box_background_color' => ee()->input->post('box_background_color'),
			'box_text_color' => ee()->input->post('box_text_color'),
			'box_border_color' => ee()->input->post('box_border_color'),
			'box_header_background_color' => ee()->input->post('box_header_background_color'),
			'box_header_text_color' => ee()->input->post('box_header_text_color'),
			'tickets' => array(
							array(
								'ticket' => array(
									'currency' => ''
								)
							)
			),
		);

		//Has the form been submitted?
		if(ee()->input->post('submit')){
			
			//Is this an existing event with id?
			if(ee()->input->get_post('id')){
				//If it exists, use EB API to update them
				$vars['event']['id'] = ee()->input->get('id');
	
				$update = ee()->eventbrite->event_update($vars['event']);
				
				//Have we got errors? Pass to error handler
				if(isset($update['error'])){
					$this->_error_handler($update);
				}
				
				//No errors? Set flashdata and redirect
				else{
					ee()->session->set_flashdata('message_success', lang('eventbrite_event_saved'));
ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=events');
				}
			}
			
			//If no ID, we'll create a new one
			else{
				
				//Use EB API to create new event
				$new = ee()->eventbrite->event_new($vars['event']);
				
				//Have we got errors? Pass to error handler
				if(isset($new['error'])){
					$this->_error_handler($new);
				}
				
				//No errors? Set flashdata and redirect
				else{
					ee()->session->set_flashdata('message_success', lang('eventbrite_event_saved'));
ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=events');
				}
			}
		}
		
		//Form not submitted
		else{
			
			//If the ID has been passed, we can grab the currently saved data
			if(ee()->input->get('id')){
				
				//Use EB API to grab data based on ID
				$event = ee()->eventbrite->event_get(array('id' => ee()->input->get('id'), 'display' => 'custom_header,custom_footer,confirmation_page,confirmation_email'));
				
				//If we have errors, pass to error handler
				if(isset($event['error'])){
					$this->_error_handler($event);
				}
				
				//If not, prep data for view file
				else{
					$vars['event'] = $event['event'];				
					$vars['id'] = ee()->input->get_post('id');
				}
			}
		}
		//Venues
		$vars['venues'] = $this->_get_venues_array();

		//Organizers
		$vars['organizers'] = $this->_get_organizers_array();
		
		//Privacy
		$vars['privacy'] = $this->privacy;
								
		//Statuses
		$vars['statuses'] = $this->statuses;

		//Currency
		$vars['currencies'] = $this->_get_currency();

		// Timezones
		$vars['timezones'] = $this->_get_timezones();
								
		//Load jquery UI
		ee()->cp->add_js_script(array('ui'	 => array('datepicker')));

		$vars['theme_folder_url'] = URL_THIRD_THEMES.'eventbrite';

		//Split the ISO8601 time into date and time vars for datepicker and timepicker
		$start_date = $this->_split_date($vars['event']['start_date']);
		$vars['event']['start_date'] = $start_date['date'];
		$vars['event']['start_time'] = $start_date['time'];

		$end_date = $this->_split_date($vars['event']['end_date']);
		$vars['event']['end_date'] = $end_date['date'];
		$vars['event']['end_time'] = $end_date['time'];

		//Set the nav
		$this->_nav('eventbrite_event');

		//Finally load the view...
		return ee()->load->view('event', $vars, TRUE);
	}

	function tickets()
	{
		//Do we have an event ID?
		if($id = ee()->input->get('id')){
			//Use the API to get the attendees for the event
			$tickets = ee()->eventbrite->event_get(array('id' => $id, 'only_display' => 'tickets'));
			//If there is any error, lets show it
			if(isset($tickets['error'])){
				$this->_error_handler($tickets);
			}
			//If not, lets pass the attendees to the view file
			else{
				//Set the nav
				$this->_nav('tickets');
				if(!isset($vars)){
					$vars['no_results'] = TRUE;
				}
				return ee()->load->view('tickets', $tickets['event'], TRUE);
			}
		}
		else{
			show_error(lang('not_valid_event'));
		}
	}
	
	function ticket()
	{		
		//Set the initial empty array for the view
		$vars = array(
			'event_id' => ee()->input->get_post('eid'), 
			'is_donation' => ee()->input->post('is_donation'), 
			'name' => ee()->input->post('name'), 
			'description' => ee()->input->post('description'), 
			'price' => ee()->input->post('price'), 
			'quantity_available' => ee()->input->post('quantity_available'), 
			'start_date' => ee()->input->post('start_date') . ' ' . ee()->input->post('start_time'), 
			'end_date' => ee()->input->post('end_date') . ' ' . ee()->input->post('end_time'), 
			'include_fee' => ee()->input->post('include_fee'), 
			'min' => ee()->input->post('min'), 
			'max' => ee()->input->post('max'),
			'type' => ee()->input->post('type')
		);

		//Set some variables
		$id = ee()->input->get('id');
		$eid = ee()->input->get('eid');

		//Has the form been submitted?
		if(ee()->input->post('submit')){
			
			//Is this an existing ticket with id?
			if($id){

				//Set the ID of the ticket
				$vars['id'] = $id;

				//If they exist, use EB API to update them
				$update = ee()->eventbrite->ticket_update($vars);
				
				//Have we got errors? Pass to error handler
				if(isset($update['error'])){
					$this->_error_handler($update);
				}
				
				//No errors? Set flashdata and redirect
				else{
					ee()->session->set_flashdata('message_success', lang('eventbrite_ticket_saved'));
ee()->functions->redirect($_SERVER['HTTP_REFERER']);
				}
			}
			
			//If no ID, we'll create a new one
			else{

				//Use EB API to create new venue
				$new = ee()->eventbrite->ticket_new($vars);
				
				//Have we got errors? Pass to error handler
				if(isset($new['error'])){
					$this->_error_handler($new);
				}
				
				//No errors? Set flashdata and redirect
				else{
					ee()->session->set_flashdata('message_success', lang('eventbrite_ticket_saved'));
ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=events');
				}
			}
		}
		
		//Form not submitted
		else{
			//If the ID has been passed, we can grab the currently saved data
			if($id && $eid){
				
				//Use EB API to grab data based on ID
				$tickets = ee()->eventbrite->event_get(array('id' => ee()->input->get('eid'), 'only_display' => 'tickets'));
				
				//If we have errors, pass to error handler
				if(isset($venue['error'])){
					$this->_error_handler($venue);
				}
				//If not, prep data for view file
				else{
					foreach($tickets['event']['tickets'] as $row){
						if($row['ticket']['id'] == $id){
							$vars = $row['ticket'];
						}
					}
				}
			}
		}
		//Load jquery UI
		ee()->cp->add_js_script(array('ui'	 => array('datepicker')));
		$vars['eid'] = $eid;
		$vars['theme_folder_url'] = URL_THIRD_THEMES.'eventbrite';

		//Split the ISO8601 time into date and time vars for datepicker and timepicker
		$start_date = $this->_split_date($vars['start_date']);
		$vars['start_date'] = $start_date['date'];
		$vars['start_time'] = $start_date['time'];

		$end_date = $this->_split_date($vars['end_date']);
		$vars['end_date'] = $end_date['date'];
		$vars['end_time'] = $end_date['time'];

		//Set the nav
		$this->_nav('ticket');

		//Finally load the view...
		return ee()->load->view('ticket', $vars, TRUE);
	}

	function list_attendees()
	{
		//Do we have an event ID?
		if($id = ee()->input->get('id')){
			//Use the API to get the attendees for the event
			$attendees = ee()->eventbrite->event_list_attendees(array('id' => $id));

			//If there is any error, lets show it
			if(isset($attendees['error'])){
				$this->_error_handler($attendees);
			}
			//If not, lets pass the attendees to the view file
			else{
				//Set the nav
				$this->_nav('attendees');

				return ee()->load->view('list_attendees', $attendees, TRUE);
			}
		}
		else{
			show_error(lang('not_valid_event'));
		}
	}

	function duplicate()
	{
		$vars = array(
			'id' => ee()->input->get('id'),
			'title' => ee()->input->post('title')
		);
	
		if($vars['title'] && $vars['id']){
			$copy = ee()->eventbrite->event_copy($vars);
			if(isset($copy['error'])){
				$this->_error_handler($copy);
			}
			else{
				ee()->session->set_flashdata('message_success', lang('duplication_complete'));
				ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=event'.AMP.'id='.$copy['process']['id']);
			}
		}
		elseif($vars['id']){
			$this->_nav('duplicate');
			return ee()->load->view('duplicate', $vars, TRUE);
		}
		else{
			show_error(lang('not_valid_event'));
		}
	}

	function _get_settings()
	{
		$result = ee()->db->get('eventbrite_settings');
		if($result->num_rows() == 0 && ee()->input->get('method') != 'settings'){
			ee()->session->set_flashdata('message_error', lang('eventbrite_no_settings'));
			ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=settings');
		}
		else{
			return $result->row_array();
		}

	}

	/**
	 * Returns an array of organizer names
	 * @return Array
	 */
	function _get_venues_array()
	{
		

		$venues = ee()->eventbrite->user_list_venues();
		if(isset($venues['error'])){
			$this->_error_handler($venues);
		}
		else{
			foreach($venues as $venue){
				foreach($venue as $row){
					$index = $row['venue']['id'];
					$vars['venues']["$index"] = $row['venue']['name'];
				}
			}
		}
		if(!isset($vars)){
			ee()->session->set_flashdata('message_error', lang('no_venues'));
			ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=venue');
		}
		return $vars['venues'];
	}

	/**
	 * Lists all venues created by authenticated user
	 * @return Array
	 */
	function _get_venues()
	{
		$venues = ee()->eventbrite->user_list_venues();
		//If there are no venues, let the view file know
		if(isset($venues['error'])){
			$this->_error_handler($venues);
		}
		//If there are venues, prep the array for the view file
		else{
			foreach($venues as $venue){
				foreach($venue as $row){
					$vars['venues'][] = $row['venue'];
				}
			}
		}
		if(!isset($vars)){
			$vars['no_results'] = TRUE;
		}
		return $vars;
	}

	/**
	 * Returns an array of organizer names
	 * @return Array
	 */
	function _get_organizers_array()
	{
		$organizers = ee()->eventbrite->user_list_organizers();
		if(isset($organizers['error'])){
			$this->_error_handler($organizers);
		}
		else{
			foreach($organizers as $organizer){
				foreach($organizer as $row){
					$index = $row['organizer']['id'];
					$vars['organizers']["$index"] = $row['organizer']['name'];
				}
			}
		}
		if(!isset($vars)){
			ee()->session->set_flashdata('message_error', lang('no_organizers'));
			ee()->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=eventbrite_ee'.AMP.'method=organizer');
		}
		return $vars['organizers'];
	}

	/**
	 * Lists all organizers for authenticated user
	 * @return Array
	 */
	function _get_organizers()
	{
		$organizers = ee()->eventbrite->user_list_organizers();
		
		//If there are no organizers, let the view file know
		if(isset($organizers['error'])){
			$this->_error_handler($organizers);
		}
		
		//If there are organizers, prep the array for the view file
		else{
			foreach($organizers as $organizer){
				foreach($organizer as $row){
					$vars['organizers'][] = $row['organizer'];
				}
			}
		}
		if(!isset($vars)){
			$vars['no_results'] = TRUE;
		}
		return $vars;
	}
	
	/**
	 * Handles errors received from the EB API
	 * @param  Array $errors
	 * @return function
	 */
	function _error_handler($errors)
	{
		$output = '';
		if(isset($errors['error']['error_type']) && $errors['error']['error_type'] == 'Not Found'){return;}
		foreach($errors as $error){
			$output .= $error['error_message'].'<br />';
		}
		return show_error($output);
	}

	/**
	 * split an ISO8601 date into date and time sections
	 * @param  string $date_time ISO8601 date
	 * @return array date and time
	 */
	function _split_date($date)
	{
		$date = strtotime($date);
		$data = array(
				'date' => date('Y-m-d', $date),
				'time' => date('H:i:s', $date)
			);
		return $data;
	}

	/**
	 * Return control panel navigation
	 * @return void
	 */
	function _nav($title)
	{
		//Set page title
		ee()->cp->set_variable('cp_page_title', lang($title));

		//Set the breadcrumb
		ee()->cp->set_breadcrumb($this->mod_url, lang('eventbrite_ee_module_name'));

		//Navigation
		ee()->cp->set_right_nav(array(
				'eventbrite_dashboard' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'
	                .AMP.'module=eventbrite_ee',
		        'eventbrite_events' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'
	                .AMP.'module=eventbrite_ee'.AMP.'method=events',
		        'eventbrite_venues' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'
	                .AMP.'module=eventbrite_ee'.AMP.'method=venues',
				'eventbrite_organizers' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'
	                .AMP.'module=eventbrite_ee'.AMP.'method=organizers',
	        	'eventbrite_settings' => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'
	                .AMP.'module=eventbrite_ee'.AMP.'method=settings'
		));
	}

	/**
	 * returns a list of timezones
	 * @return array
	 */
	function _get_timezones(){
		$timezones = array();
		$tz = timezone_identifiers_list();
		foreach($tz as $row){
			$timezones[$row] = $row;
		}
		return $timezones;
	}

	function _get_currency(){
		return array(
		'AED' => 'United Arab Emirates Dirham',
		'AFN' => 	'Afghanistan Afghani',
		'ALL' => 	'Albania Lek',
		'AMD' => 	'Armenia Dram',
		'ANG' => 	'Netherlands Antilles Guilder',
		'AOA' => 	'Angola Kwanza',
		'ARS' => 	'Argentina Peso',
		'AUD' => 	'Australia Dollar',
		'AWG' => 	'Aruba Guilder',
		'AZN' => 	'Azerbaijan New Manat',
		'BAM' => 	'Bosnia and Herzegovina Convertible Marka',
		'BBD' => 	'Barbados Dollar',
		'BDT' => 	'Bangladesh Taka',
		'BGN' => 	'Bulgaria Lev',
		'BHD' => 	'Bahrain Dinar',
		'BIF' => 	'Burundi Franc',
		'BMD' => 	'Bermuda Dollar',
		'BND' => 	'Brunei Darussalam Dollar',
		'BOB' => 	'Bolivia Boliviano',
		'BRL' => 	'Brazil Real',
		'BSD' => 	'Bahamas Dollar',
		'BTN' => 	'Bhutan Ngultrum',
		'BWP' => 	'Botswana Pula',
		'BYR' => 	'Belarus Ruble',
		'BZD' => 	'Belize Dollar',
		'CAD' => 	'Canada Dollar',
		'CDF' => 	'Congo/Kinshasa Franc',
		'CHF' => 	'Switzerland Franc',
		'CLP' => 	'Chile Peso',
		'CNY' => 	'China Yuan Renminbi',
		'COP' => 	'Colombia Peso',
		'CRC' => 	'Costa Rica Colon',
		'CUC' => 	'Cuba Convertible Peso',
		'CUP' => 	'Cuba Peso',
		'CVE' => 	'Cape Verde Escudo',
		'CZK' => 	'Czech Republic Koruna',
		'DJF' => 	'Djibouti Franc',
		'DKK' => 	'Denmark Krone',
		'DOP' => 	'Dominican Republic Peso',
		'DZD' => 	'Algeria Dinar',
		'EGP' => 	'Egypt Pound',
		'ERN' => 	'Eritrea Nakfa',
		'ETB' => 	'Ethiopia Birr',
		'EUR' => 	'Euro Member Countries',
		'FJD' => 	'Fiji Dollar',
		'FKP' => 	'Falkland Islands (Malvinas) Pound',
		'GBP' => 	'United Kingdom Pound',
		'GEL' => 	'Georgia Lari',
		'GGP' => 	'Guernsey Pound',
		'GHS' => 	'Ghana Cedi',
		'GIP' => 	'Gibraltar Pound',
		'GMD' => 	'Gambia Dalasi',
		'GNF' => 	'Guinea Franc',
		'GTQ' => 	'Guatemala Quetzal',
		'GYD' => 	'Guyana Dollar',
		'HKD' => 	'Hong Kong Dollar',
		'HNL' => 	'Honduras Lempira',
		'HRK' => 	'Croatia Kuna',
		'HTG' => 	'Haiti Gourde',
		'HUF' => 	'Hungary Forint',
		'IDR' => 	'Indonesia Rupiah',
		'ILS' => 	'Israel Shekel',
		'IMP' => 	'Isle of Man Pound',
		'INR' => 	'India Rupee',
		'IQD' => 	'Iraq Dinar',
		'IRR' => 	'Iran Rial',
		'ISK' => 	'Iceland Krona',
		'JEP' => 	'Jersey Pound',
		'JMD' => 	'Jamaica Dollar',
		'JOD' => 	'Jordan Dinar',
		'JPY' => 	'Japan Yen',
		'KES' => 	'Kenya Shilling',
		'KGS' => 	'Kyrgyzstan Som',
		'KHR' => 	'Cambodia Riel',
		'KMF' => 	'Comoros Franc',
		'KPW' => 	'Korea (North) Won',
		'KRW' => 	'Korea (South) Won',
		'KWD' => 	'Kuwait Dinar',
		'KYD' => 	'Cayman Islands Dollar',
		'KZT' => 	'Kazakhstan Tenge',
		'LAK' => 	'Laos Kip',
		'LBP' => 	'Lebanon Pound',
		'LKR' => 	'Sri Lanka Rupee',
		'LRD' => 	'Liberia Dollar',
		'LSL' => 	'Lesotho Loti',
		'LTL' => 	'Lithuania Litas',
		'LVL' => 	'Latvia Lat',
		'LYD' => 	'Libya Dinar',
		'MAD' => 	'Morocco Dirham',
		'MDL' => 	'Moldova Leu',
		'MGA' => 	'Madagascar Ariary',
		'MKD' => 	'Macedonia Denar',
		'MMK' => 	'Myanmar (Burma) Kyat',
		'MNT' => 	'Mongolia Tughrik',
		'MOP' => 	'Macau Pataca',
		'MRO' => 	'Mauritania Ouguiya',
		'MUR' => 	'Mauritius Rupee',
		'MVR' => 	'Maldives (Maldive Islands) Rufiyaa',
		'MWK' => 	'Malawi Kwacha',
		'MXN' => 	'Mexico Peso',
		'MYR' => 	'Malaysia Ringgit',
		'MZN' => 	'Mozambique Metical',
		'NAD' => 	'Namibia Dollar',
		'NGN' => 	'Nigeria Naira',
		'NIO' => 	'Nicaragua Cordoba',
		'NOK' => 	'Norway Krone',
		'NPR' => 	'Nepal Rupee',
		'NZD' => 	'New Zealand Dollar',
		'OMR' => 	'Oman Rial',
		'PAB' => 	'Panama Balboa',
		'PEN' => 	'Peru Nuevo Sol',
		'PGK' => 	'Papua New Guinea Kina',
		'PHP' => 	'Philippines Peso',
		'PKR' => 	'Pakistan Rupee',
		'PLN' => 	'Poland Zloty',
		'PYG' => 	'Paraguay Guarani',
		'QAR' => 	'Qatar Riyal',
		'RON' => 	'Romania New Leu',
		'RSD' => 	'Serbia Dinar',
		'RUB' => 	'Russia Ruble',
		'RWF' => 	'Rwanda Franc',
		'SAR' => 	'Saudi Arabia Riyal',
		'SBD' => 	'Solomon Islands Dollar',
		'SCR' => 	'Seychelles Rupee',
		'SDG' => 	'Sudan Pound',
		'SEK' => 	'Sweden Krona',
		'SGD' => 	'Singapore Dollar',
		'SHP' => 	'Saint Helena Pound',
		'SLL' => 	'Sierra Leone Leone',
		'SOS' => 	'Somalia Shilling',
		'SPL' =>    'Seborga Luigino',
		'SRD' => 	'Suriname Dollar',
		'STD' => 	'São Tomé and Príncipe Dobra',
		'SVC' => 	'El Salvador Colon',
		'SYP' => 	'Syria Pound',
		'SZL' => 	'Swaziland Lilangeni',
		'THB' => 	'Thailand Baht',
		'TJS' => 	'Tajikistan Somoni',
		'TMT' => 	'Turkmenistan Manat',
		'TND' => 	'Tunisia Dinar',
		'TOP' => 	"Tonga Pa'anga",
		'TRY' => 	'Turkey Lira',
		'TTD' => 	'Trinidad and Tobago Dollar',
		'TVD' => 	'Tuvalu Dollar',
		'TWD' => 	'Taiwan New Dollar',
		'TZS' => 	'Tanzania Shilling',
		'UAH' => 	'Ukraine Hryvna',
		'UGX' => 	'Uganda Shilling',
		'USD' => 	'United States Dollar',
		'UYU' => 	'Uruguay Peso',
		'UZS' => 	'Uzbekistan Som',
		'VEF' => 	'Venezuela Bolivar',
		'VND' => 	'Viet Nam Dong',
		'VUV' => 	'Vanuatu Vatu',
		'WST' => 	'Samoa Tala',
		'XAF' => 	'Communauté Financière Africaine (BEAC) CFA Franc BEAC',
		'XCD' => 	'East Caribbean Dollar',
		'XDR' => 	'International Monetary Fund (IMF) Special Drawing Rights',
		'XOF' => 	'Communauté Financière Africaine (BCEAO) Franc',
		'XPF' => 	'Comptoirs Français du Pacifique (CFP) Franc',
		'YER' => 	'Yemen Rial',
		'ZAR' => 	'South Africa Rand',
		'ZMW' => 	'Zambia Kwacha',
		'ZWD' => 	'Zimbabwe Dollar'
		);
	}
}
// END CLASS

/* End of file mcp.eventbrite.php */
/* Location: ./system/expressionengine/third_party/eventbrite/mcp.eventbrite.php */
?>