<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="<?php echo base_url(); ?>assets/js/Chart.min.js"></script>
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
		<tr><td colspan=9><center><div class="table-label">&nbsp;Broadcast Summary per Campaign</div></center></td></tr>
		<tr><td colspan=9>&nbsp;</td></tr>
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
			<td>&nbsp;</td>			
			<td><a id="url" download="filename.png"><i class="fa fa-camera" style="font-size:36px"></i></a></td>
			<td>&nbsp;</td>			
			<td width="100%"><a id="download" type="button" href="#" class="btn btn-primary">Download</a></td>
		</tr>
		<tr><td colspan=9><hr></td></tr>
		<tr><td colspan=9><div id="chart"><canvas id="mychart"></canvas></div></td></tr>																	
		</table>
	</div>
</body>
<style type="text/css">
#chart {
  position:relative;
  height:60vh;
  margin:auto;
}
</style>
<script>
var myChart;  
function done()
{
	
	var url = myChart.toBase64Image();
	$("#url").attr('href',url);
	if($('#range').val()=="1")
	{
		$("#url").attr('download','Broadcast Summary per Campaign (Today)'+new Date()+'.png');
	}
	if($('#range').val()=="2")
	{
		$("#url").attr('download','Broadcast Summary per Campaign (Last 7 Days)'+new Date()+'.png');
	}
	if($('#range').val()=="3")
	{
		$("#url").attr('download','Broadcast Summary per Campaign (Last 30 Days)'+new Date()+'.png');
	}
	if($('#range').val()=="4")
	{
		$("#url").attr('download','Broadcast Summary per Campaign (This Month)'+new Date()+'.png');
	}
	if($('#range').val()=="5")
	{
		$("#url").attr('download','Broadcast Summary per Campaign from '+$('#start_date').val()+' to '+$('#end_date').val()+' '+new Date()+'.png');
	}

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


	  $("#start_date").val(toDay2);
	  $("#end_date").val(toDay2);
$(".chosen-select").chosen();
$('#range').val('2').trigger('chosen:updated');

					$.ajax({
						url : "<?php echo base_url(); ?>Excel/createReport2?type=2",
						type : "GET",
						dataType: "text",
						success : function(data)
						{
						},
						error: function(response)
						{
						},	
					  });		
					
					$("#download").attr('href',"<?php echo base_url(); ?>assets/uploads/files/Broadcast Summary per Campaign (Last 7 Days).xlsx");			

					$.ajax({
						url : "<?php echo base_url(); ?>Report2/getReport?type=2",
						type : "GET",
						dataType: "text",
						success : function(data)
						{
							var chartData = jQuery.parseJSON('' + data + '');
								
							var chartOptions = {
								animation: {
									onComplete: done
								},	
								responsive:true,
								maintainAspectRatio: false,
							  legend: {
								position: "right"
							  },
							  title: {
								display: true,
								text: "Broadcast Summary per Campaign (Last 7 Days)"
							  },
							  scales: {
								yAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Total Broadcast'
										  },									
								  ticks: {
									beginAtZero: true
								  }
								}],
								xAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Date'
										  }									
								}]
							  }
							}  
								
							myChart = new Chart(document.getElementById("mychart"), {
								type: 'bar',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		

$('#start_date').datepicker().on('changeDate', function(e) {
		start_date = e.format(0,"yyyy-mm-dd");
//		end_date = $("#end_date").val();
		var title = "Broadcast Summary per Campaign from "+start_date+" to "+end_date;

					$.ajax({
						url : "<?php echo base_url(); ?>Excel/createReport2?type=5&start_date="+e.format(0,"yyyy-mm-dd")+"&end_date="+end_date,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
						},
						error: function(response)
						{
						},	
					  });		
					
					$("#download").attr('href',"<?php echo base_url(); ?>assets/uploads/files/"+title+".xlsx");			

					$.ajax({
						url : "<?php echo base_url(); ?>Report2/getReport?type=5&start_date="+e.format(0,"yyyy-mm-dd")+"&end_date="+end_date,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
							var chartData = jQuery.parseJSON('' + data + '');
								
							var chartOptions = {
								animation: {
									onComplete: done
								},	
								responsive:true,
								maintainAspectRatio: false,
							  legend: {
								position: "right"
							  },
							  title: {
								display: true,
								text: title
							  },
							  scales: {
								yAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Total Broadcast'
										  },									
								  ticks: {
									beginAtZero: true
								  }
								}],
								xAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Date'
										  }									
								}]
							  }
							}  
								
							myChart = new Chart(document.getElementById("mychart"), {
								type: 'bar',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		
	myChart.destroy();		

	});  


$('#end_date').datepicker().on('changeDate', function(e) {
//		start_date = $("#start_date").val();
		end_date = e.format(0,"yyyy-mm-dd");
		var title = "Broadcast Summary per Campaign from "+start_date+" to "+end_date;

					$.ajax({
						url : "<?php echo base_url(); ?>Excel/createReport2?type=5&end_date="+e.format(0,"yyyy-mm-dd")+"&start_date="+start_date,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
						},
						error: function(response)
						{
						},	
					  });		
					
					$("#download").attr('href',"<?php echo base_url(); ?>assets/uploads/files/"+title+".xlsx");			

					$.ajax({
						url : "<?php echo base_url(); ?>Report2/getReport?type=5&end_date="+e.format(0,"yyyy-mm-dd")+"&start_date="+start_date,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
							var chartData = jQuery.parseJSON('' + data + '');
								
							var chartOptions = {
								animation: {
									onComplete: done
								},	
								responsive:true,
								maintainAspectRatio: false,
							  legend: {
								position: "right"
							  },
							  title: {
								display: true,
								text: title
							  },
							  scales: {
								yAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Total Broadcast'
										  },									
								  ticks: {
									beginAtZero: true
								  }
								}],
								xAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Date'
										  }									
								}]
							  }
							}  
								
							myChart = new Chart(document.getElementById("mychart"), {
								type: 'bar',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		
	myChart.destroy();		

	});  



$('select').on('change', function(event, params) 
{
	var range=$('select').val();
	if(range=="5")
	{
		$("div.input-group.date").show();
		start_date = $("#start_date").val();
		end_date = $("#end_date").val();
		var title = "Broadcast Summary per Campaign from "+start_date+" to "+end_date;
					$.ajax({
						url : "<?php echo base_url(); ?>Excel/createReport2?type=5&start_date="+start_date+"&end_date="+end_date,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
						},
						error: function(response)
						{
						},	
					  });		
					
					$("#download").attr('href',"<?php echo base_url(); ?>assets/uploads/files/"+title+".xlsx");			
					$.ajax({
						url : "<?php echo base_url(); ?>Report2/getReport?type="+range+"&start_date="+start_date+"&end_date="+end_date,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
							var chartData = jQuery.parseJSON('' + data + '');
								
							var chartOptions = {
								animation: {
									onComplete: done
								},	
								responsive:true,
								maintainAspectRatio: false,
							  legend: {
								position: "right"
							  },
							  title: {
								display: true,
								text: title
							  },
							  scales: {
								yAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Total Broadcast'
										  },									
								  ticks: {
									beginAtZero: true
								  }
								}],
								xAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Date'
										  }									
								}]
							  }
							}  
								
							myChart = new Chart(document.getElementById("mychart"), {
								type: 'bar',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		
	myChart.destroy();		
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
						url : "<?php echo base_url(); ?>Excel/createReport2?type="+range,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
						},
						error: function(response)
						{
						},	
					  });		
					
					$("#download").attr('href',"<?php echo base_url(); ?>assets/uploads/files/"+title+".xlsx");			

					$.ajax({
						url : "<?php echo base_url(); ?>Report2/getReport?type="+range,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
							var chartData = jQuery.parseJSON('' + data + '');
								
							var chartOptions = {
								animation: {
									onComplete: done
								},	
								responsive:true,
								maintainAspectRatio: false,
							  legend: {
								position: "right"
							  },
							  title: {
								display: true,
								text: title
							  },
							  scales: {
								yAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Total Broadcast'
										  },									
								  ticks: {
									beginAtZero: true
								  }
								}],
								xAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Date'
										  }									
								}]
							  }
							}  
								
							myChart = new Chart(document.getElementById("mychart"), {
								type: 'bar',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		
	myChart.destroy();		
	}
});







						

					  
});
</script>
</html>
