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
</body>
<script>
$(function()
{
	var pathname = $(location).attr('pathname');
	var read=pathname.split('/');

	$("a.btn.btn-primary.search-button.t5").hide();	
	$("div.table-label").hide();
	$("div.floatL.t20.l5").hide();	
	$("ul.pagination").hide();
	$("div.btn-group.floatR.t20.l10.settings-button-container").hide();
	$("div.floatR.r10.t30").hide();
	$("input[name='name_campaign']").hide();
	$("input[name='text']").hide();
	$("input[name='type']").hide();
	$("input[name='total_recipient']").hide();
	$("input[name='start_date_campaign']").hide();
	$("input[name='start_time_campaign']").hide();
	$("input[name='end_date_campaign']").hide();
	$("input[name='end_time_campaign']").hide();
	$("input[placeholder='Search Status']").hide();
	
});

</script>
</html>
