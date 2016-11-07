jQuery(document).ready(function($) {
    function edd_checkout_wizard_tabs_toggle() {
        $('.edd-checkout-wizard-nav-tab').each(function() {
            var selector = $(this).attr('data-selector');

            if($(this).hasClass('nav-tab-active')) {
                $(selector).css({display: 'block'});
            } else {
                $(selector).css({display: 'none'});
            }
        });

        edd_checkout_wizard_buttons_toggle();
    }

    function edd_checkout_wizard_buttons_toggle() {
        var is_first_tab = ($('.edd-checkout-wizard-nav-tab.nav-tab-active').attr('id') == $('.edd-checkout-wizard-nav-tab').first().attr('id'));
        var is_last_tab = ($('.edd-checkout-wizard-nav-tab.nav-tab-active').attr('id') == $('.edd-checkout-wizard-nav-tab').last().attr('id'));

        $('#edd-checkout-wizard-next-button').attr('aria-hidden', ((is_last_tab) ? 'true' : 'false'));
        $('#edd-checkout-wizard-prev-button').attr('aria-hidden', ((is_first_tab) ? 'true' : 'false'));
    }

    function edd_checkout_wizard_validate_current_view() {
        var selector = $('.edd-checkout-wizard-nav-tab.nav-tab-active').attr('data-selector');
        var valid = true;

        $('.edd_errors').remove();

        $(selector).find('input, select, textarea').each(function() {
            var value = $(this).val();

            if( ( $(this).hasClass('required') || $(this)[0].hasAttribute('required') ) && value == '') {
                var label = $(this).parent().find('label').text().replace('*', '').trim().toLowerCase();

                $('<div class="edd_errors edd-alert edd-alert-error">' +
                        '<p class="edd_error" id="edd_error_invalid_first_name">' +
                            '<strong>Error</strong>: Please enter your ' + label +
                        '</p>' +
                    '</div>').insertAfter($(this).parent());

                valid = false;
            }
        });

        return valid;
    }

    edd_checkout_wizard_tabs_toggle();

    $('body').on('edd_gateway_loaded edd_taxes_recalculated', function() {
        edd_checkout_wizard_tabs_toggle();
    });

    $('.edd-checkout-wizard-nav-tab').click(function(e) {
        e.preventDefault();

        if( $(this).attr('data-validated') == 'true' || $(this).attr('data-current') == 'true' ) {
            if( ! $(this).hasClass('nav-tab-active') ) {
                $('.edd-checkout-wizard-nav-tab.nav-tab-active').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');

                edd_checkout_wizard_tabs_toggle();
            }
        }
    });

    $('.edd-checkout-wizard-button').click(function(e) {
        e.preventDefault();

        var active_tab = $('.edd-checkout-wizard-nav-tab.nav-tab-active');

        if($(this).attr('id') == 'edd-checkout-wizard-next-button') {
            if(edd_checkout_wizard_validate_current_view()) {
                active_tab.attr('data-validated', 'true');

               if( active_tab.next().length ) {
                   active_tab.attr('data-current', 'false');
                   active_tab.removeClass('nav-tab-active');
                   active_tab.next().attr('data-current', 'true');
                   active_tab.next().addClass('nav-tab-active');

                   edd_checkout_wizard_tabs_toggle();
               }
            }
        } else {
            if( active_tab.prev().length ) {
                active_tab.removeClass('nav-tab-active');
                active_tab.prev().addClass('nav-tab-active');

                edd_checkout_wizard_tabs_toggle();
            }
        }
    });
});