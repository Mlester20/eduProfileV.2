<?php

    //define base_url for production later on
    define('BASE_URL', 'eduProfile');

    function base_url($path = ''){
        $path = ltrim($path, '/');
        return '/' . trim(BASE_URL, '/') . ($path !== '' ? '/' . $path : '/');
    }