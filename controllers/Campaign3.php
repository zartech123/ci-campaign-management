<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign3 extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
		$this->load->library('email');
	}

	public function _campaign3_output($output = null)
	{
		$this->load->view('campaign3',(array)$output);
	}

	
	public function index()
	{
		try{
			date_default_timezone_set('Asia/Jakarta');

			$crud = new Grocery_CRUD();
						
			$crud->set_theme('bootstrap');
			$crud->set_table('campaign');
			$crud->set_subject('');
			$crud->columns('id_campaign','name_campaign','type','total_recipient','start_date_campaign','start_time_campaign','end_date_campaign','end_time_campaign','id_state');
			$crud->unset_add();
			$crud->unset_edit();
			$crud->unset_export();
			$crud->unset_read();
			$crud->unset_print();
			$crud->unset_delete();
			$crud->unset_clone();
			$dt = new DateTime();
			$now = $dt->format('Y-m-d');
			$crud->order_by('start_date_campaign,start_time_campaign,campaign.id_state');
			$crud->where('start_date_campaign>=',$now);
			$crud->callback_column('name_campaign',array($this,'name_campaign'));
			$crud->field_type('type', 'dropdown',array('0'=>'Manual','1'=>'Automatic'));
			$crud->display_as('id_campaign','ID');
			$crud->display_as('name_campaign','Name');
			$crud->set_relation('id_state','state_campaign','name_state',null,'id_state asc');
			$crud->display_as('total_recipient','Total Recipient');
			$crud->display_as('id_state','Status');
			$crud->display_as('end_date_campaign','End Date');
			$crud->display_as('end_time_campaign','End Time');
			$crud->display_as('start_date_campaign','Start Date');
			$crud->display_as('start_time_campaign','Start Time');
			$crud->set_lang_string('form_update_changes','Update');
			$crud->set_lang_string('form_update_and_go_back','Update & Return');
			$crud->set_lang_string('form_save_and_go_back','Save & Return');
			$crud->set_lang_string('form_upload_delete','Delete');
			
			

				$output = $crud->render();
				if($this->session->userdata('id_group')==1)
				{	
					$this->load->view('menu_admin.html');

					$this->_campaign3_output($output);
				}	
				else if($this->session->userdata('id_group')==2)
				{	
					$this->load->view('menu_approver.html');

					$this->_campaign3_output($output);
				}	
				else if($this->session->userdata('id_group')==3)
				{	
					$this->load->view('menu_creator.html');

					$this->_campaign3_output($output);
				}	
				else if($this->session->userdata('id_group')==5)
				{	
					$this->load->view('menu_view.html');

					$this->_campaign3_output($output);
				}	
				else
				{
					redirect("/Login");
				}


		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	
	//		$this->_example_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	}

	public function name_campaign($value, $row)
	{
		return "<a href=".base_url()."Report4/index?id=".$row->id_campaign.">".$value."</a>";
	}	

	
}
