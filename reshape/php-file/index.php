<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>color-lib-thing</title>
</head>
<body>
<?php
	$Pictures = scandir(__DIR__.'/screenshot/');
?>
<?php foreach($Pictures AS $image): ?>
	<?php if ($image != '.' && $image != '..'): ?>
<div><img src="screenshot/<?php echo  $image ; ?>" style="width: 100%"></div>
	<?php endif; ?>
<?php endforeach; ?>
</body>
</html>