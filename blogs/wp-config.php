<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'techlive_wp763' );

/** Database username */
define( 'DB_USER', 'techlive_wp763' );

/** Database password */
define( 'DB_PASSWORD', 'sDXDFrEgUWxs' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define('AUTH_KEY','a~4!7.pAmGp/Ge%m8=.a2~+riiY r%ygLz:[ee=-TH)qdEF Hyl2qE2q:`^K$YPZ');
define('SECURE_AUTH_KEY','d|fEys-)=2J*X&/-lg:{JNNzf2&DiOD+cPZLNTm0w7|]^aM]3U[UhI_+^CxZT}xI');
define('LOGGED_IN_KEY','>[=sJpvIr;{P.%}3(+GXJ6%PJc@1U_&B.<x$>uc-#G&0_=?M7UDJmd2r7:D03Lzs');
define('NONCE_KEY','?zaI{L6QaQaH=TwBX[oEz*Tov7%b/)eE:oZ8U;O-W6kq.x-Dmw_Y|-*DTT/R(Zz|');
define('AUTH_SALT','/z#:tGPJ--Htu,1n-XF|yK=@{SMqWGefBjz2&XxL{-05sz6UZsw-#b/]N2zT~X&;');
define('SECURE_AUTH_SALT','.(6Nrp9>9&8Lx,yeZC|}A:MlO:,^[tz]=F0?l}?}~X7G`<|%@3%ZP0q0?K0s,:1d');
define('LOGGED_IN_SALT','AR;rra3k(mhx+K*L~Y|)9^v?3cQ{aA#XzG1W-4du9Tn8n+A)*=qfP[4-6+++-<Pv');
define('NONCE_SALT','FkxRchjy@==,(GK*SPMt]DfY!{uMM1E-46r(u7-_4~$Qw5;XbT7O(@.!=1952OU#');

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
