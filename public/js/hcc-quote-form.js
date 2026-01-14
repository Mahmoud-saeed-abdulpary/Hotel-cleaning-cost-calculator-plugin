/* ========================================
   FILE: public/js/hcc-quote-form.js
   ======================================== */

(function($) {
    'use strict';

    const HCCQuoteForm = {
        init: function($calculator) {
            this.bindEvents($calculator);
        },

        bindEvents: function($calculator) {
            $calculator.on('submit', '.hcc-quote-form', function(e) {
                e.preventDefault();
                HCCQuoteForm.submitQuote($(this));
            });
        },

        submitQuote: function($form) {
            const $calculator = $form.closest('.hcc-calculator-wrapper');
            const calcData = HCCPublic.getCalculatorData($calculator);
            
            if (!calcData || !calcData.calculation) {
                HCCPublic.showMessage($calculator, 'Please calculate first', 'error');
                return;
            }
            
            const formData = {
                client_name: $form.find('[name="client_name"]').val(),
                client_email: $form.find('[name="client_email"]').val(),
                client_phone: $form.find('[name="client_phone"]').val(),
                client_address: $form.find('[name="client_address"]').val(),
                rooms_data: JSON.stringify(calcData.rooms),
                calculation_data: JSON.stringify(calcData.calculation)
            };
            
            if (!formData.client_name || !formData.client_email) {
                HCCPublic.showMessage($calculator, 'Name and email are required', 'error');
                return;
            }
            
            if (!HCCPublic.isValidEmail(formData.client_email)) {
                HCCPublic.showMessage($calculator, 'Please enter a valid email', 'error');
                return;
            }
            
            HCCPublic.showLoading($calculator);
            
            $.ajax({
                url: hccData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'hcc_submit_quote',
                    nonce: hccData.nonce,
                    ...formData
                },
                success: function(response) {
                    if (response.success) {
                        HCCPublic.showMessage($calculator, response.data.message, 'success');
                        $form[0].reset();
                    } else {
                        HCCPublic.showMessage($calculator, response.data.message, 'error');
                    }
                },
                error: function(error) {
                    HCCPublic.handleError($calculator, error);
                },
                complete: function() {
                    HCCPublic.hideLoading($calculator);
                }
            });
        }
    };

    window.HCCQuoteForm = HCCQuoteForm;

})(jQuery);

