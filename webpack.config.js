var path = require('path');
const UglifyJSPlugin = require('uglifyjs-webpack-plugin');

module.exports = {
  mode: 'development',
  entry: {
    // public: './jssrc/public.js',
    'public-aframe': './jssrc/public-aframe.js',
    // admin: './jssrc/admin.js'
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'js')
  },
  // force use top-level THREE so that RayInput doesn't use its own internal THREE instance
  resolve: {
    // alias: {
    //   three: path.resolve(__dirname, 'node_modules/three')
    // }
  },
  module: {
    // rules: [
    //     {
    //         test: /\.js$/,
    //         loader: "imports-loader?THREE=three"
    //     }
    // ]
  }
  // plugins: [
  //    new UglifyJSPlugin()
  // ]
};
