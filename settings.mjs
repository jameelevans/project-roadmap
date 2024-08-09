// settings.mjs

const settings = {
    urlToPreview: 'http://project-roadmap.local/', // Local project URL of your already running WordPress site. Could be something like wpgulp.local or localhost:3000 depending upon your local WordPress setup.
    productURL: './', // Theme URL. Leave it like it is, since gulpfile.js lives in the root folder.
  
    // Style options
    styleSRC: './assets/css/style.scss', // Path to main .scss file 
    styleMain: './style.css', // Path to main .scss file
    styleDestination: './', // Path to place the compiled CSS file. Default set to root folder
    outputStyle: 'expanded', // Expanded so that our CSS is readable to wordpress
    precision: 10,
  
    // JS options
    jsWebpack: './webpack.config.js', // Path to Webpack config file 
    jsMain: "./assets/js/scripts.js", // Path to main js file
    jsBundled: "./assets/js/scripts-bundled.js", // Path to bundled js file
  
    // Images options
    imgSRC: './assets/img/raw/**/*', // Source folder of images which should be optimized and watched
    imgDST: './assets/img/', // Destination folder of optimized images
  
    // Watch files paths
    watchStyles: './assets/css/**/*.scss', // Path to all *.scss files inside css folder and inside them
    watchJsModules: './assets/js/modules/*.js', // Path to all JS modules
    watchJsMain: './assets/js/scripts.js', // Path to all main JS file
    watchPhp: './**/*.php', // Path to all PHP files
  
    // Browsers you care about for autoprefixing
    BROWSERS_LIST: [
      'last 2 version',
      '> 1%',
      'ie >= 11',
      'last 1 Android versions',
      'last 1 ChromeAndroid versions',
      'last 2 Chrome versions',
      'last 2 Firefox versions',
      'last 2 Safari versions',
      'last 2 iOS versions',
      'last 2 Edge versions',
      'last 2 Opera versions'
    ]
  };
  
  export default settings;