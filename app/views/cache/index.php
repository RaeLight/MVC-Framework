<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="description" content="mvc">
<meta name="keywords" content="mvc">
<meta name="author" content="RaeLight">
<title>ttteee</title>

</head>
<body>
    <nav>
Navbar
</nav>
<hr>
    
<div>
	Content<br><br>
	
	Time: <?php echo time(); ?><br><br>
	
	@{{ time() }}<br><br>
	
	
<?php if(empty($users)): ?>
<div>
	Users is empty
</div>
<?php else: foreach($users as $user): ?>
<div>
	-- User Info -- <br>
	
	<span>Name:<?php echo $user["name"]; ?></span>,<br>
	<span>Surname:<?php echo $user["surname"]; ?></span>
</div>
<?php endforeach; endif; ?>
	
	<br>
</div>

    <hr>
<footer>
	Footer
</footer>
    
</body>
</html>

