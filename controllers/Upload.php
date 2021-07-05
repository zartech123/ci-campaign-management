<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
	}

	public function _upload_output($output = null)
	{
		$this->load->view('upload',(array)$output);
	}

	
	public function index()
	{
		try{
			date_default_timezone_set('Asia/Jakarta');

			$crud = new Grocery_CRUD();
						
			$crud->set_theme('bootstrap');
			$crud->set_table('file');
			$crud->set_subject('Campaign Recipient');
			$crud->columns('id_campaign','name_file','rows_file');
			$crud->unset_edit();
			$crud->unset_add();
			$crud->unset_delete();
			$crud->unset_read();
			$crud->unset_print();
			$crud->unset_clone();
			$crud->display_as('rows_file','Number of Row');
			$crud->display_as('name_file','File Name');
			$crud->display_as('id_campaign','Campaign Name');
			$crud->set_lang_string('form_update_changes','Update');
			$crud->set_lang_string('form_update_and_go_back','Update & Return');
			$crud->set_lang_string('form_save_and_go_back','Save & Return');
			$crud->set_lang_string('form_upload_delete','Delete');
			$crud->callback_column('extract',array($this,'extract_data'));

			$output = $crud->render();
			if($this->session->userdata('id_group')==1)
			{	
				$this->load->view('menu_admin.html');
				$this->_upload_output($output);
			}	
			else if($this->session->userdata('id_group')==3)
			{	
				$this->load->view('menu_creator.html');
				$this->_upload_output($output);
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
