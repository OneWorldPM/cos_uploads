<style>
    .dropzone {
        border: 1px solid rgba(0, 0, 0, 0.07) !important;
    }
    .uploaded-file-names{
        font-size: 13px;
    }
</style>

<!-- Files Modal -->
<div class="modal fade" id="filesModal" tabindex="-1" role="dialog" aria-labelledby="filesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filesModalLabel">Files</h5>
            </div>
            <div class="modal-body">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Category: <span id="sessionName"></span></li>
                    </ol>
                </nav>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Presentation: <span id="presentationName"></span></li>
                    </ol>
                </nav>

                <section class="mt-3" id="existingFilesSection">
                    <div>
                        <h5 class="modal-title">Uploaded Files <button class="download-all-btn btn btn-sm btn-success text-white mb-2"><i class="fas fa-file-archive"></i> Download All</button></h5>
                    </div>
                    <div class="text-center" id="uploadedFiles" style="border: 1px solid #9f9f9f52;">
                        <img src="<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>">
                    </div>
                </section>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary text-white" data-dismiss="modal"><i class="far fa-times-circle"></i> Close</button>
            </div>
        </div>
    </div>
</div>


<script>

    $(document).ready(function() {

        $('.download-all-btn').on('click', function () {
            toastr.warning("Under development");
        });

    });


    function showFiles(user_id, presentation_id, session_name, presentation_name)
    {
        fillUploadedFiles(user_id, presentation_id);

        $('#sessionName').text(session_name);
        $('#presentationName').text(presentation_name);


        $('#filesModal').modal({ //Should not auto-close
            backdrop: 'static',
            keyboard: false
        });
    }

    function fillUploadedFiles(user_id, presentation_id) {
        $('#uploadedFiles').html('<img src="<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>">');

        $.get( "<?=base_url('admin/dashboard/getUploadedFiles/')?>"+user_id+"/"+presentation_id, function(response) {
            response = JSON.parse(response);

            if (response.status == 'success')
            {
                $('#uploadedFiles').html('');
                $('#uploadedFiles').append('<ul class="list-group">');
                $.each(response.files, function(i, file) {


                    $('#uploadedFiles').append('' +
                        '<li class="list-group-item">' +
                        '<a href="<?=base_url('admin/dashboard/openFile/')?>'+file.id+'" target="_blank"><button class="btn btn-sm btn-info mr-3"><i class="fas fa-save"></i> Download</button></a>' +
                        '<span class="uploaded-file-names badge badge-success"><i class="fas fa-clipboard-check"></i> '+file.name+' <span class="badge badge-info">'+Math.ceil(file.size/1000)+' kb</span></span>' +
                        '<!--<button class="delete-file-btn btn btn-sm btn-danger ml-3" presentation-id="'+file.presentation_id+'" user-id="'+file.presenter_id+'" file-id="'+file.id+'" file-name="'+file.name+'"><i class="fas fa-trash"></i> Delete</button>-->' +
                        '</li>');
                });
                $('#uploadedFiles').append('</ul>');

            }else{
                $('#uploadedFiles').html('');
                $('#uploadedFiles').append('<ul class="list-group">');
                $('#uploadedFiles').append('<li class="list-group-item">No files</li>');
                $('#uploadedFiles').append('</ul>');
            }

        }).fail(function() {
            toastr.error('Unable to load your uploaded files');
        })
    }

    $('#uploadedFiles').on('click', '.delete-file-btn', function () {

        let file_id = $(this).attr('file-id');
        let file_name = $(this).attr('file-name');
        let user_id = $(this).attr('user-id');
        let presentation_id = $(this).attr('presentation-id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You are you about to delete "+file_name,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {

                $.post( "<?=base_url('dashboard/deleteFile')?>",
                    {
                        user_id: user_id,
                        presentation_id: presentation_id,
                        file_id: file_id
                    })
                    .done(function( data ) {

                        data = JSON.parse(data);
                        if (data.status == 'success')
                        {
                            deletedFilesNo = deletedFilesNo+1;

                            Swal.fire(
                                'Deleted!',
                                'Your file has been deleted.',
                                'success'
                            );

                            fillUploadedFiles(user_id, presentation_id);

                        }else{
                            Swal.fire(
                                'Unable to delete '+file_name,
                                data.msg,
                                'error'
                            )
                        }

                    })
                    .fail(function () {
                        Swal.fire(
                            'Unable to delete '+file_name,
                            'Network Error',
                            'error'
                        )
                    });

            }
        })
    });

</script>
