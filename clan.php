<?php
session_start();

require_once 'zadatak5_db.php';
require_once 'zadatak5_db_settings.php';

$db = DB::getConnection();
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body>
<h2>TeamUp!</h2>
<h3>Izbornik</h3>
	<nav>
		<ul>
			<li><a href="teamup.php">Svi projekti</a></li>
			<li><a href="clan.php">Moji projekti</a></li>
			<li><a href="novi.php">Započni novi projekt</a></li>
		</ul>
	</nav>
	<h2>Moji projekti</h2>
	<form method="post" action="project_info.php">
	<?php
	$username='';
	if( isset( $_SESSION['login'] ) ){
		list( $c_username, $cookie_hash ) = explode( ',' , $_SESSION['login'] );
		$secret_word = 'racunarski praktikum 2!!!';
		if( md5( $c_username . $secret_word ) === $cookie_hash ){
			$username = $c_username; //echo $username.'<br>';
		}
		else{
			session_unset();
			session_destroy();
			exit('Poslan je pokvareni kolačić :)') ;
		}
	}
	$st0 = $db->prepare('SELECT id FROM dz2_users WHERE username=:username');
	$st0 -> execute(array( 'username' => $username));
	$row0 = $st0->fetch();
	$id = $row0['id'];
	$st = $db->prepare('SELECT id_project FROM dz2_members WHERE id_user=:id');
	$st -> execute(array( 'id' => $id));
	while($row=$st->fetch()){
		$st2 = $db->prepare('SELECT id, id_user, title FROM dz2_projects WHERE id=:id');
		$st2 -> execute(array( 'id' => $row['id_project']));
		while($row2=$st2->fetch())
			if($row2['id_user']===$id){
				?>
				<button type="submit" name="project_id" value="<?php echo $row2['id'];?>">
				<span  style="background-color: #66ff33"> <?php echo $row2['title']; ?> </span>
				</button>
				<br>
				<?php
			}
			else{
				?>
				<button type="submit" name="project_id" value="<?php echo $row2['id'];?>">
				<?php echo $row2['title']; ?>
				</button>
				<br>
				<?php
			}
	}
	?>
	</form>
	<br>
	<form method="POST" action="teamup.php">
		<input type="hidden" name="logout">
		<input type="submit" value="Log Out">
	</form>
</body>
</html>
	