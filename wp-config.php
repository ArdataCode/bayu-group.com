<?php
define( 'WP_CACHE', true );


//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL cookie settings

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'landingu_bayuuu' );

/** Database username */
define( 'DB_USER', 'landingu_bayyuu' );

/** Database password */
define( 'DB_PASSWORD', 'Ardata2024' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ck3kijvup5wu1zobgvjhhe8zki7xhlxubq0ux3icedvjkoug8s5w0vlhgjpn6yqr' );
define( 'SECURE_AUTH_KEY',  'bjwzpjxaoyespg98g3c8aa2eqklgo8kv35yb5c5dia6pae6lpf7xkfwrgydvb0im' );
define( 'LOGGED_IN_KEY',    'xiqnwker5hvygydk3xfk6qain1lky3hd2votcsau2ipbn9bgyhgl9qgcfzonwzzh' );
define( 'NONCE_KEY',        'vqlvktnvnjedvgpkqd1ps1wi9nqavtd9otns68uyknwdatetwxrcuinhurm8nqrq' );
define( 'AUTH_SALT',        'yvdf37vo1fmk7bieg46vbqo0ihzd8yai5rzzpewf1futovixqeqbwjnigqojptmm' );
define( 'SECURE_AUTH_SALT', 'qubltvt20ozqlsdpyraop0yjps12mvmlpesx9kzkemldjpxub8nlkdojawhiz1eq' );
define( 'LOGGED_IN_SALT',   'rsaewb7rxmvwlnk6dozgkbgfv1w7gocwpuqqcbxqhfxuohtk2juh79ccjca52wem' );
define( 'NONCE_SALT',       '3jcwytgdxopgbn1khzxvqo4siki5giogt4udcpqokpdye2kdzmmfn2vf0hyiiojg' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpul_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_MEMORY_LIMIT', '256M' );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
