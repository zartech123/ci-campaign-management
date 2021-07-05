<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
	}

	public function _report_output($output = null)
	{
		$this->load->view('report',(array)$output);
	}

	
	public function index()
	{
		try{
			date_default_timezone_set('Asia/Jakarta');

			$crud = new Grocery_CRUD();
			
			
			$crud->set_theme('bootstrap');
			$crud->set_table('campaign');
			$crud->set_subject('Broadcast Schedule');
			$output = $crud->render();
			
			
			if($this->session->userdata('id_group')==1)
			{	
				$this->load->view('menu_admin.html');

				$this->_report_output($output);
			}	
			else if($this->session->userdata('id_group')==3)
			{	
				$this->load->view('menu_creator.html');

				$this->_report_output($output);
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

	public function getCampaign()
	{
		$where = "";
		$type=$_GET['type'];
		if($type=="1")
		{	
			$where = "and MONTH(start_date_campaign) = MONTH(CURRENT_DATE) AND YEAR(start_date_campaign) = YEAR(CURRENT_DATE) AND DATE(start_date_campaign) = DATE(CURRENT_DATE)";
		}
		else if($type=="2")
		{	
			$where = "and DATE(start_date_campaign) >= (NOW() - INTERVAL 7 DAY) AND DATE(start_date_campaign) <= (NOW() - INTERVAL 1 DAY)";
		}
		else if($type=="3")
		{	
			$where = "and DATE(start_date_campaign) >= (NOW() - INTERVAL 30 DAY) AND DATE(start_date_campaign) <= (NOW() - INTERVAL 1 DAY)";
		}
		else if($type=="4")
		{	
			$where = "and MONTH(start_date_campaign) = MONTH(CURRENT_DATE) AND YEAR(start_date_campaign) = YEAR(CURRENT_DATE)";
		}
		else if($type=="5")
		{	
		    $start_date=$_GET['start_date'];
		    $end_date=$_GET['end_date'];
			$where = "and start_date_campaign>='".$start_date."' and end_date_campaign<='".$end_date."'";
		}

		$result="[";
//		$query = $this->db->query("SELECT id_campaign, CONCAT(name_campaign,' | ',start_time_campaign,' to ',end_time_campaign) as name  FROM campaign where start_date_campaign='".$_GET['date']."'");
		$query = $this->db->query("SELECT id_campaign, total_recipient, if(type='0','Manual','Automatic') as type, name_campaign,start_time_campaign,end_time_campaign, CONCAT(start_date_campaign,' (',DAYNAME(start_date_campaign),')') as start_date_campaign  FROM campaign where id_state<6 ".$where." order by start_date_campaign");
		foreach ($query->result() as $row2)
		{			
//			$result=$result.$row2->name;
			$result=$result."{\"name\":\"".$row2->start_date_campaign."|".$row2->name_campaign."\",\"start_time\":\"".$row2->start_time_campaign."\",\"end_time\":\"".$row2->end_time_campaign."\",\"start_date\":\"".$row2->start_date_campaign."\",\"type\":\"".$row2->type."\",\"total_recipient\":\"".$row2->total_recipient."\",\"id_campaign\":\"".$row2->id_campaign."\"}";
			$result=$result.",";
		}
		$result=rtrim($result, ",");
		$result=$result."]";	
		echo $result;
	}
	
}
