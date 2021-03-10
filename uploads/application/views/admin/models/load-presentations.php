<!-- Load Presentation Modal -->
<div class="modal fade" id="loadPresentationsModal" tabindex="-1" role="dialog" aria-labelledby="loadPresentationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loadPresentationsModalLabel">Load Presentations</h5>
            </div>
            <div class="modal-body">

                <form id="presentationsLoader">

                    <div class="input-group">

                        <div class="custom-file">
                            <input accept=".xlsx,.xlx,.csv" type="file" class="custom-file-input btn btn-outline-secondary" id="inputPresentationsFile" style="cursor: pointer;">
                            <label class="custom-file-label" for="inputPresentationsFile">Choose file</label>
                        </div>

                        <div class="input-group-append">
                            <button class="upload-presentations-btn btn btn-primary text-white" type="button">Upload</button>
                        </div>

                    </div>
                    <small style="color: red;">Only .xlsx, .xls or .csv file are allowed.</small>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary text-white" data-dismiss="modal"><i class="far fa-times-circle"></i> Close</button>
            </div>
        </div>
    </div>
</div>


<script>

    $(document).ready(function() {
        $('#inputPresentationsFile').on('change',function(){
            //get the file name
            let fileName = ($(this).val()).replace(/^.*[\\\/]/, '');
            //replace the "Choose a file" label
            $(this).next('.custom-file-label').html(fileName);
        });

        $('.upload-presentations-btn').on('click', function () {

            if ($('#inputPresentationsFile').get(0).files.length === 0) {
                toastr.error("Please select a file to load");
                return false;
            }

            let fileName = ($('#inputPresentationsFile').val()).replace(/^.*[\\\/]/, '');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to load \""+fileName+"\"",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, load it!'
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'Please Wait',
                        text: 'Doing the magic...',
                        imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
                        imageAlt: 'Loading...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });

                    let formData = new FormData();
                    formData.append('file', $('#inputPresentationsFile')[0].files[0]);
                    $.ajax({
                        url: "<?=base_url('admin/dashboard/loadPresentations')?>",
                        type: "POST",
                        data:  formData,
                        contentType: false,
                        cache: false,
                        processData:false,
                        beforeSend : function()
                        {

                        },
                        success: function(data)
                        {
                            data = JSON.parse(data);

                            if (data.status == 'success')
                            {
                                $("#inputPresentationsFile").val('').clone(true);
                                $('.custom-file-label').html('Please select the file');
                                $('#loadPresentationsModal').modal('hide');
                                loadPresentations();

                                Swal.fire(
                                    'Done!',
                                    data.msg+'<br>'+'<span><i class="fas fa-check-circle" style="color: green;"></i> '+data.createdPresentations+' entries created</span><br><span><i class="fas fa-exclamation-triangle" style="color: darkorange;"></i> '+data.duplicatedRows+' duplicate entries (ignored)</span>',
                                    'success'
                                ).then(()=>{

                                });
                            }else{
                                $("#inputPresentationsFile").val("");

                                Swal.fire(
                                    'Problem!',
                                    'Data load was aborted! <br> Reason: '+data.msg+'<br>No Entries created',
                                    'error'
                                ).then(()=>{

                                });
                            }
                        },
                        error: function(e)
                        {
                            Swal.fire(
                                'Problem!',
                                 e.responseText,
                                'error'
                            ).then(()=>{

                            });
                        }
                    });
                }
            })
        });

    });

</script>
