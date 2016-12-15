
function initializeGltfElement() {
	$el = jQuery(this);
	console.log( $el );
	var container, camera, scene, renderer, loader, gltf;

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

	var modelUrl = $el.data( 'model' );
	var modelScale = $el.data( 'scale' );
	container = $el.get(0);
	console.log( "loading model from " + modelUrl + " at scale "+modelScale);
	scene = new THREE.Scene();

	camera = new THREE.PerspectiveCamera( 75, container.offsetWidth / container.offsetHeight, 1, 2000 );
	camera.position.set(0, 2, 3);
	scene.add( camera );
	addLights();

	renderer = new THREE.WebGLRenderer({antialias:true});
	renderer.setClearColor( 0x222222 );
	renderer.setPixelRatio( window.devicePixelRatio );
	renderer.setSize( container.offsetWidth, container.offsetHeight );

	addControls();

	var manager = new THREE.LoadingManager();
    manager.onProgress = function ( item, loaded, total ) {
        console.log( item, loaded, total );
    };

	container.appendChild( renderer.domElement );

	THREE.GLTFLoader.Shaders.removeAll(); // remove all previous shaders
	loader = new THREE.GLTFLoader;

	loader.load( modelUrl, function( data ) {
		gltf = data;

		var object = gltf.scene;
		object.scale.set(modelScale, modelScale, modelScale);

		var animations = gltf.animations;
        if ( animations && animations.length ) {
            mixer = new THREE.AnimationMixer( object );
            for ( var i = 0; i < animations.length; i ++ ) {
                var animation = animations[ i ];
                mixer.clipAction( animation ).play();
            }
        }

		scene.add( object );
	} );

	animate();
}

jQuery( function() {
	jQuery( '.gltf-model' ).each( initializeGltfElement );
} );