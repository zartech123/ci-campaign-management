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
		$("#field-user_name").css("width","360");
		$("#field-name").css("width","360");
		$("#field-email").css("width","360");
		$("input[name='photo']").hide();
		$("input[name='active']").hide();
//		$("#field-old_password").after('<i>&nbsp;Please fill to change your password</i>');
//		$("#field-new_password").after('<i>&nbsp;Please fill to change your password</i>');
		$("#field-old_password").css("width","100");
		$("#field-new_password1").css("width","100");
		$("#field-new_password2").css("width","100");
});
</script>
</html>
