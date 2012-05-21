<?php
include('config.php');
$q="";
if ($_POST)
{
	$q = $_POST['q'];
}
else
{
	$q = $argv[1];
}

$t = "";

if(strlen($q)>=3)
{
	$sql = "SELECT cards.name, cards.number, cards.text, sets.name AS set_name, sets.abrev, sets.order_no
			FROM legality
			JOIN sets ON sets.id = set_id
			JOIN cards ON cards.name = legality.name && cards.set_no = set_id
			WHERE legality.name LIKE '%" . mysql_real_escape_string($q) . "%'
			ORDER BY order_no DESC
			LIMIT 25;";

	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
        /*
		$name=$row['name'];
		$img="user_img/alex.jpg";

		$t .= "<div class='display_box' align='left'>";
		$t .= "<img src='http://magiccards.info/scans/en/" . $row['abrev'] . "/" . $row['number'] . ".jpg' style='width:312px; float:right; margin-right:6px' />";
		$t .= $name;

		$t .= "<br /><span style='font-size:28px; color:#999999'>" . $row['set_name'] . "</span>";
		$t .= "<br /><span style='font-size:22px;'>" . $row['text'] . "</span></div><br />";
         */
        $r = array();
        $r['name'] = $row['name'];
        $r['number'] = $row['number'];
        $r['text'] = $row['text'];
        $r['set_name'] = $row['set_name'];
        $r['abrev'] = $row['abrev'];
        $r['image'] = "http://magiccards.info/scans/en/" . $row['abrev'] . "/" . $row['number'] . ".jpg";
        array_push($t, $r);
	}
}
echo json_encode($t);
?>
