<?php

define('WP_CACHE', false); //Added by WP-Cache Manager
define('DISABLE_WP_CRON', true);
define('FS_METHOD', 'direct');

define('DB_NAME', 'p284179_onesum');
define('DB_USER', 'p284179_onesum');
define('DB_PASSWORD', 'v4KEX9jp4E');

define('DB_HOST', 'localhost');

/** Кодировка базы данных для создания таблиц. */
define('DB_CHARSET', 'utf8');

/** Схема сопоставления. Не меняйте, если не уверены. */
define('DB_COLLATE', '');

define('CACHE_READ_WHITELIST','_transient|posts WHERE ID IN|limit_login_'); // do not read from cache is sql contains these
define('CACHE_WRITE_WHITELIST','_transient|limit_login_'); // do not reset cache if sql contains these

define('AUTH_KEY',         '(^+#`C+aIuT(>c{q/bk^FgSjNVH?^{Z-E]8_eRqHM,r<ofv%5tAHZpT.#`8=-PZp');
define('SECURE_AUTH_KEY',  'QYCW-$EC/[)qR> L6K{Ft?(-A=k,Ytr`zTmS-3^F`r0gyr?ez/f~8+G 3H.z+Pw_');
define('LOGGED_IN_KEY',    ':3GnmtrriLZ}uCSFaPsV,o2.--7.k5FUGXm,O+IIH2wl`%no|x<Vz+AFA?1qQ`J%');
define('NONCE_KEY',        'g|U.Fr_!n%3!{[.RS:o=;t9I;ZJzHJ(Z5(W8G::,l=xfS+}}k*W[h<|oU({g1Lph');
define('AUTH_SALT',        'bw7+PE[fl-bf>ooZm]M`C|B!7YFPS1,$iSU(J,9)BYKiA|_06B+19,X9D3IzT+F&');
define('SECURE_AUTH_SALT', '!-x]pyAd+7(Zzde|auA;HiHDz<#rGGZ0L|>D$5i${VE9sv^1@~G6mHWz@&<iYo@|');
define('LOGGED_IN_SALT',   'Y7lu-hY7lSKK3`5;MW6q~ncP@d5gb`ZY3VX-8pEVg4<6X:RBf;ZiP#7s;hhN(7~2');
define('NONCE_SALT',       '_Fdnd#pB}!m#2w%)(~=EzL#N(U+_M6^-gkJ;+Q(UF7rYw1foG)M3.7W+^_Ptf0k}');

/**#@-*/

define( 'CONCATENATE_SCRIPTS', false );


/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько блогов в одну базу данных, если вы будете использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix  = '60a5d2_';

/**
 * Язык локализации WordPress, по умолчанию английский.
 *
 * Измените этот параметр, чтобы настроить локализацию. Соответствующий MO-файл
 * для выбранного языка должен быть установлен в wp-content/languages. Например,
 * чтобы включить поддержку русского языка, скопируйте ru_RU.mo в wp-content/languages
 * и присвойте WPLANG значение 'ru_RU'.
 */
define('WPLANG', 'ru_RU');

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Настоятельно рекомендуется, чтобы разработчики плагинов и тем использовали WP_DEBUG
 * в своём рабочем окружении.
 */
define('WP_DEBUG', false);

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Инициализирует переменные WordPress и подключает файлы. */
require_once(ABSPATH . 'wp-settings.php');
//Disable File Edits
define('DISALLOW_FILE_EDIT', false);
