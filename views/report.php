<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="<?php echo base_url(); ?>assets/js/timetable.js"></script>
	<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/timetablejs.css">
	<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/demo.css"/>
	<script src="<?php echo base_url(); ?>assets/js/chosen.jquery.js"></script>
	<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/chosen.css"/>
	<script src="<?php echo base_url(); ?>assets/js/bootstrap-datepicker.js"></script>
	<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datepicker.css"/>
<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>

	</head>
<body>
    <div class="container-fluid gc-container">
		<table width="100%">
		<tr><td colspan=6><center><div class="table-label">&nbsp;Broadcast Response</div></center></td></tr>
		<tr><td colspan=6>&nbsp;</td></tr>
		<tr>
			<td>
				<select id="range" class="chosen-select" size="8" data-placeholder="Date Range">
				<option value=1>To Day</options>									
				<option value=2>Last 7 Days</options>									
				<option value=3>Last 30 Days</options>									
				<option value=4>This Month</options>									
				<option value=5>Custom</options>									
				</select>
			</td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td>
			<div id="start_date" class="input-group date" data-date-format="dd-mm-yyyy">
				<span class="input-group-addon">Start Date : </span>
				<input class="form-control" type="text" readonly value="Start Date"/>
				<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
			</div>
			</td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td>
			<div id="end_date" class="input-group date" data-date-format="dd-mm-yyyy">
				<span class="input-group-addon">End Date : </span>
				<input class="form-control" type="text" readonly value="End Date"/>
				<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
			</div>
			</td>
			<td width="100%">&nbsp;</td>
		</tr>
		<tr><td colspan=6><hr></td></tr>
		<tr><td colspan=6><div class="timetable"></div></td></tr>																	
		<tr><td colspan=6><hr></td></tr>
		</table>
	</div>
	<table width="100%">
		<tr><iframe id="iframe1" src="<?php echo base_url(); ?>Campaign4?menu=0" style="height:600px;width:100%;border:none;overflow:hidden;"></iframe></td></tr>
	</table>	
</body>
<style type="text/css">
</style>
<script>

						var timetable = new Timetable();
						var renderer = new Timetable.Renderer(timetable);
						timetable.setScope(8, 23); // optional, only whole hours between 0 and 23
						renderer.draw('.timetable'); // any css selector

function remove_duplicates(arr) {
    var obj = {};
    var ret_arr = [];
    for (var i = 0; i < arr.length; i++) {
        obj[arr[i]] = true;
    }
    for (var key in obj) {
        ret_arr.push(key);
    }
    return ret_arr;
}

$(function(){


	$("div.input-group.date").hide();
	$("#start_date").css("width","240");	
	$("#end_date").css("width","240");	
	$("#start_date").datepicker({ 
			autoclose: true, 
			todayHighlight: true
	  }).datepicker('update', new Date());
	$("#end_date").datepicker({ 
			autoclose: true, 
			todayHighlight: true
	  }).datepicker('update', new Date());
	var toDay = new Date();
	var toDay2 = toDay.getFullYear()+"-"+(toDay.getMonth()<10?'0':'')+ (toDay.getMonth()+1)+"-"+(toDay.getDate()<10?'0':'')+ toDay.getDate();
	var start_date;
	var end_date;
	
	var day_name = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
	
	


	  $("#start_date").val(toDay2);
	  $("#end_date").val(toDay2);
$(".chosen-select").chosen();
$('#range').val('2').trigger('chosen:updated');

			  $.ajax({
			  		url: "<?php echo base_url(); ?>Report/getCampaign?type=2",
						type: "GET",
						dataType: "text",
			  		success: function(response)
					{
						var timetable = new Timetable();
						timetable.setScope(8, 23); // optional, only whole hours between 0 and 23
						var json = $.parseJSON(response);
						var start_time = [];
						var end_time = [];
						var start_date = [];
						var name = [];
						var type = [];
						var start_date2 = [];
						var total = [];
						var id_campaign = "";
						for (var i=0;i<json.length;++i)
						{							
							start_time.push(json[i].start_time);
							end_time.push(json[i].end_time);
							start_date.push(json[i].start_date);
							name.push(json[i].name);
							type.push(json[i].type);
							total.push(json[i].total_recipient);
							id_campaign=id_campaign+json[i].id_campaign+"|";	
												//alert(json[i].name+' '+json[i].start_time+' '+json[i].end_time+' '+json[i].start_date);
						}
						$("#iframe1").attr("src","http://localhost/wibpush/Campaign4?menu=0&id_campaign="+id_campaign);
						start_date2 = remove_duplicates(start_date);
						timetable.addLocations(start_date2);
						//alert(start_date2.length);
						for (var i=0;i<start_date2.length;++i)
						{				
							for (var j=0;j<name.length;++j)
							{
								var name2 = name[j].split('|');
								var start_time2 = start_time[j].split(':');
								var end_time2 = end_time[j].split(':');
								var legend = name2[1].trim()+' ('+total[i]+')';
								if(name2[0]==start_date2[i])
								{
									if(type[j]=="Manual")
									{	
										timetable.addEvent(legend, start_date2[i], new Date(2015,7,17,start_time2[0].trim(),start_time2[1].trim()), new Date(2015,7,17,end_time2[0].trim(),end_time2[1].trim()),'#');
									}
									else if(type[j]=="Automatic")
									{	
										timetable.addEvent(legend, start_date2[i], new Date(2015,7,17,start_time2[0].trim(),start_time2[1].trim()), new Date(2015,7,17,end_time2[0].trim(),end_time2[1].trim()),{ class: 'vip-only' });
									}
								}									
							}								
						}
						var renderer = new Timetable.Renderer(timetable);
						renderer.draw('.timetable'); // any css selector
						
					},
			  		error: function(response)
			  		{
			  		},
			  });


$('#start_date').datepicker().on('changeDate', function(e) {
		start_date = e.format(0,"yyyy-mm-dd");
//		end_date = $("#end_date").val();

			  $.ajax({
			  		url: "<?php echo base_url(); ?>Report/getCampaign?type=5&start_date="+start_date+"&end_date="+end_date,
						type: "GET",
						dataType: "text",
			  		success: function(response)
					{
						var timetable = new Timetable();
						timetable.setScope(8, 23); // optional, only whole hours between 0 and 23
						var json = $.parseJSON(response);
						var start_time = [];
						var end_time = [];
						var start_date = [];
						var name = [];
						var type = [];
						var start_date2 = [];
						var total = [];
						var id_campaign = "";
						for (var i=0;i<json.length;++i)
						{							
							start_time.push(json[i].start_time);
							end_time.push(json[i].end_time);
							start_date.push(json[i].start_date);
							name.push(json[i].name);
							type.push(json[i].type);
							total.push(json[i].total_recipient);
							id_campaign=id_campaign+json[i].id_campaign+"|";	
												//alert(json[i].name+' '+json[i].start_time+' '+json[i].end_time+' '+json[i].start_date);
						}
						$("#iframe1").attr("src","http://localhost/wibpush/Campaign4?menu=0&id_campaign="+id_campaign);
						start_date2 = remove_duplicates(start_date);
						timetable.addLocations(start_date2);
						//alert(start_date2.length);
						for (var i=0;i<start_date2.length;++i)
						{				
							for (var j=0;j<name.length;++j)
							{
								var name2 = name[j].split('|');
								var start_time2 = start_time[j].split(':');
								var end_time2 = end_time[j].split(':');
								var legend = name2[1].trim()+' ('+total[i]+')';
								if(name2[0]==start_date2[i])
								{
									if(type[j]=="Manual")
									{	
										timetable.addEvent(legend, start_date2[i], new Date(2015,7,17,start_time2[0].trim(),start_time2[1].trim()), new Date(2015,7,17,end_time2[0].trim(),end_time2[1].trim()),'#');
									}
									else if(type[j]=="Automatic")
									{	
										timetable.addEvent(legend, start_date2[i], new Date(2015,7,17,start_time2[0].trim(),start_time2[1].trim()), new Date(2015,7,17,end_time2[0].trim(),end_time2[1].trim()),{ class: 'vip-only' });
									}
								}									
							}								
						}
						var renderer = new Timetable.Renderer(timetable);
						renderer.draw('.timetable'); // any css selector
						
					},
			  		error: function(response)
			  		{
			  		},
			  });
	});  


$('#end_date').datepicker().on('changeDate', function(e) {
//		start_date = $("#start_date").val();
		end_date = e.format(0,"yyyy-mm-dd");

			  $.ajax({
			  		url: "<?php echo base_url(); ?>Report/getCampaign?type=5&start_date="+start_date+"&end_date="+end_date,
						type: "GET",
						dataType: "text",
			  		success: function(response)
					{
						var timetable = new Timetable();
						timetable.setScope(8, 23); // optional, only whole hours between 0 and 23
						var json = $.parseJSON(response);
						var start_time = [];
						var end_time = [];
						var start_date = [];
						var name = [];
						var type = [];
						var start_date2 = [];
						var total = [];
						var id_campaign = "";
						for (var i=0;i<json.length;++i)
						{							
							start_time.push(json[i].start_time);
							end_time.push(json[i].end_time);
							start_date.push(json[i].start_date);
							name.push(json[i].name);
							type.push(json[i].type);
							total.push(json[i].total_recipient);
							id_campaign=id_campaign+json[i].id_campaign+"|";	
												//alert(json[i].name+' '+json[i].start_time+' '+json[i].end_time+' '+json[i].start_date);
						}
						$("#iframe1").attr("src","http://localhost/wibpush/Campaign4?menu=0&id_campaign="+id_campaign);
						start_date2 = remove_duplicates(start_date);
						timetable.addLocations(start_date2);
						//alert(start_date2.length);
						for (var i=0;i<start_date2.length;++i)
						{				
							for (var j=0;j<name.length;++j)
							{
								var name2 = name[j].split('|');
								var start_time2 = start_time[j].split(':');
								var end_time2 = end_time[j].split(':');
								var legend = name2[1].trim()+' ('+total[i]+')';
								if(name2[0]==start_date2[i])
								{
									if(type[j]=="Manual")
									{	
										timetable.addEvent(legend, start_date2[i], new Date(2015,7,17,start_time2[0].trim(),start_time2[1].trim()), new Date(2015,7,17,end_time2[0].trim(),end_time2[1].trim()),'#');
									}
									else if(type[j]=="Automatic")
									{	
										timetable.addEvent(legend, start_date2[i], new Date(2015,7,17,start_time2[0].trim(),start_time2[1].trim()), new Date(2015,7,17,end_time2[0].trim(),end_time2[1].trim()),{ class: 'vip-only' });
									}
								}									
							}								
						}
						var renderer = new Timetable.Renderer(timetable);
						renderer.draw('.timetable'); // any css selector
						
					},
			  		error: function(response)
			  		{
			  		},
			  });
	});  



$('select').on('change', function(event, params) 
{
	var range=$('select').val();
	if(range=="5")
	{
		$("div.input-group.date").show();
		start_date = $("#start_date").val();
		end_date = $("#end_date").val();
			  $.ajax({
			  		url: "<?php echo base_url(); ?>Report/getCampaign?type=5&start_date="+start_date+"&end_date="+end_date,
						type: "GET",
						dataType: "text",
			  		success: function(response)
					{
						var timetable = new Timetable();
						timetable.setScope(8, 23); // optional, only whole hours between 0 and 23
						var json = $.parseJSON(response);
						var start_time = [];
						var end_time = [];
						var start_date = [];
						var name = [];
						var type = [];
						var start_date2 = [];
						var total = [];
						var id_campaign = "";
						for (var i=0;i<json.length;++i)
						{							
							start_time.push(json[i].start_time);
							end_time.push(json[i].end_time);
							start_date.push(json[i].start_date);
							name.push(json[i].name);
							type.push(json[i].type);
							total.push(json[i].total_recipient);
							id_campaign=id_campaign+json[i].id_campaign+"|";	
												//alert(json[i].name+' '+json[i].start_time+' '+json[i].end_time+' '+json[i].start_date);
						}
						$("#iframe1").attr("src","http://localhost/wibpush/Campaign4?menu=0&id_campaign="+id_campaign);
						start_date2 = remove_duplicates(start_date);
						timetable.addLocations(start_date2);
						//alert(start_date2.length);
						for (var i=0;i<start_date2.length;++i)
						{				
							for (var j=0;j<name.length;++j)
							{
								var name2 = name[j].split('|');
								var start_time2 = start_time[j].split(':');
								var end_time2 = end_time[j].split(':');
								var legend = name2[1].trim()+' ('+total[i]+')';
								var day = new Date(start_date2[i]).getDay();
								if(name2[0]==start_date2[i])
								{
									if(type[j]=="Manual")
									{	
										timetable.addEvent(legend, start_date2[i], new Date(2015,7,17,start_time2[0].trim(),start_time2[1].trim()), new Date(2015,7,17,end_time2[0].trim(),end_time2[1].trim()),'#');
									}
									else if(type[j]=="Automatic")
									{	
										timetable.addEvent(legend, start_date2[i], new Date(2015,7,17,start_time2[0].trim(),start_time2[1].trim()), new Date(2015,7,17,end_time2[0].trim(),end_time2[1].trim()),{ class: 'vip-only' });
									}
								}									
							}								
						}
						var renderer = new Timetable.Renderer(timetable);
						renderer.draw('.timetable'); // any css selector
						
					},
			  		error: function(response)
			  		{
			  		},
			  });
	}
	else
	{	
		if(range==1)
		{	
		 var title = "Broadcast Summary per Campaign (Today)";
		} 
		else if(range==2)
		{	
		 var title = "Broadcast Summary per Campaign (Last 7 Days)";
		} 
		else if(range==3)
		{	
		 var title = "Broadcast Summary per Campaign (Last 30 Days)";
		} 
		else if(range==4)
		{	
		 var title = "Broadcast Summary per Campaign (This Month)";
		} 
		$("div.input-group.date").hide();

			  $.ajax({
			  		url: "<?php echo base_url(); ?>Report/getCampaign?type="+range,
						type: "GET",
						dataType: "text",
			  		success: function(response)
					{
						var timetable = new Timetable();
						timetable.setScope(8, 23); // optional, only whole hours between 0 and 23
						var json = $.parseJSON(response);
						var start_time = [];
						var end_time = [];
						var start_date = [];
						var name = [];
						var type = [];
						var start_date2 = [];
						var total = [];
						var id_campaign = "";
						for (var i=0;i<json.length;++i)
						{							
							start_time.push(json[i].start_time);
							end_time.push(json[i].end_time);
							start_date.push(json[i].start_date);
							name.push(json[i].name);
							type.push(json[i].type);
							total.push(json[i].total_recipient);
							id_campaign=id_campaign+json[i].id_campaign+"|";	
												//alert(json[i].name+' '+json[i].start_time+' '+json[i].end_time+' '+json[i].start_date);
						}
						$("#iframe1").attr("src","http://localhost/wibpush/Campaign4?menu=0&id_campaign="+id_campaign);
						start_date2 = remove_duplicates(start_date);
						timetable.addLocations(start_date2);
						//alert(start_date2.length);
						for (var i=0;i<start_date2.length;++i)
						{				
							for (var j=0;j<name.length;++j)
							{
								var name2 = name[j].split('|');
								var start_time2 = start_time[j].split(':');
								var end_time2 = end_time[j].split(':');
								var legend = name2[1].trim()+' ('+total[i]+')';
								if(name2[0]==start_date2[i])
								{
									if(type[j]=="Manual")
									{	
										timetable.addEvent(legend, start_date2[i], new Date(2015,7,17,start_time2[0].trim(),start_time2[1].trim()), new Date(2015,7,17,end_time2[0].trim(),end_time2[1].trim()));
									}
									else if(type[j]=="Automatic")
									{	
										timetable.addEvent(legend, start_date2[i], new Date(2015,7,17,start_time2[0].trim(),start_time2[1].trim()), new Date(2015,7,17,end_time2[0].trim(),end_time2[1].trim()),{ class: 'vip-only' });
									}
								}									
							}								
						}
						var renderer = new Timetable.Renderer(timetable);
						renderer.draw('.timetable'); // any css selector
						
					},
			  		error: function(response)
			  		{
			  		},
			  });
		
	}
});







						

					  
});
</script>
</html>
