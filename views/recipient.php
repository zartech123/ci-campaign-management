<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
$(function()
{
		$("input[name='id_state']").hide();
		
});
</script>
</html>
