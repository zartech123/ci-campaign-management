<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Campaign extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		

		$this->load->database();
		$this->load->helper('url');

		$this->load->library('grocery_CRUD');
		$this->load->library('session');
		$this->load->library('email');
	}

	public function _campaign_output($output = null)
	{
		$this->load->view('campaign',(array)$output);
	}

	
	public function index()
	{
		try{
			date_default_timezone_set('Asia/Jakarta');

			$crud = new Grocery_CRUD();
			
			$time = array('09:00'=>'09:00','09:30'=>'09:30','10:00'=>'10:00','10:30'=>'10:30','11:00'=>'11:00','11:30'=>'11:30','12:00'=>'12:00','12:30'=>'12:30','13:00'=>'13:00','13:30'=>'13:30','14:00'=>'14:00','15:30'=>'15:30','16:00'=>'16:00','16:30'=>'16:30','17:00'=>'17:00','17:30'=>'17:30','18:00'=>'18:00','18:30'=>'18:30','19:00'=>'19:00','19:30'=>'19:30','20:00'=>'20:00','20:30'=>'20:30');
			$crud->set_theme('bootstrap');
			$crud->set_table('campaign');
			$crud->set_subject('Manual Campaign');
			$crud->columns('name_campaign','type','total_recipient','start_date_campaign','start_time_campaign','end_date_campaign','end_time_campaign','text','id_state');
			$crud->required_fields('name_file','layer','name_campaign','total_recipient','start_date_campaign','start_time_campaign','layer1_text');
			$crud->add_fields('type','created_by','approved_by','layer','name_campaign','total_recipient','start_date_campaign','start_time_campaign','sti','sti_text','tone','tone_title','tone_duration','layer1_text','layer2_text','layer3_text','id_action','action_destination','sms_text','conflict','name_file');
			$crud->edit_fields('type','created_by','layer','name_campaign','total_recipient','start_date_campaign','start_time_campaign','sti','sti_text','tone','tone_title','tone_duration','layer1_text','layer2_text','layer3_text','id_action','action_destination','sms_text','conflict','name_file');
			$crud->callback_column('type',array($this,'type'));
			$crud->unset_read();
			$crud->unset_print();
			$crud->unset_delete();
			$crud->unset_clone();
			$crud->where('type','0');
			$crud->order_by('campaign.id_state,start_date_campaign,start_time_campaign');
			$crud->set_field_upload('name_file', 'assets/uploads');
			$crud->field_type('total_recipient','integer');
			$crud->field_type('tone_duration','integer');
			$crud->field_type('start_time_campaign', 'dropdown',$time);
			$crud->field_type('sti', 'dropdown',array('0'=>'No','1'=>'Yes'));
			$crud->field_type('tone', 'dropdown',array('0'=>'No','1'=>'Yes'));
			$crud->field_type('layer', 'dropdown',array('1'=>'1','2'=>'2','3'=>'3'));
			$crud->display_as('layer','Number Of Layer');
			$crud->display_as('name_campaign','Name');
			$crud->display_as('text','Content');
			$crud->display_as('tone_duration','Tone Duration (s)');
			$crud->display_as('tone_title','Tone Title');
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
			$crud->field_type('type','hidden',"0");
			$crud->field_type('created_by','hidden',$this->session->userdata('id_user'));
			$crud->field_type('approved_by','hidden',$this->session->userdata('id_user'));
			$crud->field_type('layer1_text','text');
			$crud->field_type('layer2_text','text');
			$crud->field_type('layer3_text','text');
			$crud->field_type('sti_text','text');
			$crud->field_type('sms_text','text');
			$crud->unset_texteditor('layer1_text','layer2_text','layer3_text','sti_text','sms_text');
			$crud->display_as('total_recipient','Total Recipient');
			$crud->display_as('id_state','Status');
			$crud->display_as('end_date_campaign','End Date');
			$crud->display_as('end_time_campaign','End Time');
			$crud->display_as('start_date_campaign','Start Date');
			$crud->display_as('start_time_campaign','Start Time');
			$crud->display_as('name_file','File Name');
			$crud->set_lang_string('form_update_changes','Update');
			$crud->set_lang_string('form_update_and_go_back','Update & Return');
			$crud->set_lang_string('form_save_and_go_back','Save & Return');
			$crud->set_rules('action_destination','Destination','callback_checkDestination');
			$crud->set_rules('layer2_text','Layer-2 (Text)','callback_layer2_text');
			$crud->set_rules('tone_duration','Tone Duration (s)','callback_tone_duration');
			$crud->set_rules('tone_title','Tone Title','callback_tone_title');
			$crud->set_rules('layer3_text','Layer-3 (Text)','callback_layer3_text');
			$crud->set_rules('sti_text','Save to Inbox (SMS)','callback_sti_text');
			$crud->set_rules('sms_text','SMS','callback_sms_text');
			$crud->set_rules('layer','Layer','callback_layer');
			$crud->callback_before_upload(array($this, 'valid_files'));
			$crud->callback_after_upload(array($this, 'after_upload'));
			$crud->set_rules('name_file','File Name','callback_name_file');
			
			$crud->set_rules('total_recipient','Total Recipient','callback_total_recipient');
			$crud->callback_column('text',array($this,'text'));
			$crud->set_lang_string('form_upload_delete','Delete');
						
			$crud->callback_after_update(array($this, 'after_update'));
			$crud->callback_after_insert(array($this, 'after_insert'));
			

				$output = $crud->render();
				if($this->session->userdata('id_group')==1)
				{	
					$this->load->view('menu_admin.html');

					$this->_campaign_output($output);
				}	
				else if($this->session->userdata('id_group')==3)
				{	
					$this->load->view('menu_creator.html');

					$this->_campaign_output($output);
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

	function name_file($post_array) 
	{		
		if(!empty($_POST['name_file']))
		{	
			$file = APP_PATH."assets/uploads/".$_POST['name_file'];
			$count=0;
			$fp= fopen($file, "r");
			while($line = fgetss($fp)) 
				$count++;


			if($_POST['total_recipient']!=$count)
			{
				$this->form_validation->set_message('name_file', "Upload File contain ".$count." rows data which is not match with Total Recipient");
				return FALSE;
			}			
			else
			{
				return TRUE;
			}				
		}	
		else
		{
			$this->form_validation->set_message('name_file', "Please upload the file first");
			return FALSE;
		}			
	}
	
	
	public function getState()
	{
		$result="[";
		$query = $this->db->query("SELECT id_state, id_state as name  FROM campaign WHERE id_campaign=".$_GET['id']);
		foreach ($query->result() as $row2)
		{			
			$result=$result."{\"id\":\"".$row2->id_state."\",\"name\":\"".$row2->name."\"}";
			$result=$result.",";
		}
		$result=rtrim($result, ",");
		$result=$result."]";	
		echo $result;
	}

	public function getStartTime()
	{
		date_default_timezone_set('Asia/Jakarta');
		$date=explode("/",$_GET['id']);
		$date2=$date[2]."-".$date[1]."-".$date[0];
		if(strtotime($date2)==strtotime(date('Y-m-d')))
		{
			$time = array('09:00'=>'09:00','09:30'=>'09:30','10:00'=>'10:00','10:30'=>'10:30','11:00'=>'11:00','11:30'=>'11:30','12:00'=>'12:00','12:30'=>'12:30','13:00'=>'13:00','13:30'=>'13:30','14:00'=>'14:00','15:30'=>'15:30','16:00'=>'16:00','16:30'=>'16:30','17:00'=>'17:00','17:30'=>'17:30','18:00'=>'18:00','18:30'=>'18:30','19:00'=>'19:00','19:30'=>'19:30','20:00'=>'20:00','20:30'=>'20:30');
//			$time = array('10:00'=>'10:00','10:30'=>'10:30','11:00'=>'11:00','11:30'=>'11:30','12:00'=>'12:00','12:30'=>'12:30','13:00'=>'13:00','13:30'=>'13:30','14:00'=>'14:00','15:30'=>'15:30','16:00'=>'16:00','16:30'=>'16:30','17:00'=>'17:00','17:30'=>'17:30','18:00'=>'18:00','18:30'=>'18:30','19:00'=>'19:00','19:30'=>'19:30','20:00'=>'20:00','20:30'=>'20:30');
			$i = count($time);		
			foreach ($time as $key => $value) 
			{
				if (time() <= strtotime($value.":00 - 90 minutes"))
				{	
					break;
				}	
				else
				{					
					unset($time[$key]);
					$i=$i-1;
				}
			}
		}
		else		
		{
			$time = array('09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30');
		}


		if(isset($_GET['primary_key']))
		{
			$query = $this->db->query("SELECT start_time_campaign, IF(end_time_campaign<start_time_campaign,'24:00',end_time_campaign) AS end_time_campaign FROM campaign WHERE id_state!=7 and id_state!=6 and id_state!=8 and id_campaign!=".$_GET['primary_key']." AND start_date_campaign='".$date2."'");
		}
		else
		{		
			$query = $this->db->query("SELECT start_time_campaign, IF(end_time_campaign<start_time_campaign,'24:00',end_time_campaign) AS end_time_campaign FROM campaign WHERE id_state!=7 and id_state!=6 and id_state!=8 AND start_date_campaign='".$date2."'");
		}	
		$range = array();
		foreach ($query->result() as $row2)
		{			
			$start_seconds = explode(":",$row2->start_time_campaign);
			$end_seconds = explode(":",$row2->end_time_campaign);


			$times = array();

			if ( empty( $format ) ) {
				$format = 'H:i';
			}

			$lower = intval(trim($start_seconds[0]))*3600+intval(trim($start_seconds[1]))*60;
			$upper = intval(trim($end_seconds[0]))*3600+intval(trim($end_seconds[1]))*60;
			$step = 60 * 30;
			
			foreach ( range( $lower, $upper, $step ) as $increment ) {
				$increment = gmdate( 'H:i', $increment );

				list( $hour, $minutes ) = explode( ':', $increment );

				$date = new DateTime( $hour . ':' . $minutes );

				$times[] = $date->format( $format );
			}
			if(sizeof($times)>1)
			{	
				array_pop($times);
			}	
			$range = array_merge($range,$times);


			
		}
		$time = array_diff($time,$range);
		foreach ($time as $key => $value) {
			$time2[] = $value;
		}
		echo json_encode($time2);		
	}

	function randomCharacter($length)
	{
		return $randomletter = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz0123456789"), 0, $length);	
	}

	function after_upload($uploader_response,$field_info, $files_to_upload)
	{	 
		//$file_uploaded = $field_info->upload_path.'/'.$uploader_response[0]->name; 
	 
		//file_put_contents($file_uploaded,str_replace(PHP_EOL,',0,0'.PHP_EOL,file_get_contents($file_uploaded)));
	 
		return true;
	}	
	
	function after_insert($post_array,$primary_key)
	{		
		$query = $this->db->query("select tone_title, tone_duration, layer, layer1_text, layer2_text, layer3_text, action_destination, id_action, sms_text, sti, sti_text from campaign where id_campaign='".$primary_key."'");
//		$randomString = "----".$this->randomCharacter(8)."-".$this->randomCharacter(4)."-".$this->randomCharacter(4)."-".$this->randomCharacter(4)."-".$this->randomCharacter(12);
		$randomString = "----ab3f9e2d-e0cd-4ec1-8931-307648fdcae9";
		$wml = $wml.$randomString;
		$wml = $wml.PHP_EOL;
		$wml = $wml.PHP_EOL;
		$wml = $wml."Content-type: application/xml";
		$wml = $wml.PHP_EOL;
		$wml = $wml."<?xml version='1.0'?><!DOCTYPE pap PUBLIC \"-//WAPFORUM//DTD PAP 2.0//EN\" \"http://www.wapforum.org/DTD/pap_2.0.dtd\"><pap><push-message push-id=\"".$primary_key."|_MSISDN_\" ppg-notify-requested-to=\"10.162.18.103:8080/wibpush/notify\"><address address-value=\"_MSISDN_\"/><quality-of-service delivery-method=\"confirmed\"/></push-message></pap>";
		$wml = $wml.PHP_EOL;
		$wml = $wml.PHP_EOL;
		$wml = $wml.$randomString;
		$wml = $wml.PHP_EOL;
		$wml = $wml.PHP_EOL;
		$wml = $wml."Content-type: text/vnd.wap.wml";
		$wml = $wml.PHP_EOL;
		$wml = $wml."<?xml version='1.0' encoding='UTF-8'?><wml xmlns='http://www.smarttrust.com/WIG-WML/5.0' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.smarttrust.com/WIG-WML/5.0 http://www.smarttrust.com/xsd/wigwml-5.0.xsd'>";
		$row2 = $query->row();
		if($row2->layer=="1")
		{
			$wml = $wml."<card id='layer1'><p>";
			if($row2->tone=="1")
			{	
				$wml = $wml."<playtone toneid='ringing' duration='".$row2->tone_duration."' title='".$row2->tone_title."'/>";
			}	

			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Received</userdata></sendsm>".$row2->layer1_text;
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer1</userdata></sendsm>";
			if($row2->sti=="1")
			{	
				$wml = $wml."<select title='Option' name='option'>";
				$wml = $wml."<option value='sti'>Save to Inbox</option>";
				$wml = $wml."</select>";
				$wml = $wml."<conditionaljump compare='$(option)'>";
				$wml = $wml."<test href='#savetoinbox' value='sti'/>";
				$wml = $wml."</conditionaljump>";				
			}
			if($row2->id_action=="1")
			{
				$wml = $wml."<sendsm><destaddress value='".$row2->action_destination."'/><userdata>".$row2->sms_text."</userdata></sendsm>";
			}				
			else if($row2->id_action=="2")
			{
				$wml = $wml."<sendussd ussd='".$row2->action_destination."'/>";
			}				
			else if($row2->id_action=="3")
			{
				$wml = $wml."<setupcall><destaddress value='".$row2->action_destination."'/></setupcall>";
			}				
			else if($row2->id_action=="4")
			{
				$wml = $wml."<launchbrowser url='".$row2->action_destination."'/>";
			}						
			$wml = $wml."</p></card>";
			if($row2->sti=="1")
			{	
				$wml = $wml."<card id='savetoinbox'>";
				$wml = $wml."<go href='".base_url()."Campaign/sti?id=".$primary_key."|_MSISDN_'/>";
				$wml = $wml."</card>";
			}	
		}				
		else if($row2->layer=="2")
		{
			$wml = $wml."<card id='layer1'><p>";
			if($row2->tone=="1")
			{	
				$wml = $wml."<playtone toneid='ringing' duration='".$row2->tone_duration."' title='".$row2->tone_title."'/>";
			}	
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Received</userdata></sendsm>".$row2->layer1_text;
			if($row2->sti=="0")
			{	
				$wml = $wml."<do type='accept'><go href='#layer2'/></do>";
			}	
			else if($row2->sti=="1")
			{	
				$wml = $wml."<select title='Option' name='option'>";
				$wml = $wml."<option value='cont'>Next</option>";
				$wml = $wml."<option value='sti'>Save to Inbox</option>";
				$wml = $wml."</select>";
				$wml = $wml."<conditionaljump compare='$(option)'>";
				$wml = $wml."<test href='#layer2' value='cont'/>";
				$wml = $wml."<test href='#savetoinbox' value='sti'/>";
				$wml = $wml."</conditionaljump>";				
			}
			$wml = $wml."</p></card>";
			if($row2->sti=="1")
			{	
				$wml = $wml."<card id='savetoinbox'>";
				$wml = $wml."<go href='".base_url()."Campaign/sti?id=".$primary_key."|_MSISDN_'/>";
				$wml = $wml."</card>";
			}	
			$wml = $wml."<card id='layer2'><p>";
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer1</userdata></sendsm>".$row2->layer2_text;
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer2</userdata></sendsm>";
			if($row2->id_action=="1")
			{
				$wml = $wml."<sendsm><destaddress value='".$row2->action_destination."'/><userdata>".$row2->sms_text."</userdata></sendsm>";
			}				
			else if($row2->id_action=="2")
			{
				$wml = $wml."<sendussd ussd='".$row2->action_destination."'/>";
			}				
			else if($row2->id_action=="3")
			{
				$wml = $wml."<setupcall><destaddress value='".$row2->action_destination."'/></setupcall>";
			}				
			else if($row2->id_action=="4")
			{
				$wml = $wml."<launchbrowser url='".$row2->action_destination."'/>";
			}						
			$wml = $wml."</p></card>";			
		}				
		else if($row2->layer=="3")
		{
			$wml = $wml."<card id='layer1'><p>";
			if($row2->tone=="1")
			{	
				$wml = $wml."<playtone toneid='ringing' duration='".$row2->tone_duration."' title='".$row2->tone_title."'/>";
			}	
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Received</userdata></sendsm>".$row2->layer1_text;
			if($row2->sti=="0")
			{	
				$wml = $wml."<do type='accept'><go href='#layer2'/></do>";
			}
			else if($row2->sti=="1")
			{	
				$wml = $wml."<select title='Option' name='option'>";
				$wml = $wml."<option value='cont'>Next</option>";
				$wml = $wml."<option value='sti'>Save to Inbox</option>";
				$wml = $wml."</select>";
				$wml = $wml."<conditionaljump compare='$(option)'>";
				$wml = $wml."<test href='#layer2' value='cont'/>";
				$wml = $wml."<test href='#savetoinbox' value='sti'/>";
				$wml = $wml."</conditionaljump>";				
			}
			$wml = $wml."</p></card>";
			if($row2->sti=="1")
			{	
				$wml = $wml."<card id='savetoinbox'>";
				$wml = $wml."<go href='".base_url()."Campaign/sti?id=".$primary_key."|_MSISDN_'/>";
				$wml = $wml."</card>";
			}	
			$wml = $wml."<card id='layer2'><p>";
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer1</userdata></sendsm>".$row2->layer2_text;
			$wml = $wml."<do type='accept'><go href='#layer3'/></do>";
			$wml = $wml."</p></card>";
			$wml = $wml."<card id='layer3'><p>";
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer2</userdata></sendsm>".$row2->layer3_text;
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer3</userdata></sendsm>";
			if($row2->id_action=="1")
			{
				$wml = $wml."<sendsm><destaddress value='".$row2->action_destination."'/><userdata>".$row2->sms_text."</userdata></sendsm>";
			}				
			else if($row2->id_action=="2")
			{
				$wml = $wml."<sendussd ussd='".$row2->action_destination."'/>";
			}				
			else if($row2->id_action=="3")
			{
				$wml = $wml."<setupcall><destaddress value='".$row2->action_destination."'/></setupcall>";
			}				
			else if($row2->id_action=="4")
			{
				$wml = $wml."<launchbrowser url='".$row2->action_destination."'/>";
			}						
			$wml = $wml."</p></card>";
		}				
		$wml = $wml."</wml>";
		$wml = $wml.PHP_EOL;
		$wml = $wml.PHP_EOL;
		$wml = $wml.$randomString."--";
			
		$end_time_campaign = ceil($post_array["total_recipient"]/50000)*30;		
		$selectedTime = $post_array["start_time_campaign"].":00";
		$time=strtotime($selectedTime." +".$end_time_campaign." minutes");
		$date2 = DateTime::createFromFormat( 'd/m/Y', $post_array["start_date_campaign"]);
		$toDay = $date2->format( 'Y-m-d' );
		$nextday = date('Y-m-d', strtotime($toDay .' +1 day'));
		$time=date("H:i",$time);
		if($time<date('H:i',strtotime($selectedTime)))
		{	
			$updateData=array("boundary"=>substr($randomString,2),"wml"=>$wml,"end_time_campaign"=>$time,"end_date_campaign"=>$nextday,"id_state"=>"2");
		}
		else
		{
			$updateData=array("boundary"=>substr($randomString,2),"wml"=>$wml,"end_time_campaign"=>$time,"end_date_campaign"=>$toDay,"id_state"=>"2");
		}		
		$this->db->where("id_campaign",$primary_key);
		$this->db->update('campaign',$updateData);

		$this->extract_data($primary_key);
		
		return true;	 
	}

	function after_update($post_array,$primary_key)
	{		
		$query = $this->db->query("select tone_title, tone_duration, layer, layer1_text, layer2_text, layer3_text, action_destination, id_action, sms_text, sti, sti_text from campaign where id_campaign='".$primary_key."'");
//		$randomString = "----".$this->randomCharacter(8)."-".$this->randomCharacter(4)."-".$this->randomCharacter(4)."-".$this->randomCharacter(4)."-".$this->randomCharacter(12);
		$randomString = "----ab3f9e2d-e0cd-4ec1-8931-307648fdcae9";
		$wml = $wml.$randomString;
		$wml = $wml.PHP_EOL;
		$wml = $wml.PHP_EOL;
		$wml = $wml."Content-type: application/xml";
		$wml = $wml.PHP_EOL;
		$wml = $wml."<?xml version='1.0'?><!DOCTYPE pap PUBLIC \"-//WAPFORUM//DTD PAP 2.0//EN\" \"http://www.wapforum.org/DTD/pap_2.0.dtd\"><pap><push-message push-id=\"".$primary_key."|_MSISDN_\" ppg-notify-requested-to=\"10.162.18.103:8080/wibpush/notify\"><address address-value=\"_MSISDN_\"/><quality-of-service delivery-method=\"confirmed\"/></push-message></pap>";
		$wml = $wml.PHP_EOL;
		$wml = $wml.PHP_EOL;
		$wml = $wml.$randomString;
		$wml = $wml.PHP_EOL;
		$wml = $wml.PHP_EOL;
		$wml = $wml."Content-type: text/vnd.wap.wml";
		$wml = $wml.PHP_EOL;
		$wml = $wml."<?xml version='1.0' encoding='UTF-8'?><wml xmlns='http://www.smarttrust.com/WIG-WML/5.0' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.smarttrust.com/WIG-WML/5.0 http://www.smarttrust.com/xsd/wigwml-5.0.xsd'>";
		$row2 = $query->row();
		if($row2->layer=="1")
		{
			$wml = $wml."<card id='layer1'><p>";
			if($row2->tone=="1")
			{	
				$wml = $wml."<playtone toneid='ringing' duration='".$row2->tone_duration."' title='".$row2->tone_title."'/>";
			}	

			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Received</userdata></sendsm>".$row2->layer1_text;
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer1</userdata></sendsm>";
			if($row2->sti=="1")
			{	
				$wml = $wml."<select title='Option' name='option'>";
				$wml = $wml."<option value='sti'>Save to Inbox</option>";
				$wml = $wml."</select>";
				$wml = $wml."<conditionaljump compare='$(option)'>";
				$wml = $wml."<test href='#savetoinbox' value='sti'/>";
				$wml = $wml."</conditionaljump>";				
			}
			if($row2->id_action=="1")
			{
				$wml = $wml."<sendsm><destaddress value='".$row2->action_destination."'/><userdata>".$row2->sms_text."</userdata></sendsm>";
			}				
			else if($row2->id_action=="2")
			{
				$wml = $wml."<sendussd ussd='".$row2->action_destination."'/>";
			}				
			else if($row2->id_action=="3")
			{
				$wml = $wml."<setupcall><destaddress value='".$row2->action_destination."'/></setupcall>";
			}				
			else if($row2->id_action=="4")
			{
				$wml = $wml."<launchbrowser url='".$row2->action_destination."'/>";
			}						
			$wml = $wml."</p></card>";
			if($row2->sti=="1")
			{	
				$wml = $wml."<card id='savetoinbox'>";
				$wml = $wml."<go href='".base_url()."Campaign/sti?id=".$primary_key."|_MSISDN_'/>";
				$wml = $wml."</card>";
			}	
		}				
		else if($row2->layer=="2")
		{
			$wml = $wml."<card id='layer1'><p>";
			if($row2->tone=="1")
			{	
				$wml = $wml."<playtone toneid='ringing' duration='".$row2->tone_duration."' title='".$row2->tone_title."'/>";
			}	
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Received</userdata></sendsm>".$row2->layer1_text;
			if($row2->sti=="0")
			{	
				$wml = $wml."<do type='accept'><go href='#layer2'/></do>";
			}	
			else if($row2->sti=="1")
			{	
				$wml = $wml."<select title='Option' name='option'>";
				$wml = $wml."<option value='cont'>Next</option>";
				$wml = $wml."<option value='sti'>Save to Inbox</option>";
				$wml = $wml."</select>";
				$wml = $wml."<conditionaljump compare='$(option)'>";
				$wml = $wml."<test href='#layer2' value='cont'/>";
				$wml = $wml."<test href='#savetoinbox' value='sti'/>";
				$wml = $wml."</conditionaljump>";				
			}
			$wml = $wml."</p></card>";
			if($row2->sti=="1")
			{	
				$wml = $wml."<card id='savetoinbox'>";
				$wml = $wml."<go href='".base_url()."Campaign/sti?id=".$primary_key."|_MSISDN_'/>";
				$wml = $wml."</card>";
			}	
			$wml = $wml."<card id='layer2'><p>";
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer1</userdata></sendsm>".$row2->layer2_text;
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer2</userdata></sendsm>";
			if($row2->id_action=="1")
			{
				$wml = $wml."<sendsm><destaddress value='".$row2->action_destination."'/><userdata>".$row2->sms_text."</userdata></sendsm>";
			}				
			else if($row2->id_action=="2")
			{
				$wml = $wml."<sendussd ussd='".$row2->action_destination."'/>";
			}				
			else if($row2->id_action=="3")
			{
				$wml = $wml."<setupcall><destaddress value='".$row2->action_destination."'/></setupcall>";
			}				
			else if($row2->id_action=="4")
			{
				$wml = $wml."<launchbrowser url='".$row2->action_destination."'/>";
			}						
			$wml = $wml."</p></card>";			
		}				
		else if($row2->layer=="3")
		{
			$wml = $wml."<card id='layer1'><p>";
			if($row2->tone=="1")
			{	
				$wml = $wml."<playtone toneid='ringing' duration='".$row2->tone_duration."' title='".$row2->tone_title."'/>";
			}	
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Received</userdata></sendsm>".$row2->layer1_text;
			if($row2->sti=="0")
			{	
				$wml = $wml."<do type='accept'><go href='#layer2'/></do>";
			}
			else if($row2->sti=="1")
			{	
				$wml = $wml."<select title='Option' name='option'>";
				$wml = $wml."<option value='cont'>Next</option>";
				$wml = $wml."<option value='sti'>Save to Inbox</option>";
				$wml = $wml."</select>";
				$wml = $wml."<conditionaljump compare='$(option)'>";
				$wml = $wml."<test href='#layer2' value='cont'/>";
				$wml = $wml."<test href='#savetoinbox' value='sti'/>";
				$wml = $wml."</conditionaljump>";				
			}
			$wml = $wml."</p></card>";
			if($row2->sti=="1")
			{	
				$wml = $wml."<card id='savetoinbox'>";
				$wml = $wml."<go href='".base_url()."Campaign/sti?id=".$primary_key."|_MSISDN_'/>";
				$wml = $wml."</card>";
			}	
			$wml = $wml."<card id='layer2'><p>";
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer1</userdata></sendsm>".$row2->layer2_text;
			$wml = $wml."<do type='accept'><go href='#layer3'/></do>";
			$wml = $wml."</p></card>";
			$wml = $wml."<card id='layer3'><p>";
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer2</userdata></sendsm>".$row2->layer3_text;
			$wml = $wml."<sendsm><destaddress value='12385'/><userdata>".$primary_key.",Layer3</userdata></sendsm>";
			if($row2->id_action=="1")
			{
				$wml = $wml."<sendsm><destaddress value='".$row2->action_destination."'/><userdata>".$row2->sms_text."</userdata></sendsm>";
			}				
			else if($row2->id_action=="2")
			{
				$wml = $wml."<sendussd ussd='".$row2->action_destination."'/>";
			}				
			else if($row2->id_action=="3")
			{
				$wml = $wml."<setupcall><destaddress value='".$row2->action_destination."'/></setupcall>";
			}				
			else if($row2->id_action=="4")
			{
				$wml = $wml."<launchbrowser url='".$row2->action_destination."'/>";
			}						
			$wml = $wml."</p></card>";
		}				
		$wml = $wml."</wml>";
		$wml = $wml.PHP_EOL;
		$wml = $wml.PHP_EOL;
		$wml = $wml.$randomString."--";
			
		$end_time_campaign = ceil($post_array["total_recipient"]/50000)*30;		
		$selectedTime = $post_array["start_time_campaign"].":00";
		$time=strtotime($selectedTime." +".$end_time_campaign." minutes");
		$date2 = DateTime::createFromFormat( 'd/m/Y', $post_array["start_date_campaign"]);
		$toDay = $date2->format( 'Y-m-d' );
		$nextday = date('Y-m-d', strtotime($toDay .' +1 day'));
		$time=date("H:i",$time);
		if($time<date('H:i',strtotime($selectedTime)))
		{	
			$updateData=array("boundary"=>substr($randomString,2),"wml"=>$wml,"end_time_campaign"=>$time,"end_date_campaign"=>$nextday);
		}
		else
		{
			$updateData=array("boundary"=>substr($randomString,2),"wml"=>$wml,"end_time_campaign"=>$time,"end_date_campaign"=>$toDay);
		}		
		$this->db->where("id_campaign",$primary_key);
		$this->db->update('campaign',$updateData);
		
	}
	
	
	public function extract_data($id)
	{

		$query = $this->db->query("SELECT id_campaign as id_campaign, name_file, name_campaign, type, FORMAT(total_recipient,0) AS total_recipient, start_date_campaign, start_time_campaign, email, name from campaign b, user c where b.created_by = c.id_user and id_campaign='".$id."'");
		$row = $query->row();

		//$query = $this->db->query("LOAD DATA LOCAL INFILE '".APP_PATH."assets/uploads/".$row->name_file."' INTO TABLE recipient FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' LINES TERMINATED BY '\n' (msisdn, id_campaign, id_state)");
		//$query = $this->db->query("update recipient set thread=(RAND()*(9)+1), id_campaign = '".$row->id_campaign."' where id_campaign=0");
		//$query = $this->db->query("delete from recipient where msisdn in (select msisdn from blacklist)");
		
		$email_approver = "";
		$query2 = $this->db->query("select email from user where id_group=2");
		foreach ($query2->result() as $row2)
		{			
			$email_approver = $email_approver.$row2->email.",";
		}
		$email_approver=rtrim($email_approver,",");
		$email_approver=$email_approver;
		
		if($row->type=="0")
		{	
			$row->type="Manual";
		}
		else if($row->type=="1")
		{
			$row->type="Automatic";
		}					
		$this->email->from('dpwibpushapp@xl.co.id', 'WIBPUSH Administrator');
		$this->email->to($email_approver);
		$this->email->subject('Approval Request for Campaign : '.$row->name_campaign);
		$file=fopen(APP_PATH."assets/campaign_approval.html", "r") or die("Unable to open file!");
		$content=fread($file,filesize(APP_PATH."assets/campaign_approval.html"));
		$content_text = htmlentities($content);
		$content_text=str_replace("_admin_email","dpwibpushapp@xl.co.id",$content_text);
		$content_text=str_replace("_name_campaign",$row->name_campaign,$content_text);
		$content_text=str_replace("_name",$row->name,$content_text);
		$content_text=str_replace("_email",$row->email,$content_text);
		$content_text=str_replace("_id_campaign",$row->id_campaign,$content_text);
		$content_text=str_replace("_type",$row->type,$content_text);
		$content_text=str_replace("_total_recipient",$row->total_recipient,$content_text);
		$content_text=str_replace("_start_date_campaign",$row->start_date_campaign,$content_text);
		$content_text=str_replace("_start_time_campaign",$row->start_time_campaign,$content_text);
		$content_text=str_replace("_approved","xxx",$content_text);
		$content_text=str_replace("_reject","yyy",$content_text);
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
				
	}

	
	public function valid_files($files_to_upload, $field_info)
	{
		$type=$files_to_upload[$field_info->encrypted_field_name]['type'];
		/*if ($type!= 'text/plain')
  	    {
			return 'You can upload only Text File';
		}*/
		return true;
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

/*	function send_approval($primary_key , $row)
	{
		return true;
	}*/

	function layer2_text($post_array) 
	{		
		if($_POST['layer']=="2" || $_POST['layer']=="3")
		{
			if(trim($_POST['layer2_text'])=="")
			{
				$this->form_validation->set_message('layer2_text', 'Please fill Layer-2 (Text)');
				return FALSE;
			}				
			else
			{
				return TRUE;
			}				
		}			
	}
	
	function tone_duration($post_array) 
	{		
		if($_POST['tone_duration']=="0" && $_POST['tone']=="1")
		{
			$this->form_validation->set_message('tone_duration', 'Tone Duration can\'t be 0');
			return FALSE;
		}			
		else
		{
			return TRUE;
		}			
	}

	function tone_title($post_array) 
	{		
		if($_POST['tone_title']=="" && $_POST['tone']=="1")
		{
			$this->form_validation->set_message('tone_title', 'Tone Title can\'t be blank');
			return FALSE;
		}			
		else
		{
			return TRUE;
		}			
	}

	function total_recipient($post_array) 
	{		
		if($_POST['total_recipient']!="")
		{
			if($_POST['conflict']!="")
			{
				$this->form_validation->set_message('total_recipient', $_POST['conflict']);
				return FALSE;
			}			
			else
			{
				if($_POST['total_recipient']=="0")
				{	
					$this->form_validation->set_message('total_recipient', "Total Recipient can't be 0");
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
				$this->form_validation->set_message('total_recipient', "Please fill the Total Recipient");
				return FALSE;
		}			
		
	}	
	
	function layer($post_array) 
	{		
		if($_POST['layer']=="1" && $_POST['layer']=="1" && $_POST['id_cation']=="1")
		{
			$this->form_validation->set_message('layer', 'We can\'t have Save to Inbox together with Action (Final) at Layer1');
			return FALSE;
		}			
		else
		{
			return TRUE;
		}				
	}

	function sti_text($post_array) 
	{		
		if($_POST['sti']=="1")
		{
			if(trim($_POST['sti_text'])=="")
			{
				$this->form_validation->set_message('sti_text', 'Please fill Save to Inbox (SMS)');
				return FALSE;
			}				
			else
			{
				return TRUE;
			}				
		}			
	}


	function layer3_text($post_array) 
	{		
		if($_POST['layer']=="3")
		{
			if(trim($_POST['layer3_text'])=="")
			{
				$this->form_validation->set_message('layer3_text', 'Please fill Layer-3 (Text)');
				return FALSE;
			}				
			else
			{
				return TRUE;
			}				
		}			
	}

	function sms_text($post_array) 
	{		
		if($_POST['id_action']=="1")
		{
			if(trim($_POST['sms_text'])=="")
			{
				$this->form_validation->set_message('sms_text', 'Please fill SMS');
				return FALSE;
			}				
			else
			{
				return TRUE;
			}				
		}			
	}

	public function getConflict()
	{
		$conflict2 = 0;
		if(isset($_GET['primary_key']))
		{	
			$conflict2=$this->db->count_all("campaign WHERE id_state!=7 and id_state!=6 and id_state!=8 and id_campaign!=".$_GET['primary_key']." and ((start_date_campaign='".$_GET['start_date']."' AND start_time_campaign<'".$_GET['end_time']."' AND end_time_campaign>='".$_GET['end_time']."') OR (start_date_campaign='".$_GET['start_date']."' AND start_time_campaign<'".$_GET['end_time']."' AND end_time_campaign<='".$_GET['end_time']."' and start_time_campaign>'".$_GET['start_time']."'))");
		}
		else
		{
			$conflict2=$this->db->count_all("campaign WHERE id_state!=7 and id_state!=6 and id_state!=8 and ((start_date_campaign='".$_GET['start_date']."' AND start_time_campaign<'".$_GET['end_time']."' AND end_time_campaign>='".$_GET['end_time']."') OR (start_date_campaign='".$_GET['start_date']."' AND start_time_campaign<'".$_GET['end_time']."' AND end_time_campaign<='".$_GET['end_time']."' and start_time_campaign>'".$_GET['start_time']."'))");
		}			
		echo $conflict2;
	}

	function checkDestination($post_array) 
	{		
		if($_POST['id_action']!=0)
		{	
			if(trim($_POST['action_destination'])=="")
			{
				$this->form_validation->set_message('checkDestination', 'Please fill Destination');
				return FALSE;				
			}
			else
			{		
				if($_POST['id_action']=="1" || $_POST['id_action']=="3")
				{	
					if(preg_match('/^[0-9]+$/',$_POST['action_destination'])) 
					{
						return TRUE;
					}
					else
					{
						$this->form_validation->set_message('checkDestination', 'Please check your Destination Format');
						return FALSE;
					}
				}	
				else if($_POST['id_action']=="2")
				{	
					if(preg_match('/^\*[0-9]+([0-9*#])*#$/',$_POST['action_destination'])) 
					{
						return TRUE;
					}
					else
					{
						$this->form_validation->set_message('checkDestination', 'Please check your Destination Format');
						return FALSE;
					}
				}
			}	
		}
	}

	public function text($value, $row)
	{
		$query = $this->db->query("select concat(if(layer1_text!=\"\" and layer>=1,concat('<div class=\'alert alert-success\'><b>1</b> : ',layer1_text,'</div>'),\"\"),if(layer2_text!=\"\"  and layer>=2,concat('<div class=\'alert alert-warning\'><b>2</b> : ',layer2_text,'</div>'),\"\"),if(layer3_text!=\"\" and layer=3,concat('<div class=\'alert alert-danger\'><b>3</b> : ',layer3_text,'</div>'),\"\"),if(sti_text!=\"\" and sti=1,concat('<button class=\'btn btn-default\'><i style=\'font-size:18px\' class=\'fa fa-mobile\'></i> : ',sti_text,'</button></p><p>'),\"\"),if(action_destination!=\"\",concat('<button class=\'btn btn-light\'>',if(id_action=0,'',if(id_action=1,concat('<i class=\'fa fa-sms\'></i> : ',action_destination),if(id_action=2,concat('<i class=\'fa fa-phone\'></i> : ',action_destination),if(id_action=3,'<i class=\'fa fa-phone\'></i> : ',concat('<i class=\'fa fa-internet-explorer\'></i> ',action_destination))))),'</b></button>'),\"\"),if(id_action=1,concat(' <button class=\'btn btn-light\'> : ',sms_text,'</button>'),\"\"),if(tone=1,concat('<div class=\'alert alert-info\'><b><i class=\'fa fa-volume-up\'></i></b> : ',tone_title,' (',tone_duration,' s)</div>'),\"\")) as text from campaign where id_campaign='".$row->id_campaign."'");
		$row2 = $query->row();

		$button=$row2->text;
			
		return $button;
			
	}
	
	
}
