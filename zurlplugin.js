( function() {
    tinymce.PluginManager.add( 'at_zurlpreview', function( editor, url ) {

        // Add a button that opens a window
        editor.addButton( 'at_zurlpreview_button_key', {

            title: 'Insert URL',
            image: url + '/button.png',
            onclick: function() {
                // Open window
                editor.windowManager.open( {
                    title: 'Z-URL Preview Plugin',
                    body: [{
                        type: 'textbox',
                        name: 'title',
                        label: 'Enter URL'
                    }],
                    onsubmit: function( e ) {
                        // Insert content when the window form is submitted
                        //editor.insertContent( 'Title in e: ' + e.data.title + "\n" );
			//editor.insertContent( 'URL in e: ' + url + "\n");
                        jQuery.ajax({
                            url: url + '/class.zlinkpreview.php',
                            //data: 'url=' + e.data.title + '&image_no=' + 1 + '&css=' + true,
			    data: "url=" + encodeURIComponent(e.data.title) + '&image_no=' + 1 + '&css=' + true,
                            type: 'get',
                            success: function(html) {
                                //loader.stop();
                                //editor.insertContent(html);
                                var html1 = html.trim();
                                editor.insertContent(html);
                                if (!(document.forms['post'].elements["title"] === undefined)) {
									if (document.forms['post'].elements["title"].value == "") {
										var topar = document.createElement('div');
										topar.innerHTML = html;
										var parh2col = topar.getElementsByTagName('h3');
										var pt = '';
										for(var i = 0, max = parh2col.length; i < max; i++) {
											pt = parh2col[i].innerHTML;
										}
										document.forms['post'].elements["title"].value = pt;
										if (pt != "") {
											var obj = document.getElementsByTagName('label');
											for(var i = 0, max = obj.length; i < max; i++) {
												if (obj[i].id == "title-prompt-text") {
													obj[i].className = 'screen-reader-text';
													break;
												}
											}
										}
									}
								}
                            }
                        })
                    }
                } );
            }
        } );
    } );
} )();
