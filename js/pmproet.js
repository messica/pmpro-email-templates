jQuery(document).ready(function($) {

    $subject = $("#email_template_subject").closest("tr");
    $editor = $("#wp-email_template_body-wrap");
    var template;

//    $("#pmpro_email_template_switcher").val("");

    /* PMPro Email Template Switcher */
    $("#pmpro_email_template_switcher").change(function() {
        $(".status_message").hide();
        template = $(this).val();

        if (template) {
            templateSwitcher(template);
        }
        else {
            $(".hide-while-loading").hide();
            $("#template_editor_container").html("");
        }
    });

    function templateSwitcher(template) {

        $(".hide-while-loading").hide();
        $("#pmproet-spinner").show();

        //get template data
        $data = {
            template: template,
            action: 'pmproet_get_template_data'
        };

        $.post(ajaxurl, $data, function(response) {

            $("#pmproet-spinner").hide();
            $(".hide-while-loading").show();

            if (template !== 'email_header' || template !== 'email_footer') {
                $subject.show();
            }

            //get subject from cookie

            $("#template_editor_container").html(response);

            // initialize new editor
            tinyMCE.init({
                skin : "wp_theme",
                mode : "exact",
                elements: "editor",
                theme: "advanced",
                valid_elements: "*"
            });

            if (template == 'email_header' || template == 'email_footer') {
                $subject.hide();
            }
            else {
                $subject.show();
            }

        });
    }

});