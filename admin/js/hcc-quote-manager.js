/**
 * Quote Manager JavaScript
 *
 * @package    Hotel_Cleaning_Calculator_Pro
 * @subpackage Hotel_Cleaning_Calculator_Pro/admin/js
 * @since      2.0.0
 */

(function($) {
    'use strict';

    const QuoteManager = {

        /**
         * Initialize quote manager
         */
        init: function() {
            this.handleStatusChange();
            this.handleDeleteQuote();
            this.handleBulkActions();
            this.handleSearch();
            this.handleFilters();
            this.handleViewQuote();
            this.handleExport();
            this.initDataTable();
        },

        /**
         * Initialize DataTable if available
         */
        initDataTable: function() {
            if (typeof $.fn.DataTable !== 'undefined' && $('#hcc-quotes-table').length) {
                $('#hcc-quotes-table').DataTable({
                    order: [[4, 'desc']], // Order by date
                    pageLength: 25,
                    responsive: true,
                    language: {
                        search: "Search quotes:",
                        lengthMenu: "Show _MENU_ quotes per page",
                        info: "Showing _START_ to _END_ of _TOTAL_ quotes",
                        infoEmpty: "No quotes found",
                        infoFiltered: "(filtered from _MAX_ total quotes)"
                    }
                });
            }
        },

        /**
         * Handle status change
         */
        handleStatusChange: function() {
            $(document).on('change', '.hcc-quote-status', function() {
                const $select = $(this);
                const quoteId = $select.closest('tr').data('quote-id');
                const newStatus = $select.val();
                const originalStatus = $select.data('original-status');
                
                if (!confirm('Change quote status to "' + newStatus + '"?')) {
                    $select.val(originalStatus);
                    return;
                }
                
                $.ajax({
                    url: hccAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'hcc_update_quote_status',
                        nonce: hccAdmin.nonce,
                        quote_id: quoteId,
                        status: newStatus
                    },
                    beforeSend: function() {
                        $select.prop('disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            HCCAdmin.showNotice('success', response.data.message);
                            $select.data('original-status', newStatus);
                            
                            // Update badge
                            const $badge = $select.closest('td').find('.hcc-badge');
                            $badge.removeClass('status-pending status-approved status-rejected');
                            $badge.addClass('status-' + newStatus);
                        } else {
                            HCCAdmin.showNotice('error', response.data.message);
                            $select.val(originalStatus);
                        }
                    },
                    error: function() {
                        HCCAdmin.showNotice('error', 'Failed to update status');
                        $select.val(originalStatus);
                    },
                    complete: function() {
                        $select.prop('disabled', false);
                    }
                });
            });
        },

        /**
         * Handle delete quote
         */
        handleDeleteQuote: function() {
            $(document).on('click', '.hcc-delete-quote', function(e) {
                e.preventDefault();
                
                if (!confirm('Are you sure you want to delete this quote? This action cannot be undone.')) {
                    return;
                }
                
                const $row = $(this).closest('tr');
                const quoteId = $row.data('quote-id');
                
                $.ajax({
                    url: hccAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'hcc_delete_quote',
                        nonce: hccAdmin.nonce,
                        quote_id: quoteId
                    },
                    beforeSend: function() {
                        HCCAdmin.showLoading();
                    },
                    success: function(response) {
                        if (response.success) {
                            $row.fadeOut(300, function() {
                                $(this).remove();
                                QuoteManager.updateQuoteCount();
                            });
                            HCCAdmin.showNotice('success', response.data.message);
                        } else {
                            HCCAdmin.showNotice('error', response.data.message);
                        }
                    },
                    complete: function() {
                        HCCAdmin.hideLoading();
                    }
                });
            });
        },

        /**
         * Handle bulk actions
         */
        handleBulkActions: function() {
            // Select all checkbox
            $(document).on('change', '#select-all-quotes', function() {
                $('.quote-checkbox').prop('checked', $(this).is(':checked'));
                QuoteManager.updateBulkActionButton();
            });
            
            // Individual checkboxes
            $(document).on('change', '.quote-checkbox', function() {
                QuoteManager.updateBulkActionButton();
            });
            
            // Apply bulk action
            $(document).on('click', '#apply-bulk-action', function(e) {
                e.preventDefault();
                
                const action = $('#bulk-action-select').val();
                const selectedQuotes = QuoteManager.getSelectedQuotes();
                
                if (!action) {
                    alert('Please select an action');
                    return;
                }
                
                if (selectedQuotes.length === 0) {
                    alert('Please select at least one quote');
                    return;
                }
                
                if (!confirm('Apply "' + action + '" to ' + selectedQuotes.length + ' quote(s)?')) {
                    return;
                }
                
                QuoteManager.executeBulkAction(action, selectedQuotes);
            });
        },

        /**
         * Execute bulk action
         */
        executeBulkAction: function(action, quoteIds) {
            const ajaxAction = action === 'delete' ? 'hcc_bulk_delete_quotes' : 'hcc_bulk_update_quote_status';
            
            $.ajax({
                url: hccAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: ajaxAction,
                    nonce: hccAdmin.nonce,
                    quote_ids: quoteIds,
                    status: action !== 'delete' ? action : null
                },
                beforeSend: function() {
                    HCCAdmin.showLoading();
                },
                success: function(response) {
                    if (response.success) {
                        HCCAdmin.showNotice('success', response.data.message);
                        
                        if (action === 'delete') {
                            // Remove deleted rows
                            quoteIds.forEach(function(id) {
                                $('tr[data-quote-id="' + id + '"]').fadeOut(300, function() {
                                    $(this).remove();
                                });
                            });
                        } else {
                            // Reload page to show updated statuses
                            location.reload();
                        }
                    } else {
                        HCCAdmin.showNotice('error', response.data.message);
                    }
                },
                complete: function() {
                    HCCAdmin.hideLoading();
                }
            });
        },

        /**
         * Get selected quote IDs
         */
        getSelectedQuotes: function() {
            const selected = [];
            $('.quote-checkbox:checked').each(function() {
                selected.push($(this).val());
            });
            return selected;
        },

        /**
         * Update bulk action button state
         */
        updateBulkActionButton: function() {
            const selectedCount = $('.quote-checkbox:checked').length;
            const $button = $('#apply-bulk-action');
            
            if (selectedCount > 0) {
                $button.prop('disabled', false);
                $button.find('.count').text('(' + selectedCount + ')');
            } else {
                $button.prop('disabled', true);
                $button.find('.count').text('');
            }
        },

        /**
         * Handle search
         */
        handleSearch: function() {
            const searchInput = $('#quote-search');
            
            if (searchInput.length) {
                searchInput.on('keyup', HCCAdmin.debounce(function() {
                    const searchTerm = $(this).val().toLowerCase();
                    
                    $('#hcc-quotes-table tbody tr').each(function() {
                        const $row = $(this);
                        const text = $row.text().toLowerCase();
                        
                        if (text.indexOf(searchTerm) > -1) {
                            $row.show();
                        } else {
                            $row.hide();
                        }
                    });
                }, 300));
            }
        },

        /**
         * Handle filters
         */
        handleFilters: function() {
            $(document).on('change', '.hcc-quote-filter', function() {
                const filterType = $(this).data('filter');
                const filterValue = $(this).val();
                
                if (filterValue === 'all') {
                    $('#hcc-quotes-table tbody tr').show();
                } else {
                    $('#hcc-quotes-table tbody tr').each(function() {
                        const $row = $(this);
                        const rowValue = $row.data(filterType);
                        
                        if (rowValue == filterValue) {
                            $row.show();
                        } else {
                            $row.hide();
                        }
                    });
                }
            });
        },

        /**
         * Handle view quote details
         */
        handleViewQuote: function() {
            $(document).on('click', '.hcc-view-quote', function(e) {
                e.preventDefault();
                
                const quoteId = $(this).data('quote-id');
                
                // Load quote details via AJAX
                $.ajax({
                    url: hccAdmin.ajaxUrl,
                    type: 'POST',
                    data: {
                        action: 'hcc_get_quote_details',
                        nonce: hccAdmin.nonce,
                        quote_id: quoteId
                    },
                    beforeSend: function() {
                        HCCAdmin.showLoading();
                    },
                    success: function(response) {
                        if (response.success) {
                            QuoteManager.displayQuoteModal(response.data);
                        } else {
                            HCCAdmin.showNotice('error', 'Failed to load quote details');
                        }
                    },
                    complete: function() {
                        HCCAdmin.hideLoading();
                    }
                });
            });
        },

        /**
         * Display quote in modal
         */
        displayQuoteModal: function(quote) {
            const modalHtml = `
                <div class="hcc-modal" id="hcc-quote-modal">
                    <div class="hcc-modal-backdrop"></div>
                    <div class="hcc-modal-content" style="max-width: 800px;">
                        <div class="hcc-modal-header">
                            <h2>Quote #${quote.quote_number}</h2>
                            <button class="hcc-modal-close">&times;</button>
                        </div>
                        <div class="hcc-modal-body">
                            <div class="hcc-quote-details">
                                <h3>Client Information</h3>
                                <p><strong>Name:</strong> ${quote.client_name}</p>
                                <p><strong>Email:</strong> ${quote.client_email}</p>
                                <p><strong>Phone:</strong> ${quote.client_phone || 'N/A'}</p>
                                <p><strong>Address:</strong> ${quote.client_address || 'N/A'}</p>
                                
                                <h3>Room Details</h3>
                                <div id="rooms-data">${QuoteManager.formatRoomsData(quote.rooms_data)}</div>
                                
                                <h3>Pricing</h3>
                                <p><strong>Total Area:</strong> ${quote.total_area} m²</p>
                                <p><strong>Subtotal:</strong> $${parseFloat(quote.subtotal).toFixed(2)}</p>
                                <p><strong>Discount:</strong> $${parseFloat(quote.discount_amount).toFixed(2)}</p>
                                <p><strong>Total:</strong> <span style="font-size: 20px; color: #2563eb;">$${parseFloat(quote.total_price).toFixed(2)}</span></p>
                                
                                <h3>Status</h3>
                                <p><span class="hcc-badge status-${quote.status}">${quote.status}</span></p>
                                
                                <h3>Created</h3>
                                <p>${quote.created_at}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal
            $('#hcc-quote-modal').remove();
            
            // Add to body
            $('body').append(modalHtml);
            
            // Show modal
            $('#hcc-quote-modal').fadeIn(300);
        },

        /**
         * Format rooms data for display
         */
        formatRoomsData: function(roomsData) {
            try {
                const rooms = JSON.parse(roomsData);
                let html = '<ul style="list-style: none; padding: 0;">';
                
                rooms.forEach(function(room) {
                    html += `<li style="padding: 10px; border-bottom: 1px solid #e5e7eb;">
                        <strong>${room.type_name || 'Room'}:</strong> ${room.area} m² @ $${room.price_per_m2}/m² = $${room.subtotal.toFixed(2)}
                    </li>`;
                });
                
                html += '</ul>';
                return html;
            } catch(e) {
                return '<p>Unable to display room details</p>';
            }
        },

        /**
         * Handle export
         */
        handleExport: function() {
            $(document).on('click', '#export-quotes', function(e) {
                e.preventDefault();
                
                const format = $('#export-format').val() || 'csv';
                const selectedQuotes = QuoteManager.getSelectedQuotes();
                
                let url = hccAdmin.ajaxUrl + '?action=hcc_export_quotes&format=' + format + '&nonce=' + hccAdmin.nonce;
                
                if (selectedQuotes.length > 0) {
                    url += '&quote_ids=' + selectedQuotes.join(',');
                }
                
                window.location.href = url;
            });
        },

        /**
         * Update quote count
         */
        updateQuoteCount: function() {
            const totalQuotes = $('#hcc-quotes-table tbody tr').length;
            $('.quote-count').text(totalQuotes);
        }
    };

    /**
     * Close modal handlers
     */
    $(document).on('click', '.hcc-modal-close, .hcc-modal-backdrop', function() {
        $('#hcc-quote-modal').fadeOut(300, function() {
            $(this).remove();
        });
    });

    $(document).on('click', '.hcc-modal-content', function(e) {
        e.stopPropagation();
    });

    /**
     * Initialize when document is ready
     */
    $(document).ready(function() {
        if ($('#hcc-quotes-table').length) {
            QuoteManager.init();
        }
    });

    /**
     * Make QuoteManager globally accessible
     */
    window.HCCQuoteManager = QuoteManager;

})(jQuery);