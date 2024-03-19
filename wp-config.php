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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u725151912_p2FVx' );

/** Database username */
define( 'DB_USER', 'u725151912_dsSmT' );

/** Database password */
define( 'DB_PASSWORD', 'LtMMsCEIXN' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'aR|Gd}k)M?2bZ8Z9>%`o`8pnB60Oj<W$_Q%j&:hC.C1HCCaK>a2_dLWZv~>h]Su?' );
define( 'SECURE_AUTH_KEY',   '{}t56y=F_,~x)]=]6:{^OuMdH7=9%p}6{C1=xdZ]LK+~T3`pLbtC<;^/qWsE=C,C' );
define( 'LOGGED_IN_KEY',     '(G2aiD3LD6>nR|$Zc9ixgyj{p8Hy.OiXcnMZW1 AbvK+Q=FV594q:<dm`^&v=Ram' );
define( 'NONCE_KEY',         'g!.@&9:uEuzD@((rPg/HoQ~25W@#Btwt55.$XDVtXu1Fo#[Xa)g<JjOA;qupG[%l' );
define( 'AUTH_SALT',         '(PB-!U~?-6eCAW@fG?wh(8RGwvl;&*}[)J%EUM)4=S}8$VHaIY+6[c{W(QRKQ&nx' );
define( 'SECURE_AUTH_SALT',  '#TvIk5*<R!wn/f/s%;LIQu+W)s].@&n~8uC1p|g[xwQQ8j4lA5Bv@(6,9Q2zsLuL' );
define( 'LOGGED_IN_SALT',    'hmT<q0Rn-to(__/|eh7l}>x[r;FZuOswT/`h@ZOU&l9*|=Z+PBrX~i@^!U9e17d8' );
define( 'NONCE_SALT',        '4Q( (t9usM{76BfHRAno9 .&d{+VH9~%<29-zMTm[W6s71q?yUa2^3t)p7kdV-tF' );
define( 'WP_CACHE_KEY_SALT', ')^>%k92(Jv);{#`$iw-+.C9.q$4<,I-rqvXc7hkRNFf<;Jw:-1R=Emeb~ud9`@mz' );


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



define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
