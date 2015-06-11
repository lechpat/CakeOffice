<?php
use Cake\Routing\Router;

Router::plugin('Office', function ($routes) {
    $routes->fallbacks('InflectedRoute');
});
