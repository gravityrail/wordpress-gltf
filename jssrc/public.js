require( 'three/examples/js/controls/VRControls' );
require( 'three/examples/js/controls/OrbitControls' );
require( 'three/examples/js/effects/VREffect' );
require( 'three/examples/js/loaders/GLTFLoader' );
// require( 'three/examples/js/loaders/GLTF2Loader' );

import RayInput from 'ray-input'
import * as webvrui from 'webvr-ui';

function initializeGltfElement() {
	var $el = jQuery(this);

	var container, camera, scene, renderer, controls, mixer, vreffect, input;

	function addCamera() {
		camera = new THREE.PerspectiveCamera( 40, container.offsetWidth / container.offsetHeight, 0.1, 1000 );
		camera.position.set(0, 5, 3);
		scene.add( camera );
	}

	function addLights() {
		var ambient = new THREE.AmbientLight( 0xFFFFFF, 1 );
		scene.add( ambient );

		var directionalLight = new THREE.DirectionalLight( 0xffeedd );
		directionalLight.position.set( 0, 0, 1 );
		scene.add( directionalLight );
	}

	function addWebVRButton( effect ) {

		var options = {
			color: 'white',
			background: false,
			corners: 'square',
		};

		var enterVR = new webvrui.EnterVRButton(renderer.domElement, options)
			.on("enter", function(){
				console.log("enter VR");
			})
			.on("exit", function(){
				console.log("exit VR");
			});

		var buttonContainer = document.createElement( 'div' );
		buttonContainer.style.position = 'absolute';
		buttonContainer.style.left = 'calc(50% - 100px)';
		buttonContainer.style.bottom = '20px';
		buttonContainer.style.width = '200px';
		buttonContainer.appendChild( enterVR.domElement );

		jQuery( buttonContainer ).find( 'button' ).first().click( function( e ) { e.preventDefault() });

		container.appendChild( buttonContainer );
	}

	function addFallbackControls() {
		controls = new THREE.OrbitControls( camera, renderer.domElement );
		controls.userPan = false;
		controls.userPanSpeed = 0.0;
		controls.maxDistance = 5000.0;
		controls.maxPolarAngle = Math.PI * 0.495;
		controls.autoRotate = false;
	}

	function addControls() {
		vreffect = new THREE.VREffect( renderer );

		// add WebVR controls
		if ( navigator.getVRDisplays !== undefined ) {
			controls = new THREE.VRControls( camera );
			controls.standing = true;
			addWebVRButton( vreffect );
		} else {
			addFallbackControls();
		}
	}

	function addController() {
		input = new RayInput( camera, renderer.domElement );

		input.on('raydown', (opt_mesh) => {
			// Called when an object was activated. If there is a selected object,
			// opt_mesh is that object.
			console.log('raydown');
			// console.log(opt_mesh);
		});

		// Register a callback when an object is selected.
		input.on('rayover', (mesh) => {
			// Called when an object was selected.
			console.log('rayover');
			// console.log(mesh);
		});

		// render a visual representation of the ray input
		scene.add( input.getMesh() );

		input.setSize( renderer.getSize() );
	}

	function addRenderer() {
		renderer = new THREE.WebGLRenderer({ antialias:true });
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

	function onResize() {
		if ( ! vreffect.isPresenting ) {
			var width = container.offsetWidth;
			var height = container.offsetHeight;
			camera.aspect = width / height;
			camera.updateProjectionMatrix();
			vreffect.setSize( width, height );
		}

		input.setSize( renderer.getSize() );
	}

	function addListeners() {
		window.addEventListener('resize', onResize, true);
		window.addEventListener('vrdisplaypresentchange', onResize, true);
	}

	function loadModel( modelUrl, modelScale ) {
		var loader = new THREE.GLTFLoader();
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

			// register with the controller input
			input.add( object );
		} );
	}

	function animate() {
		controls.update();
		input.update();
		if ( typeof mixer != "undefined" ) {
			mixer.update();
		}
		vreffect.render( scene, camera );
		vreffect.requestAnimationFrame( animate );
	}

	function addGround() {
		var groundMaterial = new THREE.MeshPhongMaterial({
				color: 0xFFFFFF,
				shading: THREE.SmoothShading
			});
		var ground = new THREE.Mesh( new THREE.PlaneBufferGeometry(512, 512), groundMaterial);
		// ground.receiveShadow = true;
		ground.position.z = -2;
		ground.rotation.x = -Math.PI / 2;
		scene.add(ground);
	}

	function addSpotlight() {
		var spot1   = new THREE.SpotLight( 0xffffff, 1 );
		spot1.position.set( 10, 20, 10 );
		spot1.angle = 0.25;
		spot1.distance = 1024;
		spot1.penumbra = 0.75;

		//shadows
		spot1.castShadow = true;
		spot1.shadow.bias = 0.0001;
		spot1.shadow.mapSize.width = 2048;
		spot1.shadow.mapSize.height = 2048;
		scene.add( spot1 );
	}

	container = $el.get(0);

	// necessary for the enter VR button to appear in the right position
	$el.css( { 'position': 'relative' } );

	scene = new THREE.Scene();

	addLights(); // LIGHTS!
	addCamera(); // CAMERA!
	addRenderer(); // ACTION! :)
	addControls();
	addController();
	addLoadingLogger();
	addListeners();
	// addGround();
	// addSpotlight();

	container.appendChild( renderer.domElement );

	loadModel( $el.data( 'model' ), $el.data( 'scale' ) );

	animate();
}

// make function globally available
window.initializeGltfElement = initializeGltfElement;

jQuery( function() {
	jQuery( '.gltf-model' ).each( initializeGltfElement );
} );