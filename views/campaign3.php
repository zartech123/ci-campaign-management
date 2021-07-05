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
	<table width="100%">
	<tr style="background-color:#96c5cd"><td colspan=8>&nbsp;</td></tr>
	<tr style="background-color:#96c5cd">
<?php
		$query = $this->db->query("SELECT id_state, name_state from state_campaign");
		foreach ($query->result() as $row)
		{?>
				<td width="12.5%" style="font-size:14px;color:#245269;" align="center"><b>&nbsp;<?php echo $row->name_state; ?>&nbsp;</b></td>	
			<?php
		} ?>
	<tr>	
	<tr style="background-color:#96c5cd"><td colspan=8>&nbsp;</td></tr>
	<tr style="background-color:#96c5cd">	
	<?php	$query = $this->db->query("SELECT id_state, name_state from state_campaign order by id_state");
		foreach ($query->result() as $row)
		{
			$jumlah = 0;
			$query2 = $this->db->query("SELECT count(*) as jumlah from campaign where id_state='".$row->id_state."'");
			foreach ($query2->result() as $row2)
			{
				$jumlah = $row2->jumlah;
			}
			if($row->id_state=="1" || $row->id_state=="2" || $row->id_state=="3")
			{?>
			<td align="center"><a href="<?php echo base_url(); ?>Campaign4?id_state=<?php echo $row->id_state; ?>"><input type="button" class="btn btn-warning" value="<?php echo $jumlah; ?>"></input></a></td>	
			<?php } 
			else if($row->id_state=="4" || $row->id_state=="5")
			{?>
			<td align="center"><a href="<?php echo base_url(); ?>Campaign4?id_state=<?php echo $row->id_state; ?>"><input type="button" class="btn btn-success" value="<?php echo $jumlah; ?>"></input></a></td>	
			<?php } 
			else if($row->id_state=="6" || $row->id_state=="8")
			{?>
			<td align="center"><a href="<?php echo base_url(); ?>Campaign4?id_state=<?php echo $row->id_state; ?>"><input type="button" class="btn btn-danger" value="<?php echo $jumlah; ?>"></input></a></td>	
			<?php }
			else
			{?>
			<td align="center"><a href="<?php echo base_url(); ?>Campaign4?id_state=<?php echo $row->id_state; ?>"><input type="button" class="btn btn-primary" value="<?php echo $jumlah; ?>"></input></a></td>	
			<?php }
				
		}	
?>
	</tr>
	<tr style="background-color:#96c5cd"><td colspan=8>&nbsp;</td></tr>
	</table>
	<?php echo $output; ?>
    <?php foreach($js_files as $file): ?>
        <script src="<?php echo $file; ?>"></script>
    <?php endforeach; ?>
	<table width="100%">
	<tr>
	<td><iframe id="iframe1" src="<?php echo base_url(); ?>Report2?menu=0" style="height:600px;width:100%;border:none;overflow:hidden;"></iframe></td>
	<td><iframe id="iframe2" src="<?php echo base_url(); ?>Report3?menu=0" style="height:600px;width:100%;border:none;overflow:hidden;"></iframe></td>
	</tr>
	</table>
</body>
<script>
$(function(){
	$("a.btn.btn-primary.search-button.t5").hide();	
	$("div.table-label").hide();
	$("div.floatL.t20.l5").hide();	
	$("ul.pagination").hide();
	$("div.btn-group.floatR.t20.l10.settings-button-container").hide();
	$("div.floatR.r10.t30").hide();
	$("input[name='id_campaign']").hide();
	/*$("input[name='name_campaign']").hide();
	$("input[name='type']").hide();
	$("input[name='total_recipient']").hide();
	$("input[name='start_date_campaign']").hide();
	$("input[name='start_time_campaign']").hide();
	$("input[name='end_date_campaign']").hide();
	$("input[name='end_time_campaign']").hide();
	$("input[placeholder='Search State']").hide();*/
});
</script>
</html>
