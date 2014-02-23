alert ('Not yet ready, use nawala instead');


// Shorthand for jQuery(document).ready() with $ indicator support within the function
(function($) {
	$('[data-ajaxbutton]').click(function() {
		var buttonData = JSON.decode( $(this).data('ajaxbutton') );

		window.data = {
			model: buttonData.model, // <r;- the model must always be passed in
			action: buttonData.action
		};
		window.data[SessionToken] = 1;

		$.ajax({
		    // the URL for the request as set with JFactory::getAjax()->get[Nawala|Gantry|Core]AjaxSupport(); explained in the NAjax class description
		    url: AjaxURL,

		    // the data to send (will be converted to a query string)
		    data: window.data,

		    // whether this is a POST or GET request
		    type: "POST",

		    // the type of data we expect back
		    dataType : "json",

		    // code to run if the request succeeds;
		    // the response is passed to the function
		    success: function( json ) {
		        alert( "Success triggered!" );
		        alert( "json output to console!" );
		    	console.log( json );
		    },

		    // code to run if the request fails; the raw request and
		    // status codes are passed to the function
		    error: function( xhr, status ) {
		        alert( "Sorry, there was a problem!" );
		        alert( "xhr output to console!" );
		        console.log(xhr);
		        alert( "status output to console!" );
		        console.log(status);
		    },

		    // code to run regardless of success or failure
		    complete: function( xhr, status ) {
		        alert( "The request is complete!" );
		        alert( "xhr output to console!" );
		        console.log(xhr);
		        alert( "status output to console!" );
		        console.log(status);
		    }
		});
	});
})(jQuery);