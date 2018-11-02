var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
     .addEntry('images/imlogo', './assets/images/lmlogo.png')
     .addEntry('images/edit_icon.png', './assets/images/edit_icon.png')
     .addEntry('images/plus_icon.png', './assets/images/plus_icon.png')
     .addEntry('images/swim_background', './assets/images/swim_background.jpg')
     .addEntry('images/key_icon.png', './assets/images/key_icon.png')
     .addEntry('images/checkmark.png', './assets/images/checkmark.png')
     .addEntry('images/facebook_logo.png', './assets/images/facebook_logo.png')
     .addEntry('images/dolphin_icon.png', './assets/images/dolphin_icon.png')
     .addEntry('images/floppy_icon.png', './assets/images/floppy_icon.png')
     .addEntry('images/century_tile.jpg', './assets/images/century_tile.jpg')
     .addEntry('images/culpeper.jpg', './assets/images/culpeper.jpg')
     .addEntry('images/HBS-Logo-for-Website.jpg', './assets/images/HBS-Logo-for-Website.jpg')
     .addEntry('images/homestead.jpg', './assets/images/homestead.jpg')
     .addEntry('images/notes_icon.png', './assets/images/notes_icon.png')
     .addEntry('images/southland_insulators.jpg', './assets/images/southland_insulators.jpg')
     .addEntry('images/ibs.png', './assets/images/ibs.png')
     .addEntry('images/morais.jpg', './assets/images/morais.jpg');

    // uncomment if you use Sass/SCSS files
    // .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    // .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
