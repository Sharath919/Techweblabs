/**
 * Custom scripts for Repeater Control
 *
 * @package Articlewave
 */
 
jQuery(document).ready(function($) {

    "use strict";

    /**
      * Function for repeater field
     */
    function articlewave_refresh_repeater_values(){
        $(".articlewave-repeater-field-control-wrap").each(function(){
            
            var values = []; 
            var $this = $(this);
            
            $this.find(".articlewave-repeater-field-control").each(function(){
            var valueToPush = {};   

                $(this).find('[data-name]').each(function(){
                    if( $(this).attr('type') === 'checkbox'){
                        dataValue = ( $(this).is(':checked') ) ? 'on' : 'off';
                    } else {
                        var dataValue = $(this).val();
                    }
                    var dataName = $(this).attr('data-name');
                    valueToPush[dataName] = dataValue;
                });

                values.push(valueToPush);
            });

            $this.next('.articlewave-repeater-collector').val(JSON.stringify(values)).trigger('change');
        });
    }

    // expand the repeater fields wrap
    $('#customize-theme-controls').on('click','.articlewave-repeater-field-title.item-visible', function(){
        $(this).closest('.articlewave-repeater-field-control').find('.articlewave-repeater-fields').slideToggle();
        $(this).closest('.articlewave-repeater-field-control').toggleClass('expanded');
    });

    // close the repeater fields wrap
    $('#customize-theme-controls').on('click', '.articlewave-repeater-field-close', function(){
        $(this).closest('.articlewave-repeater-fields').slideUp();
        $(this).closest('.articlewave-repeater-field-control').toggleClass('expanded');
    });

    // show/hide repeater field
    $("#customize-theme-controls").on("click", ".field-display", function(){
        $(this).toggleClass( "dashicons-visibility dashicons-hidden" );
        $(this).closest('.articlewave-repeater-field-control').toggleClass( 'item-visible item-not-visible' );
        $(this).closest('.articlewave-repeater-field-control').find('.articlewave-repeater-field-title').toggleClass( 'item-visible item-not-visible' );
        var dataVal =  $(this).parents('.articlewave-repeater-field-control').find('input.repeater-field-visible-holder').val();
        if(dataVal == 'show') {
            if ($(this).closest('.articlewave-repeater-field-control').find('.articlewave-repeater-fields').is(':visible')) {
                $(this).closest('.articlewave-repeater-field-control').toggleClass('expanded');
                $(this).closest('.articlewave-repeater-field-control').find('.articlewave-repeater-fields').slideUp();
            }
            $(this).closest('.articlewave-repeater-field-control').find('input.repeater-field-visible-holder').val('hide');
        } else {
            $(this).closest('.articlewave-repeater-field-control').find('input.repeater-field-visible-holder').val('show');
        }
        articlewave_refresh_repeater_values();
    });



    $("body").on("click",'.articlewave-repeater-add-control-field', function(){

        var fLimit = $(this).parent().find('.field-limit').val();
        var fCount = $(this).parent().find('.field-count').val();
        if( fCount < fLimit ) {
            fCount++;
            $(this).parent().find('.field-count').val(fCount);
        } else {
            $(this).before('<span class="articlewave-limit-msg"><em>Only '+fLimit+' social icons shall be permitted for free version.</em></span>');
            return;
        }

        var $this = $(this).parent();
        
        if(typeof $this != 'undefined') {

            var field = $this.find(".articlewave-repeater-field-control:first").clone();
            if(typeof field != 'undefined'){
                
                field.find("input[type='text'][data-name]").each(function(){
                    var defaultValue = $(this).attr('data-default');
                    $(this).val(defaultValue);
                });

                field.find("textarea[data-name]").each(function(){
                    var defaultValue = $(this).attr('data-default');
                    $(this).val(defaultValue);
                });

                field.find("select[data-name]").each(function(){
                    var defaultValue = $(this).attr('data-default');
                    $(this).val(defaultValue);
                });

                field.find(".attachment-media-view").each(function(){
                    var defaultValue = $(this).find('input[data-name]').attr('data-default');
                    $(this).find('input[data-name]').val(defaultValue);
                    if(defaultValue){
                        $(this).find(".thumbnail-image").html('<img src="'+defaultValue+'"/>').prev('.placeholder').addClass('hidden');
                    }else{
                        $(this).find(".thumbnail-image").html('').prev('.placeholder').removeClass('hidden');   
                    }
                });
                
                field.find(".articlewave-repeater-icon-list").each(function(){
                    var defaultValue = $(this).next('input[data-name]').attr('data-default');
                    $(this).next('input[data-name]').val(defaultValue);
                    $(this).prev('.articlewave-repeater-selected-icon').children('i').attr('class','').addClass(defaultValue);
                    
                    $(this).find('li').each(function(){
                        var icon_class = $(this).find('i').attr('class');
                        if(defaultValue == icon_class ){
                            $(this).addClass('icon-active');
                        }else{
                            $(this).removeClass('icon-active');
                        }
                    });
                });

                field.find('.articlewave-repeater-fields').show();

                $this.find('.articlewave-repeater-field-control-wrap').append(field);

                field.addClass('expanded').find('.articlewave-repeater-fields').show(); 
                $('.accordion-section-content').animate({ scrollTop: $this.height() }, 1000);
                articlewave_refresh_repeater_values();
            }

        }
        return false;
     });
    
    // remove the repeater field item
    $("#customize-theme-controls").on("click", ".articlewave-repeater-field-remove",function(){
        if( typeof  $(this).parent() != 'undefined'){
            $(this).closest('.articlewave-repeater-field-control').slideUp('normal', function(){
                $(this).remove();
                articlewave_refresh_repeater_values();
            });
        }
        return false;
    });

    $("#customize-theme-controls").on('keyup change', '[data-name]', function(){
        articlewave_refresh_repeater_values();
        return false;
    });

    /**
     * Drag and drop to change order
     */
    $(".articlewave-repeater-field-control-wrap").sortable({
        orientation: "vertical",
        update: function( event, ui ) {
            articlewave_refresh_repeater_values();
        }
    });

    /**
     * Image upload
     */
    var frame;
    
    //Add image
    $('.customize-control-articlewave-repeater').on( 'click', '.articlewave-upload-button', function( event ){
        event.preventDefault();

        var imgContainer = $(this).closest('.articlewave-fields-wrap').find( '.thumbnail-image'),
        placeholder = $(this).closest('.articlewave-fields-wrap').find( '.placeholder'),
        imgIdInput = $(this).siblings('.upload-id');

        frame = wp.media({
            title: 'Select or Upload Image',
            button: {
                text: 'Use Image'
            },
            multiple: false  // Set to true to allow multiple files to be selected
        });

        frame.on( 'select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            imgContainer.html( '<img src="'+attachment.url+'" style="max-width:100%;"/>' );
            placeholder.addClass('hidden');
            imgIdInput.val( attachment.url ).trigger('change');
        });

        //frame.open();
    });

    // DELETE IMAGE LINK
    $('.customize-control-articlewave-repeater').on( 'click', '.articlewave-delete-button', function( event ){
        event.preventDefault();
        var imgContainer = $(this).closest('.articlewave-fields-wrap').find( '.thumbnail-image'),
        placeholder = $(this).closest('.articlewave-fields-wrap').find( '.placeholder'),
        imgIdInput = $(this).siblings('.upload-id');
        imgContainer.find('img').remove();
        placeholder.removeClass('hidden');
        imgIdInput.val( '' ).trigger('change');
    });

    /**
     * Repeater icon selector
     */
    $('body').on('click', '.articlewave-repeater-icon-list li', function(){
        var icon_class = $(this).find('i').attr('class');
        $(this).addClass('icon-active').siblings().removeClass('icon-active');
        $(this).parent('.articlewave-repeater-icon-list').prev('.articlewave-repeater-selected-icon').children('i').attr('class','').addClass(icon_class);
        $(this).parent('.articlewave-repeater-icon-list').next('input').val(icon_class).trigger('change');
        articlewave_refresh_repeater_values();
    });

    $('body').on('click', '.articlewave-repeater-selected-icon', function(){
        $(this).next().slideToggle();
    });


});