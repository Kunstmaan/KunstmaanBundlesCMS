// With jquery-filedrop: https://github.com/weixiyen/jquery-filedrop
// Demo en PHP-voorbeeld: http://tutorialzine.com/2011/09/html5-file-upload-jquery-php/

$(function(){
	var dropbox = $('#drop-area'),
		list = $('#file-list');
		
	var loader = '<div class="loader">'+
					'<span class="a-1 loadbullet"></span>'+
					'<span class="a-2 loadbullet"></span>'+
					'<span class="a-3 loadbullet"></span>'+
					'<span class="a-4 loadbullet"></span>'+
					'<span class="a-5 loadbullet"></span>'+
				'</div>';

	dropbox.filedrop({
		// The name of the $_FILES entry:
		paramname:'pic',

		maxfiles: 10,
    	maxfilesize: 4, // in mb
		url: 'post_file.php',
		
		//When dragging files anywhere inside the browser window & leaving 
		docOver: function() {
			$('#droplabel').addClass('inside-glow');
			document.getElementById("droplabel").innerHTML = "Drop Here";
		},
		docLeave: function() {
			$('#droplabel').removeClass('inside-glow');
			document.getElementById("droplabel").innerHTML = "Add New";
		},
		
		//Droped Files
		drop: function() {
			$('#droplabel').removeClass('inside-glow');
			document.getElementById("droplabel").innerHTML = "Add New";   
		},

    	error: function(err, file) {
			switch(err) {
				case 'BrowserNotSupported':
					alert('Your browser does not support HTML5 file uploads!');
					break;
				case 'TooManyFiles':
					alert('Too many files! Please select 10 at most!');
					break;
				case 'FileTooLarge':
					alert(file.name+' is too large! Please upload files up to 2mb.');
					break;
				default:
					break;
			}
		},

		// Called before each upload is started
		beforeEach: function(file){
			if(!file.type.match(/^image\//)){
				alert('Only images are allowed!');
				// Returning false will cause the
				// file to be rejected
				return false;
			}
		},

		//Start Upload
		uploadStarted:function(i, file, len){
			createImage(file);
		},

		//Progress
		progressUpdated: function(file) {
			
		},
		
		//Upload is done
		uploadFinished:function(i,file,response){
			
		}
	});

	var template = '<li>'+
						'<a href="#" class="item">'+
							'<img alt="" />'+
						'</a>'+
						'<a href="#" class="del" data-controls-modal="delete-image" data-backdrop="true" data-keyboard="true">Delete</a>'+
						'<span class="helper">Click to edit</span>'+
					'</li>'; 
					
	
	function createImage(file){

		var preview = $(template),
			image = $('img', preview);

		var reader = new FileReader();

		image.width = 100;
		image.height = 100;

		reader.onload = function(e){
			// e.target.result holds the DataURL which
			// can be used as a source of the image:
			image.attr('src',e.target.result);
		};

		// Reading the file as a DataURL. When finished,
		// this will trigger the onload function above:
		reader.readAsDataURL(file);

		//message.hide();
		preview.insertBefore('#drop-area');

		// Associating a preview container
		// with the file, using jQuery's $.data():
		$.data(file,preview);
	}
});