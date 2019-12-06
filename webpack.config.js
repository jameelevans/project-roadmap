/** 
 * Webpack configuration
 *
 * ! What the code does:
 * *    1. Set the absolute path of project
 * *    2. Sets the main JS file to import modules to
 * *    3. Combines all the js modules into one file
 *
 */

 // Needed to set projects absolute path
const path = require('path');

module.exports = {
  entry: {
    App: './assets/js/scripts.js'
  },
  output: {
    path: path.resolve(__dirname, './assets/js'),
    filename: 'scripts-bundled.js'
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      }
    ]
  },
  mode: 'development'
}
