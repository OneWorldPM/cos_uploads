<script>
    function sendEmailToAll(templateId, templateSubject) {
        Swal.fire({
            title: 'Please Wait',
            text: 'Loading the recipients list...',
            imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
            imageAlt: 'Loading...',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false
        });

        $.get( "<?=base_url('admin/email/getAllPresenterEmails/json')?>", function(response) {

            let recipientsCount = (JSON.parse(response)).length;

            Swal.fire({
                title: 'Are you sure?',
                html: "You are about to send <br> \""+templateSubject+"\" <br> to "+recipientsCount+" recipients <br> You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send!'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendEmailToAllConfirm(templateId);
                }
            });

        }).fail(function () {
            Swal.fire(
                'Error!',
                'Unable to load the recipients list',
                'error'
            );
        });
    }

    function sendEmailToAllConfirm(templateId) {
        Swal.fire({
            title: 'Please Wait',
            text: 'Loading the template to send...',
            imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
            imageAlt: 'Loading...',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false
        });

        $.get( "<?=base_url('admin/email/getTemplateById/')?>"+templateId, function(response) {


            if (/^[\],:{}\s]*$/.test(response.replace(/\\["\\\/bfnrtu]/g, '@').
            replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
            replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
                // template is ok
            }else{
                Swal.fire(
                    'Error!',
                    'Unable to load the template',
                    'error'
                );
                return false;
            }

            response = JSON.parse(response);

            if (response.status == 'success')
            {
                let template = response.data;

                Swal.fire({
                    title: 'Please Wait',
                    text: 'Sending the emails...',
                    imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
                    imageAlt: 'Loading...',
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });

                $.post( "<?=base_url('admin/email/sendToAll')?>",
                    {
                        templateId: templateId,
                        subject: template.subject,
                        content: template.content
                    })
                    .done(function(response) {
                        response = JSON.parse(response);
                        if (response.status == 'success')
                        {
                            Swal.fire(
                                'Done!',
                                'Emails sent!',
                                'success'
                            );
                        }else{
                            Swal.fire(
                                'Error!',
                                'Unable to send the emails',
                                'error'
                            );
                        }

                    })
                    .fail(function () {
                        Swal.fire(
                            'Error!',
                            'Unable to send the emails',
                            'error'
                        );
                    });


            }else{
                Swal.fire(
                    'Error!',
                    'Unable to load the template',
                    'error'
                );
            }


        })
            .fail(function(response) {
                toastr.error("Unable to load the template");
            });

    }
</script>
