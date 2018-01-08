<?php
/**
* Add an automatic default custom taxonomy for calendar.
* If no event (taxonomy) is set, the event will be sorted as “draft” and won’t return an offset error.
*
*/

// Fix ACF Support
add_filter('_includes/acf-pro/settings/show_admin', '__return_true');

add_action('acf/input/admin_head', 'my_acf_admin_head');

function my_acf_admin_head() {

    ?>
    <script type="text/javascript">
    (function($) {

        $(document).ready(function(){

            $('.acf-field-573f70ac9357c .acf-input').append( $('#postdivrich') );

        });

    })(jQuery);
    </script>
    <style type="text/css">
        .acf-field #wp-content-editor-tools {
            background: transparent;
            padding-top: 0;
        }
    </style>
    <?php

}