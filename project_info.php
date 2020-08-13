<?php
session_start();

require_once 'zadatak5_db.php';
require_once 'zadatak5_db_settings.php';

$db = DB::getConnection();
$st = $db->query( 'SELECT * FROM dz2_projects' );
$st2 = $db->query( 'SELECT id, username FROM dz2_users' );
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
	<h2>Detalji o projektu</h2>
	<?php 
	while( $row=$st->fetch() )
		if($_POST["project_id"]===chr($row['id']+48)){
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
			$test=0;
			$id=0;
			foreach( $st2->fetchAll() as $row2 ){
				if($row2['username']===$username)
					$id=$row2['id'];
				if($row2['id']===$row['id_user']){
					echo "Autor: " . $row2['username'] . '<br>';
				}
			}
			$st2 = $db->query( 'SELECT id, username FROM dz2_users' );
			echo "Naslov: " . $row['title'] . '<br>';
			echo "Opis: " . $row['abstract'] . '<br>';
			echo "Trazeni broj clanova: " . $row['number_of_members'] . '<br>';
			echo "Popis trenutnih clanova: ";
			$st3 = $db->prepare( 'SELECT id_user FROM dz2_members WHERE id_project=:id');
			$st3->execute( array( 'id' => $row['id']));
			foreach($st3->fetchAll() as $row3){
				if($row3['id_user'] === $id)
					$test=1;
				if($row3['id_user'] !== $row['id_user']){
					$st4 = $db->prepare( 'SELECT username FROM dz2_users WHERE id=:id');
					$st4->execute( array( 'id' => $row3['id_user']));
					$row4=$st4->fetch();
					echo $row4['username'].' ';
				}
			}
			echo '<br>';
			if($row['status']==='open' AND $test===0){
				?>
				<form method="POST" action="dodaj.php">
					<button type="submit" name="dodaj" value="<?php echo $id.','.$row['id'];?>">
					Prijavi se na ovaj projekat! </button>
				</form>
				<?php
			}
			break;
		}
	?>
<br>
</table>
	<form method="POST" action="teamup.php">
		<input type="hidden" name="logout">
		<input type="submit" value="Log Out">
	</form>
</body>
</html>
