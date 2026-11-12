const mix = require("laravel-mix");

mix.js("resources/js/app.js", "public/js")
    .copy("resources/assets/img", "public/img")
    .copy("resources/assets/css", "public/css")
    .copy("resources/assets/js", "public/js")
    // .copy("resources/captions", "public/captions")
    .options({
        processCssUrls: false, // Helps reduce unnecessary processing
    });
