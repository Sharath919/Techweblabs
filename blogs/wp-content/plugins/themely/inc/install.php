<?php
// Load WordPress functions
require_once('../../../../wp-load.php');
// Variables
$themes_dir_path = get_theme_root() . '/';
$download_url = esc_url($_POST["download_url"]);
$theme_name = esc_html($_POST["theme_name"]);
$theme_slug = esc_html($_POST["theme_slug"]);
if (isset($_POST["modal_install"])) {
    $modal_install = $_POST["modal_install"];
}
if (isset($_POST["premium_install"])) {
    $premium_install = $_POST["premium_install"];
}
$theme_zip = $theme_slug . '.zip';
$theme_zip_path = $themes_dir_path . $theme_zip;
$theme_dir = $themes_dir_path . $theme_slug;
$nonce_url = add_query_arg(
    array(
        'action' => 'activate',
        'stylesheet'  => urlencode( $theme_slug ),
    ),
    admin_url( 'themes.php' )
);
$nonce_url = wp_nonce_url( $nonce_url, 'switch-theme_' . $theme_slug );
// Error message
if ( file_exists($theme_dir) ) {
    if (isset($modal_install)) {
        echo '<a href="' . admin_url( 'themes.php' ) . '" class="button button-primary button-hero disabled" title="You already installed ' . esc_attr($theme_name) . '">Installed</a>';
    } else if (isset($premium_install)) {
        echo '<div id="alert" class="alert alert-themely">Oops! 🙁 <strong>' . esc_html($theme_name) .'</strong> theme already installed, <a href="' . admin_url('themes.php') . '">visit this page to activate</a> or choose another theme.</div>';
    } else {
        echo '<a href="' . admin_url( 'themes.php' ) . '" class="w-100 text-center button button-hero button-buy disabled" title="You already installed ' . esc_attr($theme_name) . '">Installed</a>';
        exit;
    }
} else {
    // Download zip file
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $download_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    // Save zip file
    $file = fopen($theme_zip_path, 'w+');
    fwrite($file, $result);
    fclose($file);
    // Extract zip file
    $zip = new ZipArchive;         
    $zip->open($theme_zip_path);
    $zip->extractTo($themes_dir_path);
    $zip->close();
    // Remove zip file
    unlink($theme_zip_path);
    // Success message
    if ( isset($modal_install) ) {
        echo '<a href="' . esc_url($nonce_url) . '" class="button button-primary button-hero focus" title="Activate ' . esc_attr($theme_name) . '">Activate</a>';
    } else if ( isset($premium_install) ) {
        echo '<div id="alert" class="alert alert-themely">Yayy! 😊 <strong>' . esc_html($theme_name) . '</strong> successfully purchased and installed! A purchase receipt was sent to your email address. <a href="' . esc_url($download_url) . '">Download a backup copy</a> then <a href="' . esc_url($nonce_url) . '">activate ' . esc_html($theme_name) . '</a> to begin using your new theme.</div>';
    } else {
        echo '<a href="' . esc_url($nonce_url) . '" class="w-100 text-center button button-primary button-hero button-buy" title="Activate ' . esc_attr($theme_name) . '">Activate</a>';
    }
}