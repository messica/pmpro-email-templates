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

            var template = $.parseJSON(response);
			
			$("#pmproet-spinner").hide();
            $(".hide-while-loading").show();           

			console.log(template);
			console.log(template['subject']);
			console.log(template['body']);
			
            // set values
			$('#email_template_subject').val(template['subject']);
			$('#email_template_body').val(template['body']);			            

            if (template == 'email_header' || template == 'email_footer') {
                $subject.hide();
            }
            else {
                $subject.show();
            }

        });
    }

});