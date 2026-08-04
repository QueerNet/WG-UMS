<?php

if (defined('BASE_DIR')==FALSE) {
    define('BASE_DIR', __DIR__);
}

// Database
if (defined('DB_HOST')==FALSE) {define('DB_HOST', 'localhost');}
if (defined('DB_USER')==FALSE) {define('DB_USER', 'sysadmin');}
if (defined('DB_PASS')==FALSE) {define('DB_PASS', 'AH7QVt*JazxG@zFTEDV*');}
if (defined('DB_NAME')==FALSE) {define('DB_NAME', 'UMS');}

// Email server
if (defined('SMTP_HOST')==FALSE) {define('SMTP_HOST', 'mail.queerliberationserver.org');}
if (defined('SMTP_UNAME')==FALSE) {define('SMTP_UNAME', 'noreply@queerliberationserver.org');}
if (defined('SMTP_PASS')==FALSE) {define('SMTP_PASS', 'P$tzsyXEjvVa3MBvmK0X');}
if (defined('SMTP_SENDER_PRETTY')==FALSE) {define('SMTP_SENDER_PRETTY', 'Queer Liberation Server');}

// Sysadmin
if (defined('sysadmin_email')==FALSE) {define('sysadmin_email', 'mj.qls@tuta.io');}
if (defined('sysadmin_name')==FALSE) {define('sysadmin_name', 'UMS');}

// Wireguard
if (defined('WG_iFace')==FALSE) {define('WG_iFace', 'QLS');}
if (defined('WG_TBL')==FALSE) {define('WG_TBL', 'WG');}
