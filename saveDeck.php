<?php
include('config.php');
include('Card.php');

if ($_POST)
{
	$usr = $_POST['usr'];
	$name = $_POST['name'];
	$format = $_POST['format'];
	$decklist = $_POST['decklist'];

    $sql = "INSERT INTO decks(usr,name,format,decklist) VALUES ('".$usr."','".$name."',".$format.",'".$decklist."');";
    if (mysql_query($sql))
    {
        echo "true";
    }
    else
    {
        echo "false => ";
    }
}
?>
