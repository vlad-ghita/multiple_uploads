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

	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload();

	$('body').on('click', '.template-download a', function(e){
		e.preventDefault();
		parent.location.href = $(this).attr('href');
	});

	var ui = {

		$start: null,
		$cancel: null,

		init: function(){
			this.$start = $('#start');
			this.$cancel = $('#cancel');
			this.$view_entries = $('#view-entries');

			// manage buttons
			$('#fileupload').on('fileuploadadd', function(){
				ui.$start.removeAttr('disabled');
				ui.$cancel.removeAttr('disabled');
			});

			this.$cancel.on('click', function(){
				ui.$start.removeAttr('disabled');
				ui.$cancel.removeAttr('disabled');
			});

			$('#fileupload').on('fileuploadstop', function(){
				ui.$start.attr('disabled', 'disabled');
				ui.$cancel.attr('disabled', 'disabled');
				ui.$view_entries.removeAttr('disabled');
			});

			this.$view_entries.on('click', function(){
				parent.location.reload();
			});
		}

	};

	$(document).ready(function(){
		ui.init();
	});

});
