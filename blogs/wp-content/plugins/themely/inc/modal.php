<?php
if (isset($_POST['theme_id'])) {
  require_once('../../../../wp-load.php');
  $theme_id = esc_attr($_POST['theme_id']);

  $url = esc_url('https://d108fh6x7uy5wn.cloudfront.net/themes.json');
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_URL, $url);
  $directory = curl_exec($curl);

  $url = esc_url('https://d2ck1uhkzo2shg.cloudfront.net/themes.json');
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_URL, $url);
  $marketplace = curl_exec($curl);

  curl_close($curl);

  $marketplace = json_decode($marketplace, true);
  $marketplace = $marketplace['themes'];

  $directory = json_decode($directory, true);
  $directory = $directory['themes'];
  
  $array = array_merge( $marketplace, $directory );

  $themes = array_filter($array, function ($theme) use ($theme_id) {
      return ( $theme['id'] == $theme_id );
  });
  if ($themes) {
    foreach ($themes as $theme) {
      if ( isset($theme['premium']) ) {
        echo '<div class="modal-content">
          <div class="modal-header px-4">
            <h5 class="modal-title w-100" id="themeModalLabel">' . esc_html( $theme['name'] ) . ' <span style="font-size: 0.8em;"><span class="text-muted"><em>by</em></span> ' . esc_html( $theme['author'] ) . '</span></h5>                 
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body py-3 px-4">
            <div class="description mb-3">' . esc_html( $theme['description'] ) . '</div>
            <div class="row">
              <div class="col-md-9">
                <div class="category small mb-3">
                  <p class="text-muted fw-bold text-uppercase mb-1">Category</p>
                  <p class="mb-0">' . esc_html( $theme['categories'] ) . '</p>
                </div>
                <div class="license small mb-3">
                  <p class="text-muted fw-bold text-uppercase mb-1">License</p>
                  <p class="mb-0">' . ( ( esc_html($theme['license'] === "themely") ) ? esc_html("This theme is sold under the terms of the Themely License") : esc_html("This theme is licensed GNU GPL v2.0 and free for personal and commercial use.") ) . '.</p>
                </div>
                <div class="support small mb-2">
                  <p class="text-muted fw-bold text-uppercase mb-1">Support</p>
                  <p class="mb-0">' . esc_attr( $theme['support'] ) . ' priority support provided by ' . esc_html( $theme['author'] ) . '.</p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="modal-equal-buttons">
                  <form action="' . esc_url('https://api.themely.com/sessions/create/?price_id=' . esc_attr( $theme['price_id'] ) . '&wp_url=' . admin_url( 'admin.php?page=themely' )) . '" method="POST">
                    <button id="checkout-button" class="button button-hero button-primary" type="submit" title="Buy ' . esc_html( $theme['name'] ) . '">Buy $' . esc_html( $theme['price'] ) . '</button>
                  </form>
                  <a href="' . esc_url( $theme['preview_url'] ) . '" target="_blank" class="button button-hero" title="Preview ' . esc_html( $theme['name'] ) . '">Preview</a>
                  <span class="secure-checkout" title="Secure, PCI compliant & SSL encrypted checkout by Stripe"><span class="dashicons dashicons-lock"></span>Secure checkout by Stripe</span>
                </div>
              </div>
            </div>
          </div>
          <img src="' . ( ( esc_url($theme['screenshot_url_long']) ) ? esc_url($theme['screenshot_url_long']) : esc_url($theme['screenshot_url']) ) . '" class="img-fluid">
        </div>';
      } else {
        echo '<div class="modal-content">
          <div class="modal-header px-4">
            <h5 class="modal-title w-100" id="themeModalLabel">' . esc_html( $theme['name'] ) . ' <span style="font-size: 0.65em;"><span class="text-muted"><em>by</em></span> ' . esc_html( $theme['author'] ) . '</span></h5>                 
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body py-3 px-4">
            <div class="description mb-3">' . esc_html( $theme['description'] ) . '</div>
            <div class="row">
              <div class="col-md-9">
                <div class="license small mb-3">
                  <p class="text-muted fw-bold text-uppercase mb-1">License</p>
                  <p class="mb-0">This theme is licensed GNU GPL v2.0 and free for personal and commercial use.</p>
                </div>
                <div class="support small mb-2">
                  <p class="text-muted fw-bold text-uppercase mb-1">Support</p>
                  <p class="mb-0">Standard support provided by ' . esc_html( $theme['author'] ) . '.</p>
                </div>
              </div>
              <div class="col-md-3">
                <div class="modal-equal-buttons">
                  <a id="modalinstalltheme" name="modalinstalltheme" class="button button-hero button-primary" data-name="' . esc_attr( $theme['name'] ) . '" data-slug="' . esc_attr( $theme['theme_slug'] ) . '" data-url="' . esc_url( $theme['download_url'] ) . '" title="Install ' . esc_html( $theme['name'] ) . '">Install</a>
                  <a href="' . esc_url( $theme['preview_url'] ) . '" target="_blank" class="button button-hero" title="Preview ' . esc_html( $theme['name'] ) . '">Preview</a>
                </div>
              </div>
            </div>
          </div>
          <img src="' . esc_url( $theme['screenshot_url'] ) . '" class="img-fluid">
        </div>';
      }
    }
  }
};