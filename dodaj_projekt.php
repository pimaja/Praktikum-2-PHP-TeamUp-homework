<?php
session_start();

require_once 'zadatak5_db.php';
require_once 'zadatak5_db_settings.php';

$db = DB::getConnection();

$username='';
if( isset( $_SESSION['login'] ) ){
	list( $c_username, $cookie_hash ) = explode( ',' , $_SESSION['login'] );
	$secret_word = 'racunarski praktikum 2!!!';
	if( md5( $c_username . $secret_word ) === $cookie_hash ){
		$username = $c_username; echo $username.'<br>';
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
$id_user = $row0['id'];

if(isset($_POST['naslov'])){
	if( !preg_match( '/^[a-zšđžčćA-ZŠĐŽČĆ0-9,.!)(:;_ ]{3,50}$/', $_POST['naslov'] ) ){
		header( 'Location: novi.php' );
		exit;
	}
	if(isset($_POST['opis'])){
		if( !preg_match( '/^[a-zšđžčćA-ZŠĐŽČĆ0-9,.)(!:;_ ]{3,200}$/', $_POST['opis'] ) ){
			header( 'Location: novi.php' );
			exit;
		}
		if(isset($_POST['broj_clanova'])){
			$options = array( 'options' => array( 'min_range' => 1, 'max_range' => 10 ) );
			if( filter_var( $_POST['broj_clanova'], FILTER_VALIDATE_INT, $options ) === false )
			{
				header( 'Location: novi.php' );
				exit;
			}
			$id=0;
			$st = $db->query( 'SELECT id FROM dz2_projects' );
			while( $row=$st->fetch() )
				$id=$row['id'];
			$id=$id+1;
			$st = $db->prepare( 'INSERT INTO dz2_projects(id, id_user, title, abstract, number_of_members, status) VALUES (:id, :id_user, :title, :abstract, :number_of_members, :status )');
			$st->execute( array( 'id'=>$id,'id_user'=>$id_user,'title'=>$_POST['naslov'],'abstract'=>$_POST['opis'],'number_of_members'=>$_POST['broj_clanova'],'status'=>'open' ) );
			$id2=0;
			$st = $db->query( 'SELECT id FROM dz2_members' );
			while( $row=$st->fetch() )
				$id2=$row['id'];
			$id2=$id2+1;
			$st = $db->prepare( 'INSERT INTO dz2_members(id, id_project, id_user, member_type) VALUES (:id, :id_project, :id_user, :member_type )');
			$st->execute( array( 'id'=>$id2, 'id_project'=>$id,'id_user'=>$id_user, 'member_type'=>'member' ) );
		}
	}
}
header("Location: clan.php");
exit();
?>
