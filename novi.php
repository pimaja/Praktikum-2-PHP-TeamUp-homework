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
	<h2>Započni novi projekt</h2>
	<form method="post" action="dodaj_projekt.php">
	Unesi naslov: <input type="text" name="naslov">
	<br>
	Unesi opis: <input type="text" name="opis">
	<br>
	Unesi ciljani broj članova: <input type="text" name="broj_clanova">
	<br>
	<button type="submit"> Dodaj! </button>
	</form>
	<br>
	<form method="POST" action="teamup.php">
		<input type="hidden" name="logout">
		<input type="submit" value="Log Out">
	</form>
</body>
</html>