jQuery(function($){

  // Set all variables to be used in scope
  var frame,
	  metaBox = $('#gltf_select_scene_model.postbox'), // Your meta box id here
	  addModelLink = metaBox.find('.upload-main-model'),
	  delModelLink = metaBox.find( '.delete-main-model'),
	  modelContainer = metaBox.find( '.gltf-main-model-container'),
	  modelIdInput = metaBox.find( '.main-model-id' ),
		modelScaleInput = metaBox.find( '.main-model-scale' );
  
  // ADD IMAGE LINK
  addModelLink.on( 'click', function( event ){

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( frame ) {
			frame.open();
			return;
		}

		// Create a new media frame
		frame = wp.media({
			title: 'Select a GLTF model file',
			button: {
				text: 'Use this model'
			},
			multiple: false,  // Set to true to allow multiple files to be selected
			library: { type: [ 'model/gltf+json' ] }
		});

		// When an image is selected in the media frame...
		frame.on( 'select', function() {
			// Get media attachment details from the frame state
			var attachment = frame.state().get('selection').first().toJSON();

			var scale = modelScaleInput.val();

			if ( '' == scale ) {
				scale = '1.0';
			}

			// Send the attachment URL to our custom image input field.
			modelContainer.append( '<div class="gltf-model" data-model="'+attachment.url+'" data-scale="'+scale+'" style="width: 300px; height: 300px;"></div>' );

			// Send the attachment id to our hidden input
			modelIdInput.val( attachment.id );

			// Hide the add image link
			addModelLink.addClass( 'hidden' );

			// Unhide the remove image link
			delModelLink.removeClass( 'hidden' );

			jQuery( '.gltf-model' ).each( initializeGltfElement );
		});

		// Finally, open the modal on click
		frame.open();
  });
  
  
  // DELETE IMAGE LINK
  delModelLink.on( 'click', function( event ){
		event.preventDefault();

		// Clear out the preview image
		modelContainer.html( '' );

		// Un-hide the add image link
		addModelLink.removeClass( 'hidden' );

		// Hide the delete image link
		delModelLink.addClass( 'hidden' );

		// Delete the image id from the hidden input
		modelIdInput.val( '' );
  });

});