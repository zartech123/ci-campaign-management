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
<?php
		$name_campaign="";
		$query = $this->db->query("SELECT name_campaign from campaign WHERE id_campaign=".$data['id']);
		foreach ($query->result() as $row2)
		{			
			$name_campaign=$row2->name_campaign;
		}
?>


<tr><td></td></tr>
<tr><td><center>
<div class="table-label"><center>&nbsp;Broadcast Response for <?php echo $name_campaign; ?></center></div>
</center></td></tr>
<tr><td><hr></td></tr>
<tr>
<td>
<table align="center" width="100%" height="100%"><tr><td><div id="chart"><canvas id="mychart"></canvas></div></td>
</tr></table>
</td>
</tr>																	
<tr><td><hr></td></tr>
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
$(function()
{

					$.ajax({
						url : "<?php echo base_url(); ?>Report4/getReport?id="+<?php echo $data['id']; ?>,
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
								text: ""
							  },
							  scales: {
								yAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Total Response'
										  },									
								  ticks: {
									beginAtZero: true
								  }
								}],
								xAxes: [{
									scaleLabel: {
											display: true,
											labelString: 'Time'
										  }									
								}]
							  }
							}						
								
							new Chart(document.getElementById("mychart"), {
								type: 'line',
								data: chartData,
								options: chartOptions
							});	
						},
						error: function(response)
						{
						},	
					  });		
});					  
</script>
</html>
