<?php

require __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../../');
$dotenv->load();

// Database
if (defined('DB_HOST')==FALSE) {define('DB_HOST', 'localhost');}
if (defined('DB_USER')==FALSE) {define('DB_USER', 'sysadmin');}
if (defined('DB_PASS')==FALSE) {define('DB_PASS', 'AH7QVt*JazxG@zFTEDV*');}
if (defined('DB_NAME')==FALSE) {define('DB_NAME', 'UMS');}

// Email server
if (defined('SMTP_HOST')==FALSE) {define('SMTP_HOST', 'mail-eu.smtp2go.com');}
if (defined('SMTP_UNAME')==FALSE) {define('SMTP_UNAME', 'ansionnachdana');}
if (defined('SMTP_SENDER')==FALSE) {define('SMTP_SENDER', 'ansionnachdana@queerliberationserver.org');}
if (defined('SMTP_SENDER_PRETTY')==FALSE) {define('SMTP_SENDER_PRETTY', 'Queer Liberation Server');}

//TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
if (defined('SMTP_PORT')==FALSE) {define('SMTP_PORT', 465);}

// Sysadmin
if (defined('sysadmin_email')==FALSE) {define('sysadmin_email', 'mj.qls@tuta.io');}
if (defined('sysadmin_name')==FALSE) {define('sysadmin_name', 'UMS');}

// Wireguard
if (defined('WG_iFace')==FALSE) {define('WG_iFace', 'QLS');}
if (defined('WG_TBL')==FALSE) {define('WG_TBL', 'WG');}
