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
$(function(){
	var pathname = $(location).attr('pathname');
	var read=pathname.split('/');
				
});

function extract(id)
{
		$("#extract").attr('disabled','disabled');
			  $.ajax({
			  		url: "<?php echo base_url(); ?>Upload/insert_data?id="+id,
						type: "GET",
						dataType: "text",
			  		success: function(response)
					{
			  		},
			  		error: function(response)
			  		{
			  		},
			  });
		$('.gc-refresh').trigger('click');
}
</script>
</html>
