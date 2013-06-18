(function($, undefined){

	Symphony.Language.add({
		'View Entries': false,
		'Upload files': false
	});

	var iframe = {

		id: 'multiple-uploads-iframe',
		$obj: null,

		setup: function(){
			var $iframe = $('#'+this.id);

			$iframe.load(function(){
				var iFrame = document.getElementById(iframe.id);

				// set action for the form
				$("#fileupload", iFrame.contentWindow.document).attr('action', Symphony.Context.get('root')+'/extensions/multiple_uploads/lib/upload/');

				// set the section
				$("#class-name", iFrame.contentWindow.document).val(Symphony.Multiple_Uploads['class_name']);
			});

			this.$obj = $iframe;
		}

	};

	$(document).ready(function(){

		var $contents = $('#contents');

		iframe.setup();

		$("#multiple-uploads").on('click', function(){
			var $button = $(this);

			// display Entries
			if( $button.hasClass('selected') ){
				window.location.reload();
			}

			// display Upload
			else{
				$button.addClass('selected').text(Symphony.Language.get('View Entries'));

				$contents.find(' > *').not(iframe.$obj).fadeOut(200).remove();

				iframe.$obj.fadeIn(200);
			}
		});

	});

})(jQuery);
