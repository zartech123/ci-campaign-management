<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report6 extends CI_Controller {

	private $menu = "";
	public function __construct()
	{
		parent::__construct();

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
	}

	public function _report6_output($output = null)
	{
		$this->load->view('report6',(array)$output);
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
				
				$this->_report6_output($output);
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

				$this->_report6_output($output);
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

				$this->_report6_output($output);
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

				$this->_report6_output($output);
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
		date_default_timezone_set('Asia/Jakarta');
		$type=$_GET['type'];
		$ip=$_GET['ip'];
		
		$labels="{\"labels\": [";	
		$datasets="\"datasets\": [";
		$result=array();
		$count = 1;
		$date2 = array();
		if($type=="1")
		{		
			$date2[0]=date('Y-m-d');
			$x=$type-1;
		}
		else
		{	
			for($x=$type-1;$x>=0;$x--)
			{
				if($x==0)
				{
					$date2[$x] = date('Y-m-d');
				}					
				else
				{	
					$prev = date('Y-m-d',strtotime("-".$x." days"));
					$date2[$x] = $prev;
				}	
			}	
		}
		
		$data = "";

		$y=0;
		$z=0;
		for($i=$type-1;$i>=0;$i--)
		{	
			$start_seconds = explode(":","00:00");
			if($date2[$i]==date('Y-m-d'))
			{
				$end_seconds = explode(":",date('H:i'));
			}
			else
			{		
				$end_seconds = explode(":","24:55");
			}	
			

			$times = array();

			if ( empty( $format ) ) {
				$format = 'H:i';
			}
		
			$lower = intval(trim($start_seconds[0]))*3600+intval(trim($start_seconds[1]))*60;
			$upper = intval(trim($end_seconds[0]))*3600+intval(trim($end_seconds[1]))*60;
			$step = 60 * 5;
				
			foreach ( range( $lower, $upper, $step ) as $increment ) 
			{
				$increment = gmdate( 'H:i', $increment );
				list( $hour, $minutes ) = explode( ':', $increment );

				$date = new DateTime( $hour . ':' . $minutes );

				$times[(string) $increment] = $date->format( $format );
			}
			
			
			foreach ($times as $key => $value)
			{
				$labels=$labels."\"".substr($date2[$i],-5)." ".$value."\",";
				
				$query2 = $this->db->query("SELECT COUNT(*) as jumlah FROM nms where ip_address='".$ip."' and substr(created_date,1,16)='".$date2[$i]." ".$value."'");
				$j=0;
				foreach ($query2->result() as $row2)
				{						
					$data=$data."\"".$row2->jumlah."\",";
					$j=$j+1;
					$z=$z+1;
				}
				if($j==0)
				{
					$data=$data."\"".$j."\",";
				}					
			}
			$y=$y+1;
		}	
		$data=rtrim($data,",");


		$datasets=$datasets."{";
		$datasets=$datasets."\"fill\":true,";
		$datasets=$datasets."\"label\":\"UP\",";
		$datasets=$datasets."\"borderCapStyle\":\"square\",";
		$datasets=$datasets."\"borderDash\": [],";
		$datasets=$datasets."\"borderDashOffset\": 0.0,";
		$datasets=$datasets."\"borderJoinStyle\": \"miter\",";
		$datasets=$datasets."\"borderWidth\":1,";
		$datasets=$datasets."\"pointBorderColor\": \"black\",";
		$datasets=$datasets."\"pointBackgroundColor\": \"white\",";
		$datasets=$datasets."\"pointBorderWidth\": 1,";
		$datasets=$datasets."\"pointHoverRadius\": 1,";
		$datasets=$datasets."\"pointHoverBackgroundColor\":\"yellow\",";
		$datasets=$datasets."\"pointHoverBorderColor\": \"brown\",";
		$datasets=$datasets."\"pointHoverBorderWidth\": 1,";
		$datasets=$datasets."\"pointRadius\": 1,";
		$datasets=$datasets."\"pointHitRadius\": 1,";
		$datasets=$datasets."\"lineTension\":0.1,";
		$datasets=$datasets."\"spanGaps\": true,";
		$datasets=$datasets."\"backgroundColor\": [\"green\"],";
		$datasets=$datasets."\"data\": [".$data."]}";
		
		$datasets=$datasets."]}";
		$labels=rtrim($labels,",");
		$labels=$labels."],";	
		echo $labels.$datasets;
		//echo json_encode($result);
	}
	

	public function getReport2()
	{
		date_default_timezone_set('Asia/Jakarta');
		$type=$_GET['type'];
		$ip=$_GET['ip'];

		$labels="{\"labels\": [";	
		$datasets="\"datasets\": [";
		$result=array();
		$date2 = array();
		if($type=="1")
		{		
			$date2[0]=date('Y-m-d');
			$x=$type-1;
		}
		else
		{	
			for($x=$type-1;$x>=0;$x--)
			{
				if($x==0)
				{
					$date2[$x] = date('Y-m-d');
				}					
				else
				{	
					$prev = date('Y-m-d',strtotime("-".$x." days"));
					$date2[$x] = $prev;
				}	
			}	
		}
		

		$y=0;
		$z=0;
		$labels=$labels."\"UP\",\"DOWN\",";
		for($i=$type-1;$i>=0;$i--)
		{	
			$start_seconds = explode(":","00:00");
			if($date2[$i]==date('Y-m-d'))
			{
				$end_seconds = explode(":",date('H:i'));
			}
			else
			{		
				$end_seconds = explode(":","24:55");
			}	
			

			$times = array();

			if ( empty( $format ) ) {
				$format = 'H:i';
			}
		
			$lower = intval(trim($start_seconds[0]))*3600+intval(trim($start_seconds[1]))*60;
			$upper = intval(trim($end_seconds[0]))*3600+intval(trim($end_seconds[1]))*60;
			$step = 60 * 5;
				
			foreach ( range( $lower, $upper, $step ) as $increment ) 
			{
				$increment = gmdate( 'H:i', $increment );
				list( $hour, $minutes ) = explode( ':', $increment );

				$date = new DateTime( $hour . ':' . $minutes );

				$times[(string) $increment] = $date->format( $format );
			}
			
			
			foreach ($times as $key => $value)
			{
				
				$query2 = $this->db->query("SELECT COUNT(*) as jumlah FROM nms where ip_address='".$ip."' and substr(created_date,1,16)='".$date2[$i]." ".$value."'");
				foreach ($query2->result() as $row2)
				{						
					if($row2->jumlah>0)	$z=$z+1;
				}
				$y=$y+1;
			}
		}	
		
		$datasets=$datasets."{";
		$datasets=$datasets."\"label\":\"Percentage\",";
		$datasets=$datasets."\"backgroundColor\": [\"green\",\"red\"],";
		$datasets=$datasets."\"data\": [\"".$z."\",\"".($y-$z)."\"]}";

		$datasets=$datasets."]}";
		$labels=rtrim($labels,",");
		$labels=$labels."],";	
		echo $labels.$datasets;
		//echo json_encode($result);
	}



	
}
