/**
 * Room Type Manager JavaScript
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/js
 * @since      2.0.0
 */

(function($) {
    'use strict';

    const RoomManager = {

        /**
         * Initialize room manager
         */
        init: function() {
            this.initSortable();
            this.handleAddRoom();
            this.handleEditRoom();
            this.handleDeleteRoom();
            this.handleSaveRooms();
            this.handleToggleActive();
        },

        /**
         * Initialize sortable functionality
         */
        initSortable: function() {
            if (typeof $.fn.sortable !== 'undefined') {
                $('.hcc-room-type-list').sortable({
                    handle: '.hcc-room-type-drag',
                    placeholder: 'hcc-sortable-placeholder',
                    opacity: 0.8,
                    cursor: 'move',
                    update: function(event, ui) {
                        RoomManager.updateRoomOrder();
                    }
                });
            }
        },

        /**
         * Update room order after sorting
         */
        updateRoomOrder: function() {
            $('.hcc-room-type-item').each(function(index) {
                $(this).find('.room-order').val(index + 1);
            });
        },

        /**
         * Handle add new room type
         */
        handleAddRoom: function() {
            $(document).on('click', '#hcc-add-room-type', function(e) {
                e.preventDefault();
                
                const template = $('#hcc-room-type-template').html();
                const newId = 'room_' + Date.now();
                const $newRoom = $(template.replace(/\{ID\}/g, newId));
                
                $('.hcc-room-type-list').append($newRoom);
                $newRoom.hide().fadeIn(300);
                
                // Focus on name field
                $newRoom.find('.room-name').focus();
            });
        },

        /**
         * Handle edit room type
         */
        handleEditRoom: function() {
            $(document).on('click', '.hcc-edit-room', function(e) {
                e.preventDefault();
                
                const $item = $(this).closest('.hcc-room-type-item');
                const $form = $item.find('.hcc-room-edit-form');
                
                if ($form.is(':visible')) {
                    $form.slideUp(300);
                    $(this).html('<span class="dashicons dashicons-edit"></span> Edit');
                } else {
                    // Hide other open forms
                    $('.hcc-room-edit-form').slideUp(300);
                    $('.hcc-edit-room').html('<span class="dashicons dashicons-edit"></span> Edit');
                    
                    // Show this form
                    $form.slideDown(300);
                    $(this).html('<span class="dashicons dashicons-arrow-up-alt2"></span> Close');
                }
            });
        },

        /**
         * Handle delete room type
         */
        handleDeleteRoom: function() {
            $(document).on('click', '.hcc-delete-room', function(e) {
                e.preventDefault();
                
                if (!confirm('Are you sure you want to delete this room type?')) {
                    return;
                }
                
                const $item = $(this).closest('.hcc-room-type-item');
                
                $item.fadeOut(300, function() {
                    $(this).remove();
                    RoomManager.updateRoomOrder();
                });
            });
        },

        /**
         * Handle toggle active status
         */
        handleToggleActive: function() {
            $(document).on('change', '.hcc-room-active-toggle', function() {
                const $toggle = $(this);
                const isActive = $toggle.is(':checked');
                const $item = $toggle.closest('.hcc-room-type-item');
                
                if (isActive) {
                    $item.removeClass('inactive');
                } else {
                    $item.addClass('inactive');
                }
            });
        },

        /**
         * Handle save all room types
         */
        handleSaveRooms: function() {
            $(document).on('click', '#hcc-save-room-types', function(e) {
                e.preventDefault();
                
                const $button = $(this);
                const originalText = $button.html();
                const roomTypes = [];
                
                // Validate and collect room data
                let isValid = true;
                
                $('.hcc-room-type-item').each(function() {
                    const $item = $(this);
                    const id = $item.data('id') || 'room_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                    const name = $item.find('.room-name').val().trim();
                    const description = $item.find('.room-description').val().trim();
                    const price = parseFloat($item.find('.room-price').val());
                    const icon = $item.find('.room-icon').val().trim();
                    const active = $item.find('.hcc-room-active-toggle').is(':checked');
                    const order = $item.index();
                    
                    // Validation
                    if (name === '') {
                        isValid = false;
                        $item.find('.room-name').addClass('error');
                        HCCAdmin.showNotice('error', 'Room type name is required');
                        return false;
                    }
                    
                    if (isNaN(price) || price < 0) {
                        isValid = false;
                        $item.find('.room-price').addClass('error');
                        HCCAdmin.showNotice('error', 'Please enter a valid price');
                        return false;
                    }
                    
                    roomTypes.push({
                        id: id,
                        name: name,
                        description: description,
                        price_per_m2: price,
                        icon: icon,
                        active: active,
                        order: order
                    });
                });
                
                if (!isValid) {
                    return;
                }
                
                // Save via AJAX
                $.ajax({
                    url: hccAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'hcc_save_room_types',
                        nonce: hccAdmin.nonce,
                        room_types: JSON.stringify(roomTypes)
                    },
                    beforeSend: function() {
                        $button.prop('disabled', true).html('<span class="hcc-loading"></span> Saving...');
                        $('.room-name, .room-price').removeClass('error');
                    },
                    success: function(response) {
                        if (response.success) {
                            HCCAdmin.showNotice('success', response.data.message);
                            
                            // Update item IDs
                            $('.hcc-room-type-item').each(function(index) {
                                if (!$(this).data('id')) {
                                    $(this).attr('data-id', roomTypes[index].id);
                                }
                            });
                        } else {
                            HCCAdmin.showNotice('error', response.data.message);
                        }
                    },
                    error: function() {
                        HCCAdmin.showNotice('error', 'An error occurred while saving room types');
                    },
                    complete: function() {
                        $button.prop('disabled', false).html(originalText);
                    }
                });
            });
        },

        /**
         * Get room type data
         */
        getRoomTypeData: function($item) {
            return {
                id: $item.data('id'),
                name: $item.find('.room-name').val(),
                description: $item.find('.room-description').val(),
                price_per_m2: parseFloat($item.find('.room-price').val()),
                icon: $item.find('.room-icon').val(),
                active: $item.find('.hcc-room-active-toggle').is(':checked'),
                order: $item.index()
            };
        }
    };

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        if ($('.hcc-room-type-list').length) {
            RoomManager.init();
        }
    });

    /**
     * Make RoomManager globally accessible
     */
    window.HCCRoomManager = RoomManager;

})(jQuery);