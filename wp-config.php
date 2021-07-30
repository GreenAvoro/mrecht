<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

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
define( 'DB_NAME', 'mrechtwe_main' );

/** MySQL database username */
define( 'DB_USER', 'mrechtwe_main' );

/** MySQL database password */
define( 'DB_PASSWORD', 'J&=UDLk+PlO)' );

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
define( 'AUTH_KEY',         'k_@i:~g`Wj.o->O>22?Ud3Lq6PyR!6+yNb1x3<v3]5#kk5f1M/cDWgn;hC<-zI)p' );
define( 'SECURE_AUTH_KEY',  '{!#B6IzyIy7/hIk]uC8rm:bQ1adPu^-k .)Vdv<6h[6S ZYgS+#|qCMytEUxf&l:' );
define( 'LOGGED_IN_KEY',    '=*TJI:=p]kcnYK&zKr/R;a[}!3:R1q(ThJrV#8#:[42w^@HUeSBiJ-&p4S%m8LGE' );
define( 'NONCE_KEY',        'oq#93Rm<~|h/I|&1#^t)}^]WLz8H<g4|ksSg6/NN,[2xi,]iB;EPl(8vtI@&8Oq{' );
define( 'AUTH_SALT',        'Ohj;Ft&|$Pj8`W!W@vORUF&1?Lh+z]i5WAaii9j9}rj%FN8>;XzAPB_62P*m/]fB' );
define( 'SECURE_AUTH_SALT', 'Zgb?ft6]S(0.;-F%@i<M-`~BXuR^A`H{o{]kc8AG ZI`CIhXCQe4+ M}6nKy]_}h' );
define( 'LOGGED_IN_SALT',   ',U`eNa7ydlJZ8+<wbpbF)0|t4VP~)$ma&t()H&+`n}W <}4z+rs(jk7eZcCpfelN' );
define( 'NONCE_SALT',       '_;HS!2:zPJ07[ME9OM~L86h>f!Nz`Dvt88uZvX|w#+6ja)~+?7JH(poLAV.Rnj0C' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'mrecht_';

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
// define( 'WP_DEBUG_LOG', true);
// define( 'WP_DEBUG_DISPLAY', true);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
