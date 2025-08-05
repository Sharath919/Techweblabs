<?php
if (isset($_POST['searchterm'])) {
  require_once('../../../../../wp-load.php');
  $searchterm = esc_attr(trim($_POST['searchterm']));

  $url = esc_url('https://d108fh6x7uy5wn.cloudfront.net/themes.json');
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_URL, $url);
  $directory = curl_exec($curl);

  curl_close($curl);

  $directory = json_decode($directory, true);
  $directory = $directory['themes'];

  $themes = array_filter($directory, function ($theme) use ($searchterm) {
      return (preg_match("/$searchterm/i", $theme['name']) or preg_match("/$searchterm/i", $theme['description']));
  });
  if ($themes) {
    foreach ($themes as $theme) {
      echo '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 col-xl-4 col-xxl-4 col-xxxl-3">
      <div class="ocwpi-thumbnail">
      <a id="openModalbtn" data-bs-toggle="modal" data-bs-target="#themeModal" data-id="' . esc_attr( $theme['id'] ) . '" class="btn btn-link p-0 m-0 border-0"><img src="' . esc_url( $theme['screenshot_url'] ) . '" class="card-img-top" title="View More Details"></a>
      <div class="ocwpi-caption">
      <div class="row">
      <div class="col-xs-12 col-md-12 px-3 text-center">
      <span class="ocwpi-name"><strong>' . esc_html( $theme['name'] ) . '</strong></span>
      <span class="ocwpi-author"><span class="text-muted"><em>by</em></span> ' . esc_html( $theme['author'] ) . '</span>
      </div>
      </div>
      </div>
      <div class="ocwpi-buttons">
      <div class="row">
      <div class="btn-group">
      <a id="installtheme" name="installtheme" data-name="' . esc_attr( $theme['name'] ) . '" data-slug="' . esc_attr( $theme['theme_slug'] ) . '" data-url="' . esc_url( $theme['download_url'] ) . '" class="w-100 text-center button button-hero button-buy" title="Install ' . esc_attr( $theme['name'] ) . '">Install</a>
      <a href="' . esc_url( $theme['preview_url'] ) . '" target="_blank" class="col-md-4 text-center button button-hero button-preview" role="button" title="Preview ' . esc_attr( $theme['name'] ) . '">Preview</a>
      <a href="' . esc_url( $theme['download_url'] ) . '" class="col-md-4 text-center button button-hero button-details" role="button" title="Download ' . esc_attr( $theme['name'] ) . '">Download</a>
      </div>
      </div>
      </div>
      </div>
      </div>';
    }
  } else {
    echo "<div class='col-md-12'><div class='alert alert-danger'><strong>No themes found</strong>, please try a different search term.</div></div>";
  }
};