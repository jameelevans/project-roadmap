import path from 'path';
import { fileURLToPath } from 'url';

// Define __dirname for ES modules
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default {
  entry: {
    App: './assets/js/scripts.js', // Your main JS file
  },
  output: {
    path: path.resolve(__dirname, './assets/js'), // Output directory
    filename: 'scripts-bundled.js', // Output file name
  },
  module: {
    rules: [
      {
        test: /\.js$/, // For JavaScript files
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'], // Ensures modern JS compatibility
          },
        },
      },
    ],
  },
  mode: 'development',
  resolve: {
    extensions: ['.js'],
  },
};