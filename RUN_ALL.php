#!/usr/bin/php 
<?php
include_once 'Expansion.php';
include_once 'Set.php';
include_once 'Card.php';

include_once 'simple_html_dom.php';

include('config.php');

$result = @mysql_query("SELECT id, name, abrev FROM sets;");
mysql_close($link);
$temp = new Card;

while ($row = mysql_fetch_assoc($result))
{
	exec("/usr/bin/php5 /work/live/mtg/get_cards.php " . $row['abrev'] . " \"" . $row['name'] . "\" >> /work/live/mtg/latest_run.txt");
	//$SET = $temp->GetSetFromString(addslashes($row['name']));
	// echo "#### " . str_pad($row['name'] . " ",50,"#",STR_PAD_RIGHT) . "\n";

}

exec("mv /work/live/mtg/latest_run.txt /work/live/mtg/run_" . date("Y-m-d") . ".txt");
?>
