;(function($, undefined){

	$(document).ready(function(){

		$('.field-multiple_uploads').each(function(){
			var $this = $(this);
			var id = $this.attr('id');
			var callbacks = {
				'add': 'multiple_uploads.add-'+id,
				'done': 'multiple_uploads.done-'+id,
				'stop': 'multiple_uploads.stop-'+id
			};

			Symphony.Multiple_Uploads[id]['callbacks'] = callbacks;

			var iframe = {
				id: 'multiple-uploads-iframe-'+id,

				setup: function(){
					$('#'+iframe.id).load(function(){
						var iFrame = document.getElementById(iframe.id);

						// set action for the form
						$("#fileupload", iFrame.contentWindow.document).attr('action', Symphony.Context.get('root')+'/extensions/multiple_uploads/lib/upload/');

						// set the section
						$("#class-name", iFrame.contentWindow.document).val(Symphony.Multiple_Uploads[id]['class_name']);

						// set parent entry
						$("#sblp-parent", iFrame.contentWindow.document).val(Symphony.Multiple_Uploads[id]['entry_id']);

						// max file size
						var $max_file_size = parent.jQuery('input[name="MAX_FILE_SIZE"]').clone();
						$("#fileupload", iFrame.contentWindow.document).append($max_file_size);
					});
				}
			};

			// setup the iframe
			iframe.setup();

			// listen to files upload
			var entries = [];

			$('body').on(callbacks['add'], function(e, entry_id){
				$('#'+iframe.id).css('height','300px');
			});

			$('body').on(callbacks['done'], function(e, entry_id){
				entries.push(entry_id);
			});

			$('body').on(callbacks['stop'], function(){
				sblp.current = Symphony.Multiple_Uploads[id]['view'];
				sblp.restoreCurrentView(entries);
			});
		});

	});

})(jQuery);
