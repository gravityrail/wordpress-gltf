var path = require('path');
// const UglifyJSPlugin = require('uglifyjs-webpack-plugin');

module.exports = {
  entry: {
    public: './jssrc/public.js',
    admin: './jssrc/admin.js'
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'js')
  },
  // plugins: [
  //    new UglifyJSPlugin()
  // ]
};
