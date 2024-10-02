const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .css('resources/css/app.css', 'public/css')
   .version(); // Adicione isso para que você possa usar o mix() para versionamento de arquivos
