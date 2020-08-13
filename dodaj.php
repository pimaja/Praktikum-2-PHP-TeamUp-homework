<?php
require_once 'zadatak5_db.php';
require_once 'zadatak5_db_settings.php';

$db = DB::getConnection();

if(isset($_POST["dodaj"])){
	list( $user_id, $project_id ) = explode( ',' , $_POST['dodaj'] );
	//echo $user_id.' '.$project_id;
	//trazimo sljedeci id broj
	$id=0;
	$st = $db->query( 'SELECT id FROM dz2_members' );
	while( $row=$st->fetch() )
		$id=$row['id'];
	$id=$id+1;
	
	$st = $db->prepare( 'INSERT INTO dz2_members(id, id_project, id_user, member_type) VALUES (:id, :id_project, :id_user, :member_type )');
	$st->execute( array( 'id'=>$id, 'id_project'=>$project_id,'id_user'=>$user_id, 'member_type'=>'member' ) );
	
	$st = $db->prepare( 'SELECT number_of_members FROM dz2_projects WHERE id=:id');
	$st->execute( array( 'id' => $project_id));
	$row=$st->fetch();
	$st2=$db->prepare( 'SELECT id FROM dz2_members WHERE id_project=:id_project');
	$st2->execute( array( 'id_project' => $project_id));
	$br=0;
	while($row2=$st2->fetch())
		$br++;
	echo $br.' '.$row['number_of_members'];
	if($br===$row['number_of_members']){
		$st3=$db->prepare('UPDATE dz2_projects SET status=:status WHERE id=:id');
		$st3->execute(array('status'=>'closed', 'id'=>$project_id));
	}	
}
header("Location: clan.php");
exit;
?>