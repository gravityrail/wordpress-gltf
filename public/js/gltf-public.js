(function( $ ) {
	'use strict';

	 window.GltfScene = function() {
	 	//scene goes here
	 	return {
	 		render: function( id, scene ) {
	 			console.log("rendering "+id+": "+JSON.stringify( scene ));
	 		}
	 	}
	 }();

})( jQuery );
