<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en « wp-config.php » et remplir les
 * valeurs.
 *
 * Ce fichier contient les réglages de configuration suivants :
 *
 * Réglages MySQL
 * Préfixe de table
 * Clés secrètes
 * Langue utilisée
 * ABSPATH
 *
 * @link https://fr.wordpress.org/support/article/editing-wp-config-php/.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'evidenceguinee' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', 'root' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/**
 * Type de collation de la base de données.
 * N’y touchez que si vous savez ce que vous faites.
 */
define( 'DB_COLLATE', '' );

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clés secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '!/w3 Lj:@}Iz(o,}qhS0186Kb,%m/jXg]&tEpUqH~}B1~18_O4Val(LSOLa(1ga~' );
define( 'SECURE_AUTH_KEY',  'V`STUKPXgI+BG.$?=0l,r*#la^Z83 !71/I.!elm7Ylq=$<y}(n(@=6hz-WC:#*h' );
define( 'LOGGED_IN_KEY',    'OX=E@< Y3(sHb}I?XWwRjZSSrh2^1Hf^x8LX#Iz-{Q=D$m|FI*G-X_[+9r&OOnV3' );
define( 'NONCE_KEY',        '[jSru WpX6N`Od6/poObKkgZ~6S+yP!n:odO8M+W8wufIWPHq,:t7)+fwl),ZB6X' );
define( 'AUTH_SALT',        'Wu0H-pJX1~)El5us,{,GkPb?XOs0aA>r.A)o<iqIUCSjo~t$90t8P75@}S%jH.+$' );
define( 'SECURE_AUTH_SALT', 'jcXIVY#G4030>Izmh6%#^^r5GmV<!&4Ch,S<([Lhp0+PpqQ_OA{L*EVpj;4WqYkg' );
define( 'LOGGED_IN_SALT',   'B%c0uYb<a9bbq8KSsCQpJW(nNL,kk+GLgc0cglA&kmYS6g8gp6eAB!bL(F$_@:-^' );
define( 'NONCE_SALT',       '=^b^jd>ksh/]ImvP;0AIsaEMNRp*ljahU#I?<kx|^KLrI*Pj96m,WN0k;o-,ZW`3' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortement recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://fr.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( ! defined( 'ABSPATH' ) )
  define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once( ABSPATH . 'wp-settings.php' );
