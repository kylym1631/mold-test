const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/modules.js', 'public/js');
mix.js('resources/js/app.js', 'public/js').sass('resources/css/app.scss', 'public/css');
mix.js('resources/js/statistics.js', 'public/js');
mix.js('resources/js/tasks.js', 'public/js');
mix.js('resources/js/candidates.js', 'public/js');
mix.js('resources/js/add-candidate.js', 'public/js');
mix.js('resources/js/add-housing.js', 'public/js');
mix.js('resources/js/add-client.js', 'public/js');
mix.js('resources/js/status-manage.js', 'public/js');
mix.js('resources/js/arrivals-manage.js', 'public/js');
mix.js('resources/js/positions-manage.js', 'public/js');
mix.js('resources/js/housing-manage.js', 'public/js');
mix.js('resources/js/datatables.js', 'public/js');
mix.js('resources/js/work-log.js', 'public/js');
mix.js('resources/js/users.js', 'public/js');
mix.js('resources/js/leads-settings.js', 'public/js');
mix.js('resources/js/leads-import.js', 'public/js');
mix.js('resources/js/add-cars.js', 'public/js');
mix.js('resources/js/roles.js', 'public/js');
mix.js('resources/js/options.js', 'public/js');
mix.js('resources/js/transportations.js', 'public/js');
mix.js('resources/js/templates.js', 'public/js');
mix.js('resources/js/vue.js', 'public/js').vue();

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}