/**
 * Try to render XR content using a-frame
 */
import aframe from 'aframe';
import {validateCubemapSrc} from 'aframe/src/utils/src-loader';

/**
 * Register some custom components
 */

/**
 * Cubemap skybox component for A-Frame.
 * <a-scene skycube="url(#sky)"/>
 */
console.log("register cubemap");
aframe.registerComponent('cubemap', {
	// the src of the cubemap, eg. url(#sky);
	schema: { type: 'asset' },

	update: function ( oldData ) {
		// TODO: apply to mesh elements of imported GLTF assets;
		let el = this.el;

		validateCubemapSrc( 'a-cubemap'+this.data, function loadEnvMap (urls) {
			el.object3D.background = new window.THREE.CubeTextureLoader().load( urls );
		});
	},
	remove: function () {
		this.el.object3D.background = null;
	}
});