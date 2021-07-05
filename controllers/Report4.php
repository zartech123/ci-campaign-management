<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report4 extends CI_Controller {

	public $id_campaign = "";
	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
	}

	public function _report4_output($output = null)
	{
		$data['id']=$_GET['id'];
		$output->data = $data;
		$this->load->view('report4',(array)$output);
	}

	
	public function index()
	{
		try{
			date_default_timezone_set('Asia/Jakarta');

			$crud = new Grocery_CRUD();
			
			
			$crud->set_theme('bootstrap');
			$crud->set_table('smpp_mo');
			$crud->set_subject('Total Campaign Broadcast Summary by Date');
			$output = $crud->render();
			$this->id_campaign=$_GET['id'];
			
			if($this->session->userdata('id_group')==1)
			{	
				$this->load->view('menu_admin.html');

				$this->_report4_output($output);
			}	
			else if($this->session->userdata('id_group')==2)
			{	
				$this->load->view('menu_approver.html');

				$this->_report4_output($output);
			}	
			else if($this->session->userdata('id_group')==3)
			{	
				$this->load->view('menu_creator.html');

				$this->_report4_output($output);
			}	
			else if($this->session->userdata('id_group')==5)
			{	
				$this->load->view('menu_view.html');

				$this->_report4_output($output);
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
		$id=$_GET['id'];
		$labels="{\"labels\": [";	
		$datasets="\"datasets\": [";
		$result=array();
		$query = $this->db->query("SELECT start_date_campaign, start_time_campaign, IF(end_time_campaign<start_time_campaign,'24:00',end_time_campaign) AS end_time_campaign, id_campaign FROM campaign WHERE id_campaign='".$id."'");
		foreach ($query->result() as $row)
		{			
			$start_seconds = explode(":",$row->start_time_campaign);
			$end_seconds = explode(":",$row->end_time_campaign);

			$times = array();

			if ( empty( $format ) ) {
				$format = 'H:i';
			}

			$lower = intval(trim($start_seconds[0]))*3600+intval(trim($start_seconds[1]))*60;
			$upper = intval(trim($end_seconds[0]))*3600+intval(trim($end_seconds[1]))*60;
			$step = 60 * 15;
			
			foreach ( range( $lower, $upper, $step ) as $increment ) 
			{
				$increment = gmdate( 'H:i', $increment );

				list( $hour, $minutes ) = explode( ':', $increment );

				$date = new DateTime( $hour . ':' . $minutes );

				$times[(string) $increment] = $date->format( $format );
				$labels=$labels."\"".$date->format( $format )."\",";
			}

			for($i=0;$i<5;$i++)
			{
				if($i==0)
				{
					$response="Received";
				}					
				else if($i==1)
				{
					$response="Layer1";
				}					
				else if($i==2)
				{
					$response="Layer2";
				}					
				else if($i==3)
				{
					$response="Layer3";
				}					
				else if($i==4)
				{
					$response="Success Rate";
				}					
				$colour = '#'.str_pad(dechex(rand(0x000000, 0x888888)), 6, 0, STR_PAD_LEFT);
				$datasets=$datasets."{";
				$datasets=$datasets."\"label\":\"".$response."\",";
				$datasets=$datasets."\"borderCapStyle\":\"square\",";
				$datasets=$datasets."\"borderDash\": [],";
				$datasets=$datasets."\"borderDashOffset\": 0.0,";
				$datasets=$datasets."\"borderJoinStyle\": \"miter\",";
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
				$datasets=$datasets."\"fill\":false,";
				$datasets=$datasets."\"borderColor\":\"".$colour."\",";
				$datasets=$datasets."\"data\": [";
				foreach ($times as $key => $value)
				{			
					if($i<4)
					{	
						$query2 = $this->db->query("SELECT COUNT(*) AS jumlah, b.type AS TYPE FROM smpp_mo b, campaign a WHERE b.type='".$i."' and STR_TO_DATE(b.created_date,'%Y-%m-%d %H:%i')<=STR_TO_DATE(CONCAT('".$row->start_date_campaign."',' ','".$value."'),'%Y-%m-%d %H:%i') and a.id_campaign=b.id_campaign and a.id_campaign='".$row->id_campaign."' GROUP BY type ORDER BY start_date_campaign");
					}
					else if($i==4)
					{	
						$query2 = $this->db->query("SELECT COUNT(*) AS jumlah FROM smpp_mo b, campaign a WHERE STR_TO_DATE(b.created_date,'%Y-%m-%d %H:%i')<=STR_TO_DATE(CONCAT('".$row->start_date_campaign."',' ','".$value."'),'%Y-%m-%d %H:%i') and a.id_campaign=b.id_campaign and a.id_campaign='".$row->id_campaign."' ORDER BY start_date_campaign");				
					}	
					$j=0;
					foreach ($query2->result() as $row2)
					{						
						$datasets=$datasets."\"".$row2->jumlah."\",";
						$j=$j+1;
					}
					if($j==0)
					{
						$datasets=$datasets."\"".$j."\",";
					}					
				}	
				$datasets=rtrim($datasets,",");
				$datasets=$datasets."]";
				$datasets=$datasets."},";
			}	
		}
		$labels=rtrim($labels,",");
		$datasets=rtrim($datasets,",");
		$labels=$labels."],";	
		$datasets=$datasets."]}";
		
		echo $labels.$datasets;
	}
	




	
}
