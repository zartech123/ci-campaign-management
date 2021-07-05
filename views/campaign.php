<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="<?php echo base_url(); ?>assets/js/Moment.js"></script>
<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
</head>
<body>
	<?php echo $output; ?>
    <?php foreach($js_files as $file): ?>
        <script src="<?php echo $file; ?>"></script>
    <?php endforeach; ?>
	<!--table width="100%">
	<tr>
	<td><iframe id="iframe1" src="<?php echo base_url(); ?>Report2?menu=0" style="height:600px;width:100%;border:none;overflow:hidden;"></iframe></td>
	<td><iframe id="iframe2" src="<?php echo base_url(); ?>Report3?menu=0" style="height:600px;width:100%;border:none;overflow:hidden;"></iframe></td>
	</tr>
	</table-->
</body>
<script>
$(function(){

	
	var pathname = $(location).attr('pathname');
	var read=pathname.split('/');
	

  $(document).ready(function()
	{
	
	$("#field-layer1_text").attr('maxlength','110');
		$("#field-layer2_text").attr('maxlength','110');
		$("#field-layer3_text").attr('maxlength','110');
		$("#field-layer1_text").after('<i>&nbsp;110 Characters</i>');
		$("#field-layer2_text").after('<i>&nbsp;110 Characters</i>');
		$("#field-layer3_text").after('<i>&nbsp;110 Characters</i>');
		$("#field-sms_text").after('<i>&nbsp;110 Characters</i>');
		$("#field-sti_text").after('<i>&nbsp;110 Characters</i>');
		$("#field-name_campaign").css("width","360");
		$("#field-total_recipient").css("width","100");
		$("#field-start_date_campaign").css("width","100");
		$("#field-start_time_campaign").css("width","100");
		$("#field-layer1_text").css("width","360");
		$("#field-sms_text").css("width","360");
		$("#field-action_destination").css("width","360");
		$("#field-layer2_text").css("width","360");
		$("#field-layer3_text").css("width","360");
		$("#field-sti_text").css("width","360");
		$("#field-tone_duration").css("width","50");
		$("#field-tone_title").css("width","240");
		$("#field-start_date_campaign").attr("readonly","readonly");
		$("input[name='text']").hide();
		$("input[name='type']").hide();
		$('#field-conflict').hide();
		$("div.form-group.conflict_form_group").hide();
		
		$("#field-total_recipient").on("change",function()
		{
			var end_time_campaign = Math.ceil($("#field-total_recipient").val()/50000)*30;
			var date2 = $("#field-start_date_campaign").val().split("/");
			var selectedTime = date2[2]+"-"+date2[1]+"-"+date2[0]+" "+$("#field-start_time_campaign").val()+":00";
			var date1 = new Date(selectedTime);
			var newDateObj = moment(date1).add(end_time_campaign, 'm').toDate();
			var date3 = newDateObj.getFullYear()+"-"+(newDateObj.getMonth()<10?'0':'')+ (newDateObj.getMonth()+1)+"-"+(newDateObj.getDate()<10?'0':'')+ newDateObj.getDate();
			var time = (newDateObj.getHours()<10?'0':'') + newDateObj.getHours()+":"+(newDateObj.getMinutes()<10?'0':'') + newDateObj.getMinutes();
			
			$("#field-conflict").val("");
			if(read[4]=="edit")
			{
				var id=read[5];
			  $.ajax({
			  		url: "<?php echo base_url(); ?>Campaign/getConflict?primary_key="+id+"&start_time="+$("#field-start_time_campaign").val()+"&start_date="+date2[2]+"-"+date2[1]+"-"+date2[0]+"&end_time="+time,
						type: "GET",
						dataType: "text",
			  		success: function(response){
						if(response>0)
						{	
							$("#field-conflict").val("The Campaign Schedule is Conflict with others, Please change the Schedule or Total Recipient");
						}
						else if(time>'21:00' || new Date(date3)>new Date(date2[2]+"-"+date2[1]+"-"+date2[0]))
						{
							$("#field-conflict").val("The Campaign should be running until 21:00 at the latest, Please change the Schedule or Total Recipient");
						}				
						else
						{
							$("#field-conflict").val("");
						}							
							
					},
			  		error: function(response)
			  		{
			  		},
			  });
			}
			else
			{
			  $.ajax({
			  		url: "<?php echo base_url(); ?>Campaign/getConflict?start_time="+$("#field-start_time_campaign").val()+"&start_date="+date2[2]+"-"+date2[1]+"-"+date2[0]+"&end_time="+time,
						type: "GET",
						dataType: "text",
			  		success: function(response){
						
						if(response>0)
						{	
							$("#field-conflict").val("The Campaign Schedule is Conflict with others, Please change the Schedule or Total Recipient");
						}
						else if(time>'21:00' || new Date(date3)>new Date(date2[2]+"-"+date2[1]+"-"+date2[0]))
						{
							$("#field-conflict").val("The Campaign should be running until 21:00 at the latest, Please change the Schedule or Total Recipient");
						}				
						else
						{
							$("#field-conflict").val("");
						}							
							
					},
			  		error: function(response)
			  		{
			  		},
			  });				
			}	
		});
		
		$('#field-start_time_campaign').on('change', function() 
		{
			var end_time_campaign = Math.ceil($("#field-total_recipient").val()/50000)*30;
			var date2 = $("#field-start_date_campaign").val().split("/");
			var selectedTime = date2[2]+"-"+date2[1]+"-"+date2[0]+" "+$("#field-start_time_campaign").val()+":00";
			var date1 = new Date(selectedTime);
			var newDateObj = moment(date1).add(end_time_campaign, 'm').toDate();
			var date3 = newDateObj.getFullYear()+"-"+(newDateObj.getMonth()<10?'0':'')+ (newDateObj.getMonth()+1)+"-"+(newDateObj.getDate()<10?'0':'')+ newDateObj.getDate();
			var time = (newDateObj.getHours()<10?'0':'') + newDateObj.getHours()+":"+(newDateObj.getMinutes()<10?'0':'') + newDateObj.getMinutes();
			
			$("#field-conflict").val("");
			if(read[4]=="edit")
			{
			  var id=read[5];
			  $.ajax({
			  		url: "<?php echo base_url(); ?>Campaign/getConflict?primary_key="+id+"&start_time="+$("#field-start_time_campaign").val()+"&start_date="+date2[2]+"-"+date2[1]+"-"+date2[0]+"&end_time="+time,
						type: "GET",
						dataType: "text",
			  		success: function(response){
						if(response>0)
						{	
							$("#field-conflict").val("The Campaign Schedule is Conflict with others, Please change the Schedule or Total Recipient");
						}
						else if(time>'21:00' || new Date(date3)>new Date(date2[2]+"-"+date2[1]+"-"+date2[0]))
						{
							$("#field-conflict").val("The Campaign should be running until 21:00 at the latest, Please change the Schedule or Total Recipient");
						}				
						else
						{
							$("#field-conflict").val("");
						}							
							
					},
			  		error: function(response)
			  		{
			  		},
			  });
			}
			else
			{
			  $.ajax({
			  		url: "<?php echo base_url(); ?>Campaign/getConflict?start_time="+$("#field-start_time_campaign").val()+"&start_date="+date2[2]+"-"+date2[1]+"-"+date2[0]+"&end_time="+time,
						type: "GET",
						dataType: "text",
			  		success: function(response){
						if(response>0)
						{	
							$("#field-conflict").val("The Campaign Schedule is Conflict with others, Please change the Schedule or Total Recipient");
						}
						else if(time>'21:00' || new Date(date3)>new Date(date2[2]+"-"+date2[1]+"-"+date2[0]))
						{
							$("#field-conflict").val("The Campaign should be running until 21:00 at the latest, Please change the Schedule or Total Recipient");
						}				
						else
						{
							$("#field-conflict").val("");
						}							
							
					},
			  		error: function(response)
			  		{
			  		},
			  });
			}				
		});
		
		$("#field-start_date_campaign").on("change",function()
		{
			var parts = $("#field-start_date_campaign").val().split('/');
			if(new Date(parts[2]+"-"+parts[1]+"-"+parts[0])<new Date(new Date().getFullYear()+"-"+(new Date().getMonth()<10?'0':'')+ (new Date().getMonth()+1)+"-"+(new Date().getDate()<10?'0':'')+ new Date().getDate()))
			{
				$("#field-start_date_campaign").val("");
			}
				
			  $.ajax({
			  		url: "<?php echo base_url(); ?>Campaign/getStartTime?id="+$("#field-start_date_campaign").val(),
						type: "GET",
						dataType: "text",
			  		success: function(response){
						var start_time_campaign = "";
			  			var json = $.parseJSON(response);
						if(json.length>0)
						{
							$('#field-start_time_campaign').empty(); 
							$('#field-start_time_campaign').trigger("chosen:updated");
						}							
						for (var i=0;i<json.length;++i)
						{
							if(i==0)
							{
								start_time_campaign = json[i];
							}								
							$('#field-start_time_campaign').append('<option value="'+json[i]+'">'+json[i]+'</option>');
							$('#field-start_time_campaign').trigger("chosen:updated");
						}
						if(start_time_campaign!="")	
						{	
							$('#field-start_time_campaign').val(start_time_campaign).trigger('chosen:updated');

							var end_time_campaign = Math.ceil($("#field-total_recipient").val()/50000)*30;
							var date2 = $("#field-start_date_campaign").val().split("/");
							var selectedTime = date2[2]+"-"+date2[1]+"-"+date2[0]+" "+$("#field-start_time_campaign").val()+":00";
							var date1 = new Date(selectedTime);
							var newDateObj = moment(date1).add(end_time_campaign, 'm').toDate();
							var date3 = newDateObj.getFullYear()+"-"+(newDateObj.getMonth()<10?'0':'')+ (newDateObj.getMonth()+1)+"-"+(newDateObj.getDate()<10?'0':'')+ newDateObj.getDate();
							var time = (newDateObj.getHours()<10?'0':'') + newDateObj.getHours()+":"+(newDateObj.getMinutes()<10?'0':'') + newDateObj.getMinutes();
							
							$("#field-conflict").val("");

							  $.ajax({
									url: "<?php echo base_url(); ?>Campaign/getConflict?start_time="+$("#field-start_time_campaign").val()+"&start_date="+date2[2]+"-"+date2[1]+"-"+date2[0]+"&end_time="+time,
										type: "GET",
										dataType: "text",
									success: function(response){
										if(response>0)
										{	
											$("#field-conflict").val("The Campaign Schedule is Conflict with others, Please change the Schedule or Total Recipient");
										}
										else if(time>'21:00' || new Date(date3)>new Date(date2[2]+"-"+date2[1]+"-"+date2[0]))
										{
											$("#field-conflict").val("The Campaign should be running until 21:00 at the latest, Please change the Schedule or Total Recipient");
										}				
										else
										{
											$("#field-conflict").val("");
										}							
											
									},
									error: function(response)
									{
									},
							  });
							
							
						}
					},
			  		error: function(response)
			  		{
			  		},
			  });
//			$('#field-start_time_campaign').val('2').trigger('chosen:updated');
			
		});
		
		if(read[4]=="add")
		{			
//			$("#iframe1").hide();
//			$("#iframe2").hide();
			$("#field-tone_title").val("XL AXIATA");
		}
		
		if(read[4]=="edit")
		{
//			$("#iframe1").hide();
//			$("#iframe2").hide();
			var id=read[5];
			  $.ajax({
			  		url: "<?php echo base_url(); ?>Campaign/getState?id="+id,
						type: "GET",
						dataType: "text",
			  		success: function(response){
			  			var json = $.parseJSON(response);
						if(json[0].id!="1" && json[0].id!="2")
						{
							$("#form-button-save").hide();
							$("#save-and-go-back-button").hide();
							$("div.form-group.name_file_form_group").hide();
						}
						else if(json[0].id!="1")
						{
							$("div.form-group.name_file_form_group").hide();
							$("div.form-group.start_date_campaign_form_group").hide();
							$("div.form-group.total_recipient_form_group").hide();
							$("div.form-group.start_time_campaign_form_group").hide();
						}								
			  		},
			  		error: function(response)
			  		{
			  		},
			  });

			  var id_start_time = $('#field-start_time_campaign').val();
			  
			  $.ajax({
			  		url: "<?php echo base_url(); ?>Campaign/getStartTime?primary_key="+id+"&id="+$("#field-start_date_campaign").val(),
						type: "GET",
						dataType: "text",
			  		success: function(response){
			  			var json = $.parseJSON(response);
						if(json.length>0)
						{
							$('#field-start_time_campaign').empty(); 
							$('#field-start_time_campaign').trigger("chosen:updated");
						}							
						for (var i=0;i<json.length;++i)
						{
							$('#field-start_time_campaign').append('<option value="'+json[i]+'">'+json[i]+'</option>');
							$('#field-start_time_campaign').trigger("chosen:updated");
						}
						$('#field-start_time_campaign').val(id_start_time).trigger('chosen:updated');
					},
			  		error: function(response)
			  		{
			  		},
			  });

	  }			
		
		if($('#field-layer').val()!="")
		{
			if($('#field-layer').val()=='1')
			{
				$('#field-layer2_text').hide();
				$('#field-layer3_text').hide();
				$("div.form-group.layer2_text_form_group").hide();
				$("div.form-group.layer3_text_form_group").hide();
			}			
			else if($('#field-layer').val()=='2')
			{
				$('#field-layer2_text').show();
				$('#field-layer3_text').hide();			
				$("div.form-group.layer2_text_form_group").show();
				$("div.form-group.layer3_text_form_group").hide();
			}
			else if($('#field-layer').val()=='3')
			{
				$('#field-layer2_text').show();
				$('#field-layer3_text').show();			
				$("div.form-group.layer2_text_form_group").show();
				$("div.form-group.layer3_text_form_group").show();
			}
		}
		else
		{
			$('#field-layer').val(1).trigger('chosen:updated');
			if($('#field-layer').val()=='1')
			{
				$('#field-layer2_text').hide();
				$('#field-layer3_text').hide();
				$("div.form-group.layer2_text_form_group").hide();
				$("div.form-group.layer3_text_form_group").hide();
				
			}	
		}	

		if($('#field-sti').val()!="")
		{
			if($('#field-sti').val()=='0')
			{
				$("div.form-group.sti_text_form_group").hide();
			}			
			else if($('#field-sti').val()=='1')
			{
				$("div.form-group.sti_text_form_group").show();
			}
		}
		else
		{
			$('#field-sti').val(0).trigger('chosen:updated');
			if($('#field-sti').val()=='0')
			{
				$("div.form-group.sti_text_form_group").hide();
			}			
		}			

		if($('#field-tone').val()!="")
		{
			if($('#field-tone').val()=='0')
			{
				$("div.form-group.tone_duration_form_group").hide();
				$("div.form-group.tone_title_form_group").hide();
			}			
			else if($('#field-tone').val()=='1')
			{
				$("div.form-group.tone_duration_form_group").show();
				$("div.form-group.tone_title_form_group").show();
			}
		}
		else
		{
			$('#field-tone').val(0).trigger('chosen:updated');
			if($('#field-tone').val()=='0')
			{
				$("div.form-group.tone_duration_form_group").hide();
				$("div.form-group.tone_title_form_group").hide();
			}			
		}			

		if($('#field-id_action').val()!="")
		{
			if($('#field-id_action').val()=='0')
			{
				$('#field-sms_text').hide();
				$('#field-action_destination').hide();
				$("div.form-group.sms_text_form_group").hide();
				$("div.form-group.action_destination_form_group").hide();
			}			
			else if($('#field-id_action').val()=='1')
			{
				$('#field-sms_text').show();
				$('#field-action_destination').show();
				$("div.form-group.sms_text_form_group").show();
				$("div.form-group.action_destination_form_group").show();
				$("#field-action_destination").attr('maxlength','13');
			}
			else if($('#field-id_action').val()=='2')
			{
				$('#field-action_destination').show();
				$('#field-sms_text').hide();
				$("div.form-group.sms_text_form_group").hide();
				$("div.form-group.action_destination_form_group").show();
				$("#field-action_destination").attr('maxlength','13');				
			}			
			else if($('#field-id_action').val()=='3')
			{
				$('#field-action_destination').show();
				$('#field-sms_text').hide();
				$("div.form-group.sms_text_form_group").hide();
				$("div.form-group.action_destination_form_group").show();
				$("#field-action_destination").attr('maxlength','13');
			}			
			else
			{
				$('#field-action_destination').show();
				$('#field-sms_text').hide();
				$("div.form-group.sms_text_form_group").hide();
				$("div.form-group.action_destination_form_group").show();
				$("#field-action_destination").attr('maxlength','200');
			}			
		}
		else
		{
			$('#field-id_action').val(0).trigger('chosen:updated');
			if($('#field-id_action').val()=='0')
			{
				$('#field-sms_text').hide();
				$('#field-action_destination').hide();
				$("div.form-group.sms_text_form_group").hide();
				$("div.form-group.action_destination_form_group").hide();
			}			
		}					


	});
	
		
  	$('#field-id_action').on('change', function() 
	{
		if($('#field-id_action').val()=='0')
		{
			$('#field-sms_text').hide();
			$('#field-action_destination').hide();
			$("div.form-group.sms_text_form_group").hide();
			$("div.form-group.action_destination_form_group").hide();
		}			
		else if($('#field-id_action').val()=='1')
		{
			$('#field-sms_text').show();
			$('#field-action_destination').show();
			$("div.form-group.sms_text_form_group").show();
			$("div.form-group.action_destination_form_group").show();
			$("#field-action_destination").attr('maxlength','13');
		}
		else if($('#field-id_action').val()=='2')
		{
			$('#field-action_destination').show();
			$('#field-sms_text').hide();
			$("div.form-group.sms_text_form_group").hide();
			$("div.form-group.action_destination_form_group").show();
			$("#field-action_destination").attr('maxlength','13');
		}			
		else if($('#field-id_action').val()=='3')
		{
			$('#field-action_destination').show();
			$('#field-sms_text').hide();
			$("div.form-group.sms_text_form_group").hide();
			$("div.form-group.action_destination_form_group").show();
			$("#field-action_destination").attr('maxlength','13');
		}			
		else
		{
			$('#field-action_destination').show();
			$('#field-sms_text').hide();
			$("div.form-group.sms_text_form_group").hide();
			$("div.form-group.action_destination_form_group").show();
			$("#field-action_destination").attr('maxlength','200');
		}			
	});  

  	$('#field-sti').on('change', function() 
	{
		if($('#field-sti').val()=='0')
		{
			$("div.form-group.sti_text_form_group").hide();
		}			
		else if($('#field-sti').val()=='1')
		{
			$("div.form-group.sti_text_form_group").show();
		}
	});  


	
  	$('#field-tone').on('change', function() 
	{
		if($('#field-tone').val()=='0')
		{
			$("div.form-group.tone_duration_form_group").hide();
			$("div.form-group.tone_title_form_group").hide();
		}			
		else if($('#field-tone').val()=='1')
		{
			$("div.form-group.tone_duration_form_group").show();
			$("div.form-group.tone_title_form_group").show();
		}
	});  

  	$('#field-layer').on('change', function() 
	{
		if($('#field-layer').val()=='1')
		{
			$('#field-layer2_text').hide();
			$('#field-layer3_text').hide();
			$("div.form-group.layer2_text_form_group").hide();
			$("div.form-group.layer3_text_form_group").hide();
		}			
		else if($('#field-layer').val()=='2')
		{
			$('#field-layer2_text').show();
			$('#field-layer3_text').hide();			
			$("div.form-group.layer2_text_form_group").show();
			$("div.form-group.layer3_text_form_group").hide();
		}
		else if($('#field-layer').val()=='3')
		{
			$('#field-layer2_text').show();
			$('#field-layer3_text').show();			
			$("div.form-group.layer2_text_form_group").show();
			$("div.form-group.layer3_text_form_group").show();
		}
	});  
		
});
</script>
</html>
