<?php

    $projectRoot = realpath(__DIR__ . '/../..');
    $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $basePath = ($docRoot !== false && $projectRoot !== false)
        ? str_replace('\\', '/', substr($projectRoot, strlen($docRoot)))
        : '';
    define('BASE_URL', $basePath);

    function base_url($path = ''){
        $path = ltrim($path, '/');
        $base = trim(BASE_URL, '/');
        $prefix = $base !== '' ? '/' . $base : '';
        return $path !== '' ? $prefix . '/' . $path : ($prefix !== '' ? $prefix . '/' : '/');
    }