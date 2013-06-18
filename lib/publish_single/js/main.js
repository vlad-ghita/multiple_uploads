/*
 * jQuery File Upload Plugin JS Example 6.7
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */

$(function(){
	'use strict';

	function getUrlVars() { // Read a page's GET URL variables and return them as an associative array.
		var vars = [],
			hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for (var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}

	var vars = getUrlVars();

	var callbacks = parent.Symphony.Multiple_Uploads[vars['id']]['callbacks'];

	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload();

	var ui = {

		init: function(){
			this.$start = $('#start');
			this.$cancel = $('#cancel');

			// manage buttons
			$('#fileupload').on('fileuploadadd', this.enableButtons);
			this.$cancel.on('click', this.disableButtons);
			$('#fileupload').on('fileuploadstop', this.disableButtons);

			// notify parent field when a file has been added
			$('#fileupload').on('fileuploadadd', function(){
				parent.jQuery('body').trigger(callbacks['add']);
			});

			// notify parent field when a file finished uploading
			$('#fileupload').on('fileuploaddone', function(e, data){
				ui.removeUploaded();
				if( data.result != null && data.result[0].hasOwnProperty("entry_id") )
					parent.jQuery('body').trigger(callbacks['done'], data.result[0]['entry_id']);
			});

			// notify parent field when all files finished uploading
			$('#fileupload').on('fileuploadstop', function(){
				ui.removeUploaded();
				parent.jQuery('body').trigger(callbacks['stop']);
			});
		},

		disableButtons: function(){
			ui.$start.add(ui.$cancel).attr('disabled', 'disabled');
		},

		enableButtons: function(){
			ui.$start.add(ui.$cancel).removeAttr('disabled');
		},

		removeUploaded: function(){
			$('tr.template-download').fadeOut(200);
		}

	};

	$(document).ready(function(){
		ui.init();
	});

});
