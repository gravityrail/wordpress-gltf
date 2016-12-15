
function initializeGltfElement() {
	$el = jQuery(this);

	var container, camera, scene, renderer;

	function addCamera() {
		camera = new THREE.PerspectiveCamera( 75, container.offsetWidth / container.offsetHeight, 1, 2000 );
		camera.position.set(0, 2, 3);
		scene.add( camera );
	}

	function addLights() {
		var ambient = new THREE.AmbientLight( 0x101030 );
	    scene.add( ambient );

	    var directionalLight = new THREE.DirectionalLight( 0xffeedd );
	    directionalLight.position.set( 0, 0, 1 );
	    scene.add( directionalLight );
	}

	function addControls() {
		controls = new THREE.OrbitControls( camera, renderer.domElement );
		controls.userPan = false;
		controls.userPanSpeed = 0.0;
		controls.maxDistance = 5000.0;
		controls.maxPolarAngle = Math.PI * 0.495;
		controls.autoRotate = true;
		controls.autoRotateSpeed = -10.0;
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