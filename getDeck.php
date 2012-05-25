<?php
include('config.php');
include('Card.php');

session_name('deckoLogin');
session_set_cookie_params(2*7*24*60*60);
session_start();
if($_SESSION && $_SESSION['id'] && !isset($_COOKIE['tzRemember']) && !$_SESSION['rememberMe'])
{
	// If you are logged in, but you don't have the tzRemember cookie (browser restart)
	// and you have not checked the rememberMe checkbox:

	$_SESSION = array();
	session_destroy();

	// Destroy the session
}

$id = "";
$usr = "";

if ($_POST)
{
	$id = $_POST['id'];
    $usr = $_SESSION['usr'];
}
else
{
    $id = $argv[1];
    $usr = $argv[2];
    $_SESSION = array();
    $_SESSION['usr'] = $usr;
}

if ($usr != "")
{
    $sql = "SELECT name, decklist, updateDate FROM decks WHERE usr = '".$usr."' AND id = '".$id."';";
    $result = mysql_query($sql);
    if ($row = mysql_fetch_assoc($result))
    {
        $ret = array();
        $ret["name"] = $row['name'];// . " - " . $row['updateDate'];
        $ret["arr"] = json_decode($row['decklist']);
        echo json_encode($ret);
    }
    else
    {
        echo "false";
    }
}
else
{
    echo "notloggedin";
}

?>
