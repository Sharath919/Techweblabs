<?php
if (isset($_POST['searchcategory'])) {
  require_once('../../../../../wp-load.php');
  $searchcategory = esc_attr(trim($_POST['searchcategory']));

  $url = esc_url('https://d2ck1uhkzo2shg.cloudfront.net/themes.json');
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_URL, $url);
  $marketplace = curl_exec($curl);

  curl_close($curl);

  $marketplace = json_decode($marketplace, true);
  $marketplace = $marketplace['themes'];

  $themes = array_filter($marketplace, function ($theme) use ($searchcategory) {
      return (preg_match("/$searchcategory/i", $theme['categories']));
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
      <form action="' . esc_url('https://api.themely.com/v1/sessions/create/?price_id=' . esc_attr( $theme['price_id'] ) . '&wp_url=' . admin_url( 'admin.php?page=themely' )) . '" method="POST" class="col-md-4">
      <button id="checkout-button" class="w-100 text-center button button-hero button-buy" type="submit" title="Buy ' . esc_html( $theme['name'] ) . '">Buy $' . esc_html( $theme['price'] ) . '</button>
      </form>
      <a href="' . esc_url( $theme['preview_url'] ) . '" target="_blank" class="col-md-4 text-center button button-hero button-preview" role="button" title="Preview ' . esc_attr( $theme['name'] ) . '">Preview</a>
      <a id="openModalbtn" data-bs-toggle="modal" data-bs-target="#themeModal" data-id="' . esc_attr( $theme['id'] ) . '" class="col-md-4 text-center button button-hero button-details" role="button" title="View More Details">Details</a>
      </div>
      </div>
      </div>
      </div>
      </div>';
    }
  } else {
    echo "<div class='col-md-12'><div class='alert alert-danger'><strong>No themes found</strong>, please try a different category.</div></div>";
  }
};