<?php
include('config.php');
include('Card.php');

$id = "";
$usr = "";

if ($_POST)
{
	$id = $_POST['id'];
    $usr = $_POST['usr'];
}
else
{
    $id = $argv[1];
    $usr = $argv[2];
}

$sql = "SELECT name, decklist, updateDate FROM decks WHERE usr = '".$usr."' AND id = '".$id."';";
$result = mysql_query($sql);
if ($row = mysql_fetch_assoc($result))
{
    $ret = array();
    $ret["name"] = $row['name'] . " - " . $row['updateDate'];
    $ret["arr"] = json_decode($row['decklist']);
    echo json_encode($ret);
}
else
{
    echo "false";
}

?>
