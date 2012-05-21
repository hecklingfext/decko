<?php
include('config.php');
include('Card.php');

$id = 0;
if ($_GET)
{
	$id = intval($_GET['id']);
}
else
{
	$id = intval($argv[1]);
}

$c = new Card();
$c->GetCardById($id);

echo json_encode($c);
