<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
		$this->load->library('email');
	}

	public function _user_output($output = null)
	{
		$this->load->view('user',(array)$output);
	}

	
	public function index()
	{
		try{
			date_default_timezone_set('Asia/Jakarta');

			$crud = new Grocery_CRUD();
						
			$crud->set_theme('bootstrap');
			$crud->set_table('user');
			$crud->set_subject('User');
			$crud->columns('photo','user_name','email','name','id_group','active');
			$crud->required_fields('name','email','user_name','id_group');
			if($this->session->userdata('id_group')!=1)
			{
				$crud->unset_add();
			}				
			$crud->unset_read();
			$crud->unset_print();
			$crud->unset_delete();
			$crud->unset_clone();
			if($this->session->userdata('id_group')!=1)
			{
				$crud->where('id_user',$this->session->userdata('id_user'));
			}				
			$crud->set_field_upload('photo', 'assets/uploads');
			$crud->field_type('active', 'dropdown',array('1'=>'Active','0'=>'Non Active'));
			$crud->add_fields('user_name','email','name','photo','id_group');
			if($this->session->userdata('id_group')==1)
			{
				$crud->edit_fields('user_name','email','name','photo','id_group','active','new_password1','new_password2','password');
				$crud->field_type('password', 'hidden','');
				$crud->field_type('new_password1', 'password');
				$crud->field_type('new_password2', 'password');
			}	
			else
			{
				$crud->edit_fields('user_name','email','name','photo','old_password','new_password1','new_password2','password');
				$crud->field_type('password', 'hidden','');
				$crud->field_type('old_password', 'password');
				$crud->field_type('new_password1', 'password');
				$crud->field_type('new_password2', 'password');
			}	
			$crud->unique_fields('user_name');
			$crud->set_relation('id_group','groups','description',null,'id');
			$crud->display_as('id_group','User Group');
			$crud->display_as('user_name','User Name');
			$crud->display_as('active','Status');
			$crud->display_as('name','Nama');
			$crud->display_as('old_password','Old Password');
			$crud->display_as('new_password1','New Password');
			$crud->display_as('new_password2','New Password (Confirmation)');
			$crud->set_lang_string('form_update_changes','Update');
			$crud->set_lang_string('form_update_and_go_back','Update & Return');
			$crud->set_lang_string('form_save_and_go_back','Save & Return');
			$crud->set_lang_string('form_upload_delete','Delete');
			$crud->set_rules('email','Email','callback_checkEmail|required');
			$crud->callback_before_update(array($this, 'before_update'));
			$crud->callback_before_upload(array($this, 'valid_images'));
			$crud->callback_edit_field('email',array($this,'email_edit'));
			$crud->callback_edit_field('user_name',array($this,'user_name_edit'));
			$crud->callback_after_insert(array($this,'after_insert'));
			$crud->callback_after_update(array($this,'after_update'));
			$crud->set_rules('new_password1','New Password','callback_new_password1');
			$crud->set_rules('user_name','User Name','callback_check_user_name|required');
			


			$output = $crud->render();
			if($this->session->userdata('id_group')==1)
			{	
				$this->load->view('menu_admin.html');
				$this->_user_output($output);
			}	
			else if($this->session->userdata('id_group')==2)
			{	
				$this->load->view('menu_approver.html');
				$this->_user_output($output);
			}	
			else if($this->session->userdata('id_group')==3)
			{	
				$this->load->view('menu_creator.html');
				$this->_user_output($output);
			}	
			else if($this->session->userdata('id_group')==4)
			{	
				$this->load->view('menu_cs.html');
				$this->_user_output($output);
			}	
			else if($this->session->userdata('id_group')==5)
			{	
				$this->load->view('menu_view.html');
				$this->_user_output($output);
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
		
		$length = 8;
		$randomletter = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, $length);
		$this->email->from('dpwibpushapp@xl.co.id', 'WIBPUSH Administrator');
		$this->email->to($post_array['email']);
		$this->email->subject('Your WIBPUSH Account have been Active');
		$file=fopen(APP_PATH."assets/email_activation.html", "r") or die("Unable to open file!");
		$content=fread($file,filesize(APP_PATH."assets/email_activation.html"));
		$content_text = htmlentities($content);
		$content_text=str_replace("_url",base_url()."Login",$content_text);
		$content_text=str_replace("_email",$post_array['user_name'],$content_text);
		$content_text=str_replace("_password",$randomletter,$content_text);
		$content_html=html_entity_decode($content_text);
		$this->email->message($content_html);			
		$query = $this->db->query("update user set password='".md5($randomletter)."',active=1 where id_user = '".$primary_key."'");
					
		if($this->email->send())
		{	
			$this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
		}	
		else
		{	
			$this->session->set_flashdata("email_sent","You have encountered an error");		
		}		

		return true;
		
	}
	
	function check_user_name($post_array) 
	{		
		if(preg_match('/^[a-z0-9_-]{8,20}$/',$_POST['user_name'])) 
		{
     		return TRUE;
     	}
     	else
     	{
	    	$this->form_validation->set_message('check_user_name', 'Please check your User Name format');
	    	return FALSE;
     	}	
    }

	function new_password1($post_array) 
	{		
		if(isset($_POST['old_password']))
		{	
			if($_POST['new_password1']!="" || $_POST['new_password2']!="" || $_POST['old_password']!="")
			{		
				if(trim($_POST['new_password1'])=="" && trim($_POST['new_password1'])=="")
				{
					$this->form_validation->set_message('new_password1', 'Your New Password and Confirmation is blank');
					return FALSE;
				}	
				else
				{
					if(trim($_POST['new_password1'])!=trim($_POST['new_password2']))
					{
						$this->form_validation->set_message('new_password1', 'Your New Password Confirmation is not match');
						return FALSE;
					}				
					else
					{
						$i=0;
						$query = $this->db->query("SELECT password from user where id_user='".$_POST['id_user']."'");
						foreach ($query->result() as $row2)
						{
							if(trim($row2->password)==md5(trim($_POST['old_password'])))		
							{	
								$this->form_validation->set_message('new_password1', 'Your Old Password is not match');
								return FALSE;
								$i=$i+1;	
							}	
						}
						if(trim($_POST['old_password'])==trim($_POST['new_password1']))
						{
							$this->form_validation->set_message('new_password1', 'Your New Password same with Old Password');
							return FALSE;
						}						
						else
						{
							return TRUE;
						}						
					}				
				}				
			}			
			else
			{
				return TRUE;
			}
		}
		else
		{
			if($_POST['new_password1']!="" || $_POST['new_password2']!="")
			{		
				if(trim($_POST['new_password1'])=="" && trim($_POST['new_password1'])=="")
				{
					$this->form_validation->set_message('new_password1', 'Your New Password and Confirmation is blank');
					return FALSE;
				}	
				else
				{
					if(trim($_POST['new_password1'])!=trim($_POST['new_password2']))
					{
						$this->form_validation->set_message('new_password1', 'Your New Password Confirmation is not match');
						return FALSE;
					}				
					else
					{
						return TRUE;
					}				
				}				
			}			
			else
			{
				return TRUE;
			}
		}			
	}
	
	
	function checkEmail($post_array) 
	{		
		if(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/',$_POST['email'])) 
		{
     		return TRUE;
     	}
     	else
     	{
	    	$this->form_validation->set_message('checkEmail', 'Please check your email format');
	    	return FALSE;
     	}	
    }

	function after_update($post_array, $primary_key)
	{
		if(isset($post_array['password']))
		{	
			$this->email->from('dpwibpushapp@xl.co.id', 'WIBPUSH Administrator');
			$this->email->to($post_array['email']);
			$this->email->subject('Your WIBPUSH Account Password have been Changed');
			$file=fopen(APP_PATH."assets/change_password.html", "r") or die("Unable to open file!");
			$content=fread($file,filesize(APP_PATH."assets/change_password.html"));
			$content_text = htmlentities($content);
			$content_text=str_replace("_url",base_url()."Login",$content_text);
			$content_text=str_replace("_email",$post_array['user_name'],$content_text);
			$content_text=str_replace("_password",$post_array['password'],$content_text);
			$content_html=html_entity_decode($content_text);
			$query = $this->db->query("update user set password=md5(password) where id_user = '".$primary_key."'");
			$this->email->message($content_html);			
							
			if($this->email->send())
			{	
				$this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
			}	
			else
			{	
				$this->session->set_flashdata("email_sent","You have encountered an error");		
			}
		}	
	}
	
	function before_update($post_array, $primary_key) 
	{
		if($post_array['new_password1']!="")
		{
			$post_array['password']=$post_array['new_password1'];
		}			
		else
		{
			unset($post_array['password']);
		}			
			
		if(isset($post_array['old_password']))	unset($post_array['old_password']);
		unset($post_array['new_password1']);
		unset($post_array['new_password2']);
	
		return $post_array;
	}

	public function valid_images($files_to_upload, $field_info)
	{
		$type=$files_to_upload[$field_info->encrypted_field_name]['type'];
	  if ($type!= 'image/png' && $type!= 'image/jpeg' && $type!= 'image/jpg' && $type!= 'image/gif')
	  {
	   	return 'You can upload only Images File';
	  }
	  return true;
	}
	
	function email_edit($value, $primary_key)
	{
		return '<input id="field-email" class="form-control" name="email" type="text" value="'.$value.'" readonly>';
	}

	function user_name_edit($value, $primary_key)
	{
		return '<input id="field-user_name" class="form-control" name="user_name" type="text" value="'.$value.'" readonly>';
	}

}
