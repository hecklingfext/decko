<?php
class Card
{
	public $Number = "";
	public $Name = "";
	public $Set = 0;
	public $Rarity = "";
	public $Legendary = false;
	public $Type = "";
	public $Subtype = "";
	public $Power = "";
	public $Toughness = "";
	public $Cost = "";
	public $CMC = 0;
	public $Text = "";
	public $Flavor = "";
	public $Illus = "";
	public $Abrev = "";


	function GetSetFromString($s)
	{
		$result = @mysql_query("SELECT id FROM sets WHERE name='" . $s . "' LIMIT 1;");
		$row = mysql_fetch_assoc($result);

		return $row['id'];
	}

    function GetCardById($id)
    {
        $sql = "SELECT c.*, s.abrev FROM cards AS c
                JOIN sets AS s ON c.set_no = s.id
                WHERE c.id = " . $id . " LIMIT 1;";

        $result = @mysql_query($sql);
        $row = mysql_fetch_assoc($result);

        $this->Id = intval($row['id']);
        $this->Number = intval($row['number']);
        $this->Name = $row['name'];
        $this->Set = intval($row['set_no']);
        $this->Rarity = $row['rarity'];
        $this->Legendary = $row['legendary'] == 1;
        $this->Type = $row['type'];
        $this->Subtype = $row['subtype'];
        $this->Power = $row['power'];
        $this->Toughness = $row['toughness'];
        $this->Cost = $row['cost'];
        $this->CMC = intval($row['cmc']);
        $this->Text = $row['text'];
        $this->Flavor = $row['flavor'];
        $this->Illus = $row['illus'];
        $this->Abrev = $row['abrev'];
    }

	function Exists()
	{
		$sql = "SELECT * FROM cards WHERE set_no=" . $this->Set . " AND number='" . $this->Number . "' LIMIT 1;";
		$result = @mysql_query($sql);

		if (@mysql_num_rows($result) > 0) { return true; }
		else { return false; }
	}
	
	function PPrint()
	{
		echo str_pad($this->Number, 5, ' ', STR_PAD_RIGHT) . str_pad($this->Cost, 12, ' ', STR_PAD_BOTH) . str_pad($this->CMC, 5, ' ', STR_PAD_BOTH) . str_pad($this->Type, 25, ' ', STR_PAD_BOTH) . str_pad($this->Name, 25, ' ', STR_PAD_BOTH) . str_pad($this->Rarity, 10, ' ', STR_PAD_BOTH) . "\n";
	}

	function Add()
	{
		if (!$this->Exists())
		{
			$sql  = "INSERT INTO cards VALUES (";
			$sql .= "'', ";
			$sql .= "'" . addslashes($this->Number) . "', ";
			$sql .= "'" . addslashes($this->Name) . "', ";
			$sql .= $this->Set . ", ";
			$sql .= "'" . $this->Rarity . "', ";
			$sql .= ($this->Legendary ? 1 : 0) . ", ";
			$sql .= "'" . addslashes($this->Type) . "', ";
			$sql .= "'" . addslashes($this->Subtype) . "', ";
			$sql .= "'" . $this->Power . "', ";
			$sql .= "'" . $this->Toughness . "', ";
			$sql .= "'" . addslashes($this->Cost) . "', ";
			$sql .= $this->CMC . ", ";
			$sql .= "'" . addslashes($this->Text) . "', ";
			$sql .= "'" . addslashes($this->Flavor) . "', ";
			$sql .= "'" . addslashes($this->Illus) . "');";

			$result = @mysql_query($sql);
			if (!$result)
			{
				$message  = 'Invalid query: ' . mysql_error() . "\n";
				$message .= 'Whole query: ' . $sql . "\n\n========================\n";
				die($message);
			}

			$this->Id = mysql_insert_id();
		}
		else
		{
			$result = @mysql_query("SELECT * FROM cards WHERE set_no='" . $this->Set . "' AND number='" . $this->Number . "' LIMIT 1;");

			if (!$result) {
				$message  = 'Invalid query: ' . mysql_error() . "\n";
				$message .= 'Whole query: ' . $query;
				die($message);
			}

			$row = mysql_fetch_assoc($result);

			$this->Id = $row['id'];
		}
	}
}
?>
