<?php
class Set
{
	public $Id = 0;
	public $OrderNo = 0;
	public $Expansion = 0;
	public $Name = "";
	public $Abrev = "";
	public $SetLink = "";
	public $ReleaseDate = "" ;

	function Exists()
	{
		$result = @mysql_query("SELECT * FROM sets WHERE abrev='" . $this->Abrev . "' LIMIT 1;");
        if (@mysql_num_rows($result) > 0)
        {
            return true;
        }
		else
        {
            return false;
        }
	}

    function GetById($i)
    {
        $result = @mysql_query("SELECT * FROM sets WHERE id='" . $i . "' LIMIT 1;");

        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $query;
            die($message);
        }
        else if (@mysql_num_rows($result) > 0)

        {
            $row = mysql_fetch_assoc($result);

            $this->Id = $row['id'];
            $this->OrderNo = $row['order_no'];
            $this->Expansion = $row['expansionId'];
            $this->Name = $row['name'];
            $this->Abrev = $row['abrev'];
            $this->SetLink = $row['setlink'];
            $this->Abrev = $row['abrev'];

            return true;
        }

        return false;
    }

    function GetByName($n)
    {
        $result = @mysql_query("SELECT * FROM sets WHERE name='" . $n . "' LIMIT 1;");

        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $query;
            die($message);
        }
        else if (@mysql_num_rows($result) > 0)
        {
            $row = mysql_fetch_assoc($result);

            $this->Id = $row['id'];
            $this->OrderNo = $row['order_no'];
            $this->Expansion = $row['expansionId'];
            $this->Name = $row['name'];
            $this->Abrev = $row['abrev'];
            $this->SetLink = $row['setlink'];
            $this->Abrev = $row['abrev'];

            return true;
        }

        return false;
    }

    function GetByAbrev($a)
    {
        $sql = "SELECT * FROM sets WHERE abrev='" . $a . "' LIMIT 1;";
        $result = @mysql_query($sql);

        if (!$result) {
            $message  = 'Invalid query: ' . mysql_error() . "\n";
            $message .= 'Whole query: ' . $query;
            die($message);
        }
        else if (@mysql_num_rows($result) > 0)
        {
            $row = mysql_fetch_assoc($result);

            $this->Id = $row['id'];
            $this->OrderNo = $row['order_no'];
            $this->Expansion = $row['expansionId'];
            $this->Name = $row['name'];
            $this->Abrev = $row['abrev'];
            $this->SetLink = $row['setlink'];
            $this->Abrev = $row['abrev'];

            return true;
        }

        return false;
    }
	
	function Add()
	{
		if (!$this->GetByAbrev($this->Abrev))
		{
			$sql  = "INSERT INTO sets VALUES (";
			$sql .= "'', ";
			$sql .= "'" . $this->OrderNo . "', ";
			$sql .= "'" . $this->Expansion . "', ";
			$sql .= "'" . $this->Name . "', ";
			$sql .= "'" . $this->Abrev . "', ";
			$sql .= "'" . $this->SetLink . "', ";
			$sql .= "'" . $this->ReleaseDate . "');";

			$result = @mysql_query($sql);
			if (!$result)
			{
				$message  = 'Invalid query: ' . mysql_error() . "\n";
				$message .= 'Whole query: ' . $query;
				die($message);
			}

			$this->GetById(mysql_insert_id());
            return true;
		}
        else
        {
            print "CANNONT ADD, ALREADY EXISTS!\n";
            return false; // False because it was not added, but it should be filled with proper info
        }
	}
}
?>
