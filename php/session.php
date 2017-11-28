<?php
/**
 * Created by PhpStorm.
 * User: Ville Linde
 * Date: 22.11.2017
 * Time: 20.17
 */

define('SITE_ROOT', 'porssiguru');

session_save_path(realpath(dirname($_SERVER['DOCUMENT_ROOT']) . '/porssiguru/session'));
session_set_cookie_params(0, SITE_ROOT);
session_name("porssiguru");
session_start();