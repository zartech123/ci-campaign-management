<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report5 extends CI_Controller {

	private $menu = "";
	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
	}

	public function _report5_output($output = null)
	{
		$this->load->view('report5',(array)$output);
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
			
			$crud->set_theme('bootstrap');
			$crud->set_table('smpp_mo');
			$crud->set_subject('Total Campaign Broadcast Summary by Date');
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
				
				$this->_report5_output($output);
			}	
			else if($this->session->userdata('id_group')==2)
			{	
				if($this->menu!="0")	
				{	
					$this->load->view('menu_approver.html');
				}
				else
				{
					$this->load->view('load.html');
				}					

				$this->_report5_output($output);
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

				$this->_report5_output($output);
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

				$this->_report5_output($output);
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

	public function getReport()
	{
		$type=$_GET['type'];
		if($type=="5")
		{
			$start_date = $_GET['start_date'];
			$end_date = $_GET['end_date'];
		}			

		$labels="{\"labels\": [";	
		$datasets="\"datasets\": [";
		$result=array();
		$where = "";
		if($type=="1")
		{	
			$where = "and MONTH(start_date_campaign) = MONTH(CURRENT_DATE) AND YEAR(start_date_campaign) = YEAR(CURRENT_DATE) AND DATE(start_date_campaign) = DATE(CURRENT_DATE)";
		}
		if($type=="2")
		{	
			$where = "and DATE(start_date_campaign) >= (NOW() - INTERVAL 7 DAY) AND DATE(start_date_campaign) <= (NOW() - INTERVAL 1 DAY)";
		}
		if($type=="3")
		{	
			$where = "and DATE(start_date_campaign) >= (NOW() - INTERVAL 30 DAY) AND DATE(start_date_campaign) <= (NOW() - INTERVAL 1 DAY)";
		}
		if($type=="4")
		{	
			$where = "and MONTH(start_date_campaign) = MONTH(CURRENT_DATE) AND YEAR(start_date_campaign) = YEAR(CURRENT_DATE)";
		}
		if($type=="5")
		{	
			$where = "and start_date_campaign>='".$start_date."' and end_date_campaign<='".$end_date."'";
		}
		
		$colour_array="";
		$data = "";
		$data2 = "";

		$query3 = $this->db->query("SELECT DISTINCT start_date_campaign FROM campaign a, recipient b WHERE a.id_campaign=b.id_campaign ".$where." order by start_date_campaign");
		foreach ($query3->result() as $row3)
		{
			$labels=$labels."\"".$row3->start_date_campaign."\",";

			
			
			$query2 = $this->db->query("SELECT COUNT(*) as jumlah FROM campaign a, recipient b WHERE start_date_campaign='".$row3->start_date_campaign."' and a.id_campaign=b.id_campaign ORDER BY start_date_campaign");
			$j=0;
			foreach ($query2->result() as $row2)
			{						
				$colour = '#'.str_pad(dechex(rand(0x000000, 0x888888)), 6, 0, STR_PAD_LEFT);
				$colour_array=$colour_array."\"".$colour."\",";
				$data=$data."\"".$row2->jumlah."\",";
				$data2=$data2."\"120\",";
				$j=$j+1;
			}
			if($j==0)
			{
				$data=$data."\"".$j."\",";
			}					
		}
		$colour_array=rtrim($colour_array,",");
		$data=rtrim($data,",");
		$data2=rtrim($data2,",");

		$colour = '#'.str_pad(dechex(rand(0x000000, 0x888888)), 6, 0, STR_PAD_LEFT);

		$datasets=$datasets."{";
		$datasets=$datasets."\"type\":\"line\",";
		$datasets=$datasets."\"fill\":\"false\",";
		$datasets=$datasets."\"label\":\"Capacity of Utilization\",";
		$datasets=$datasets."\"borderCapStyle\":\"square\",";
		$datasets=$datasets."\"borderDash\": [],";
		$datasets=$datasets."\"borderDashOffset\": 0.0,";
		$datasets=$datasets."\"borderJoinStyle\": \"miter\",";
		$datasets=$datasets."\"borderWidth\":5,";
		$datasets=$datasets."\"pointBorderColor\": \"black\",";
		$datasets=$datasets."\"pointBackgroundColor\": \"white\",";
		$datasets=$datasets."\"pointBorderWidth\": 1,";
		$datasets=$datasets."\"pointHoverRadius\": 8,";
		$datasets=$datasets."\"pointHoverBackgroundColor\":\"yellow\",";
		$datasets=$datasets."\"pointHoverBorderColor\": \"brown\",";
		$datasets=$datasets."\"pointHoverBorderWidth\": 2,";
		$datasets=$datasets."\"pointRadius\": 4,";
		$datasets=$datasets."\"pointHitRadius\": 10,";
		$datasets=$datasets."\"lineTension\":0.1,";
		$datasets=$datasets."\"spanGaps\": true,";
		$datasets=$datasets."\"borderColor\": [\"".$colour."\"],";
		$datasets=$datasets."\"data\": [".$data."]},";
		
		$colour = '#'.str_pad(dechex(rand(0x000000, 0x888888)), 6, 0, STR_PAD_LEFT);

		$datasets=$datasets."{";
		$datasets=$datasets."\"type\":\"line\",";
		$datasets=$datasets."\"fill\":\"false\",";
		$datasets=$datasets."\"label\":\"Total Capacity\",";
		$datasets=$datasets."\"borderCapStyle\":\"square\",";
		$datasets=$datasets."\"borderDash\": [],";
		$datasets=$datasets."\"borderDashOffset\": 0.0,";
		$datasets=$datasets."\"borderJoinStyle\": \"miter\",";
		$datasets=$datasets."\"borderWidth\":5,";
		$datasets=$datasets."\"pointBorderColor\": \"black\",";
		$datasets=$datasets."\"pointBackgroundColor\": \"white\",";
		$datasets=$datasets."\"pointBorderWidth\": 1,";
		$datasets=$datasets."\"pointHoverRadius\": 8,";
		$datasets=$datasets."\"pointHoverBackgroundColor\":\"yellow\",";
		$datasets=$datasets."\"pointHoverBorderColor\": \"brown\",";
		$datasets=$datasets."\"pointHoverBorderWidth\": 2,";
		$datasets=$datasets."\"pointRadius\": 4,";
		$datasets=$datasets."\"pointHitRadius\": 10,";
		$datasets=$datasets."\"lineTension\":0.1,";
		$datasets=$datasets."\"spanGaps\": true,";
		$datasets=$datasets."\"borderColor\": [\"".$colour."\"],";
		$datasets=$datasets."\"data\": [".$data2."]},";

		$datasets=$datasets."{";
		$datasets=$datasets."\"type\":\"bar\",";
		$datasets=$datasets."\"label\":\"Total Broadcast\",";
		$datasets=$datasets."\"borderWidth\":1,";
		$datasets=$datasets."\"backgroundColor\": [".$colour_array."],";
		$datasets=$datasets."\"data\": [".$data."]}";
		
		
		$datasets=$datasets."]}";
		$labels=rtrim($labels,",");
		$labels=$labels."],";	
		echo $labels.$datasets;
		//echo json_encode($result);
	}
	




	
}
