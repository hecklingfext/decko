<?php
require 'login.php';

$deck_id = isset($_GET['deck_id']) ? $_GET['deck_id'] : 0;

$head = "
<head>
    <title>MTG Decko</title>
    <script type='text/javascript' src='js/jquery.js'></script>
    <script type='text/javascript' src='js/jquery.tokeninput.js'></script>
    <script type='text/javascript' src='js/Table.js'></script>

    <link rel='stylesheet' href='css/token-input.css' type='text/css' />
	<link rel='stylesheet' href='css/decko.css' type='text/css' />
    " . $script . "
    <link rel='SHORTCUT ICON' href='http://76.189.222.151/mtg/decko/images/favicon.ico'>
</head>";

$body = "
<body>

    " . getHeader() . "

	<div id='header'>
		<div id='brand'>MTG Decko</div>
                
        <div id='ui-panel'>" . ($_SESSION['usr'] ? "<a href='?logoff'>Log off</a>" : "Not Signed In") . "</div>
	</div>
    <div id='sidebar'>
		<div id='the_search_box'>
			<input type='text' style='width: 210px;' id='search-input' name='blah' />
			<script type='text/javascript'>
                var t;
				$(document).ready(function() {
                    t = new Table(".$deck_id.");
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
                    $('#sort_select').change(function () {
                        t.Reorder($(this).val());
                    });
                    updateDeckList();
				});
			</script>
		</div>
		<div id='deckList'></div>
        <div id='controls'>
            <button type='button' class='button' onclick='t.Clear()'>Clear</button>
            ".($_SESSION['usr'] ? "<button type='button' class='button' onclick='t.Save();updateDeckList();'>Save</button>" : "")."
        </div>
	</div>
    <div id='table_header'>
        <div id='deck_name'></div>
        <div id='sort_select_div' class='t_right'>
            <label for='sort_select' id='sort_select_label'>Sort Type:</label>
            <select id='sort_select'>
                <option value='' selected='selected'>Default</option>
                <option value='type'>Type</option>
                <option value='cmc'>CMC</option>
                <option value='rarity'>Rarity</option>
            </select>
        </div>
    </div>
	<div id='table'>
	
	</div>
";

// Print the page here
echo "<html>" . $head . $body . "</body></html>";
?>
