<?php
include('config.php');

$sql = "SELECT *
		FROM sets
		WHERE order_no <> 0
        ORDER BY order_no DESC;";
$sets = @mysql_query($sql);
if (!$sets)
{
	$message  = 'Invalid query: ' . mysql_error() . "\n";
	$message .= 'Whole query: ' . $sql . "\n\n========================\n";
	die($message);
}

while($set = mysql_fetch_assoc($sets))
{
    $dir = "/work/live/mtg/decko/img/" . $set['abrev'] . "/";
    if(!is_dir($dir))
    {
        mkdir($dir);
    }
    else
    {
        continue; // We already got all these cards
    }
    $sql = "SELECT *
            FROM cards
            WHERE set_no = " . $set['id'] . "
            ORDER BY number ASC;";
    $cards = @mysql_query($sql);
    if (!$cards)
    {
        $message  = 'Invalid query: ' . mysql_error() . "\n";
        $message .= 'Whole query: ' . $sql . "\n\n========================\n";
        die($message);
    }
	echo $sql . "\n\n";
    while($card = mysql_fetch_assoc($cards))
    {
		echo "STILL HERE\n\n";
        print_r($card);
        $file = $card['number'] . ".jpg";
        $path = $dir . $file;
        $src = "http://magiccards.info/scans/en/" . $set['abrev'] . "/" . $card['number'] . ".jpg";
        if(!file_exists($path))
        {
            exec("wget -O $path $src");
        }
        sleep(2);
    }
}
?>
