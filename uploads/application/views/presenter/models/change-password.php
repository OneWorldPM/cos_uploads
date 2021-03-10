<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text">New Password</span>
                    </div>
                    <input id="newPassword" type="password" class="form-control" aria-label="New Password">
                    <div class="input-group-append">
                        <span id="showHidePass" class="input-group-text" style="cursor: pointer;"><i id="passEyeIcon" class="fa fa-eye-slash" aria-hidden="true"></i></span>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="resetPassBtn" type="button" class="btn btn-primary text-white"><i class="fas fa-key"></i> Reset</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        $("#showHidePass").on('click', function(event) {

            if($('#newPassword').attr("type") == "text"){

                $('#newPassword').attr('type', 'password');
                $('#passEyeIcon').addClass( "fa-eye-slash" );
                $('#passEyeIcon').removeClass( "fa-eye" );

            }else if($('#newPassword').attr("type") == "password"){
                $('#newPassword').attr('type', 'text');
                $('#passEyeIcon').removeClass( "fa-eye-slash" );
                $('#passEyeIcon').addClass( "fa-eye" );
            }
        });


        $('#resetPassBtn').on('click', function () {
            let newPass = $('#newPassword').val();

            if (newPass == '')
            {
                toastr.warning('New password is required');
                return false;
            }

            Swal.fire({
                title: 'Please Wait',
                text: 'We are resetting your password',
                imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
                imageAlt: 'Loading...',
                showCancelButton: false,
                showConfirmButton: false,
                allowOutsideClick: false
            });

            $.post( "<?=base_url('login/resetPassword')?>",
                {
                    newPass: newPass
                })
                .done(function( data ) {

                    data = JSON.parse(data);
                    if (data.status == 'success')
                    {
                        Swal.fire({
                            title: 'Done!',
                            text: data.msg,
                            icon: 'success',
                            showCancelButton: false,
                            showConfirmButton: true,
                            allowOutsideClick: false
                        }).then(()=>{
                            window.location = '<?=base_url()?>';
                        });

                    }else{
                        Swal.fire(
                            'Error',
                            data.msg,
                            'error'
                        )
                    }

                })
                .fail(function () {
                    Swal.fire(
                        'Unable reset password',
                        'Network Error',
                        'error'
                    )
                });
        });

    });

</script>
