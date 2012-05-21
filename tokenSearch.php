<?php
include('config.php');

class SearchToken
{
    public $op = "";
    public $type = "";
    public $q = "";
}

$q = "";
$t = array();

if ($_POST)
{
	$q = $_POST['q'];
    $t = explode(",", $_POST['t']);
}
else
{
	$q = $argv[1];
	if(isset($argv[2])) {$t = explode(",", $argv[2]);}
}

$tokens = getOtherTokens($t);
$curr = getCurrToken($q, $tokens);
$j = array();

//print_r($curr);
//print_r($tokens);
//echo "\nSearch:" . $q . "|\n";

switch($curr->type)
{
	case "name":
	case "text":
	case "flavor":
		$j = getCardResults($curr, $tokens);
		break;
	case "illus":
		$j = getCardIllus($curr, $tokens);
		break;
	case "set":
	case "set_no":
		$j = getSet($curr, $tokens);
		break;
	case "format":
		$j = getFormat($curr, $tokens);
		break;
	case "type":
		$j = getCardType($curr, $tokens);
		break;
	case "subtype":
		$j = getCardSubtype($curr, $tokens);
		break;
	case "cmc":
		$j = getCardCMC($curr, $tokens);
		break;
	case "power":
		$j = getCardPower($curr, $tokens);
		break;
	case "toughness":
		$j = getCardToughness($curr, $tokens);
		break;
	case "rarity":
		$j = getCardRarity($curr, $tokens);
		break;
	case "":
		$j = getSearchType($q);
		break;
	default:
		break;
}


echo json_encode($j);


function getSearchType($q)
{
	if (preg_match("/^:(\w+)/", $q, $matches))
	{
		$sql = "SELECT name FROM search_types WHERE name LIKE '%" . $matches[1] . "%' ";
	
		//echo("\n" . $sql . "\n");
		
		$sql_res=mysql_query($sql);
		$t = array();

		while($row=mysql_fetch_array($sql_res))
		{
			$st = array();
			$st['id'] = ":" . $row['name'];
			$st['name'] = $row['name'];
			array_push($t, $st);
		}
		
		return $t;
	}
	else
	{
		$sql = "SELECT name FROM search_types";
	
		//echo("\n" . $sql . "\n");
		
		$sql_res=mysql_query($sql);
		$t = array();

		while($row=mysql_fetch_array($sql_res))
		{
			$st = array();
			$st['id'] = ":" . $row['name'];
			$st['name'] = $row['name'];
			array_push($t, $st);
		}
		
		return $t;
	}
}

function getCardResults($curr, $tokens)
{
	$sql_clause = "WHERE cards." . $curr->type . " LIKE '%" . $curr->q . "%' ";

	foreach ($tokens as $key => $type)
	{
		$cnt = 0;

		$type_string = $type[0]->op . " (";
		foreach($type as $token)
		{
			$type_string .= getComparisonString($key, $token->q);
			$cnt++;
			if ($cnt != count($type))
			{
				$type_string .= " " . $type[$cnt]->op . " ";
			}
		}
		$type_string .= ") ";
		$sql_clause .= $type_string;
	}

	$sql = "SELECT cards.id, sets.abrev, cards.name, sets.order_no, cards.cost 
			FROM cards
			JOIN sets ON cards.set_no = sets.id " . $sql_clause . " LIMIT 50";
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$toAdd = true;
		
		$card = array();
        $card['id'] = "#" . $row['id'];
        $card['name'] = $row['name'] . "[" . $row['abrev'] . "](" . $row['cost'] . ")";
		$card['title'] = $row['name'];
		$card['order_no'] = $row['order_no'];
		
		foreach ($t as $key => $x)
		{
			if ($x['title'] == $card['title'])
			{
				if ($x['order_no'] < $card['order_no'])
				{
					$x = $card;
				}
				$toAdd = false;
				break;
			}
		}
        if ($toAdd)
		{
			array_push($t, $card);
		}
	}
	
	return $t;
}

function getCardText($curr, $tokens)
{
	$sql_clause = "WHERE cards." . $curr->type . " LIKE '%" . $curr->q . "%' ";

	foreach ($tokens as $key => $type)
	{
		$cnt = 0;

		$type_string = $type[0]->op . " (";
		foreach($type as $token)
		{
			$type_string .= getComparisonString($key, $token->q);
			$cnt++;
			if ($cnt != count($type))
			{
				$type_string .= " " . $type[$cnt]->op . " ";
			}
		}
		$type_string .= ") ";
		$sql_clause .= $type_string;
	}

	$sql = "SELECT cards.id, sets.abrev, cards.name, cards.text, sets.order_no, cards.cost FROM cards JOIN sets ON cards.set_no = sets.id " . $sql_clause;
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$toAdd = true;
		
		$card = array();
        $card['id'] = "=" . $row['id'];
        $card['name'] = $row['name'] . "[" . $row['abrev'] . "](" . $row['cost'] . "): " . $row['text'];
		$card['title'] = $row['name'];
		$card['order_no'] = $row['order_no'];
		for ($x = 0; $x <= count($t); $x++)
		{
			if ($t[$x]['title'] == $card['title'])
			{
				if ($t[$x]['order_no'] < $card['order_no'])
				{
					$t[$x] = $card;
				}
				$toAdd = false;
				break;
			}
		}
        if ($toAdd)
		{
			array_push($t, $card);
		}
	}
	
	return $t;
}

function getCardFlavor($curr, $tokens)
{
	$sql_clause = "WHERE cards." . $curr->type . " LIKE '%" . $curr->q . "%' ";

	foreach ($tokens as $key => $type)
	{
		$cnt = 0;

		$type_string = $type[0]->op . " (";
		foreach($type as $token)
		{
			$type_string .= getComparisonString($key, $token->q);
			$cnt++;
			if ($cnt != count($type))
			{
				$type_string .= " " . $type[$cnt]->op . " ";
			}
		}
		$type_string .= ") ";
		$sql_clause .= $type_string;
	}

	$sql = "SELECT cards.id, sets.abrev, cards.name, cards.text, sets.order_no, cards.cost FROM cards JOIN sets ON cards.set_no = sets.id " . $sql_clause;
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$toAdd = true;
		
		$card = array();
        $card['id'] = "=" . $row['id'];
        $card['name'] = $row['name'] . "[" . $row['abrev'] . "](" . $row['cost'] . "): " . $row['text'];
		$card['title'] = $row['name'];
		$card['order_no'] = $row['order_no'];
		for ($x = 0; $x <= count($t); $x++)
		{
			if ($t[$x]['title'] == $card['title'])
			{
				if ($t[$x]['order_no'] < $card['order_no'])
				{
					$t[$x] = $card;
				}
				$toAdd = false;
				break;
			}
		}
        if ($toAdd)
		{
			array_push($t, $card);
		}
	}
	
	return $t;
}

function getFormat($curr, $tokens)
{
	$sql = "SELECT id, name FROM format_types WHERE name LIKE '%" . $curr->q . "%' ";
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$op_char = ($curr->op == "OR" ? "-" : "&");
		
        $format = array();
		$format['id'] = "format" . $op_char . $row['id'];
        $format['name'] = $op_char . $row['name'];
        array_push($t, $format);
	}
	
	return $t;
}

function getSet($curr, $tokens)
{
	$sql = "SELECT id, name, abrev FROM sets WHERE name LIKE '%" . $curr->q . "%' ";
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$op_char = ($curr->op == "OR" ? "-" : "&");
		
        $set = array();
		$set['id'] = "set_no" . $op_char . $row['id'];
        $set['name'] = $op_char . $row['name'];
        array_push($t, $set);
	}
	
	return $t;
}

function getCardType($curr, $tokens)
{
	$sql = "SELECT type, COUNT(DISTINCT name) AS c FROM cards WHERE type LIKE '%" . $curr->q . "%' GROUP BY type ORDER BY c DESC";
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$op_char = ($curr->op == "OR" ? "-" : "&");
		
        $type = array();
		$type['id'] = "type" . $op_char . $row['type'];
        $type['name'] = $op_char . $row['type'];
        array_push($t, $type);
	}
	
	return $t;
}

function getCardSubtype($curr, $tokens)
{
	$sql = "SELECT subtype, COUNT(DISTINCT name) AS c FROM cards WHERE subtype LIKE '%" . $curr->q . "%' GROUP BY subtype ORDER BY c DESC";
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$op_char = ($curr->op == "OR" ? "-" : "&");
		
        $stype = array();
		$stype['id'] = "subtype" . $op_char . $row['subtype'];
        $stype['name'] = $op_char . $row['subtype'];
        array_push($t, $stype);
	}
	
	return $t;
}

function getCardCMC($curr, $tokens)
{
	$sql = "";
	if ($curr->q == "")
	{
		$sql = "SELECT cmc, COUNT(DISTINCT name) AS c FROM cards GROUP BY cmc ORDER BY c DESC";
	}
	else
	{
		$sql = "SELECT cmc, COUNT(DISTINCT name) AS c FROM cards WHERE cmc = " . $curr->q . " GROUP BY cmc ORDER BY c DESC";
	}
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$op_char = ($curr->op == "OR" ? "-" : "&");
		
        $cmc = array();
		$cmc['id'] = "cmc" . $op_char . $row['cmc'];
        $cmc['name'] = $op_char . "CMC=" . $row['cmc'];
        array_push($t, $cmc);
	}
	
	return $t;
}

function getCardPower($curr, $tokens)
{
	$sql = "";
	if ($curr->q == "")
	{
		$sql = "SELECT power, COUNT(DISTINCT name) AS c FROM cards GROUP BY power ORDER BY c DESC";
	}
	else
	{
		$sql = "SELECT power, COUNT(DISTINCT name) AS c FROM cards WHERE power = " . $curr->q . " GROUP BY power ORDER BY c DESC";
	}
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$op_char = ($curr->op == "OR" ? "-" : "&");
		
        $power = array();
		$power['id'] = "power" . $op_char . $row['power'];
        $power['name'] = $op_char . "power=" . $row['power'];
        array_push($t, $power);
	}
	
	return $t;
}

function getCardToughness($curr, $tokens)
{
	$sql = "";
	if ($curr->q == "")
	{
		$sql = "SELECT toughness, COUNT(DISTINCT name) AS c FROM cards GROUP BY toughness ORDER BY c DESC";
	}
	else
	{
		$sql = "SELECT toughness, COUNT(DISTINCT name) AS c FROM cards WHERE toughness = " . $curr->q . " GROUP BY toughness ORDER BY c DESC";
	}
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$op_char = ($curr->op == "OR" ? "-" : "&");
		
        $toughness = array();
		$toughness['id'] = "toughness" . $op_char . $row['toughness'];
        $toughness['name'] = $op_char . "toughness=" . $row['toughness'];
        array_push($t, $toughness);
	}
	
	return $t;
}

function getCardRarity($curr, $tokens)
{
	$sql = "SELECT id, name FROM rarity_types WHERE name LIKE '%" . $curr->q . "%' ";
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$op_char = ($curr->op == "OR" ? "-" : "&");
		
        $rarity = array();
		$rarity['id'] = "rarity" . $op_char . $row['name'];
        $rarity['name'] = $op_char . $row['name'];
        array_push($t, $rarity);
	}
	
	return $t;
}

function getCardIllus($curr, $tokens)
{
	$sql = "";
	if ($curr->q == "")
	{
		$sql = "SELECT illus, COUNT(DISTINCT name) AS c FROM cards GROUP BY illus ORDER BY c DESC";
	}
	else
	{
		$sql = "SELECT illus, COUNT(DISTINCT name) AS c FROM cards WHERE illus LIKE '%" . $curr->q . "%' GROUP BY illus ORDER BY c DESC";
	}
	
	//echo("\n" . $sql . "\n");
	
	$sql_res=mysql_query($sql);
    $t = array();

	while($row=mysql_fetch_array($sql_res))
	{
		$op_char = ($curr->op == "OR" ? "-" : "&");
		
        $illus = array();
		$illus['id'] = "illus" . $op_char . $row['illus'];
        $illus['name'] = $op_char . $row['illus'];
        array_push($t, $illus);
	}
	
	return $t;
}

function getCurrToken($q, $tokens)
{
	$currToken = new SearchToken();
	
	if (substr($q, 0, 1) == ":")
	{
		$search = preg_match("/^:(\w+)(&|-|\s)([\w\s]*)/", $q, $matches);
		if (count($matches) > 0)
		{
			$currToken->type = getTypeString($matches[1]);
			
			switch($matches[2])
			{
				case "&":
					$currToken->op = "AND";
					break;
				case "-":
					$currToken->op = "OR";
					break;
				case " ":
					$currToken->op = ((isset($tokens)) && (isset($tokens[$currToken->type])) && (count($tokens[$currToken->type]) > 0)) ? "OR" : "AND";
					break;
				default:
					$currToken->op = "AND";
					break;
			}
			$currToken->q = $matches[3];
		}
		
		return $currToken;
	}
	else
	{
		$currToken->type = "name";
		$currToken->op = "AND";
		$currToken->q = $q;
		
		return $currToken;
	}
}

function getOtherTokens($t)
{
	$tokens = array();
	
	if (count($t) > 0)
	{
		foreach($t as $t_str)
		{
			$temp = new SearchToken();
			if(preg_match("/^(\w+)(&|-|\s)(.*)$/", $t_str, $m))
			{
				$temp->type = $m[1];
				switch($m[2])
				{
					case "&":
						$temp->op = "AND";
						break;
					case "-":
						$temp->op = "OR";
						break;
					default:
						$temp->op = "AND";
						break;
				}
				$temp->q = $m[3];

				if(!isset($tokens[$temp->type]))
				{
					$tokens[$temp->type] = array();
				}
				array_push($tokens[$temp->type], $temp);
			}
		}
	}
	
	return $tokens;
}

function getTypeString($in)
{
	switch($in)
	{
		case "set":
			return "set_no";
			break;
		case "type":
		case "text":
		case "name":
		case "format":
		case "subtype":
		case "set_no":
		case "cmc":
		case "power":
		case "toughness":
		case "rarity":
		case "flavor":
		case "illus":
		default:
			return $in;
	}
}

function getComparisonString($key, $q)
{
	$out = "";
	
	switch($key)
	{
		case "format":
			return ("set_no IN (SELECT set_id FROM formats WHERE format_no = " . $q . ")");
			break;
		case "type":
		case "subtype":
		case "text":
		case "name":
		case "rarity":
		case "flavor":
		case "illus":
			return ($key . " LIKE '%" . $q . "%'");
			break;
		case "set_no":
		case "cmc":
		case "power":
		case "toughness":
		default:
			return ($key . "='" . $q . "'");
			break;
	}
}
?>
