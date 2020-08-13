<?php
// Počinjemo ili nastavljamo sesiju.
session_start();

require_once 'zadatak5_db.php';
require_once 'zadatak5_db_settings.php';

$db = DB::getConnection();
$st = $db->query( 'SELECT * FROM dz2_projects' );
$st2 = $db->query( 'SELECT id, username FROM dz2_users' );

// Funkcija za provjeru smije li korisnik $user sa šifrom $pass pristupiti stranici.
function validate( $user, $pass )
{
  if( !preg_match( '/^[a-zA-Z]{3,10}$/', $user ) )
  {
    echo "Korisničko ime treba imati između 3 i 10 slova." . "<br/>";
    return false;
  }

	// Popis svih korisnika koji smiju i njihovih šifri. (Ovo se inače dohvaća iz baze podataka.)
  $db = DB::getConnection();
	try
		{
		$st = $db->prepare( 'SELECT id,  username, password_hash, registration_sequence, has_registered FROM dz2_users WHERE username=:username' );
  		$st->execute( array( 'username' => $user ) );
		}
		catch( PDOException $e ) { exit( 'PDO error ' . $e->getMessage() ); }

    $row = $st->fetch();

  	if( $row === false )
  	{
  		echo "Korisnik s tim imenom ne postoji.". "<br/>";
  		return false;
  	}
  	else if( $row['has_registered'] === '0' )
  	{
      echo "Korisnik s tim imenom se nije još registrirao.". "<br/>";
  	  return false;
  	}
  	else if( !password_verify( $pass, $row['password_hash'] ) )
  	{
		echo "Lozinka nije ispravna."."<br/>";
  		return false;
  	}
    else {	
      return true;
    }
}


// Ako je u formi poslan neki username i password, provjerimo je li ispravan dodamo element u $_SESSION.
// Ključ elementa je 'login', a vrijednost username,string.
// String dobijemo kao hashiranu vrijednosti usernamea na kraj kojeg smo zalijepili neku tajnu riječ.
$secret_word = 'racunarski praktikum 2!!!';
if( isset( $_POST['username'] )
	&& isset( $_POST['password'] )
	&& validate( $_POST['username'], $_POST['password'] ) )
{
	$_SESSION['login'] = $_POST['username'] . ',' . md5( $_POST['username'] . $secret_word );
}


// Sad provjeravamo je li definirana $_SESSION['login'].
// Ako je, znači da je korisnik sad (ili ranije u sesiji) prošao validaciju.
// Dohvaćamo vrijednost $_SESSION['login'], te iz nje username.
unset( $username );
if( isset( $_SESSION['login'] ) )
{
	list( $c_username, $cookie_hash ) = explode( ',' , $_SESSION['login'] );

	if( md5( $c_username . $secret_word ) === $cookie_hash )
		$username = $c_username;
	else
		echo "Poslan je pokvareni kolačić :)" ;
}


// Sad provjeravamo je li korisnik kliknuo na logout. Ako je, uništavamo sesiju.
if( isset( $username ) && isset( $_POST['logout'] ) )
{
	session_unset();
	session_destroy();
	unset( $username );
}
?>

<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body>
<?php
	if( isset( $username ) ) {
		// Ako je korisnik ulogiran, ispiši mu poruku i gumb za logout.
		?>
		<h2>TeamUp!</h2>
		<h3>Izbornik</h3>
		<nav>
			<ul>
				<li><a href="teamup.php">Svi projekti</a></li>
				<li><a href="clan.php">Moji projekti</a></li>
				<li><a href="novi.php">Započni novi projekt</a></li>
			</ul>
		</nav>
		<h2>Svi projekti</h2>
		<form method="post" action="project_info.php">
		<table>
		<tr><th>Naslov</th><th>Autor</th><th>Status</th></tr>
		<?php 
		$st = $db->query( 'SELECT * FROM dz2_projects' );
			while( $row=$st->fetch() ){
				?>
				<tr>
				<td><button type="submit" name="project_id" value="<?php echo $row['id'];?>">
				<?php echo $row['title']; ?> </button></td>
				<?php
				foreach( $st2->fetchAll() as $row2 ){
					if($row2['id']===$row['id_user']){
						echo '<td>' . $row2['username'] . '</td>';
						break;
					}
				}
				$st2 = $db->query( 'SELECT id, username FROM dz2_users' );
				echo '<td>' . $row['status'] . '</td>';
				echo '</tr>';
			}
		?>
		</table>
		</form>
		<br>
		<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			<input type="hidden" name="logout">
			<input type="submit" value="Log Out">
		</form>
		<?php
	}
	else {
		// Ako nije ulogiran, ispiši mu formu za logiranje. ?>
		<h2>TeamUp login</h2>
		<form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
			Username: <input type="text" name="username"> <br />
			Password: <input type="password" name="password"> <br />
			<br>
			<input type="submit" value="Log In">
		</form>
		<?php
		//ako nije registriran, ponudi registraciju.
		?>
		<br><br><br><br>
		Ako se niste još registrirali, a to želite, možete se registrirati ovdje:
		<h2>TeamUp registracija</h2>
		<form method="POST" action="registracija.php">
			Username: <input type="text" name="username"> <br />
			Email: <input type="text" name="email"> <br />
			Password: <input type="password" name="password"> <br />
			<br>
			<button type="submit">Stvori korisnički račun</button>
		</form>
		<?php
	}
?>

</body>
</html>
