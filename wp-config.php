<?php
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
define( 'DB_NAME', 'simsodep' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'z1t 2H O9x~rsFMFo6V]8_vF<rS=g^>VklmE$thx4:E>)NMl]YPbDkGk pXgfJ,w' );
define( 'SECURE_AUTH_KEY',  '/^,3`>(/(XGmPBvp~/~CmOLWfG<AI)p3v/vY?_b=+3/oXO3h1g6KxL,Ij?%hGsR,' );
define( 'LOGGED_IN_KEY',    'D.1v]uPOn-a.j!+ b1/ [Fn7E(kih7DTxSwW`YptCZ* PU,Af>`7u9oEPQO:i%S:' );
define( 'NONCE_KEY',        'o2Z_x7Cgi_~g&I]7633inD1R=Y:uX,? 6sY#*sb#?%g~`JJpL&F-h@&e9*LP -85' );
define( 'AUTH_SALT',        'GTfd3cTh5P7!TnZYe*cHS/eQWCf9n{$$2{ZMVMqn<p!1cXu+h5+q#ZQ;u u@+J$S' );
define( 'SECURE_AUTH_SALT', '1MhB96<g@-:}7l?`dTJKpL<vl`49c{<piw?-}.AcYZM].=50BGqy48([vFMht-Yh' );
define( 'LOGGED_IN_SALT',   '!3!spAh< }L!OGLZ#%//B4f?R.r!y!opXNcV|I}G]h%RBBrog5E6I##qt?9}CIFg' );
define( 'NONCE_SALT',       'qsrBK*)azl1 Q9q4VmX9]FzVuPzF#<.IMZCE@TCy_w,N^ShVY}uFU11=J[BG/`(K' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
