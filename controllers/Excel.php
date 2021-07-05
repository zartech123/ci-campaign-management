<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/third_party/PHPExcel.php";

class Excel extends PHPExcel  
{

	
	public function createReport3()
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel
			->getProperties()
			->setCreator("WIBPUSH Administrator")
			->setLastModifiedBy("WIBPUSH Administrator");
			
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();	

		$headerColumns= array(
			'No`',
			'Response',
			'Date',
			'Total Broadcast'
		);
		foreach ($headerColumns as $columnKey => $headerColumn) 
		{
			$sheet->setCellValueByColumnAndRow($columnKey, 1, $headerColumn);
		}

		$page = file_get_contents("http://".$_SERVER['HTTP_HOST']."/wibpush/Report3/getReport?type=".$_GET['type']."&start_date=".$_GET['start_date']."&end_date=".$_GET['end_date']);	
		$filename="Broadcast Response";
		if($_GET['type']=="1")
		{
			$filename = $filename." (Today)";
		}			
		if($_GET['type']=="2")
		{
			$filename = $filename." (Last 7 Days)";
		}			
		if($_GET['type']=="3")
		{
			$filename = $filename." (Last 30 Days)";
		}			
		if($_GET['type']=="4")
		{
			$filename = $filename." (This Month)";
		}			
		if($_GET['type']=="5")
		{
			$filename = $filename." from ".$_GET['start_date']." to ".$_GET['end_date'];		
		}			
		//echo $page;
		
		$array = $json = json_decode($page, true);
		$count_labels = sizeof($array['labels']);
		$count_datasets = sizeof($array['datasets']);
		$count = $count_labels*$count_datasets;
		//echo $array['datasets'][1]['data'][0];
		
		$i = 2;
		$k = 0;
		for($j=0;$j<$count;$j++) 
		{
			$index_datasets = floor($j/$count_datasets);
			$index_labels = floor($j/$count_labels);
			$sheet->setCellValueByColumnAndRow(0, $i, ($i-1));
//			echo $j."-".floor($j/$count_datasets)."<br>";
			$sheet->setCellValueByColumnAndRow(1, $i, $array['datasets'][$index_labels]['label']);
			$sheet->setCellValueByColumnAndRow(2, $i, $array['labels'][$k]);
			$sheet->setCellValueByColumnAndRow(3, $i, $array['datasets'][$index_labels]['data'][$k]);
			$i++;
			$k++;
			if($k==$count_labels)	$k=0;
		}		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('C:\\xampp\\htdocs\\wibpush\\assets\\uploads\\files\\'.$filename.'.xlsx');

		//echo "xx";
	}	


	public function createReport5()
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel
			->getProperties()
			->setCreator("WIBPUSH Administrator")
			->setLastModifiedBy("WIBPUSH Administrator");
			
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();	

		$headerColumns= array(
			'No`',
			'Capacity',
			'Date',
			'Total Broadcast'
		);
		foreach ($headerColumns as $columnKey => $headerColumn) 
		{
			$sheet->setCellValueByColumnAndRow($columnKey, 1, $headerColumn);
		}

		$page = file_get_contents("http://".$_SERVER['HTTP_HOST']."/wibpush/Report5/getReport?type=".$_GET['type']."&start_date=".$_GET['start_date']."&end_date=".$_GET['end_date']);	
		$filename="Push Capacity";
		if($_GET['type']=="1")
		{
			$filename = $filename." (Today)";
		}			
		if($_GET['type']=="2")
		{
			$filename = $filename." (Last 7 Days)";
		}			
		if($_GET['type']=="3")
		{
			$filename = $filename." (Last 30 Days)";
		}			
		if($_GET['type']=="4")
		{
			$filename = $filename." (This Month)";
		}			
		if($_GET['type']=="5")
		{
			$filename = $filename." from ".$_GET['start_date']." to ".$_GET['end_date'];		
		}			
		echo $page;
		
		$array = $json = json_decode($page, true);
		$count_labels = sizeof($array['labels']);
		$count_datasets = sizeof($array['datasets']);
		$count = $count_labels*$count_datasets;
//		echo $array['datasets'][0]['data'][0];
		
		$i = 2;
		$k = 0;
		for($j=0;$j<$count;$j++) 
		{
			$index_datasets = floor($j/$count_datasets);
			$index_labels = floor($j/$count_labels);
			$sheet->setCellValueByColumnAndRow(0, $i, ($i-1));
//			echo $j."-".floor($j/$count_datasets)."<br>";

			$sheet->setCellValueByColumnAndRow(1, $i, $array['datasets'][$index_labels]['label']);
			$sheet->setCellValueByColumnAndRow(2, $i, $array['labels'][$k]);
			if($array['datasets'][$index_labels]['label']=="Capacity of Utilization")
			{	
				$sheet->setCellValueByColumnAndRow(3, $i, ($array['datasets'][$index_labels]['data'][$k])/120);
			}
			else
			{
				$sheet->setCellValueByColumnAndRow(3, $i, $array['datasets'][$index_labels]['data'][$k]);
			}				
			$i++;
			$k++;
			if($k==$count_labels)	$k=0;
		}		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('C:\\xampp\\htdocs\\wibpush\\assets\\uploads\\files\\'.$filename.'.xlsx');

		//echo "xx";
	}	

	
	public function createReport2()
	{
		$objPHPExcel = new PHPExcel();
		$objPHPExcel
			->getProperties()
			->setCreator("WIBPUSH Administrator")
			->setLastModifiedBy("WIBPUSH Administrator");
			
		$objPHPExcel->setActiveSheetIndex(0);
		$sheet = $objPHPExcel->getActiveSheet();	

		$headerColumns= array(
			'No`',
			'Campaign Name',
			'Date',
			'Total Broadcast'
		);
		foreach ($headerColumns as $columnKey => $headerColumn) 
		{
			$sheet->setCellValueByColumnAndRow($columnKey, 1, $headerColumn);
		}

		$page = file_get_contents("http://".$_SERVER['HTTP_HOST']."/wibpush/Report2/getReport?type=".$_GET['type']."&start_date=".$_GET['start_date']."&end_date=".$_GET['end_date']);	
		$filename="Broadcast Summary per Campaign";
		if($_GET['type']=="1")
		{
			$filename = $filename." (Today)";
		}			
		if($_GET['type']=="2")
		{
			$filename = $filename." (Last 7 Days)";
		}			
		if($_GET['type']=="3")
		{
			$filename = $filename." (Last 30 Days)";
		}			
		if($_GET['type']=="4")
		{
			$filename = $filename." (This Month)";
		}			
		if($_GET['type']=="5")
		{
			$filename = $filename." from ".$_GET['start_date']." to ".$_GET['end_date'];		
		}			
		//echo $page;
		
		$array = $json = json_decode($page, true);
		$count_labels = sizeof($array['labels']);
		$count_datasets = sizeof($array['datasets']);
		$count = $count_labels*$count_datasets;
		//echo $array['datasets'][1]['data'][0];
		
		$i = 2;
		$k = 0;
		for($j=0;$j<$count;$j++) 
		{
			$index_datasets = floor($j/$count_datasets);
			$index_labels = floor($j/$count_labels);
			$sheet->setCellValueByColumnAndRow(0, $i, ($i-1));
//			echo $j."-".floor($j/$count_datasets)."<br>";
			$sheet->setCellValueByColumnAndRow(1, $i, $array['datasets'][$index_labels]['label']);
			$sheet->setCellValueByColumnAndRow(2, $i, $array['labels'][$k]);
			$sheet->setCellValueByColumnAndRow(3, $i, $array['datasets'][$index_labels]['data'][$k]);
			$i++;
			$k++;
			if($k==$count_labels)	$k=0;
		}		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('C:\\xampp\\htdocs\\wibpush\\assets\\uploads\\files\\'.$filename.'.xlsx');

		//echo "xx";
	}	
	
}
