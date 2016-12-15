

jQuery( function() {
	var container, camera, scene, renderer, loader;

	function addLights() {
		var ambient = new THREE.AmbientLight( 0x222222 );
		scene.add( ambient );

		var directionalLight = new THREE.DirectionalLight( 0xdddddd );
		directionalLight.position.set( 0, 0, 1 ).normalize();
		scene.add( directionalLight );

		spot1   = new THREE.SpotLight( 0xffffff, 1 );
		spot1.position.set( 10, 20, 10 );
		spot1.angle = 0.25;
		spot1.distance = 1024;
		spot1.penumbra = 0.75;

		scene.add( spot1 );
	}

	function render() {
		renderer.render( scene, camera );
	}

	function animate() {
		requestAnimationFrame( animate );
		THREE.GLTFLoader.Animations.update();
		THREE.GLTFLoader.Shaders.update(scene, camera);
		render();
	}

	container = jQuery( '.gltf-model' ).first();//document.getElementById( 'container' );
	modelUrl = container.data( 'model' );
	container = container.get(0);
	console.log( "loading model from " + modelUrl );
	scene = new THREE.Scene();
	camera = new THREE.PerspectiveCamera( 45, container.offsetWidth / container.offsetHeight, 1, 20000 );
	scene.add( camera );
	addLights();

	renderer = new THREE.WebGLRenderer({antialias:true});
	renderer.setClearColor( 0x222222 );
	renderer.setPixelRatio( window.devicePixelRatio );
	renderer.setSize( container.offsetWidth, container.offsetHeight );

	container.appendChild( renderer.domElement );

	THREE.GLTFLoader.Shaders.removeAll(); // remove all previous shaders
	loader = new THREE.GLTFLoader;

	loader.load( modelUrl, function(data) {
		scene.add( data.scene );
		// render();
	} );

	animate();
} );