<?php
$id = "";

if ($_POST)
{
	$id = $_POST['id'];
}
else
{
	$id = $argv[1];
}

$head = "
<head>
    <script type='text/javascript' src='js/jquery.js'></script>
    <script type='text/javascript' src='js/jquery.tokeninput.js'></script>
    <script type='text/javascript' src='js/Table.js'></script>

    <link rel='stylesheet' href='css/token-input.css' type='text/css' />
	<link rel='stylesheet' href='css/decko.css' type='text/css' />
</head>";

$body = "
<body>
	<div id='header'>
		<div id='brand'>MTG Decko</div>
	</div>
    <div id='sidebar'>
		<div id='the_search_box'>
			<input type='text' style='width: 210px;' id='search-input' name='blah' />
			<script type='text/javascript'>
                var t;
				$(document).ready(function() {
                    t = new Table();
					$('#search-input').tokenInput('tokenSearch.php', {
						method: 'POST',
						hintText: 'Card search...',
						noResultsText: 'No results found',
						insertSelector: '#the_search_box',
						onAdd: function (item) {
							if (item.id.substr(0,1) == ':') {
								var txt = item.id;
								$('#search-input').tokenInput('remove', {id: txt});
								$('#token-input-search-input').focus().val(txt);
							}
							else if (item.id.substr(0,1) == '#') {
								var txt = item.id.substr(1);
								$('#search-input').tokenInput('remove', {id: item.id});
								t.addCard(txt);
								$('#token-input-search-input').val(item.title);
								$('#search-input').tokenInput('search');
							}
						}
					});
				});
			</script>
		</div>
		<div id='deckList'></div>
        <div id='controls'>
            <buttone type='button' class='button' onclick='t.Clear()'>Clear</button>
        </div>
	</div>
	<div id='table'>
	
	</div>
";
	

	
$body .= "</body>";


echo "<html>" . $head . $body . "</html>";
?>
