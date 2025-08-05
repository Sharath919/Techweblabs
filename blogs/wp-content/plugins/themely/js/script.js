/* Search Premium Themes */
jQuery(document).ready(function() {
    function debounce(callback, wait) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(function () { callback.apply(this, args) }, wait);
        };
    }
    document.querySelector('#searchtermpremium').addEventListener('keyup', debounce( () => {
        var searchterm = jQuery("#searchtermpremium").val();
        var plugins_url = ThemelyScript.pluginsUrl;
        jQuery.ajax({
            url: plugins_url + '/inc/search/premium.php',
            type: "POST",
            data: {searchterm: searchterm},
            beforeSend: function () {
                jQuery("#searchspinner").show();
            },
            success: function (data) {
                jQuery("#searchresults").html(data);
                jQuery("#searchspinner").hide();
            },
            error: function (error) {
                alert("Error!");
            }
        });
    }, 800))
});

/* Search Premium Themes by Category */
jQuery(document).ready(function() {
    jQuery(document).on("change", "#searchcategory", function () {
        var searchcategory = jQuery("#searchcategory").val();
        var plugins_url = ThemelyScript.pluginsUrl;
        jQuery.ajax({
            url: plugins_url + '/inc/search/category.php',
            type: "POST",
            data: {searchcategory: searchcategory},
            success: function (data) {
                jQuery("#searchresults").html(data);
            },
            error: function (error) {
                alert("Error!");
            }
        });
    });
});

/* Search Free Themes */
jQuery(document).ready(function() {
    function debounce(callback, wait) {
        let timeout;
        return (...args) => {
            clearTimeout(timeout);
            timeout = setTimeout(function () { callback.apply(this, args) }, wait);
        };
    }
    document.querySelector('#searchtermfree').addEventListener('keyup', debounce( () => {
        var searchterm = jQuery("#searchtermfree").val();
        var plugins_url = ThemelyScript.pluginsUrl;
        jQuery.ajax({
            url: plugins_url + '/inc/search/free.php',
            type: "POST",
            data: {searchterm: searchterm},
            beforeSend: function () {
                jQuery("#searchspinner").show();
            },
            success: function (data) {
                jQuery("#searchresults").html(data);
                jQuery("#searchspinner").hide();
            },
            error: function (error) {
                alert("Error!");
            }
        });
    }, 800))
});

/* Install Theme */
jQuery(document).ready(function() {
    jQuery(document).on("click", "#installtheme", function () {
        var $button = jQuery(this);
        var download_url = $button.data("url");
        var theme_name = $button.data("name");
        var theme_slug = $button.data("slug");
        var plugins_url = ThemelyScript.pluginsUrl;
        jQuery.ajax({
            url: plugins_url + '/inc/install.php',
            type: "POST",
            data: {download_url: download_url, theme_name: theme_name, theme_slug: theme_slug},
            beforeSend: function () {
                $button.html('Installing');
            },
            success: function (data) {
                $button.replaceWith(data);
            },
            error: function (error) {
                alert("Error!");
            }
        });
    });
});

/* Modal Install Theme */
jQuery(document).ready(function() {
    jQuery(document).on("click", "#modalinstalltheme", function () {
        var $button = jQuery(this);
        var download_url = $button.data("url");
        var theme_name = $button.data("name");
        var theme_slug = $button.data("slug");
        var plugins_url = ThemelyScript.pluginsUrl;
        var modal_install = true;
        jQuery.ajax({
            url: plugins_url + '/inc/install.php',
            type: "POST",
            data: {download_url: download_url, theme_name: theme_name, theme_slug: theme_slug, modal_install: modal_install},
            beforeSend: function () {
                $button.html('Installing');
            },
            success: function (data) {
                $button.replaceWith(data);
            },
            error: function (error) {
                alert("Error!");
            }
        });
    });
});

/* Open Theme Model */
jQuery(document).ready(function() {
    jQuery(document).on("click", "#openModalbtn", function() {
        var button = jQuery(this);
        var theme_id = button.data("id");
        var plugins_url = ThemelyScript.pluginsUrl;
        jQuery.ajax({
            url: plugins_url + '/inc/modal.php',
            type: "POST",
            data: {theme_id: theme_id},
            success: function (data) {
                jQuery("#modalContent").html(data);
            },
            error: function (error) {
                alert("Error!");
            }
        });      
    });  
});

/* Destroy model contents on close */
jQuery(document).ready(function() {
    jQuery('#themeModal').on('hidden.bs.modal', function() {
        jQuery("#modalContent").html('');
    });
});

/* Get session id if exists */
jQuery(document).ready(function() {
    if( jQuery('#sessionid').val() ) {
        var plugins_url = ThemelyScript.pluginsUrl;
        var session_id = jQuery('#sessionid').val();        
        jQuery.ajax({
            url: plugins_url + '/inc/stripe/session.php',
            type: "POST",
            data: {session_id: session_id},
            beforeSend: function () {
                jQuery("#installresults").html('<div id="alert" class="alert alert-themely"><div id="installspinner" class="spinner-border install-spinner float-start" role="status"></div> Installing your new theme...</div>');
            },
            success: function (data) {
                var data = jQuery.parseJSON(data);
                var download_url = data.download_url;
                var theme_name = data.name;
                var theme_slug = data.theme_slug;
                var premium_install = true;
                jQuery.ajax({
                    url: plugins_url + '/inc/install.php',
                    type: "POST",
                    data: {download_url: download_url, theme_name: theme_name, theme_slug: theme_slug, premium_install: premium_install},
                    success: function (data) {
                        jQuery("#installresults").html(data);
                    },
                    error: function (error) {
                        alert("Error!");
                    }
                });

            },
            error: function (error) {
                jQuery("#installresults").html('');
                alert('Error!')
            }
        });
    }
});

/* Submit support ticket */
jQuery(document).ready(function() {
    jQuery(document).on("click", "#submitticket", function (e) {
        e.preventDefault();
        var ticketname = jQuery("#ticketname").val();
        var ticketemail = jQuery("#ticketemail").val();
        var ticketmessage = jQuery("#ticketmessage").val();
        var plugins_url = ThemelyScript.pluginsUrl;
        jQuery.ajax({
            url: plugins_url + '/inc/mail.php',
            type: "POST",
            data: {ticketname: ticketname, ticketemail: ticketemail, ticketmessage: ticketmessage},
            beforeSend: function () {
                jQuery("#submitticket").html('Submitting ticket...');
            },
            success: function (data) {
                jQuery("#ticketnotice").html(data);
                jQuery("#submitticket").html('Submit Ticket');
            },
            error: function (error) {
                alert("Error submitting ticket!");
            }
        });
    });
});

/* Destroy support model form on close */
jQuery(document).ready(function() {
    jQuery('#supportModal').on('hidden.bs.modal', function() {
        jQuery("#ticketname").val('');
        jQuery("#ticketemail").val('');
        jQuery("#ticketmessage").val('');
        jQuery("#ticketnotice").html('');
    });
});

/* Toggle between premium and free themes */
jQuery(document).ready(function() {
    jQuery(document).on("click", "#freetoggle", function () {
        var freetoggle = "freetoggle";
        var plugins_url = ThemelyScript.pluginsUrl;
        jQuery.ajax({
            url: plugins_url + '/inc/themes/free.php',
            type: "POST",
            data: {freetoggle: freetoggle},
            success: function (data) {
                jQuery("#premiumtoggle").removeClass('active');
                jQuery("#freetoggle").addClass('active');
                jQuery("#searchcategory").hide();
                jQuery("#searchtermpremium").hide();
                jQuery("#searchtermfree").show();
                jQuery("#searchresults").html(data);
            },
            error: function (error) {
                alert("Error!");
            }
        });
    });
});

/* Toggle between free and premium themes */
jQuery(document).ready(function() {
    jQuery(document).on("click", "#premiumtoggle", function () {
        var premiumtoggle = "premiumtoggle";
        var plugins_url = ThemelyScript.pluginsUrl;
        jQuery.ajax({
            url: plugins_url + '/inc/themes/premium.php',
            type: "POST",
            data: {premiumtoggle: premiumtoggle},
            success: function (data) {
                jQuery("#freetoggle").removeClass('active');
                jQuery("#premiumtoggle").addClass('active');
                jQuery("#searchcategory").show();
                jQuery("#searchtermfree").hide();
                jQuery("#searchtermpremium").show();
                jQuery("#searchresults").html(data);
            },
            error: function (error) {
                alert("Error!");
            }
        });
    });
});

/* Infinite Scroll
 * https://stackoverflow.com/a/5059561
 * https://stackoverflow.com/a/48899322
jQuery(window).scroll(function () { 
   if (jQuery(window).scrollTop() >= jQuery(document).height() - jQuery(window).height() - 50) {
      //Add something at the end of the page
   }
});
*/