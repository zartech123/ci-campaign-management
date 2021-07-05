<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign4 extends CI_Controller {

	private $menu = "";
	public function __construct()
	{
		parent::__construct();
		

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
	}

	public function _campaign4_output($output = null)
	{
		$this->load->view('campaign4',(array)$output);
	}

	
	public function index()
	{
		try{
			date_default_timezone_set('Asia/Jakarta');

			$crud = new Grocery_CRUD();
			
			$this->menu="";
			if(isset($_GET['menu']))
			{	
				$this->menu = $_GET['menu'];
			}	
			$crud->set_subject('Campaign Report');
			$crud->set_theme('bootstrap');
			$crud->set_table('campaign');
			$crud->set_subject('Campaign Report');
			$crud->columns('name_campaign','type','total_recipient','start_date_campaign','start_time_campaign','end_date_campaign','end_time_campaign','text','id_state');
			$crud->callback_column('type',array($this,'type'));
			$crud->unset_edit();
			$crud->unset_export();
			$crud->unset_add();
			$crud->unset_read();
			$crud->unset_print();
			$crud->unset_delete();
			$crud->unset_clone();
			if(isset($_GET['id_state']))
			{	
				$crud->where('campaign.id_state',$_GET['id_state']);
			}	
			if(isset($_GET['id_campaign']))
			{	
				$id_campaign=$_GET['id_campaign'];
				$id_campaign=rtrim($id_campaign,"|");
				$id_campaign2 = explode("|",$id_campaign);
				$i=0;
				foreach ($id_campaign2 as $key => $value)
				{
					if($i==0)	
					{	
						$crud->where('campaign.id_campaign',$value);
					}	
					else
					{
						$crud->or_where('campaign.id_campaign',$value);
					}						
					$i++;
				}		
			}
			$crud->order_by('campaign.id_state,start_date_campaign,start_time_campaign');
			$crud->display_as('layer','Number Of Layer');
			$crud->display_as('name_campaign','Name');
			$crud->display_as('text','Content');
			$crud->display_as('tone_duration','Tone Duration (s)');
			$crud->display_as('id_action','Action (Final)');
            $crud->display_as('action_destination','Destination');
			$crud->display_as('sms_text','SMS');
			$crud->display_as('sti','Save to Inbox ?');
			$crud->display_as('sti_text','Save to Inbox (SMS)');
			$crud->display_as('layer1_text','Layer-1 (Text)');
			$crud->display_as('layer2_text','Layer-2 (Text)');
			$crud->display_as('layer3_text','Layer-3 (Text)');
			$crud->set_relation('id_action','action','name_action',null,'id_action asc');
			$crud->set_relation('id_state','state_campaign','name_state',null,'id_state asc');
			$crud->display_as('total_recipient','Total Recipient');
			$crud->display_as('id_state','Status');
			$crud->display_as('end_date_campaign','End Date');
			$crud->display_as('end_time_campaign','End Time');
			$crud->display_as('start_date_campaign','Start Date');
			$crud->display_as('start_time_campaign','Start Time');
			$crud->callback_column('text',array($this,'text'));
			$crud->set_lang_string('form_update_changes','Update');
			$crud->set_lang_string('form_update_and_go_back','Update & Return');
			$crud->set_lang_string('form_save_and_go_back','Save & Return');
			$crud->set_lang_string('form_upload_delete','Delete');
						
			

				$output = $crud->render();
				if($this->session->userdata('id_group')==1)
				{	
					if($this->menu!="0")	
					{	
						$this->load->view('menu_admin.html');
					}
					else
					{
						$this->load->view('load.html');
					}

					$this->_campaign4_output($output);
				}	
				else if($this->session->userdata('id_group')==3)
				{	
					if($this->menu!="0")	
					{	
						$this->load->view('menu_creator.html');
					}
					else
					{
						$this->load->view('load.html');
					}

					$this->_campaign4_output($output);
				}	
				else if($this->session->userdata('id_group')==5)
				{	
					if($this->menu!="0")	
					{	
						$this->load->view('menu_view.html');
					}
					else
					{
						$this->load->view('load.html');
					}

					$this->_campaign4_output($output);
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


	public function type($value, $row)
	{
		if($value==0)
		{	
			return "Manual";
		}
		else if($value==1)
		{
			return "Automatic";
		}			
	}	


	public function text($value, $row)
	{
		$query = $this->db->query("select concat(if(layer1_text!=\"\" and layer>=1,concat('<p><b>Layer-1 (Text)</b> : ',layer1_text,'</p>'),\"\"),if(layer2_text!=\"\"  and layer>=2,concat('<p><b>Layer-2 (Text)</b> : ',layer2_text,'</p>'),\"\"),if(layer3_text!=\"\" and layer=3,concat('<p><b>Layer-3 (Text)</b> : ',layer3_text,'</p>'),\"\"),if(sti_text!=\"\" and sti=1,concat('<p><b>Save to Inbox : </b>',sti_text,'</p>'),\"\"),if(action_destination!=\"\",concat('<p><b>Action Destination : </b><b>',if(id_action=0,'None',if(id_action=1,concat('SMS to ',action_destination),if(id_action=2,concat('USSD to ',action_destination),if(id_action=3,'Call to ',concat('Browser to ',action_destination))))),'</b></p>'),\"\"),if(id_action=1,concat('<p><b>SMS</b> : ',sms_text,'</p>'),\"\")) as text from campaign where id_campaign='".$row->id_campaign."'");
		$row2 = $query->row();

		$button=$row2->text;
			
		return $button;
			
	}
	
	
}
