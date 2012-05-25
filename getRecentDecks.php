<?php
require 'config.php';

session_name('deckoLogin');
session_set_cookie_params(2*7*24*60*60);
session_start();
if($_SESSION['id'] && !isset($_COOKIE['tzRemember']) && !$_SESSION['rememberMe'])
{
	// If you are logged in, but you don't have the tzRemember cookie (browser restart)
	// and you have not checked the rememberMe checkbox:

	$_SESSION = array();
	session_destroy();

	// Destroy the session
}

$my_decks = "<ul>";
$usr = $_SESSION['usr'];
$q = "SELECT id,name,updateDate FROM decks WHERE usr = '".$usr."' ORDER BY updateDate desc LIMIT 7";
$result = mysql_query($q);
while($row=mysql_fetch_array($result))
{
    $my_decks .= "<a href='?deck_id=".$row['id']."'><li>".$row['name']." - ".$row['updateDate']."</li></a>";
}
$my_decks .= "</ul>";

echo $my_decks;
?>
