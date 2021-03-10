<title>Admin - COS ePoster Submission Site</title>

<main role="main" style="margin-top: 70px;margin-left: 20px;margin-right: 20px;">
    <div class="row">
        <div class="col-md-12">
            <h3>Email</h3>
        </div>
    </div>


    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h5>Templates</h5>
                <button class="create-template-btn btn btn-success float-right"><i class="fas fa-plus"></i> Create</button>
                <table id="emailTemplatesTable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Edit</th>
                        <th>Actions</th>
                    </tr>
                    </thead>

                    <tbody id="emailTemplatesTableBody">
                    <!-- Will be filled by JQuery AJAX -->

                    </tbody>

                </table>
            </div>
        </div>
    </div>

    </div>

    <hr>
</main>

<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css" crossorigin="anonymous" />


<script>
    $(document).ready(function() {
        loadEmailTemplates();

        $('#emailTemplatesTable').on('click', '.preview-email-template', function () {
            let templateId = $(this).attr('template-id');
            loadEmailTemplatePreview(templateId);
        });

        $('#emailTemplatesTable').on('click', '.edit-email-template', function () {
            let templateId = $(this).attr('template-id');
            editEmailTemplate(templateId);
        });

        $('#emailTemplatesTable').on('click', '.email-sendtocustom-btn', function () {
            let templateId = $(this).attr('template-id');
            let templateSubject = $(this).attr('template-subject');
            sendToCustomEmail(templateId, templateSubject);
        });

        $('#emailTemplatesTable').on('click', '.email-sendtoall-btn', function () {
            let templateId = $(this).attr('template-id');
            let templateSubject = $(this).attr('template-subject');
            sendEmailToAll(templateId, templateSubject);
        });

        $('#emailTemplatesTable').on('click', '.logs-email-template', function () {
            toastr.warning('Under development');
        });

        $('#emailTemplatesTable').on('click', '.remove-email-template', function () {
            let templateId = $(this).attr('template-id');
            let templateSubject = $(this).attr('template-subject');

            Swal.fire({
                title: 'Are you sure?',
                html: "You are about to disable <br> \""+templateSubject+"\" <br> You CAN revert this later!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, disable!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.get("<?=base_url('admin/email/disableTemplate/')?>"+templateId, function (response) {
                        response = JSON.parse(response);

                        if (response.status == 'success')
                        {
                            loadEmailTemplates();
                            Swal.fire(
                                'Done!',
                                'Template removed (possible to recover later)',
                                'success'
                            );
                        }else{
                            Swal.fire(
                                'Unable to remove template',
                                'Database Error',
                                'error'
                            );
                        }

                    }).fail(function () {
                        Swal.fire(
                            'Unable to remove template',
                            'Network Error',
                            'error'
                        );
                    });
                }
            });
        });

        $('.create-template-btn').on('click', function () {
            addEmailTemplate();
        });
    } );

    function loadEmailTemplates() {
        $.get( "<?=base_url('admin/email/getAllTemplates')?>", function(response) {
            response = JSON.parse(response);

            if ( $.fn.DataTable.isDataTable('#emailTemplatesTable') ) {
                $('#emailTemplatesTable').DataTable().destroy();
            }

            $('#emailTemplatesTableBody').html('');
            $.each(response.data, function(i, template) {

                let previewButton = '<button class="preview-email-template btn btn-info" template-id="'+template.id+'"><i class="fas fa-envelope-open-text"></i> Preview</button>';
                let editButton = '<button class="edit-email-template btn btn-info" template-id="'+template.id+'"><i class="fas fa-edit"></i> Edit</button>';
                let logsButton = '<button class="logs-email-template btn btn-info"><i class="fas fa-history"></i> Logs</button>';
                let removeButton = '<button class="remove-email-template btn btn-danger ml-3" template-id="'+template.id+'" template-subject="'+template.subject+'"><i class="fas fa-trash"></i> Remove</button>';

                let sendToAllButton = '<button class="email-sendtoall-btn btn btn-warning text-white" template-id="'+template.id+'" template-subject="'+template.subject+'"><i class="fas fa-mail-bulk"></i> Send to all</button>';
                let sendToCustomEmailButton = '<button class="email-sendtocustom-btn btn btn-warning text-white" template-id="'+template.id+'" template-subject="'+template.subject+'"><i class="fas fa-at"></i> Send to custom email</button>';

                $('#emailTemplatesTableBody').append('' +
                   '<tr>\n' +
                   '  <td>\n' +
                   '    '+template.id+'\n' +
                   '  </td>\n' +
                   '  <td>'+template.subject+'</td>\n' +
                   '  <td>\n' +
                   '    '+previewButton+'\n' +
                   '    '+editButton+'\n' +
                   '    '+logsButton+'\n' +
                   '    '+removeButton+'\n' +
                   '  </td>\n' +
                   '  <td>\n' +
                   '   '+sendToAllButton+'\n' +
                   '   '+sendToCustomEmailButton+'\n' +
                   '  </td>\n' +
                   '</tr>');
            });

            $('#emailTemplatesTable').DataTable({
                initComplete: function() {
                    $(this.api().table().container()).find('input').attr('autocomplete', 'off');
                    $(this.api().table().container()).find('input').attr('type', 'text');
                    $(this.api().table().container()).find('input').val('edit');
                }
            });

        })
            .fail(function(response) {
                $('#emailTemplatesTable').DataTable();
                toastr.error("Unable to load templates");
            });
    }

</script>

