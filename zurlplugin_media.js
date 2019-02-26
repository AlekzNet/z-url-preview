jQuery(function($) {
    $(document).ready(function(){
        $('#insert-zurlpreview-media').click(function() {
            var editor = tinymce.activeEditor;
            editor.windowManager.open( {
                title: 'Z-URL Preview Plugin',
                body: [{
                    type: 'textbox',
                    name: 'title',
                    label: 'Enter URL'
                }],
                onsubmit: function( e ) {
                    // Insert content when the window form is submitted
                    //editor.insertContent( 'Title: ' + e.data.title );

                    jQuery.ajax({
                        url: '/wp-content/plugins/z-url-preview/class.zlinkpreview.php',
                        data: 'url=' + e.data.title + '&image_no=' + 1 + '&css=' + true,
                        type: 'get',
                        success: function(html) {
                            //loader.stop();
                            //editor.insertContent(html);
                            var html1 = html.trim();
                            editor.insertContent(html1);
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
        });
    });
});