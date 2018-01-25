function get( what )
{
    return document.getElementById( what );
}

function errorDialog( message )
{
    var dialog = $('#dialogBox')
	.html( message + "<br/>" +
	       "<br/>Returning to the start page." )
	.dialog({
	    title: 'Error',
	    modal: true,
	    buttons: {
		OK: function() {
		    window.location.href = "index.php";
		}
	    },
	    close: function(event, ui) { 
		window.location.href = "index.php";
		}
	});
}

function parseJSONAndCheckErrors( response )
{
    var data;
    try 
    {
	data = JSON.parse( response );
    }
    catch ( e )
    {
	data =  new Object();
	data["Error"] = "Bad data was received.";
    }
    

    if ( data["Error"] )
    {
	errorDialog( data["Error"] );
    }
    return data;
}

function addCommas(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
	x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}


var fullscreenHelper =  {};

fullscreenHelper.requestFullScreen = function(element) {
 if (element.webkitRequestFullScreen) {
    element.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
 } else if (element.mozRequestFullScreen) {
    element.mozRequestFullScreen();
 }
};

fullscreenHelper.cancelFullScreen = function(element) {
 if (document.webkitCancelFullScreen) {
    document.webkitCancelFullScreen();
 } else if (document.mozCancelFullScreen) {
    document.mozCancelFullScreen();
 }
};

fullscreenHelper.onFullScreenChange = function(element, callback) {
    element.addEventListener('fullscreenchange', function(event) {
	callback(document.fullscreenEnabled);
    });
    element.addEventListener('webkitfullscreenchange', function(event) {
	var fullscreenEnabled = document.webkitFullscreenEnabled;
	if (fullscreenEnabled === undefined) {
	    fullscreenEnabled = document.webkitIsFullScreen;
	}
	callback(fullscreenEnabled);
    });
    element.addEventListener('mozfullscreenchange', function(event) {
	var fullscreenEnabled = document.mozFullscreenEnabled;
	if (fullscreenEnabled === undefined) {
	    fullscreenEnabled = document.mozFullScreen;
	}
	callback(fullscreenEnabled);
    });
};