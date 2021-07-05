<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');
		$this->load->helper('form');

		$this->load->library('session');
		$this->load->library('email');
		
	}

	public function getFirstTime()
	{
		$first = "";
		$query = $this->db->query("SELECT is_change FROM user WHERE user_name='".$_GET['id']."'");
		foreach ($query->result() as $row2)
		{			
			$first = $row2->is_change;
		}
		echo $first;
	}

	public function login()
	{
		$email = $this->input->post('username');
		$password = $this->input->post('password');
		$password = md5($password);
		
		$this->db->where("user_name",$email);
		$this->db->where("password",$password);
		$this->db->where("active","1");
		$query = $this->db->get("user");

		if ($query->num_rows() == 1) 
		{
			$id_user = $query->row()->id_user;
			$id_group = $query->row()->id_group;
			$name = $query->row()->name;
				
			$this->session->set_userdata('id_user',$id_user);
			$this->session->set_userdata('id_group',$id_group);
			$this->session->set_userdata('name',$name);
			if($id_group==4)
			{	
				redirect('/Blacklist');					
			}	
			else
			{	
				redirect('/Campaign3');					
			}
		}
		else
		{					
			$data['error']= "Username or Password is not match";
			$this->load->view('login.php', $data);
		}	
		
	}
	
	public function index()
	{
		try{
			date_default_timezone_set('Asia/Jakarta');

			if($this->session->userdata('id_group')==1)
			{			
				$this->load->view('menu_admin.html');			
			}
			else if($this->session->userdata('id_group')==2)
			{				
				$this->load->view('menu_approver.html');			
			}
			else if($this->session->userdata('id_group')==3)
			{				
				$this->load->view('menu_creator.html');			
			}
			else if($this->session->userdata('id_group')==4)
			{				
				$this->load->view('menu_cs.html');			
			}
			else if($this->session->userdata('id_group')==5)
			{				
				$this->load->view('menu_view.html');			
			}
			else
			{			
				$this->load->view('login');
			}	


		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	
	//		$this->_example_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	}

	public function logout()
	{
		$this->session->unset_userdata(array('id_user', 'id_group','name'));		
		$this->session->sess_destroy();
		redirect('/Login');
	}

	public function forgot()
	{
		$this->load->view('forgot.php');
	}

	public function change()
	{
		if(isset($_GET['key']))
		{	
			$email=$_GET['key'];
			$this->db->where("md5(user_name)",$email);
			$this->db->where("active","0");
			$query = $this->db->get("user");
			
			if ($query->num_rows() == 1) 
			{
				$data['key']=$email;
				$this->load->view('change.php',$data);			
			}
			else
			{
				$data['error']= "Your Link is no longer valid";
				$this->load->view('info.php', $data);
			}			
		}
		else
		{
			redirect('/Login');
		}			
	}

	public function first_login()
	{
		if(isset($_GET['key']))
		{	
			$data['key']=$_GET['key'];
			$this->load->view('first_login.php',$data);			
		}
		else
		{
			redirect('/Login');
		}			
	}

	public function activate()
	{
		if(isset($_GET['key']))
		{	
			$email=$_GET['key'];
			$this->db->where("md5(user_name)",$email);
			$this->db->where("active","0");
			$query = $this->db->get("user");
			
			if ($query->num_rows() == 1) 
			{
				$data['key']=$email;
				$this->load->view('activate.php',$data);			
			}
			else
			{
				$data['error']= "Your Link is no longer valid";
				$this->load->view('info.php', $data);
			}			
		}
		else
		{
			redirect('/Login');
		}			
	}
	
	
	public function forgotpassword()
	{				
		$email = $this->input->post('username');
		
		$this->db->where("user_name",$email);
		$this->db->where("active","1");
		$query = $this->db->get("user");

		if ($query->num_rows() == 1) 
		{
			foreach ($query->result() as $row)				
			{
				$email2 = $row->email;
			}
				
			$length = 5;
			$randomletter = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, $length);
			$this->email->from('dpwibpushapp@xl.co.id', 'WIBPUSH Administrator');
			$this->email->to($email2);
			$this->email->subject('Forgot Your Password ?');
			$file=fopen(APP_PATH."assets/email_forgot.html", "r") or die("Unable to open file!");
			$this->db->set('active',"0");
			$this->db->set('code',$randomletter);
			$this->db->where("user_name",$email);
			$this->db->where("active","1");
			$this->db->update('user');
			$content=fread($file,filesize(APP_PATH."assets/email_forgot.html"));
			$content_text = htmlentities($content);
			$content_text=str_replace("_url",base_url()."Login/change?key=".md5($email),$content_text);
			$content_text=str_replace("_code",$randomletter,$content_text);
			$content_html=html_entity_decode($content_text);
			$this->email->message($content_html);			
					
			if($this->email->send())
			{	
				$this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
				redirect('/Login');
			}	
			else
			{	
				$this->session->set_flashdata("email_sent","You have encountered an error");		
			}
		}
		else
		{					
			$data['error']= "Email is not register yet";
			$this->load->view('forgot.php', $data);
		}	
	}
	
	public function changepassword()
	{
		$code = $this->input->post('code');
		$email = $this->input->post('email');
		$password1 = $this->input->post('password1');
		$password2 = $this->input->post('password2');
		
		//if($password1==$password2)
		//{
			$this->db->where("code",$code);
			$this->db->where("md5(user_name)",$email);
			$this->db->where("active","0");
			$query = $this->db->get("user");

			if ($query->num_rows() == 1) 
			{
				foreach ($query->result() as $row)				
				{
					$email2 = $row->email;
				}

				$this->db->set('password',md5($password1));
				$this->db->set('active',"1");
				$this->db->where("active","0");
				$this->db->where("md5(user_name)",$email);
				$this->db->where("code",$code);
				$this->db->update('user');

				$this->email->from('dpwibpushapp@xl.co.id', 'Wib Push Administrator');
				$this->email->to($email2);
				$this->email->subject('Your Wib Push Account have been Active');
				$file=fopen(APP_PATH."assets/email_activation.html", "r") or die("Unable to open file!");
				$content=fread($file,filesize(APP_PATH."assets/email_activation.html"));
				$content_text = htmlentities($content);
				$content_text=str_replace("_url",base_url()."Login",$content_text);
				$content_text=str_replace("_email",$email,$content_text);
				$content_text=str_replace("_password",$password2,$content_text);
				$content_html=html_entity_decode($content_text);
				$this->email->message($content_html);			
						
				if($this->email->send())
				{	
					$this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
					redirect('/Login');
				}	
				else
				{	
					$this->session->set_flashdata("email_sent","You have encountered an error");		
				}

			}
			else
			{
				$data['error']= "Your Security Code is not match";
				$this->load->view('info.php', $data);
					
			}
		//}
		/*else
		{
			$data['error']= "Your New Password and Confirmation is not match";
			//$this->load->view('change.php?key='.$email, $data);
		}*/			
		
	}

	public function changepassword2()
	{
		$email = $this->input->post('email');
		$password1 = $this->input->post('password1');
		$password2 = $this->input->post('password2');
		
		//if($password1==$password2)
		//{
			$this->db->where("user_name",$email);
			$this->db->where("active","1");
			$query = $this->db->get("user");

			if ($query->num_rows() == 1) 
			{
				foreach ($query->result() as $row)				
				{
					$email2 = $row->email;
				}

				$this->db->set('password',md5($password1));
				$this->db->set('is_change','1');
				$this->db->where("user_name",$email);
				$this->db->update('user');

				$this->email->from('dpwibpushapp@xl.co.id', 'Wib Push Administrator');
				$this->email->to($email2);
				$this->email->subject('Your Wib Push Account have been Changed');
				$file=fopen(APP_PATH."assets/change_password.html", "r") or die("Unable to open file!");
				$content=fread($file,filesize(APP_PATH."assets/change_password.html"));
				$content_text = htmlentities($content);
				$content_text=str_replace("_url",base_url()."Login",$content_text);
				$content_text=str_replace("_email",$email,$content_text);
				$content_text=str_replace("_password",$password2,$content_text);
				$content_html=html_entity_decode($content_text);
				$this->email->message($content_html);			
						
				if($this->email->send())
				{	
					$this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
					redirect('/Login');
				}	
				else
				{	
					$this->session->set_flashdata("email_sent","You have encountered an error");		
				}

			}
			else
			{
				$data['error']= "Your User Name is not match";
				$this->load->view('info.php', $data);
					
			}
		//}
		/*else
		{
			$data['error']= "Your New Password and Confirmation is not match";
			//$this->load->view('change.php?key='.$email, $data);
		}*/			
		
	}
	
	
	public function activateaccount()
	{
		$code = $this->input->post('code');
		$email = $this->input->post('email');
		
		//if($password1==$password2)
		//{
			$this->db->where("code",$code);
			$this->db->where("md5(user_name)",$email);
			$this->db->where("active","0");
			$query = $this->db->get("user");

			if ($query->num_rows() == 1) 
			{
				foreach ($query->result() as $row)				
				{
					$email2 = $row->email;
				}

				$length = 8;
				$randomletter = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, $length);
								
				$this->db->set('password',md5($randomletter));
				$this->db->set('active',"1");
				$this->db->where("active","0");
				$this->db->where("md5(user_name)",$email);
				$this->db->where("code",$code);
				$this->db->update('user');

				$this->email->from('dpwibpushapp@xl.co.id', 'WIBPUSH Administrator');
				$this->email->to($email2);
				$this->email->subject('Your WIBPUSH Account have been Active');
				$file=fopen(APP_PATH."assets/email_activation.html", "r") or die("Unable to open file!");
				$content=fread($file,filesize(APP_PATH."assets/email_activation.html"));
				$content_text = htmlentities($content);
				$content_text=str_replace("_url",base_url()."Login",$content_text);
				$content_text=str_replace("_email",$email,$content_text);
				$content_text=str_replace("_password",$randomletter,$content_text);
				$content_html=html_entity_decode($content_text);
				$this->email->message($content_html);			
						
				if($this->email->send())
				{	
					$this->session->set_flashdata("email_sent","Congragulation Email Send Successfully.");
					redirect('/Login/logout');					
				}	
				else
				{	
					$this->session->set_flashdata("email_sent","You have encountered an error");		
				}

			}
			else
			{
				$data['error']= "Your Security Code is not match";
				$this->load->view('info.php', $data);
					
			}
		//}
		/*else
		{
			$data['error']= "Your New Password and Confirmation is not match";
			//$this->load->view('change.php?key='.$email, $data);
		}*/			
		
	}	
}
