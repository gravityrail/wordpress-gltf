
function initializeGltfElement() {
	$el = jQuery(this);

	var container, camera, scene, renderer;

	function addCamera() {
		camera = new THREE.PerspectiveCamera( 75, container.offsetWidth / container.offsetHeight, 1, 2000 );
		camera.position.set(0, 2, 3);
		scene.add( camera );
	}

	function addLights() {
		// var ambient = new THREE.AmbientLight( 0x101030 );
		var ambient = new THREE.AmbientLight( 0xFFFFFF, 1 );
	    scene.add( ambient );

	    var directionalLight = new THREE.DirectionalLight( 0xffeedd );
	    directionalLight.position.set( 0, 0, 1 );
	    scene.add( directionalLight );
	}

	function addWebVRButton( effect ) {
		var button = document.createElement( 'button' );
		button.style.position = 'absolute';
		button.style.left = 'calc(50% - 50px)';
		button.style.bottom = '20px';
		button.style.width = '100px';
		button.style.border = '0';
		button.style.padding = '8px';
		button.style.cursor = 'pointer';
		button.style.backgroundColor = '#000';
		button.style.color = '#fff';
		button.style.fontFamily = 'sans-serif';
		button.style.fontSize = '13px';
		button.style.fontStyle = 'normal';
		button.style.textAlign = 'center';
		button.style.zIndex = '999';
		button.textContent = 'ENTER VR';
		button.onclick = function() {
			effect.isPresenting ? effect.exitPresent() : effect.requestPresent();
		};
		window.addEventListener( 'vrdisplaypresentchange', function ( event ) {

			button.textContent = effect.isPresenting ? 'EXIT VR' : 'ENTER VR';

		}, false );

		container.appendChild( button );
	}

	function addFallbackControls() {
		console.log("doing regular");
		controls = new THREE.OrbitControls( camera, renderer.domElement );
		controls.userPan = false;
		controls.userPanSpeed = 0.0;
		controls.maxDistance = 5000.0;
		controls.maxPolarAngle = Math.PI * 0.495;
		controls.autoRotate = false;
	}

	function addControls() {
		console.log("adding controls");
		// controls.autoRotateSpeed = -10.0;

		// add WebVR controls
		if ( navigator.getVRDisplays !== undefined ) {
			console.log("doing VR");
			controls = new THREE.VRControls( camera );
			effect = new THREE.VREffect( renderer );
			navigator.getVRDisplays()
				.then( function ( displays ) {
					effect.setVRDisplay( displays[ 0 ] );
					controls.setVRDisplay( displays[ 0 ] );
					addWebVRButton( effect );
				} )
				.catch( function () {
					// no displays
					addFallbackControls();
				} );
		} else {
			addFallbackControls();
		}
	}

	function addRenderer() {
		renderer = new THREE.WebGLRenderer({antialias:true});
		renderer.setClearColor( 0x222222 );
		renderer.setPixelRatio( window.devicePixelRatio );
		renderer.setSize( container.offsetWidth, container.offsetHeight );
	}

	function addLoadingLogger() {
		var manager = new THREE.LoadingManager();
	    manager.onProgress = function ( item, loaded, total ) {
	        console.log( item, loaded, total );
	    };
	}

	function loadModel( modelUrl, modelScale ) {
		THREE.GLTFLoader.Shaders.removeAll(); // remove all previous shaders
		var loader = new THREE.GLTFLoader;
		loader.load( modelUrl, function( data ) {
			var object = data.scene;
			object.scale.set(modelScale, modelScale, modelScale);

			var animations = data.animations;
	        if ( animations && animations.length ) {
	            mixer = new THREE.AnimationMixer( object );
	            for ( var i = 0; i < animations.length; i ++ ) {
	                var animation = animations[ i ];
	                mixer.clipAction( animation ).play();
	            }
	        }

			scene.add( object );
		} );
	}

	function render() {
		renderer.render( scene, camera );
	}

	function animate() {
		requestAnimationFrame( animate );
		THREE.GLTFLoader.Animations.update();
		THREE.GLTFLoader.Shaders.update(scene, camera);
		render();
		controls.update();
	}

	container = $el.get(0);

	scene = new THREE.Scene();

	addLights(); // LIGHTS!
	addCamera(); // CAMERA!
	addRenderer(); // ACTION! :)
	addControls();
	addLoadingLogger();

	container.appendChild( renderer.domElement );

	loadModel( $el.data( 'model' ), $el.data( 'scale' ) );

	animate();
}

jQuery( function() {
	jQuery( '.gltf-model' ).each( initializeGltfElement );
} );