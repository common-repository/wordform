jQuery(document).ready(function($) {					
	//console.log('wordform_admin_view_page_script loaded...');
	//console.log(wordform_admin_view_page_script_obj);
	
	var wordformPostIDReadyHandler = 0;
	var wordformSelectFormListReadyHandler = 0;

	// Block-Editor: Wait to have Post ID Ready - then insert wordform
	function wordformBlockEditorWaitTohavePostIdReady() {
		let postID = wp.data.select("core/editor").getCurrentPostId();			
		if ( postID ) { 
			//console.log('Block-Editor Post ID ready: '+ postID); 
			clearInterval( wordformPostIDReadyHandler ); 
			// Insert WordForm Block
			let block = wp.blocks.createBlock( 'wordform-block/wordform', { wordformid: wordform_admin_view_page_script_obj.attachto_wordformid } );
			wp.data.dispatch('core/block-editor').insertBlock( block );					
		}			
		else { 
			//console.log('Waiting to have Post Id get Ready...'); 
		}
	}
			
	// Attach to: Attach wordform block with Post | Page Editor - Attach only when users click the Attach to Post | Page from All Forms page
	if ( wordform_admin_view_page_script_obj.attachto_wordformid && wordform_admin_view_page_script_obj.attachto_wordformid.length > 0 ) {
		//console.log('AttachFormID: '+ wordform_admin_view_page_script_obj.attachto_wordformid );			
		wordformPostIDReadyHandler = setInterval( () => wordformBlockEditorWaitTohavePostIdReady() , 1000 );		
	}				
	
	// All Forms: Display all created forms Page
	if ( $('div#wordformAllFormsWrapperTable').length > 0 ) {
		//$('#wordformAllFormsTable').DataTable();								
		$('#wordformAllFormsTable').DataTable({
			order: [[ 2, 'desc' ]]
		});
		$('#wordformAllFormsWrapperTable').fadeIn(300);
		// Fix Show Entries select box UI
		$('body').removeClass('wp-core-ui');

		// Copy Shortcode
		new ClipboardJS('.sc-wordform-shortcode-copy-text');

		$('body').on('mouseenter', '#wordformAllFormsTable tr', function() {
			$(this).find('.sc-wordform-shortcode-copy-text').show();
		});
		$('body').on('mouseleave', '#wordformAllFormsTable tr', function() {
			$(this).find('.sc-wordform-shortcode-copy-text').hide();
		});

		$('body').on('click', '.sc-wordform-shortcode-copy-text', function() {	
			let copyBtnTextEle	= $(this);
			copyBtnTextEle.html( wordform_admin_view_page_script_obj.wordform_copied_text );
			setTimeout(function() { copyBtnTextEle.html( wordform_admin_view_page_script_obj.wordform_copy_shortcode_text );}, 3000);
		});
	}
	
    // Create Form : Create form without save leaving window
	if ( $('div.wordform-draggable-elements-div-wrapper').length > 0 ) {
		$(window).on('beforeunload', function(e) {			 
			 let scWordFormElementDropped  = $('.wordform-dropzone-div-wrapper').children().length;
			 let scWordFormID	= $('.wordform-created-form-id').val();
			 //console.log(scWordFormID.length );
			 // Changes but not saved
			 if ( $.trim( scWordFormID ).length === 0 && scWordFormElementDropped > 0 ) {
				 return 'Please click Save Form button to save your form.';	 
			 }
			 else if ( $.trim( scWordFormID ).length > 0 ) {				
				return '';	 
			 }            

			return '';
		});
	}
	
	// Color Picker Configuration
	var myOptions = {			
		defaultColor: false,
		// a callback to fire whenever the color changes to a valid color
		change: function(event, ui){},
		// a callback to fire when the input is emptied or an invalid color
		clear: function() {},
		// hide the color picker controls on load
		hide: true,
		// show a group of common colors beneath the square
		// or, supply an array of colors to customize further
		palettes: true
	};		
	// Enable Color Picker for selected Element
	$('.sc-wordform-form-submit-button-background-color, .sc-wordform-form-submit-button-background-hover-color, .sc-wordform-form-submit-button-font-color').wpColorPicker( {
		change: function(event, ui ) {
			//console.log(ui);
			//console.log(event);
		}
	});
	
	// Tabs : Enable
	if ( $('div#tabs').length > 0 ) {
		$('#tabs').tabs().promise().done(function() {
			$('.sc-wordform-tabs-wrapper-div').fadeIn(300);
		});		
	}
	
	// Submission Data : Display all users submission data
	if ( $('div#wordformUsersSubmissionWrapperTable').length > 0 ) {		
		$('#wordformUsersSubmissionDataTable').DataTable({
			//"processing": true,    										
			"ajax": { 
				'type': 'POST',
				'url':  wordform_admin_view_page_script_obj.adminajax_url,
				'data': { 
						  action: 'sc_wordform_users_submission_data_load', 
						  security: wordform_admin_view_page_script_obj.nonce, 
						} 
			},

			columns: [
					{ data: 'formName' },
					{ data: 'submissionData' },
					{ data: 'date' }
				],
			// order: [[ 2, 'desc' ]]
			order: []

		});
		$('#wordformUsersSubmissionWrapperTable').fadeIn(300);
		// Fix Show Entries select box UI
		$('body').removeClass('wp-core-ui');
	}
	
	// Form Width Slider	
	if ( $('.sc-wordform-form-width-slider').length > 0 ) {		
		$('.sc-wordform-form-width-slider').slider({
			value: wordform_admin_view_page_script_obj.wordform_form_width,
			step: 5,		
			min: 30,
			max: 100,		
			change: function( event, ui ) { $('.sc-wordform-form-width').val(ui.value); }
		});
	}
	
}); // jQuery(document).ready(function($)