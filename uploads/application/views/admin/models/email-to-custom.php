<!-- sendToCustomEmailModal -->
<div class="modal fade" id="sendToCustomEmailModal" tabindex="-1" role="dialog" aria-labelledby="sendToCustomEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendToCustomEmailModalLabel">Send: <span id="tocustomemailTemplateName"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">To</span>
                    </div>
                    <input id="tocustomemailToEmail" type="email" class="form-control" placeholder="Email" aria-label="Email" aria-describedby="basic-addon1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="send-tocustomemail-confirm-btn" template-id="" template-subject="" type="button" class="btn btn-primary text-white"><i class="fas fa-paper-plane"></i> Send</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#send-tocustomemail-confirm-btn').on('click', function () {

            let templateId = $(this).attr('template-id');
            let templateSubject = $(this).attr('template-subject');

            let toEmail = $('#tocustomemailToEmail').val();

            if(toEmail == '')
            {
                toastr.error("Please enter an email");
                return false;
            }

            Swal.fire({
                title: 'Are you sure?',
                html: "You are about to send <br> \""+templateSubject+"\" <br> to "+toEmail+" <br> You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, send!'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendToCustomEmailConfirm(templateId, toEmail);
                }
            })
        });
    });

    function sendToCustomEmail(templateId, templateSubject) {
        $('#send-tocustomemail-confirm-btn').attr('template-id', templateId);
        $('#send-tocustomemail-confirm-btn').attr('template-subject', templateSubject);
        $('#tocustomemailTemplateName').html(templateSubject);
        $('#sendToCustomEmailModal').modal('show');
    }

    function sendToCustomEmailConfirm(templateId, email) {
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
                    text: 'Sending the email...',
                    imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
                    imageAlt: 'Loading...',
                    showCancelButton: false,
                    showConfirmButton: false,
                    allowOutsideClick: false
                });

                $.post( "<?=base_url('admin/email/sendToCustomEmail')?>",
                    {
                        toEmail: email,
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
                                'Email sent!',
                                'success'
                            );
                        }else{
                            Swal.fire(
                                'Error!',
                                'Unable to send the email',
                                'error'
                            );
                        }

                    })
                    .fail(function () {
                        Swal.fire(
                            'Error!',
                            'Unable to send the email',
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
