#!/usr/bin/php 
<?php
include_once 'Expansion.php';
include_once 'Set.php';
include_once 'Card.php';

include_once 'simple_html_dom.php';

include('config.php');

if (isset($argv[1]))
{
    $SET = new Set;
    if (!$SET->GetByAbrev($argv[1]))
    {
        $SET->OrderNo = isset($argv[3]) ? $argv[3] : 0;
        $SET->Name = $argv[2];
        $SET->Abrev = $argv[1];
        if ($SET->Add())
        {
			$SET->Id = 149;
            echo "#### " . str_pad($SET->Name . " ",50,"#",STR_PAD_RIGHT) . "\n";

            $url = "http://magiccards.info/query?q=" . rawurlencode("++e:" . $SET->Abrev . "/en") . "&v=spoiler&s=issue";
			
            $f=file_get_html($url);

            foreach($f->find('table',3)->find('td') as $x)
            {
                if (preg_match("/colspan/",$x,$fail))
                {
                    continue;
                }

                $CARD = new Card;

                $span = $x->find('span',0);
                $ps = $x->find('p');

                preg_match("/.*en\/(.+)\.html\">(.*)<\/a>/",$span,$matches);
                $CARD->Number = $matches[1];
                $CARD->Name = $matches[2];


                preg_match("/> (.*), <i>(.*)<\/i>/",$ps[0],$a);
                $CARD->Set = $SET->Id;
                $CARD->Rarity = $a[2];

                preg_match("/<p>\s*(.*),\s+(.*)<\/p>/",$ps[1],$b);
				
                if (preg_match("/(.*) \((.*)\)/",$b[2],$cost))
                {
                    $CARD->Cost = $cost[1];
                    $CARD->CMC = $cost[2];
                }

                preg_match("/^(\w+)/", $b[1], $type);

                if ($type[1] == "Legendary")
                {
                    $CARD->Legendary = true;
                    preg_match("/Legendary (\w+)/", $b[1], $type);
                }

                switch($type[1])
                {
                    case "Artifact":
                        if (preg_match("/^(.+) - (.*) (.*)\/(.*)/", $b[1], $art))
                        {
                            $CARD->Type = $art[1];
                            $CARD->Subtype = $art[2];
                            $CARD->Power = $art[3];
                            $CARD->Toughness = $art[4];
                        }
                        else
                        {
                            $CARD->Type = $type[1];
                            if (preg_match("/^(.+) - (.*)/", $b[1], $type))
                            {
                                $CARD->Subtype = $type[2];
                            }
                        }
                        break;
                    case "Creature":
						$b[1] = str_replace("\xe2\x80\x94", '-', $b[1]);
                        if (preg_match("/^(.+) - (.*) (.*)\/(.*)$/", $b[1], $type))
                        {
                            $CARD->Type = $type[1];
                            $CARD->Subtype = $type[2];
                            $CARD->Power = $type[3];
                            $CARD->Toughness = $type[4];
                        }
                        else
                        {
                            echo "ERROR in adding type\n";
                        }
                        break;
                    default:
                        $CARD->Type = $type[1];
                        if (preg_match("/^(.+) - (.*)/", $b[1], $type))
                        {
                            $CARD->Subtype = $type[2];
                        }
                        break;
                }

                preg_match("/<b>(.*)<\/b>/",$ps[2],$c);
                $CARD->Text = $c[1];
				
                preg_match("/<i>(.*)<\/i>/",$ps[3],$d);
                $CARD->Flavor = $d[1];

                preg_match("/ (.*)<\/p>/",$ps[4],$e);
                $CARD->Illus = $e[1];

                //var_dump($CARD);
                $CARD->Add();
                $CARD->PPrint();
                unset($CARD);
            }

            echo "\n\n";
            //break;
        }

    }

}


function utfCharToNumber($char)
{
	$i = 0;
	$number = '';
	while (isset($char{$i}))
	{
		$number.= ord($char{$i});
		++$i;
	}
return $number;
}
?>
