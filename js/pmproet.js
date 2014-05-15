jQuery(document).ready(function($) {

    /* Variables */
    $subject = $("#email_template_subject").closest("tr");
    $editor = $("#wp-email_template_body-wrap");
    var template;

    $(".hide-while-loading").hide();
    $(".controls").hide();
    $(".striped tr:even").css('background-color','#efefef');

    /* PMPro Email Template Switcher */
    $("#pmpro_email_template_switcher").change(function() {
        $(".status_message").hide();
        template = $(this).val();

        if (template) {
            templateSwitcher(template);
        }
        else {
            $(".hide-while-loading").hide();
            $(".controls").hide();
        }
    });

    $("#submit_template_data").click(function() {
        saveTemplate()
    });

    $("#reset_template_data").click(function() {
        resetTemplate();
    });

    /* Functions */
    function templateSwitcher(template) {

        $(".hide-while-loading").hide();
        $("#pmproet-spinner").show();

        //get template data
        $data = {
            template: template,
            action: 'pmproet_get_template_data'
        };

        $.post(ajaxurl, $data, function(response) {

            var template_data = JSON.parse(response);

			$("#pmproet-spinner").hide();
            $(".controls").show();
            $(".hide-while-loading").show();
            $(".status").hide();

            if (template == 'email_header' || template === 'email_footer')
                $subject.hide();
			
            // set values
			$('#email_template_subject').val(template_data['subject']);
			$('#email_template_body').val(template_data['body']);

        });
    }

    function saveTemplate() {

//        $(".controls").hide();
        $("#submit_template_data").attr("disabled", true);
        $(".status").hide();

        $data = {
            template: template,
            subject: $("#email_template_subject").val(),
            body: $("#email_template_body").val(),
            action: 'pmproet_save_template_data'
        };
        $.post(ajaxurl, $data, function(response) {
            if(response != 0) {
                $("#message").addClass('updated');
            }
            else {
                $("#message").addClass("error");
            }
//            $(".controls").show();
            $("#submit_template_data").attr("disabled", false);
            $(".status_message").html(response);
            $(".status").show();
            $(".status_message").show();
        });
    }

    function resetTemplate() {

        var r = confirm('Are you sure? Your current template settings will be deleted permanently.');

        if(!r) return false;

        $data = {
            template: template,
            action: 'pmproet_reset_template_data'
        };
        $.post(ajaxurl, $data, function(response) {
            var template_data = $.parseJSON(response);
            $('#email_template_subject').val(template_data['subject']);
            $('#email_template_body').val(template_data['body']);
        });
    }

});