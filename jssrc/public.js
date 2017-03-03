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
		// camera.rotation.y = Math.PI;
		// camera.position.set(0, 2, 3);
		// camera.quaternion.set(0,0,0,1);
		camera.position.set(0, 5, 3);
		// camera.position.set(0, controls.userHeight, 0);
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

		container.appendChild( buttonContainer );
	}

	function addFallbackControls() {
		console.log("doing regular");
		controls = new OrbitControls( camera, renderer.domElement );
		controls.userPan = false;
		controls.userPanSpeed = 0.0;
		controls.maxDistance = 5000.0;
		controls.maxPolarAngle = Math.PI * 0.495;
		controls.autoRotate = false;
	}

	function addControls() {
		console.log("adding controls");
		// controls.autoRotateSpeed = -10.0;

		vreffect = new VREffect( renderer );

		// add WebVR controls
		if ( navigator.getVRDisplays !== undefined ) {
			console.log("doing VR");
			vreffect.scale = 2.0;
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
		console.log("resizing");

		if ( vreffect.isPresenting ) {
			// resize canvas container
			// container.style.position = 'absolute';
			// container.style.top = '0';
			// container.style.left = '0';
			// container.style.width = window.innerWidth + 'px';
			// container.style.height = window.innerHeight + 'px';
			// var size = renderer.getSize();
			// console.log(size);
			// var width = size.width;
			// var height = size.height;
			// camera.aspect = width / height;
			// camera.updateProjectionMatrix();
			// camera.aspect = ( width / 2 ) / height;
		} else {
			// container.style.position = 'inherit';
			// container.style.width = null;
			// container.style.height = null;
			// container.style.top = null;
			// container.style.left = null;
			var width = container.offsetWidth;
			var height = container.offsetHeight;
			console.log("setting resolution to " + width + " by " + height );
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
	// fullscreenContainer = 

	// necessary for the enter VR button to appear in the right position
	$el.css({'position':'relative'});

	// set the height of the container so that it has the same aspect ratio
	// as the screen
	// var elHeight = ( window.screen.height / window.screen.width ) * $el.offsetWidth;
	// $el.css({'height': elHeight + 'px'});

	scene = new THREE.Scene();

	addLights(); // LIGHTS!
	addCamera(); // CAMERA!
	addRenderer(); // ACTION! :)
	addControls();
	addLoadingLogger();
	addListeners();

	// make sure the canvas scales to full screen
	// renderer.domElement.style.width = '100%';
	// renderer.domElement.style.height = '100%';
	// renderer.domElement.style.top = '0';

	container.appendChild( renderer.domElement );

	loadModel( $el.data( 'model' ), $el.data( 'scale' ) );

	animate();
}

jQuery( function() {
	jQuery( '.gltf-model' ).each( initializeGltfElement );
	// jQuery( 'body' ).each( initializeGltfElement );
} );