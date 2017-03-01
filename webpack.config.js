var path = require('path');

module.exports = {
  entry: {
    public: './jssrc/public.js',
    admin: './jssrc/admin.js'
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'js')
  }
};
