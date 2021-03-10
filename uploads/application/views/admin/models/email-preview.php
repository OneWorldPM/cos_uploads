<!-- Modal -->
<div class="modal fade" id="emailPreviewModal" tabindex="-1" role="dialog" aria-labelledby="emailPreviewModalLabel" aria-hidden="true" style="z-index: 2000;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailPreviewModalLabel"><span id="emailPreviewSubject">Unable to load</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button><br>
            </div>
            <div class="row" style="padding-left: 10px;padding-right: 10px;">
                <div class="col-md-8">
                    <span class="float-left" style="margin-left: 5px;font-size: 15px;"><strong>COS ePoster Submission</strong> &lt;presentations@yourconference.live&gt;</span>
                </div>
                <div class="col-md-4">
                    <span class="float-right" style="font-size: 15px;">5:42 PM (2 minutes ago)</span>
                </div>
                <div class="col-md-12">
                    <span class="float-left" style="margin-left: 5px;font-size: 14px;">to me <i class="fas fa-caret-down"></i></span>
                </div>
            </div>
            <div id="emailPreviewContent" class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {


    });

    function loadEmailTemplatePreview(templateId) {

        Swal.fire({
            title: 'Please Wait',
            text: 'Loading the template preview...',
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
                    'Unable to load template preview',
                    'error'
                );
                return false;
            }

            response = JSON.parse(response);

            if (response.status == 'success')
            {
                let template = response.data;


                $.post( "<?=base_url('upload_system_files/email_templates/common_template.php')?>",
                    {
                        content: template.content
                    })
                    .done(function(email_template) {
                        $('#emailPreviewSubject').html(template.subject);
                        $('#emailPreviewContent').html(email_template);

                        $('#emailPreviewModal').modal('show');
                        swal.close();
                    })
                    .fail(function () {
                        Swal.fire(
                            'Error!',
                            'Unable to load template preview',
                            'error'
                        );
                    });


            }else{
                Swal.fire(
                    'Error!',
                    'Unable to load template preview',
                    'error'
                );
            }


        })
            .fail(function(response) {
                toastr.error("Unable to load template preview");
            });
    }
</script>
