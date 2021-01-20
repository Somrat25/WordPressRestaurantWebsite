<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'db_resturant' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         ')7V#W22r9.GAHokIl!bDbKQ/E#%QKt.xW_2H;8rQJ{r_olZN_>3rG~&IKKV2YP*S' );
define( 'SECURE_AUTH_KEY',  '5Sm/[L-I]&}#AQ}c6VXqCpqt&9Tp8u.fr^F37!AnpFbaYwU=t=_.,&*E}g_.A44w' );
define( 'LOGGED_IN_KEY',    'TP(nfxO%g-*)b]9JY|! oI))X~!FezSdwgH@FZ@3R[u2GATttAu@bC4jkaJ>so!a' );
define( 'NONCE_KEY',        '?qxiF]V H[sg#^]1[+R}fmB[x?$WFusqH u`:HvphB:+wb~9fKPK>x#6F*KY<R>-' );
define( 'AUTH_SALT',        'H8g#eD=W&pzD0R*H^#A|DpLYR.53}f6?`aKM[%9hPZ~n@rfVW?,/F&Mmob7T44 4' );
define( 'SECURE_AUTH_SALT', '*mu-qyd=PDXv`SURe_kVPdN}e.Ffi07~6GvuyH9yhdh*COzx_lSHYj_Z:%(g8q^3' );
define( 'LOGGED_IN_SALT',   '<Zer2u%`/U3o/-z|by!.[yHwWx@WoEhf6J&X~J(<hd,DWFv2_k?+~L]y)x8Q>xf5' );
define( 'NONCE_SALT',       'lWP.pbk5pdt[Ki~D[95l{$E@eJV +GH KY`X1p|h:bc<.-4lqX/{w]=3D<]eU~:4' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
