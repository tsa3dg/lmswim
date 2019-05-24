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
    .addEntry('images/key_icon.png', './assets/images/key_icon.png')
    .addEntry('images/checkmark.png', './assets/images/checkmark.png')
    .addEntry('images/dolphin_icon.png', './assets/images/dolphin_icon.png')
    .addEntry('images/floppy_icon.png', './assets/images/floppy_icon.png')
    .addEntry('images/notes_icon.png', './assets/images/notes_icon.png')
    .addEntry('images/ibs.png', './assets/images/ibs.png')
	.enableReactPreset()

    // uncomment if you use Sass/SCSS files
    // .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    // .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
