<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Recipient extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
	}

	public function _recipient_output($output = null)
	{
		$this->load->view('recipient',(array)$output);
	}

	
	public function index()
	{
		try{
			date_default_timezone_set('Asia/Jakarta');

			$crud = new Grocery_CRUD();
						
			$crud->set_theme('bootstrap');
			$crud->set_table('recipient');
			$crud->set_subject('Campaign Recipient');
			$crud->columns('msisdn','id_campaign','id_state');
			$crud->unset_edit();
			$crud->unset_add();
			$crud->unset_delete();
			$crud->unset_read();
			$crud->unset_print();
			$crud->unset_clone();
			$dt = new DateTime();
			$now = $dt->format('Y-m-d');
			//$crud->where('start_date_campaign>=',$now);
			$crud->order_by('recipient.id_state','created_date');
			$crud->set_relation('id_campaign','campaign','{name_campaign}  [{start_date_campaign}:{start_time_campaign}]',null);
			$crud->set_relation('id_state','state_recipient','name_state',null,'id_state asc');
			$crud->display_as('id_state','State');
			$crud->display_as('msisdn','MSISDN');
			$crud->display_as('id_campaign','Campaign Name');
			$crud->set_lang_string('form_update_changes','Update');
			$crud->set_lang_string('form_update_and_go_back','Update & Return');
			$crud->set_lang_string('form_save_and_go_back','Save & Return');
			$crud->set_lang_string('form_upload_delete','Delete');

			$output = $crud->render();
			if($this->session->userdata('id_group')==1)
			{	
				$this->load->view('menu_admin.html');
				$this->_recipient_output($output);
			}	
			else if($this->session->userdata('id_group')==3)
			{	
				$this->load->view('menu_creator.html');
				$this->_recipient_output($output);
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

}
