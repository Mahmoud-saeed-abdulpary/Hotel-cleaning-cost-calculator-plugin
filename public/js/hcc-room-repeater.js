/**
 * Room Repeater JavaScript
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/public/js
 * @since      2.0.0
 */

(function($) {
    'use strict';

    const HCCRoomRepeater = {

        /**
         * Room counter
         */
        roomCounter: 0,

        /**
         * Initialize room repeater
         */
        init: function($calculator) {
            this.roomCounter = $calculator.find('.hcc-room-card').length;
            this.bindEvents($calculator);
        },

        /**
         * Bind events
         */
        bindEvents: function($calculator) {
            // Add room
            $calculator.on('click', '.hcc-add-room-btn', function(e) {
                e.preventDefault();
                HCCRoomRepeater.addRoom($calculator);
            });
            
            // Remove room
            $calculator.on('click', '.hcc-remove-room', function(e) {
                e.preventDefault();
                HCCRoomRepeater.removeRoom($(this).closest('.hcc-room-card'));
            });
        },

        /**
         * Add new room
         */
        addRoom: function($calculator) {
            this.roomCounter++;
            
            const roomHTML = HCCRoomRepeater.getRoomTemplate(this.roomCounter);
            const $roomsList = $calculator.find('.hcc-rooms-list');
            
            $roomsList.append(roomHTML);
            
            const $newRoom = $roomsList.find('.hcc-room-card').last();
            $newRoom.hide().fadeIn(300);
            
            // Update room numbers
            HCCRoomRepeater.updateRoomNumbers($calculator);
            
            // Focus on room type select
            $newRoom.find('.hcc-room-type').focus();
            
            // Trigger custom event
            $calculator.trigger('hcc-room-added', [$newRoom]);
        },

        /**
         * Remove room
         */
        removeRoom: function($room) {
            const $calculator = $room.closest('.hcc-calculator-wrapper');
            const $roomsList = $calculator.find('.hcc-rooms-list');
            
            // Don't allow removing if only one room
            if ($roomsList.find('.hcc-room-card').length <= 1) {
                HCCPublic.showMessage($calculator, 'At least one room is required', 'error');
                return;
            }
            
            $room.addClass('removing').fadeOut(300, function() {
                $(this).remove();
                HCCRoomRepeater.updateRoomNumbers($calculator);
                
                // Recalculate if calculator exists
                if (typeof HCCCalculator !== 'undefined') {
                    HCCCalculator.calculate($calculator, true);
                }
                
                // Trigger custom event
                $calculator.trigger('hcc-room-removed');
            });
        },

        /**
         * Update room numbers
         */
        updateRoomNumbers: function($calculator) {
            $calculator.find('.hcc-room-card').each(function(index) {
                $(this).find('.hcc-room-number').text('Room ' + (index + 1));
            });
        },

        /**
         * Get room template
         */
        getRoomTemplate: function(roomNumber) {
            const roomTypes = hccData.roomTypes || [];
            
            let html = '<div class="hcc-room-card">';
            html += '<div class="hcc-room-number">Room ' + roomNumber + '</div>';
            html += '<button type="button" class="hcc-remove-room">' + HCCPublic.getString('removeRoom') + '</button>';
            
            html += '<div class="hcc-room-fields">';
            
            // Room Type Select
            html += '<div class="hcc-form-group">';
            html += '<label class="hcc-form-label required">' + HCCPublic.getString('roomType') + '</label>';
            html += '<select class="hcc-select hcc-room-type" required>';
            html += '<option value="">' + HCCPublic.getString('selectRoomType') + '</option>';
            
            roomTypes.forEach(function(roomType) {
                if (roomType.active) {
                    html += '<option value="' + roomType.id + '">' + roomType.name + ' (' + HCCPublic.formatCurrency(roomType.price_per_m2) + '/m²)</option>';
                }
            });
            
            html += '</select>';
            html += '</div>';
            
            // Room Area Input
            html += '<div class="hcc-form-group">';
            html += '<label class="hcc-form-label required">' + HCCPublic.getString('roomArea') + '</label>';
            html += '<input type="number" class="hcc-input hcc-room-area" placeholder="25" min="1" step="0.01" required/>';
            html += '</div>';
            
            html += '</div>'; // .hcc-room-fields
            
            // Subtotal
            html += '<div class="hcc-room-subtotal">';
            html += '<span class="hcc-room-subtotal-label">' + HCCPublic.getString('subtotal') + ':</span>';
            html += '<span class="hcc-room-subtotal-value">-</span>';
            html += '</div>';
            
            html += '</div>'; // .hcc-room-card
            
            return html;
        },

        /**
         * Get all rooms data
         */
        getRoomsData: function($calculator) {
            const rooms = [];
            
            $calculator.find('.hcc-room-card').each(function() {
                const $room = $(this);
                const roomData = {
                    type_id: $room.find('.hcc-room-type').val(),
                    area: parseFloat($room.find('.hcc-room-area').val()) || 0
                };
                
                if (roomData.type_id && roomData.area > 0) {
                    rooms.push(roomData);
                }
            });
            
            return rooms;
        },

        /**
         * Clear all rooms
         */
        clearAllRooms: function($calculator) {
            $calculator.find('.hcc-room-card').fadeOut(300, function() {
                $(this).remove();
            });
            
            this.roomCounter = 0;
            
            // Add one empty room
            setTimeout(function() {
                HCCRoomRepeater.addRoom($calculator);
            }, 350);
        }
    };

    /**
     * Make HCCRoomRepeater globally accessible
     */
    window.HCCRoomRepeater = HCCRoomRepeater;

})(jQuery);