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
	<script src="<?php echo base_url(); ?>assets/js/chartjs-plugin-datalabels.min.js"></script>
	<script src="<?php echo base_url(); ?>assets/js/Chart.PieceLabel.min.js"></script>
<?php 
foreach($css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>

	</head>
<body>
    <div class="container-fluid gc-container">
		<table width="100%">
		<tr><td colspan=6><center><div class="table-label">&nbsp;Server Up Time</div></center></td></tr>
		<tr><td colspan=6>&nbsp;</td></tr>
		<tr>
			<td>
				<select id="ip" class="chosen-select" size="8" data-placeholder="IP Address">
				<option value=1>10.162.16.104</options>									
				<option value=2>10.162.16.103</options>									
				</select>
			</td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td>
				<select id="range" class="chosen-select" size="8" data-placeholder="Date Range">
				<option value=1>To Day</options>									
				<option value=2>Last 2 Days</options>									
				<option value=3>Last 3 Days</options>									
				<option value=4>Last 4 Days</options>									
				<option value=5>Last 5 Days</options>									
				<option value=6>Last 6 Days</options>									
				<option value=7>Last 7 Days</options>									
				</select>
			</td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td>
			</td>
			<td width="100%">&nbsp;</td>
		</tr>
		<tr><td colspan=6><hr></td></tr>
		<tr><td colspan=6><div id="chart"><canvas id="mychart"></canvas></div></td></tr>																	
		<tr><td colspan=6>&nbsp;</td></tr>																	
		<tr><td colspan=6><div id="chart2"><canvas id="mychart2"></canvas></div></td></tr>																	
		</table>
	</div>
</body>
<style type="text/css">
#chart {
  position:relative;
  height:40vh;
  margin:auto;
}
#chart2 {
  position:relative;
  height:40vh;
  margin:auto;
}
</style>
<script>
$(function(){

	var myChart;  
	var myChart2;  
	$("div.input-group.date").hide();

$('#range').val('1').trigger('chosen:updated');
$('#ip').val('1').trigger('chosen:updated');

$(".chosen-select").chosen();
					$.ajax({
						url : "<?php echo base_url(); ?>Report6/getReport?type=1&ip=10.162.16.104",
						type : "GET",
						dataType: "text",
						success : function(data)
						{
							var chartData = jQuery.parseJSON('' + data + '');
								
							var chartOptions = {
								responsive:true,
								maintainAspectRatio: false,
							  legend: {
								position: "right"
							  },
							  title: {
								display: true,
								text: "Server [10.162.16.104] Up Time (Today)"
							  },
							  scales: {
								yAxes: [{
									position: 'left',
									scaleLabel: {
											display: true,
											labelString: 'Server Status'
										  },									
								  ticks: {
									display: true,
									stepSize: 1,
									beginAtZero: true	
								  },
								gridLines: {
									display:false
									}								
								}
								],
								xAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Time'
										  },	
								gridLines: {
									display:false
									}								
								}]
							  }
							}  
								
							myChart = new Chart(document.getElementById("mychart"), {
								type: 'line',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		


					$.ajax({
						url : "<?php echo base_url(); ?>Report6/getReport2?type=1&ip=10.162.16.104",
						type : "GET",
						dataType: "text",
						success : function(data)
						{
							var chartData = jQuery.parseJSON('' + data + '');
								
							var chartOptions = {
									animation: {
										animateRotate: true,
										animateScale: true
									  },
									cutoutPercentage: 65,									
									pieceLabel: {
										fontColor: '#fff',
											 render: 'percentage' //show values
										  },									
									maintainAspectRatio: false,
									responsive: true,
									tooltips: {
										 enabled: true,
										 mode: 'single',
										 callbacks: {
										label: function(tooltipItem, data) {
			var dataset = data.datasets[tooltipItem.datasetIndex];
          var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
            return parseInt(previousValue) + parseInt(currentValue);
          });
          var currentValue = dataset.data[tooltipItem.index];
          var precentage = Math.floor(((currentValue/total) * 100)+0.5);         
          return precentage + "%";
		  //												return (data.datasets[0].data[tooltipItems.index] / sum * 100) +' %';

											  }										 
										}
									 },
								  title: {
									display: false
								  }
							}  
								
							myChart2 = new Chart(document.getElementById("mychart2"), {
								type: 'pie',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		



$('#range').on('change', function(event, params) 
{
	var range=$('#range').val();
	var ip = $('#ip option:selected').text();
		if(range==1)
		{	
		 var title = "Server ["+ip.trim()+"] Up Time (Today)";
		} 
		else
		{	
		 var title = "Server ["+ip.trim()+"] Up Time (Last "+range+" Days)";
		} 
					$.ajax({
						url : "<?php echo base_url(); ?>Report6/getReport?type="+range+"&ip="+ip,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
							var chartData = jQuery.parseJSON('' + data + '');
								
							var chartOptions = {
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
									position: 'left',
									scaleLabel: {
											display: true,
											labelString: 'Server Status'
										  },									
								  ticks: {
									display: true,
									stepSize: 1,
									beginAtZero: true	
								  },
								gridLines: {
									display:false
									}								
								}
								],
								xAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Time'
										  },	
								gridLines: {
									display:false
									}								
								}]
							  }
							}  
								
							myChart = new Chart(document.getElementById("mychart"), {
								type: 'line',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		

					$.ajax({
						url : "<?php echo base_url(); ?>Report6/getReport2?type="+range+"&ip="+ip,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
							var chartData = jQuery.parseJSON('' + data + '');
								
							var chartOptions = {
									animation: {
										animateRotate: true,
										animateScale: true
									  },
									cutoutPercentage: 65,									
									pieceLabel: {
										fontColor: '#fff',
											 render: 'percentage' //show values
										  },									
									maintainAspectRatio: false,
									responsive: true,
									tooltips: {
										 enabled: true,
										 mode: 'single',
										 callbacks: {
										label: function(tooltipItem, data) {
			var dataset = data.datasets[tooltipItem.datasetIndex];
          var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
            return parseInt(previousValue) + parseInt(currentValue);
          });
          var currentValue = dataset.data[tooltipItem.index];
          var precentage = Math.floor(((currentValue/total) * 100)+0.5);         
          return precentage + "%";
		  //												return (data.datasets[0].data[tooltipItems.index] / sum * 100) +' %';

											  }										 
										}
									 },
								  title: {
									display: false
								  }
							}  
								
							myChart2 = new Chart(document.getElementById("mychart2"), {
								type: 'pie',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		
	
	myChart.destroy();		
	myChart2.destroy();		
	
});

$('#ip').on('change', function(event, params) 
{
	var range=$('#range').val();
	var ip = $('#ip option:selected').text();
		if(range==1)
		{	
		 var title = "Server ["+ip.trim()+"] Up Time (Today)";
		} 
		else
		{	
		 var title = "Server ["+ip.trim()+"] Up Time (Last "+range+" Days)";
		} 
					$.ajax({
						url : "<?php echo base_url(); ?>Report6/getReport?type="+range+"&ip="+ip,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
							var chartData = jQuery.parseJSON('' + data + '');
								
							var chartOptions = {
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
									position: 'left',
									scaleLabel: {
											display: true,
											labelString: 'Server Status'
										  },									
								  ticks: {
									display: true,
									stepSize: 1,
									beginAtZero: true	
								  },
								gridLines: {
									display:false
									}								
								}
								],
								xAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Time'
										  },	
								gridLines: {
									display:false
									}								
								}]
							  }
							}  
								
							myChart = new Chart(document.getElementById("mychart"), {
								type: 'line',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		

					$.ajax({
						url : "<?php echo base_url(); ?>Report6/getReport2?type="+range+"&ip="+ip,
						type : "GET",
						dataType: "text",
						success : function(data)
						{
							var chartData = jQuery.parseJSON('' + data + '');
								
							var chartOptions = {
									animation: {
										animateRotate: true,
										animateScale: true
									  },
									cutoutPercentage: 65,									
									pieceLabel: {
										fontColor: '#fff',
											 render: 'percentage' //show values
										  },									
									maintainAspectRatio: false,
									responsive: true,
									tooltips: {
										 enabled: true,
										 mode: 'single',
										 callbacks: {
										label: function(tooltipItem, data) {
			var dataset = data.datasets[tooltipItem.datasetIndex];
          var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
            return parseInt(previousValue) + parseInt(currentValue);
          });
          var currentValue = dataset.data[tooltipItem.index];
          var precentage = Math.floor(((currentValue/total) * 100)+0.5);         
          return precentage + "%";
		  //												return (data.datasets[0].data[tooltipItems.index] / sum * 100) +' %';

											  }										 
										}
									 },
								  title: {
									display: false
								  }
							}  
								
							myChart2 = new Chart(document.getElementById("mychart2"), {
								type: 'pie',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		
	
	myChart.destroy();		
	myChart2.destroy();		
	
});






						

					  
});
</script>
</html>
