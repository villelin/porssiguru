<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 22.11.2017
 * Time: 20.17
 */

define('SITE_ROOT', 'porssiguru');
define('SESSION_LIFETIME', 60*60*30);       // logataan ulos 30 minuutin päästä

session_set_cookie_params(SESSION_LIFETIME, SITE_ROOT);
session_name("porssiguru");
session_start();