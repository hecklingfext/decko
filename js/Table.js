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

function Table () {
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
	};

    this.Clear = function() {
        clear();
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
                switch(coll[cs][0].type) {
                    case "Creature":
                    case "Artifact Creature":
                    case "Eaturecray":
                    case "Summon":
                    case "Legendary Creature":
                    case "Legendary Artifact Creature":
                        creatures += coll[cs].length + " " + cs + "<br />";
                        creatures_count += coll[cs].length;
                        break;
                    case "Basic":
                    case "Land":
                    case "Legendary Land":
                        lands += coll[cs].length + " " + cs + "<br />";
                        lands_count += coll[cs].length;
                        break;
                    case "Planeswalker":
                        planeswalkers += coll[cs].length + " " + cs + "<br />";
                        planeswalkers_count += coll[cs].length;
                        break;
                    default:
                        others += coll[cs].length + " " + cs + "<br />";
                        others_count += coll[cs].length;
                        break;
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
                var card = coll[cs][0];
                var key = card[sort];

                if (typeof the_list[key] === 'undefined')
                {
                    the_list[key] = "<br /><b>"+key+"</b><br /><br />";
                }

                the_list[key] += coll[cs].length + " " + cs + "<br />";
                total += coll[cs].length;
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
	
	function update() {
        store();
		var x = 0;
		var y = 0;
		
		for (cs in coll) {
			var rowY = y;
			for(var i = 0; i < coll[cs].length; i++) {
				var c = coll[cs][i];
				c.position.top = rowY;
				rowY += vertOffset;
				c.position.left = x;
				$("#"+c.img_id).css({"top": c.position.top+"px", "left": c.position.left+"px", "z-index": (i+10)});
			}
			
			x += colWidth;
			if (x > (tableWidth-colWidth)) {
				x = 0;
				y += colHeight;
			}
			
		}
		
		$("#deckList").html(getList(coll,"cmc"));
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
        if(typeof(Storage)!=="undefined")
        {
            var c = localStorage["current"];
            var store = jQuery.parseJSON(c);

            for (var i=0;i<store.length;i++) {
                c = new Card(store[i]);
                pushCard(c);
            }

            update();
        }
    }

    function clear() {
        if(typeof(Storage)!=="undefined") {
            delete localStorage["current"];
            coll = new Collection();
            $("#table").html("");
            update();
        }
    }
}
