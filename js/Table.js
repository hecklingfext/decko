var listChanged = false;

function updateDeckList() {
    $.ajax({
        type: "GET",
        url: "getRecentDecks.php",
        success: function(c)
        {
            $("#recent_deck_lists").html(c);
        }
    });
}

function Collection() {
}

function Card (id) {
	this.id = "";
	this.img_id = "";
	this.number = "";
	this.name = "";
	this.set = 0;
	this.rarity = "";
	this.legendary = false;
	this.type = "";
	this.subtype = "";
	this.power = "";
	this.toughness = "";
	this.cost = "";
	this.cmc = 0;
	this.text = "";
	this.flavor = "";
	this.illus = "";
	this.abrev = "";
	
	this.position = {
		top: 0,
		left: 0
	};

    if (typeof id !== 'undefined') {
        var that = this ;
        $.ajax({
            type: "GET",
            async: false,
            url: "getCard.php",
            data: "id=" + id,
            success: function(c)
            {
                var c_o = jQuery.parseJSON(c);

                that.id = c_o.Id;
                that.number = c_o.Number;
                that.name = c_o.Name;
                that.set = c_o.Set;
                that.rarity = c_o.Rarity;
                that.legendary = c_o.Legendary;
                that.type = c_o.Type;
                that.subtype = c_o.Subtype;
                that.power = c_o.Power;
                that.toughness = c_o.Toughness;
                that.cost = c_o.Cost;
                that.cmc = c_o.CMC;
                that.text = c_o.Text;
                that.flavor = c_o.Flavor;
                that.illus = c_o.Illus;
                that.abrev = c_o.Abrev;
            }
        });
    } else {
    
    }
}

function Table (d_in) {
    var deck_id = typeof d_in !== 'undefined' ? d_in : 0;

	var coll = new Collection();
	var collectionCount = 0;

	var tableWidth = $(window).width() - $("#sidebar").width();
	var tableHeight = $(window).height() - $("#header").height();
	$("#table").css({"width": tableWidth+"px", "height": tableHeight+"px"});
	
	var cardWidth = 160;
	var cardHeight = 240
	
	var colWidth = 180;
	var colHeight = 305;
	
	var vertOffset = 20;

    var sortType = "type";

	load();
	
	this.addCard = function (id) {
		c = new Card(id);

		pushCard(c);
        deckUpdate();
	};

    this.removeCard = function (id) {
        for (cs in coll) {
            if (coll[cs] !== 'undefined' && coll[cs][0].id == id) {
                $('#' + coll[cs][coll[cs].length-1].img_id).remove();
                coll[cs].pop();
                if (coll[cs].length == 0) {
                    delete coll[cs];
                }
                update();
                break;
            }
        }
        deckUpdate();
    };
    
    this.Save = function() {
        var deck_name = "";
        if(typeof(Storage)!=="undefined")
        {
            deck_name = localStorage['current_name'];
        }

        var name=prompt("Enter Deck Name",deck_name);
        if (name!=null && name!="")
        {
            var usr = $("#usr_name").html();
            $.ajax({
                type: "POST",
                async: false,
                url: "saveDeck.php",
                data: {
                    usr:        usr,
                    name:       name,
                    decklist:   localStorage['current'],
                    format:     0
                },
                success: function(c)
                {
                    if (c == "true") {
                        //alert("Success!");//Should still alert somehow...
                        if(typeof(Storage)!=="undefined") {
                            localStorage['current_name'] = name;
                            $("#deck_name").html(name);
                            deckClear();
                        }
                    } else {
                        alert("Not your deck! - log in?");
                    }
                }
            });
        }
    };

    this.Clear = function() {
        clear();
    };

    this.Reorder = function(sort) {
        update(sort);
    };
	
    function getList (coll, sort) {
        sort = typeof sort !== 'undefined' ? sort : "type";
        if (sort == "type")
        {
            var lands = "";
            var lands_count = 0;
            
            var creatures = "";
            var creatures_count = 0;
            
            var planeswalkers = "";
            var planeswalkers_count = 0;
            
            var others = "";
            var others_count = 0;
            
            var totals = "";

            for (cs in coll) {
                if (coll[cs] !== 'undefined') {
                    switch(coll[cs][0].type) {
                        case "Creature":
                        case "Artifact Creature":
                        case "Eaturecray":
                        case "Summon":
                        case "Legendary Creature":
                        case "Legendary Artifact Creature":
                            creatures += coll[cs].length + " " + cs + arrows(coll[cs][0].id);
                            creatures_count += coll[cs].length;
                            break;
                        case "Basic":
                        case "Land":
                        case "Legendary Land":
                            lands += coll[cs].length + " " + cs + arrows(coll[cs][0].id);
                            lands_count += coll[cs].length;
                            break;
                        case "Planeswalker":
                            planeswalkers += coll[cs].length + " " + cs + arrows(coll[cs][0].id);
                            planeswalkers_count += coll[cs].length;
                            break;
                        default:
                            others += coll[cs].length + " " + cs + arrows(coll[cs][0].id);
                            others_count += coll[cs].length;
                            break;
                    }
                }
            }

            creatures = "<br /><b>Creatures: " + creatures_count + "</b><br />" + creatures + "<br /><br />";
            lands = "<b>Lands: " + lands_count + "</b><br />" + lands + "<br /><br />";
            planeswalkers = "<b>Planeswalkers: " + planeswalkers_count + "</b><br />" + planeswalkers + "<br /><br />";
            others = "<b>Others: " + others_count + "</b><br />" + others + "<br /><br />";
            
            totals = "<br /><h3>Total Cards: " + (creatures_count+lands_count+planeswalkers_count+others_count) + "</h3>";

            return creatures+planeswalkers+others+lands+totals;
        } else {
            var the_list = {};
            var total = 0;

            for (cs in coll) {
                if (coll[cs] !== 'undefined') {
                    var card = coll[cs][0];
                    var key = card[sort];

                    if (typeof the_list[key] === 'undefined')
                    {
                        the_list[key] = "<br /><b>"+key+"</b><br /><br />";
                    }

                    the_list[key] += coll[cs].length + " " + cs + arrows(coll[cs][0].id);
                    total += coll[cs].length;
                }
            }

            var out = "";

            for (key in the_list)
            {
                out += the_list[key];
            }

            out += "<br /><h3>Total Cards: " + total + "</h3>";

            return out;
        }
    }
	
	function pushCard(Card) {
		count = typeof coll[Card.name] !== 'undefined' ? coll[Card.name].length+1 : 1;
		
		var imgSrc = "img/" + Card.abrev + "/" + Card.number + ".jpg";
		var imgId = Card.name.replace(/\W/g,"") + "_" + count;
		var imgTag = "<img id='" + imgId + "' class='card' src='" + imgSrc + "' alt='" + Card.name + "' height='" + cardHeight + "' width='" + cardWidth + "' />"
		$("#table").prepend(imgTag);
		
		Card.img_id = imgId;
		if (typeof coll[Card.name] !== 'undefined') {
			coll[Card.name].push(Card);
		} else {
			coll[Card.name] = new Array(Card);
		}
		collectionCount++;
		
		update();
	}
	
	function update(sort) {
		sort = typeof sort !== 'undefined' && sort != '' ? sort : 'default';
        var t_sorted = {};

        store();

        tableWidth = $(window).width() - $("#sidebar").width();
        tableHeight = $(window).height() - $("#header").height();
        $("#table").css({"width": tableWidth+"px", "height": tableHeight+"px"});

		for (cs in coll) {
            if (sort != "default") {
                if (typeof coll[cs][0] !== 'undefined') {
                    if (typeof t_sorted[coll[cs][0][sort]] === 'undefined') {t_sorted[coll[cs][0][sort]] = [];}
                    t_sorted[coll[cs][0][sort]].push(coll[cs]);
                }
            } else {
                if (typeof coll[cs][0] !== 'undefined') {
                    var s_type = getDefault(coll[cs][0].type);
                    if (typeof t_sorted[s_type] === 'undefined') {t_sorted[s_type] = [];}
                    t_sorted[s_type].push(coll[cs]);
                }
            }
        }

		var x = 0;
		var y = 0;
        var y_extra = 0;
		
		for (s_type in t_sorted) {
            for (cs in t_sorted[s_type]) {
                var rowY = y;
                for(var i = 0; i < t_sorted[s_type][cs].length; i++) {
                    var c = t_sorted[s_type][cs][i];
                    c.position.top = rowY;
                    rowY += vertOffset;
                    if (i > 3 && y_extra < ((i-3)*vertOffset)) {
                        y_extra = ((i-3)*vertOffset);
                    }
                    c.position.left = x;
                    $("#"+c.img_id).css({"top": c.position.top+"px", "left": c.position.left+"px", "z-index": (i+10)});
                }
                
                x += colWidth;
                if (x > (tableWidth-colWidth)) {
                    x = 0;
                    y += colHeight + y_extra;
                    y_extra = 0;
                }
			}

            if (x != 0) {
                x = 0;
                y += colHeight + y_extra;
                y_extra = 0;
            }
		}
		
		$("#deckList").html(getList(coll));
	}

    function store() {
        if(typeof(Storage)!=="undefined")
        {
            var store = [];
            for (cs in coll) {
                for (var i=0;i<coll[cs].length;i++) {
                    store.push(coll[cs][i].id);
                }
            }
            localStorage['current']=JSON.stringify(store);
        }
    }

    function load() {
        if (deck_id == 0) {
            if(typeof(Storage)!=="undefined")
            {
                var c = localStorage["current"];
                if(typeof(c)!=="undefined") {
                    var store = jQuery.parseJSON(c);

                    for (var i=0;i<store.length;i++) {
                        c = new Card(store[i]);
                        pushCard(c);
                    }
                } else {
                    localStorage["current"] = "";
                }

                update();
                $("#deck_name").html(localStorage["current_name"]);
            }
        } else {
            var usr = $("#usr_name").html();
            if (usr != "") {
                $.ajax({
                    type: "POST",
                    async: false,
                    url: "getDeck.php",
                    data: {
                        usr:        usr,
                        id:         deck_id
                    },
                    success: function(c)
                    {
                        if (c == "notloggedin") {
                            alert("Are you logged in?");
                        } else if (c == "false") {
                            alert("This deck doesn't seem to belong to you...");
                        } else {
                            //alert("Success!");//Should still alert somehow...
                            if(typeof(Storage)!=="undefined")
                            {
                                var temp = jQuery.parseJSON(c);
                                var store = temp.arr;

                                for (var i=0;i<store.length;i++) {
                                    c = new Card(store[i]);
                                    pushCard(c);
                                }

                                localStorage["current"] = c;
                                localStorage['current_name'] = temp.name;
                                $("#deck_name").html(temp.name);

                                update();
                            }
                        }
                    }
                });
            } else {
                // They have to sign in
            }
        }
    }

    function clear() {
        if(typeof(Storage)!=="undefined") {
            delete localStorage["current"];
            delete localStorage["current_name"];
            $("#deck_name").html("");
            coll = new Collection();
            $("#table").html("");
            update();
        }
    }

    function getDefault(type) {
        switch(type) {
            case "Creature":
            case "Artifact Creature":
            case "Eaturecray":
            case "Summon":
            case "Legendary Creature":
            case "Legendary Artifact Creature":
                return "Creature";
            case "Basic":
            case "Land":
            case "Legendary Land":
                return "Land";
            case "Planeswalker":
                return "Planeswalker";
            default:
                return "Other";
        }
    }

    function arrows(id) {
        var out = " &nbsp; <div class='countAdjust'><a href='javascript:t.addCard("+id+")'><img src='images/up.png'></a>";
        out += " &nbsp; <div class='countAdjust'><a href='javascript:t.removeCard("+id+")'><img src='images/down.png'></a>";
        out += "<br />";

        return out;
    }

    function deckUpdate() {
        listChanged = true;
        var deck_name = $("#deck_name").html();
        if (deck_name.substr(0,1) != "*") {$("#deck_name").html("*"+deck_name);}
    }

    function deckClear() {
        listChanged = false;
        var deck_name = $("#deck_name").html();
        if (deck_name.substr(0,1) == "*") {$("#deck_name").html(deck_name.substr(1));}
    }
}
