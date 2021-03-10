<style>
    p{
        color:unset;
        font-weight: unset;
        margin-top:unset;
        margin-bottom:unset;
    }
</style>

<!-- Modal -->
<div class="modal fade" id="newEmailModal" tabindex="-1" role="dialog" aria-labelledby="newEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newEmailModalLabel">Add Email Template</h5>
                <button type="button" class="discardAndCloseNewTemplateBtn close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label for="emailSubject">Email Subject</label>
                    <input type="text" class="form-control" id="newEmailSubject" aria-describedby="newEmailSubjectHelp" placeholder="Enter email subject">
                    <small id="newEmailSubjectHelp" class="form-text text-muted">This will be the subject of the email you send.</small>
                </div>

                <div class="form-group">
                    <label for="newEmailContent">Email Content</label>
                    <textarea class="form-control" id="newEmailContent" rows="10" aria-describedby="newEmailContentHelp"></textarea>
                    <small id="newEmailContentHelp" class="form-text text-muted">This will be inserted into the body of the email you send.</small>
                </div>

            </div>
            <div class="modal-footer">
                <button id="saveAndPreviewNewTemplateBtn" class="btn btn-info" template-id=""><i class="fas fa-envelope-open-text"></i> Save & Preview</button>
                <button id="saveAndCloseNewTemplateBtn" class="btn btn-success" template-id=""><i class="fas fa-save"></i> Save & Close</button>
                <button type="button" class="discardAndCloseNewTemplateBtn btn btn-danger"><i class="fas fa-window-close"></i> Discard & Close</button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<script>
    $(document).ready(function() {
        $('.discardAndCloseNewTemplateBtn').on('click', function () {
            $('#newEmailModal').modal('hide');
            $('#newEmailContent').summernote('code', '');
            $('#newEmailContent').summernote('reset');
        });

        $('#saveAndPreviewNewTemplateBtn').on('click', function () {

            let subject = $('#newEmailSubject').val();
            let content = $('#newEmailContent').summernote('code');

            if (subject == '')
            {
                toastr.error("Please enter a subject");
                return false;
            }

            Swal.fire({
                title: 'Please Wait',
                text: 'Saving the template content...',
                imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
                imageAlt: 'Loading...',
                showCancelButton: false,
                showConfirmButton: false,
                allowOutsideClick: false
            });

            $.post( "<?=base_url('admin/email/addEmailTemplate')?>",
                {
                    subject: subject,
                    content: content
                })
                .done(function( data ) {

                    data = JSON.parse(data);
                    if (data.status == 'success')
                    {
                        loadEmailTemplates();
                        editEmailTemplate(data.templateId);
                        loadEmailTemplatePreview(data.templateId);
                        $('#newEmailModal').modal('hide');
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

        $('#saveAndCloseNewTemplateBtn').on('click', function () {

            let subject = $('#newEmailSubject').val();
            let content = $('#newEmailContent').summernote('code');

            if (subject == '')
            {
                toastr.error("Please enter a subject");
                return false;
            }

            Swal.fire({
                title: 'Please Wait',
                text: 'Saving the template content...',
                imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
                imageAlt: 'Loading...',
                showCancelButton: false,
                showConfirmButton: false,
                allowOutsideClick: false
            });

            $.post( "<?=base_url('admin/email/addEmailTemplate')?>",
                {
                    subject: subject,
                    content: content
                })
                .done(function( data ) {

                    data = JSON.parse(data);
                    if (data.status == 'success')
                    {
                        loadEmailTemplates();
                        $('#newEmailModal').modal('hide');
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

    function addEmailTemplate() {

        $('#newEmailContent').summernote({
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

        $('#newEmailModal').modal({backdrop: 'static', keyboard: false});
    }
</script>
