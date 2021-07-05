<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Approval extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
		$this->load->library('email');
	}

	public function _approval_output($output = null)
	{
		$this->load->view('approval',(array)$output);
	}

	
	public function index()
	{
		try{
			date_default_timezone_set('Asia/Jakarta');

			$crud = new Grocery_CRUD();
						
			$crud->set_theme('bootstrap');
			$crud->set_table('campaign');
			$crud->set_subject('Manual Campaign');
			$crud->columns('name_campaign','type','total_recipient','start_date_campaign','start_time_campaign','end_date_campaign','end_time_campaign','text','id_state','approved');
			$crud->callback_column('type',array($this,'type'));
			$crud->unset_read();
			$crud->unset_add();
			$crud->unset_edit();
			$crud->unset_print();
			$crud->unset_delete();
			$crud->unset_clone();
			$crud->order_by('id_state, start_date_campaign');
			$crud->where('campaign.id_state>=','2');
			$crud->or_where('campaign.id_state<','5');
			$crud->display_as('approved','Approval');
			$crud->display_as('layer','Number Of Layer');
			$crud->set_relation('id_state','state_campaign','name_state',null,'id_state asc');
			$crud->display_as('name_campaign','Name');
			$crud->display_as('total_recipient','Total Recipient');
			$crud->display_as('end_date_campaign','End Date');
			$crud->display_as('end_time_campaign','End Time');
			$crud->display_as('text','Content');
			$crud->display_as('id_state','Status');
			$crud->display_as('start_date_campaign','Start Date');
			$crud->display_as('start_time_campaign','Start Time');
			$crud->set_lang_string('form_update_changes','Update');
			$crud->set_lang_string('form_update_and_go_back','Update & Return');
			$crud->set_lang_string('form_save_and_go_back','Save & Return');
			$crud->set_lang_string('form_upload_delete','Delete');
			$crud->callback_column('approved',array($this,'approved'));
			$crud->callback_column('text',array($this,'text'));
			
			

				$output = $crud->render();
				if($this->session->userdata('id_group')==1)
				{	
					$this->load->view('menu_admin.html');
					$this->_approval_output($output);
				}
				else if($this->session->userdata('id_group')==2)
				{	
					$this->load->view('menu_approver.html');
					$this->_approval_output($output);
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

	
	public function approved_campaign()
	{
		$query = $this->db->query('SELECT b.id_campaign as id_campaign, name_campaign, type, FORMAT(total_recipient,0) AS total_recipient, start_date_campaign, start_time_campaign, email, name from campaign b, user c where b.created_by = c.id_user and id_campaign='.$_GET['id']);
		$row = $query->row();

		

				if($row->type=="0")
				{	
					$row->type="Manual";
					$updateData=array(id_state=>"4","approved_by"=>$this->session->userdata('id_user'),"approved_date"=>date('Y-m-d H:i:s'));
					$this->db->where("id_campaign",$row->id_campaign);
					$this->db->update('campaign',$updateData);
				}
				else if($row->type=="1")
				{
					$row->type="Automatic";
					$updateData=array(id_state=>"3","approved_by"=>$this->session->userdata('id_user'),"approved_date"=>date('Y-m-d H:i:s'));
					$this->db->where("id_campaign",$row->id_campaign);
					$this->db->update('campaign',$updateData);
				}					

				$email_approver = "";
				$query2 = $this->db->query("select email from user where id_group=2");
				foreach ($query2->result() as $row2)
				{			
					$email_approver = $email_approver.$row2->email.",";
				}
				$email_approver=$email_approver.$row->email;

				$this->email->from('dpwibpushapp@xl.co.id', 'WIBPUSH Administrator');
				$this->email->to($email_approver);
				$this->email->subject('Approval Response for Campaign : '.$row->name_campaign);
				$file=fopen(APP_PATH."assets/approval_response.html", "r") or die("Unable to open file!");
				$content=fread($file,filesize(APP_PATH."assets/approval_response.html"));
				$content_text = htmlentities($content);
				$content_text=str_replace("_admin_email","wibpushapp@xl.co.id",$content_text);
				$content_text=str_replace("_name_campaign",$row->name_campaign,$content_text);
				$content_text=str_replace("_id_campaign",$row->id_campaign,$content_text);
				$content_text=str_replace("_type",$row->type,$content_text);
				$content_text=str_replace("_total_recipient",$row->total_recipient,$content_text);
				$content_text=str_replace("_start_date_campaign",$row->start_date_campaign,$content_text);
				$content_text=str_replace("_start_time_campaign",$row->start_time_campaign,$content_text);
				$content_html=html_entity_decode($content_text);
				$this->email->message($content_html);			
						
				if($this->email->send())
				{	
					$this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
				}	
				else
				{	
					$this->session->set_flashdata("email_sent","You have encountered an error");		
				}		
		
		return "success approved";
		
	}

	public function canceled_campaign()
	{
		$query = $this->db->query('SELECT b.id_campaign as id_campaign, name_campaign, type, FORMAT(total_recipient,0) AS total_recipient, start_date_campaign, start_time_campaign, email, name from campaign b, user c where b.created_by = c.id_user and id_campaign='.$_GET['id']);
		$row = $query->row();

		

					$row->type="Manual";
					$updateData=array(id_state=>"6","approved_by"=>$this->session->userdata('id_user'),"approved_date"=>date('Y-m-d H:i:s'));
					$this->db->where("id_campaign",$row->id_campaign);
					$this->db->update('campaign',$updateData);

				$email_approver = "";
				$query2 = $this->db->query("select email from user where id_group=2");
				foreach ($query2->result() as $row2)
				{			
					$email_approver = $email_approver.$row2->email.",";
				}
				$email_approver=$email_approver.$row->email;

				$this->email->from('dpwibpushapp@xl.co.id', 'WIBPUSH Administrator');
				$this->email->to($email_approver);
				$this->email->subject('Approval Response for Campaign : '.$row->name_campaign);
				$file=fopen(APP_PATH."assets/approval_response.html", "r") or die("Unable to open file!");
				$content=fread($file,filesize(APP_PATH."assets/cancel_response.html"));
				$content_text = htmlentities($content);
				$content_text=str_replace("_admin_email","wibpushapp@xl.co.id",$content_text);
				$content_text=str_replace("_name_campaign",$row->name_campaign,$content_text);
				$content_text=str_replace("_id_campaign",$row->id_campaign,$content_text);
				$content_text=str_replace("_type",$row->type,$content_text);
				$content_text=str_replace("_total_recipient",$row->total_recipient,$content_text);
				$content_text=str_replace("_start_date_campaign",$row->start_date_campaign,$content_text);
				$content_text=str_replace("_start_time_campaign",$row->start_time_campaign,$content_text);
				$content_html=html_entity_decode($content_text);
				$this->email->message($content_html);			
						
				if($this->email->send())
				{	
					$this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
				}	
				else
				{	
					$this->session->set_flashdata("email_sent","You have encountered an error");		
				}		
		
		return "success approved";
		
	}

	
	public function rejected_campaign()
	{
		$query = $this->db->query('SELECT b.id_campaign as id_campaign, name_campaign, type, FORMAT(total_recipient,0) AS total_recipient, start_date_campaign, start_time_campaign, email, name from campaign b, user c where b.created_by = c.id_user and id_campaign='.$_GET['id']);
		$row = $query->row();

		

				if($row->type=="0")
				{	
					$row->type="Manual";
				}
				else if($row->type=="1")
				{
					$row->type="Automatic";
				}	
					
				$updateData=array(id_state=>"8","approved_by"=>$this->session->userdata('id_user'),"approved_date"=>date('Y-m-d H:i:s'));
				$this->db->where("id_campaign",$row->id_campaign);
				$this->db->update('campaign',$updateData);

				$email_approver = "";
				$query2 = $this->db->query("select email from user where id_group=2");
				foreach ($query2->result() as $row2)
				{			
					$email_approver = $email_approver.$row2->email.",";
				}
				$email_approver=$email_approver.$row->email;

				$this->email->from('dpwibpushapp@xl.co.id', 'WIBPUSH Administrator');
				$this->email->to($email_approver);
				$this->email->subject('Rejection Response for Campaign : '.$row->name_campaign);
				$file=fopen(APP_PATH."assets/rejection_response.html", "r") or die("Unable to open file!");
				$content=fread($file,filesize(APP_PATH."assets/rejection_response.html"));
				$content_text = htmlentities($content);
				$content_text=str_replace("_admin_email","wibpushapp@xl.co.id",$content_text);
				$content_text=str_replace("_name_campaign",$row->name_campaign,$content_text);
				$content_text=str_replace("_id_campaign",$row->id_campaign,$content_text);
				$content_text=str_replace("_type",$row->type,$content_text);
				$content_text=str_replace("_total_recipient",$row->total_recipient,$content_text);
				$content_text=str_replace("_start_date_campaign",$row->start_date_campaign,$content_text);
				$content_text=str_replace("_start_time_campaign",$row->start_time_campaign,$content_text);
				$content_html=html_entity_decode($content_text);
				$this->email->message($content_html);			
						
				if($this->email->send())
				{	
					$this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
				}	
				else
				{	
					$this->session->set_flashdata("email_sent","You have encountered an error");		
				}		
				redirect('/Approval');
		
		return "success rejected";
		
	}
	
		public function approved($value, $row)
		{
			$query = $this->db->query("select id_state from campaign where id_campaign='".$row->id_campaign."'");
			$row2 = $query->row();
			$disabled="";
			if($row2->id_state!="2")
			{
				$disabled=" style='visibility:hidden'";
				$disabled2=" style='visibility:show'";
					
			}
			else
			{
				$disabled2=" style='visibility:hidden'";
				$disabled=" style='visibility:show'";
			}				

			$button="<a id='approved' href='javascript:approved($row->id_campaign);' class='btn btn-success' ".$disabled.">Approve</a>";
			$button=$button."<a id='rejected' href='javascript:rejected($row->id_campaign);' class='btn btn-danger' ".$disabled.">&nbsp;Reject&nbsp;&nbsp;</a>";
			$button=$button."<a id='canceled' href='javascript:canceled($row->id_campaign);' class='btn btn-primary' ".$disabled2.">&nbsp;Cancel&nbsp;&nbsp;</a>";
			
			return $button;
			return "";
			
		}

		public function text($value, $row)
		{
			$query = $this->db->query("select concat(if(layer1_text!=\"\",concat('<p><b>Layer-1 (Text)</b> : ',layer1_text,'</p>'),\"\"),if(layer2_text!=\"\",concat('<p><b>Layer-2 (Text)</b> : ',layer2_text,'</p>'),\"\"),if(layer3_text!=\"\",concat('<p><b>Layer-3 (Text)</b> : ',layer3_text,'</p>'),\"\"),if(sti_text!=\"\",concat('<p><b>Save to Inbox : </b>',sti_text,'</p>'),\"\"),if(action_destination!=\"\",concat('<p><b>Action Destination : </b><b>',if(id_action=0,'None',if(id_action=1,'SMS',if(id_action=2,'USSD',if(id_action=3,'Call','Browser')))),'</b> to ',action_destination,'</p>'),\"\"),if(sms_text!=\"\",concat('<p><b>SMS</b> : ',sms_text,'</p>'),\"\")) as text from campaign where id_campaign='".$row->id_campaign."'");
			$row2 = $query->row();

			$button=$row2->text;
			
			return $button;
			
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





	
	






}
