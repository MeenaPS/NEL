jQuery( function($) {
	'use strict';
	$('.checkout-addon-action .button').click(function(){
		var pluginStatus = $(this).attr('data-status');
		var pluginAction = $(this).attr('data-action');
		var pluginId = $(this).attr('data-plugin-id');
		var pluginSlug = $(this).attr('data-plugin-slug');
		var pluginUrl = $(this).attr('data-plugin-url');
		var disabledStatus = $(this).hasClass('disabled');
		console.log('status start');
		console.log(pluginStatus);
		console.log(pluginAction);
		console.log(pluginSlug);
		
		// Abort function if current user can't activate plugins.
		if( true === disabledStatus ) {
			return;
		}

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'change_klarna_addon_status',
				plugin_status: pluginStatus,
				plugin_action: pluginAction,
				plugin_id: pluginId,
				plugin_slug: pluginSlug,
				plugin_url: pluginUrl,
				nonce: 'deactivate_klarna_addon_nonce'
			},
			dataType: 'json',
			success: function(data) {
				console.log('success');
				console.log(data);
			},
			error: function(data) {
				console.log('error');
				console.log(data);
			},
			complete: function(data) {
				
				console.log('complete');
				console.log(data);
				if( true === data.responseJSON.success ) {
					// Change the status.
					$( '.checkout-addon.' + pluginId + ' .button' ).attr('data-status', data.responseJSON.data.new_status );
					$( '.checkout-addon.' + pluginId + ' .button' ).attr('data-action', data.responseJSON.data.new_action );
					$( '.checkout-addon.' + pluginId + ' .button .action-text').text( data.responseJSON.data.new_status_label );

					if( 'installed' === data.responseJSON.data.new_status ) {
						$( '.checkout-addon.' + pluginId + ' .button .switch' ).removeClass('download');
						$( '.checkout-addon.' + pluginId + ' .button .switch .round' ).removeClass('dashicons').removeClass('dashicons-download').addClass('slider');
					}
				} else {
					
					$( '.checkout-addon.' + pluginId ).append( '<p>' + data.responseJSON.error + '</p>' );
				}
				
				
			}
		});
						
	});

});