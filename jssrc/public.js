import * as THREE from 'three';
import VRControls from './controls/VRControls';
import OrbitControls from './controls/OrbitControls';
import VREffect from './effects/VREffect';
import GLTFLoader from './loaders/GLTFLoader';

function initializeGltfElement() {
	var $el = jQuery(this);

	var container, camera, scene, renderer, controls, mixer, vreffect;

	function addCamera() {
		camera = new THREE.PerspectiveCamera( 75, container.offsetWidth / container.offsetHeight, 1, 2000 );
		// camera.rotation.y = Math.PI;
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
			controls = new VRControls( camera );
			controls.standing = true;
			addWebVRButton( vreffect );
			navigator.getVRDisplays()
				.then( function ( displays ) {
					vreffect.setVRDisplay( displays[ 0 ] );
					controls.setVRDisplay( displays[ 0 ] );
				} )
				.catch( function () {
					// no displays
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

	function onResize() {
		console.log("resizing");

		if ( vreffect.isPresenting ) {
			var width = window.innerWidth;
			var height = window.innerHeight;
			camera.aspect = ( width / 2 ) / height;
		} else {
			var width = container.offsetWidth;
			var height = container.offsetHeight;
			camera.aspect = width / height;
		}

		camera.updateProjectionMatrix();
		vreffect.setSize( width, height );
	}

	function addListeners() {
		window.addEventListener('resize', onResize);
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
		GLTFLoader.Shaders.update( scene, camera );
		vreffect.render( scene, camera );
		controls.update();
	}

	container = $el.get(0);

	// necessary for the enter VR button to appear in the right position
	$el.css({'position':'relative'});

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