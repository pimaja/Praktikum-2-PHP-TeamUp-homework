<?php

// Manualno inicijaliziramo bazu ako već nije.
require_once 'db.class.php';

$db = DB::getConnection();

$has_tables = false;

try
{
	$st = $db->prepare(
		'SHOW TABLES LIKE :tblname'
	);

	$st->execute( array( 'tblname' => 'dz2_users' ) );
	if( $st->rowCount() > 0 )
		$has_tables = true;

	$st->execute( array( 'tblname' => 'dz2_projects' ) );
	if( $st->rowCount() > 0 )
		$has_tables = true;

	$st->execute( array( 'tblname' => 'dz2_members' ) );
	if( $st->rowCount() > 0 )
		$has_tables = true;
}
catch( PDOException $e ) { exit( "PDO error [show tables]: " . $e->getMessage() ); }


if( $has_tables )
{
	exit( 'Tablice dz2_users / dz2_projects / dz2_members već postoje. Obrišite ih pa probajte ponovno.' );
}


try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS dz2_users (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'username varchar(50) NOT NULL,' .
		'password_hash varchar(255) NOT NULL,'.
		'email varchar(50) NOT NULL,' .
		'registration_sequence varchar(20) NOT NULL,' .
		'has_registered int)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error [create dz2_users]: " . $e->getMessage() ); }

echo "Napravio tablicu dz2_users.<br />";

try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS dz2_projects (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'id_user int NOT NULL,' .
		'title varchar(50) NOT NULL,' .
		'abstract varchar(1000) NOT NULL,' .
		'number_of_members int NOT NULL,' .
		'status varchar(10) NOT NULL)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error [create dz2_projects]: " . $e->getMessage() ); }

echo "Napravio tablicu dz2_projects.<br />";


try
{
	$st = $db->prepare(
		'CREATE TABLE IF NOT EXISTS dz2_members (' .
		'id int NOT NULL PRIMARY KEY AUTO_INCREMENT,' .
		'id_project INT NOT NULL,' .
		'id_user INT NOT NULL,' .
		'member_type varchar(20) NOT NULL)'
	);

	$st->execute();
}
catch( PDOException $e ) { exit( "PDO error [create dz2_members]: " . $e->getMessage() ); }

echo "Napravio tablicu dz2_members.<br />";


// Ubaci neke korisnike unutra
try
{
	$st = $db->prepare( 'INSERT INTO dz2_users(username, password_hash, email, registration_sequence, has_registered) VALUES (:username, :password, \'a@b.com\', \'abc\', \'1\')' );

	$st->execute( array( 'username' => 'mirko', 'password' => password_hash( 'mirkovasifra', PASSWORD_DEFAULT ) ) );
	$st->execute( array( 'username' => 'ana', 'password' => password_hash( 'aninasifra', PASSWORD_DEFAULT ) ) );
	$st->execute( array( 'username' => 'maja', 'password' => password_hash( 'majinasifra', PASSWORD_DEFAULT ) ) );
	$st->execute( array( 'username' => 'slavko', 'password' => password_hash( 'slavkovasifra', PASSWORD_DEFAULT ) ) );
	$st->execute( array( 'username' => 'pero', 'password' => password_hash( 'perinasifra', PASSWORD_DEFAULT ) ) );
	$st->execute( array( 'username' => 'klara', 'password' => password_hash( 'klarinasifra', PASSWORD_DEFAULT ) ) );
}
catch( PDOException $e ) { exit( "PDO error [insert dz2_users]: " . $e->getMessage() ); }

echo "Ubacio u tablicu dz2_users.<br />";


// Ubaci neke projekte unutra (ovo nije baš pametno ovako raditi, preko hardcodiranih id-eva usera)
try
{
	$st = $db->prepare( 'INSERT INTO dz2_projects(id_user, title, abstract, number_of_members, status) VALUES (:id, :t, :a, :no, :st)' );

	$st->execute( array( 'id' => 1, 't' => 'Igra - Go', 'a' => 'Implementirat ćemo Go u kojem korisnik može igrati protiv AI-ja.', 'no' => 3, 'st' => 'open' ) ); // mirko
	$st->execute( array( 'id' => 2, 't' => 'Novi Fejsbuk', 'a' => 'U ovom projektu napravit ćemo novi Fejsbuk koji će još skupljati još više informacija o svojim korisnicima i koji će
                još češće i još slučajnije curiti te informacije trećim stranama.', 'no' => '5', 'st' => 'open' ) ); // ana
	$st->execute( array( 'id' => 3, 't' => 'Web-aplikacija za recepte', 'a' => 'Napravit ćemo aplikaciju sa svim receptima iz kuharice moje bake.', 'no' => 4, 'st' => 'open' ) ); // maja
 	$st->execute( array( 'id' => 1, 't' => 'Kao Amazon, ali bolje', 'a' => 'Jeff Bezos nema pojma, naš Amazon će biti puno bolji.', 'no' => 2, 'st' => 'closed' ) ); // mirko
	$st->execute( array( 'id' => 4, 't' => 'Projekt za RP2', 'a' => 'Već ćemo nešto smisliti, prvo idemo okupiti tim.', 'no' => 4, 'st' => 'open' ) ); // slavko
}
catch( PDOException $e ) { exit( "PDO error [dz2_projects]: " . $e->getMessage() ); }

echo "Ubacio u tablicu dz2_projects.<br />";


// Ubaci neke članove unutra (ovo nije baš pametno ovako raditi, preko hardcodiranih id-eva usera i projekata)
try
{
	$st = $db->prepare( 'INSERT INTO dz2_members(id_project, id_user, member_type) VALUES (:id_project, :id_user, :type)' );

	$st->execute( array( 'id_project' => 1, 'id_user' => 1, 'type' => 'member' ) ); // autor (mirko) - go
	$st->execute( array( 'id_project' => 2, 'id_user' => 2, 'type' => 'member' ) ); // autor (ana) - fejsbuk
	$st->execute( array( 'id_project' => 3, 'id_user' => 3, 'type' => 'member' ) ); // autor (maja) - recepti
	$st->execute( array( 'id_project' => 4, 'id_user' => 1, 'type' => 'member' ) ); // autor (mirko) - amazon
	$st->execute( array( 'id_project' => 5, 'id_user' => 4, 'type' => 'member' ) ); // autor (slavko) - rp2
	$st->execute( array( 'id_project' => 2, 'id_user' => 3, 'type' => 'invitation_accepted' ) ); // maja - fejsbuk
	$st->execute( array( 'id_project' => 2, 'id_user' => 5, 'type' => 'application_accepted' ) ); // pero - fejsbuk
	$st->execute( array( 'id_project' => 4, 'id_user' => 4, 'type' => 'application_accepted' ) ); // slavko - amazon
	$st->execute( array( 'id_project' => 3, 'id_user' => 5, 'type' => 'member' ) ); // pero - recepti
	$st->execute( array( 'id_project' => 3, 'id_user' => 1, 'type' => 'application_pending' ) ); // mirko - recepti
	$st->execute( array( 'id_project' => 5, 'id_user' => 2, 'type' => 'invidation_pending' ) ); // ana - rp2
}
catch( PDOException $e ) { exit( "PDO error [dz2_members]: " . $e->getMessage() ); }

echo "Ubacio u tablicu dz2_members.<br />";

?>
