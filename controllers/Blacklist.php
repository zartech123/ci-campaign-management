<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blacklist extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
	}

	public function _blacklist_output($output = null)
	{
		$this->load->view('blacklist',(array)$output);
	}

	
	public function index()
	{
		try{
			date_default_timezone_set('Asia/Jakarta');

			$crud = new Grocery_CRUD();
						
			$crud->set_theme('bootstrap');
			$crud->set_table('blacklist');
			$crud->set_subject('Campaign Blacklist');
			$crud->columns('msisdn');
			$crud->required_fields('msisdn');
			$crud->fields('msisdn');
			$crud->unset_read();
			$crud->unset_print();
			$crud->unset_clone();
			$crud->display_as('msisdn','MSISDN');
			$crud->set_lang_string('form_update_changes','Update');
			$crud->set_lang_string('form_update_and_go_back','Update & Return');
			$crud->set_lang_string('form_save_and_go_back','Save & Return');
			$crud->set_lang_string('form_upload_delete','Delete');

			$crud->callback_after_update(array($this, 'after_insert'));
			$crud->callback_after_insert(array($this, 'after_insert'));
			
			
			$output = $crud->render();
			if($this->session->userdata('id_group')==1)
			{	
				$this->load->view('menu_admin.html');
				$this->_blacklist_output($output);
			}	
			else if($this->session->userdata('id_group')==4)
			{	
				$this->load->view('menu_cs.html');
				$this->_blacklist_output($output);
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

	function after_insert($post_array,$primary_key)
	{		
		$query = $this->db->query("delete from recipient where msisdn in (select msisdn from blacklist)");
		return true;	 
	}



}
