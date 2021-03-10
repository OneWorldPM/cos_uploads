<style>
    p{
        color:unset;
        font-weight: unset;
        margin-top:unset;
        margin-bottom:unset;
    }
</style>

<!-- Modal -->
<div class="modal fade" id="editEmailModal" tabindex="-1" role="dialog" aria-labelledby="editEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editEmailModalLabel">Edit Email Template</h5>
                <button type="button" class="discardAndCloseTemplateBtn close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="emailSubject">Email Subject</label>
                    <input type="text" class="form-control" id="emailSubject" aria-describedby="emailSubjectHelp" placeholder="Enter email subject">
                    <small id="emailSubjectHelp" class="form-text text-muted">This will be the subject of the email you send.</small>
                </div>

                <div class="form-group">
                    <label for="emailContent">Email Content</label>
                    <textarea class="form-control" id="emailContent" rows="10" aria-describedby="emailContentHelp"></textarea>
                    <small id="emailContentHelp" class="form-text text-muted">This will be inserted into the body of the email you send.</small>
                </div>

            </div>
            <div class="modal-footer">
                <button id="saveAndPreviewTemplateBtn" class="btn btn-info" template-id=""><i class="fas fa-envelope-open-text"></i> Save & Preview</button>
                <button id="saveAndCloseTemplateBtn" class="btn btn-success" template-id=""><i class="fas fa-save"></i> Save & Close</button>
                <button type="button" class="discardAndCloseTemplateBtn btn btn-danger"><i class="fas fa-window-close"></i> Discard & Close</button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<script>
    $(document).ready(function() {
        $('.discardAndCloseTemplateBtn').on('click', function () {
            $('#editEmailModal').modal('hide');
            $('#emailContent').summernote('code', '');
            $('#emailContent').summernote('reset');
        });

        $('#saveAndPreviewTemplateBtn').on('click', function () {

            Swal.fire({
                title: 'Please Wait',
                text: 'Saving the template content...',
                imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
                imageAlt: 'Loading...',
                showCancelButton: false,
                showConfirmButton: false,
                allowOutsideClick: false
            });

            let templateId = $(this).attr('template-id');

            let subject = $('#emailSubject').val();
            let content = $('#emailContent').summernote('code');

            $.post( "<?=base_url('admin/email/editEmailTemplate')?>",
                {
                    id: templateId,
                    subject: subject,
                    content: content
                })
                .done(function( data ) {

                    data = JSON.parse(data);
                    if (data.status == 'success')
                    {
                        loadEmailTemplatePreview(templateId);
                    }else{
                        Swal.fire(
                            'Unable to save',
                            'Database error',
                            'error'
                        );
                    }

                })
                .fail(function () {
                    Swal.fire(
                        'Unable to save',
                        'Network Error',
                        'error'
                    );
                });
        });

        $('#saveAndCloseTemplateBtn').on('click', function () {

            Swal.fire({
                title: 'Please Wait',
                text: 'Saving the template content...',
                imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
                imageAlt: 'Loading...',
                showCancelButton: false,
                showConfirmButton: false,
                allowOutsideClick: false
            });

            let templateId = $(this).attr('template-id');

            let subject = $('#emailSubject').val();
            let content = $('#emailContent').summernote('code');

            $.post( "<?=base_url('admin/email/editEmailTemplate')?>",
                {
                    id: templateId,
                    subject: subject,
                    content: content
                })
                .done(function( data ) {

                    data = JSON.parse(data);
                    if (data.status == 'success')
                    {
                        $('#editEmailModal').modal('hide');
                        swal.close();
                    }else{
                        Swal.fire(
                            'Unable to save',
                            'Database error',
                            'error'
                        );
                    }

                })
                .fail(function () {
                    Swal.fire(
                        'Unable to save',
                        'Network Error',
                        'error'
                    );
                });
        });
    });

    function editEmailTemplate(templateId) {
        Swal.fire({
            title: 'Please Wait',
            text: 'Loading the template content...',
            imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
            imageAlt: 'Loading...',
            showCancelButton: false,
            showConfirmButton: false,
            allowOutsideClick: false
        });

        $('#saveAndCloseTemplateBtn, #saveAndPreviewTemplateBtn').attr('template-id', templateId);

        $.get( "<?=base_url('admin/email/getTemplateById/')?>"+templateId, function(response) {
            response = JSON.parse(response);

            if (response.status == 'success')
            {
                let template = response.data;

                $('#emailSubject').val(template.subject);

                $('#emailContent').summernote({
                    height: 200,
                    toolbar:
                        [
                            ["history", ["undo", "redo"]],
                            ["style", ["style"]],
                            ["font", ["bold", "italic", "underline", "fontname", "strikethrough", "superscript", "subscript", "clear"]],
                            ['fontsize', ['fontsize']],
                            ["color", ["forecolor", "backcolor", "color"]],
                            ["paragraph", ["ul", "ol", "paragraph", "height"]],
                            ["table", ["table"]],
                            ["insert", ["link", "resizedDataImage", "picture", "video"]],
                            ["view", ["codeview"] ]
                        ],
                    fontSizes: ['8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '36', '48' , '64', '82', '150']
                });

                $('#emailContent').summernote('code', template.content);

                $('#editEmailModal').modal({backdrop: 'static', keyboard: false});
                swal.close();

            }else{
                toastr.error("Unable to load template content");
            }


        })
            .fail(function(response) {
                toastr.error("Unable to load template content");
            });
    }
</script>
