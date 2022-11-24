(function() {

    var ufks_management_string = 'Менеджеры';
    var UKFS_MANAGEMENT_SHORTCODE = '[ufks_management][/ufks_management]';

    var html = function () {
        return '<div class="wp-ufks_management" data-shortcode=ufks_management>' + ufks_management_string + '</div>';
    }

    var replaceShortcodes = function(content) {
        return  content.replace(UKFS_MANAGEMENT_SHORTCODE, function() {
                    return html();
                });
    }

    function restoreShortcodes( content ) {
        $('iframe#content_ifr').load(function(){
            $('[data-shortcode=ufks_management]').replaceWith(UKFS_MANAGEMENT_SHORTCODE)
        });
    }

    tinymce.create("tinymce.plugins.management_button_plugin", {

        // url argument holds the absolute url of our plugin directory
        init : function(editor, url) {

            var data = {
                action: 'list_for_tiny_mce'
            };
            jQuery.post( ajaxurl, data, function(response) {
                ufks_management_string = response;
            });

            // add new button     
            editor.addButton("management_tinymce_button", {
                title : "Менеджеры",
                cmd : "management_command",
                image : "https://cdn3.iconfinder.com/data/icons/softwaredemo/PNG/32x32/Circle_Green.png"
            });

            // button functionality.
            editor.addCommand("management_command", function() {
                jQuery(document).ready(function($) {
                    editor.execCommand("mceInsertContent", 0, UKFS_MANAGEMENT_SHORTCODE);
                });
            });

            // replace from shortcode to an placeholder image
            editor.on('BeforeSetcontent', function(event){
                // event.content = replaceShortcodes( event.content );
            });
             
            // replace from placeholder image to shortcode
            editor.on('GetContent', function(event){
                // restoreShortcodes(event.content);
            });

        },

        createControl : function(n, cm) {
            return null;
        },

        getInfo : function() {
            return {
                longname : "Extra Buttons",
                author : "raxee.ru",
                version : "1"
            };
        }
    });

    tinymce.PluginManager.add("management_button_plugin", tinymce.plugins.management_button_plugin);
})();