/**
 * Custom scripts for Typography Control
 *
 * @package Articlewave
 */

( function( api ) {

/*-------------------- Typography Control----------------------------------*/
    /**
     * Typography control
     */
    api.controlConstructor['mt-typography'] = api.Control.extend( {
        ready: function() {
            var control = this;

            control.container.on( 'change', '.typography-font-style select', function() {
                    control.settings['style'].set( jQuery( this ).val() );
                }
            );

            control.container.on( 'click', '.typography-font-transform input', function() {
                    control.settings['transform'].set( jQuery( this ).val() );
                }
            );

            control.container.on( 'change', '.typography-text-decoration select', function() {
                    control.settings['text_decoration'].set( jQuery( this ).val() );
                }
            );
        }
    } );

} )( wp.customize );

jQuery(document).ready(function($){

    "use-strict";
    $('.customize-control-select2').select2({
        allowClear: true
    });
    
});