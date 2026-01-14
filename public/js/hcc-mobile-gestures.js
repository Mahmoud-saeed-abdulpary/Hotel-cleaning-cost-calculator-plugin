
/* ========================================
   FILE: public/js/hcc-mobile-gestures.js
   ======================================== */

(function($) {
    'use strict';

    const HCCMobileGestures = {
        init: function($calculator) {
            if (!this.isTouchDevice()) return;
            
            this.setupSwipeToDelete($calculator);
            this.setupTouchOptimizations($calculator);
        },

        isTouchDevice: function() {
            return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        },

        setupSwipeToDelete: function($calculator) {
            let startX, startY, element;
            
            $calculator.on('touchstart', '.hcc-room-card', function(e) {
                const touch = e.originalEvent.touches[0];
                startX = touch.clientX;
                startY = touch.clientY;
                element = $(this);
            });
            
            $calculator.on('touchmove', '.hcc-room-card', function(e) {
                if (!element) return;
                
                const touch = e.originalEvent.touches[0];
                const deltaX = touch.clientX - startX;
                const deltaY = touch.clientY - startY;
                
                if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > 50) {
                    element.css('transform', 'translateX(' + deltaX + 'px)');
                    element.css('opacity', 1 - Math.abs(deltaX) / 200);
                }
            });
            
            $calculator.on('touchend', '.hcc-room-card', function(e) {
                if (!element) return;
                
                const transform = element.css('transform');
                const matrix = new WebKitCSSMatrix(transform);
                const deltaX = matrix.m41;
                
                if (Math.abs(deltaX) > 150) {
                    element.find('.hcc-remove-room').trigger('click');
                } else {
                    element.css({transform: '', opacity: ''});
                }
                
                element = null;
            });
        },

        setupTouchOptimizations: function($calculator) {
            $calculator.find('.hcc-select, .hcc-input').attr('autocomplete', 'off');
            $calculator.find('input[type="number"]').attr('inputmode', 'decimal');
        }
    };

    $(document).ready(function() {
        $('.hcc-calculator-wrapper').each(function() {
            HCCMobileGestures.init($(this));
        });
    });

    window.HCCMobileGestures = HCCMobileGestures;

})(jQuery);