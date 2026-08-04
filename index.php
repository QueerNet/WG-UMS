<?php

//https://ma.ttias.be/setting-https-server-variables-in-php-fpm-with-nginx/
include (__DIR__.'/app/lib/Session.php');
include (__DIR__.'/app/config/config.php');

Session::init();
Session::checkUserLogin();
Session::checkUserSession();


