
$(document).ready(function(){
	var scenariolinks = $('a[href^="#scenario"]');
	var scenariotexts = $('[id^="scenario"]');
	
	scenariotexts.hide();

	scenariolinks.click( function (event) {
		event.preventDefault();
		var scen = $(this).attr("href");
		var vis = $(scen).is(":visible");
		if (!vis){
			//get group
			groupname = $(scen).attr('class');
			scenariotexts.each(function() {
				if ($(this).attr('class') == groupname){
					$(this).hide();
				}

		    });
			//scenariotexts.hide();
		}
		
		console.log("showing:"+scen)
		$(scen).fadeToggle( "slow" );
		parent.$(parent.document).trigger("resize");
		return false;
	});
	
	var scenariolinks2 = $('a[href^="#ascenario"]');
	var scenariotexts2 = $('[id^="ascenario"]');
	scenariotexts2.hide();

	scenariolinks2.click( function (event) {
		event.preventDefault();
		var scen = $(this).attr("href");
		$(scen).fadeToggle("slow");
		parent.$(parent.document).trigger("resize");
		return false;
	});


	var scenariolinks3 = $('a[href^="#hidescenario"]');
	var scenariotexts3 = $('[id^="hidescenario"]');
	scenariotexts3.hide();

	scenariolinks3.click( function (event) {
		event.preventDefault();
		var scen = $(this).attr("href");
		$(scen).fadeToggle("slow");
		groupname = $(scen).attr('class');
		scenariolinks3.each(function() {
			if ($(this).attr('class') == groupname){
				$(this).unbind( "click" );
			}

	    });
		parent.$(parent.document).trigger("resize");
		return false;
	});

});