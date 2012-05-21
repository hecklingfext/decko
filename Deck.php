<?php
class Deck
{
	public $Id = 0;
	public $list = array();
	
	function Exists()
	{
		$sql = "SELECT * FROM cards WHERE set_no=" . $this->Set . " AND number='" . $this->Number . "' LIMIT 1;";
		$result = @mysql_query($sql);

		if (@mysql_num_rows($result) > 0) { return true; }
		else { return false; }
	}
}
?>