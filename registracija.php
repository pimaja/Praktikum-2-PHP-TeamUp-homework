<?php
if( !preg_match( '/^[A-Za-z]{3,10}$/', $_POST['username'] ) )
{
	header("Location: teamup.php");
	exit;
}
else if( !filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL) )
{
	header("Location: teamup.php");
	exit;
}
else
{
	// Provjeri jel već postoji taj korisnik u bazi
	require_once 'zadatak5_db.php';
	require_once 'zadatak5_db_settings.php';
	$db = DB::getConnection();

	try
	{
		$st = $db->prepare( 'SELECT * FROM dz2_users WHERE username=:username' );
		$st->execute( array( 'username' => $_POST['username'] ) );
	}
	catch( PDOException $e ) { header("Location: teamup.php"); exit( 'Greška u bazi: ' . $e->getMessage() ); }

	if( $st->rowCount() !== 0 )
	{
		echo "Taj user u bazi već postoji";
		//header("Location: teamup.php");
		//exit;
	}

	// Dakle sad je napokon sve ok.
	// Dodaj novog korisnika u bazu. Prvo mu generiraj random string od 10 znakova za registracijski link.
	$reg_seq = '';
	for( $i = 0; $i < 4; ++$i )
		$reg_seq .= chr( rand(0, 25) + ord( 'a' ) ); // Zalijepi slučajno odabrano slovo

	try
	{
		$st = $db->prepare( 'INSERT INTO dz2_users(username, password_hash, email, registration_sequence, has_registered) VALUES ' .
							'(:username, :password, :email, :registration_sequence, 0)' );

		$st->execute( array( 'username' => $_POST['username'],
							 'password' => password_hash( $_POST['password'], PASSWORD_DEFAULT ),
							 'email' => $_POST['email'],
							 'registration_sequence'  => $reg_seq ) );
	}
	catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

	// Sad mu još pošalji mail
	$to       = $_POST['email'];
	$subject  = 'Registracijski mail';
	$message  = 'Poštovani ' . $_POST['username'] . "!\nZa dovršetak registracije kliknite na sljedeći link: ";
	$message .= 'http://' . $_SERVER['SERVER_NAME'] . htmlentities( dirname( $_SERVER['PHP_SELF'] ) ) . '/registracija.php';//'/register.php?niz=' . $reg_seq . "\n";
	$message .= "!\nA potom se ulogirajte za početak rada! :)";
	$headers  = 'From: rp2@studenti.math.hr' . "\r\n" .
				'Reply-To: rp2@studenti.math.hr' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();

	$isOK = mail($to, $subject, $message, $headers);

	if( !$isOK ){
		exit( 'Greška: ne mogu poslati mail. (Pokrenite na rp2 serveru.)' );
	}
	
	echo "Pogledajte mail koji Vam je poslan na Vasu email adresu.";
	
	try
	{
		$st = $db->prepare( 'SELECT * FROM dz2_users WHERE registration_sequence=:registration_sequence' );
		$st->execute( array( 'registration_sequence' => $reg_seq ) );
	}
	catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

	$row = $st->fetch();

	if( $st->rowCount() !== 1 )
		echo 'Taj registracijski niz ima ' . $st->rowCount() . 'korisnika, a treba biti točno 1 takav.';
	else
	{
		// Sad znamo da je točno jedan takav. Postavi mu has_registered na 1.
		try
		{
			$st = $db->prepare( 'UPDATE dz2_users SET has_registered=1 WHERE registration_sequence=:registration_sequence' );
			$st->execute( array( 'registration_sequence' => $reg_seq ) );
		}
		catch( PDOException $e ) { exit( 'Greška u bazi: ' . $e->getMessage() ); }

		//header("Location: teamup.php");
		//exit;
	}
}
?>
