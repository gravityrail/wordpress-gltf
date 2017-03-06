import * as THREE from 'three';
import VRControls from './controls/VRControls';
import OrbitControls from './controls/OrbitControls';
import VREffect from './effects/VREffect';
import GLTFLoader from './loaders/GLTFLoader';
import * as webvrui from 'webvr-ui';

function initializeGltfElement() {
	var $el = jQuery(this);

	var container, camera, scene, renderer, controls, mixer, vreffect, fullscreenContainer;

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
		controls = new OrbitControls( camera, renderer.domElement );
		controls.userPan = false;
		controls.userPanSpeed = 0.0;
		controls.maxDistance = 5000.0;
		controls.maxPolarAngle = Math.PI * 0.495;
		controls.autoRotate = false;
	}

	function addControls() {
		vreffect = new VREffect( renderer );

		// add WebVR controls
		if ( navigator.getVRDisplays !== undefined ) {
			controls = new VRControls( camera );
			controls.standing = true;
			addWebVRButton( vreffect );
		} else {
			addFallbackControls();
		}
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
	}

	function addListeners() {
		window.addEventListener('resize', onResize, true);
		window.addEventListener('vrdisplaypresentchange', onResize, true);
	}

	function loadModel( modelUrl, modelScale ) {
		GLTFLoader.Shaders.removeAll(); // remove all previous shaders
		var loader = new GLTFLoader();
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

	function animate() {
		vreffect.requestAnimationFrame( animate );
		if ( typeof mixer != "undefined" ) {
			mixer.update();
		}
		vreffect.render( scene, camera );
		controls.update();
	}

	container = $el.get(0);

	// necessary for the enter VR button to appear in the right position
	$el.css( { 'position': 'relative' } );

	scene = new THREE.Scene();

	addLights(); // LIGHTS!
	addCamera(); // CAMERA!
	addRenderer(); // ACTION! :)
	addControls();
	addLoadingLogger();
	addListeners();

	container.appendChild( renderer.domElement );

	loadModel( $el.data( 'model' ), $el.data( 'scale' ) );

	animate();
}

jQuery( function() {
	jQuery( '.gltf-model' ).each( initializeGltfElement );
} );