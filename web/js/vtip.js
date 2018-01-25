/**
Vertigo Tip by www.vertigo-project.com
Requires jQuery
*/

this.vtip = function() {    
    this.xOffset = -10; // x distance from mouse
    this.yOffset = 10; // y distance from mouse       
    
    $("[helpText]").hover(    
        function(e) {
	    if ( vtip.enabled )
	    {
		this.t = this.getAttribute( "helpText" );
		this.top = (e.pageY + yOffset); this.left = (e.pageX + xOffset);
		
		$('body').append( '<p id="vtip"><img id="vtipArrow" />' + this.t + '</p>' );
                
		$('p#vtip #vtipArrow').attr("src", 'images/vtip_arrow.png');
		$('p#vtip').css("top", this.top+"px").css("left", this.left+"px").fadeIn("fast");
	    }
        },
        function() {
	    if ( vtip.enabled )
	    {
		$("p#vtip").fadeOut("fast", function() { $(this).remove(); } );
	    }
        }
    ).mousemove(
        function(e) {
	    if ( vtip.enabled )
	    {
		this.top = (e.pageY + yOffset);
		this.left = (e.pageX + xOffset);
                
		$("p#vtip").css("top", this.top+"px").css("left", this.left+"px");
            }
	}
    );            
    
};

this.vtip.enabled = false;

jQuery(document).ready(function($){vtip();}) 