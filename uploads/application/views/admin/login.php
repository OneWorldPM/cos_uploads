<title>COS ePoster Submission Site</title>
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-md-6 order-md-2">
                <img src="<?=base_url('upload_system_files/vendor/')?>images/undraw_safe_bnk7.svg" alt="Image" class="img-fluid">
            </div>
            <div class="col-md-6 contents">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <h4>COS ePoster Submission Site</h4>
                            <h5><strong>Admin Panel</strong></h5>
                        </div>
                        <form action="#" method="post">
                            <div class="form-group first">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username">

                            </div>
                            <div class="form-group last mb-4">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password">

                            </div>

                            <div class="d-flex mb-5 align-items-center">
                                <!--                                <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>-->
                                <!--                                    <input type="checkbox" checked="checked"/>-->
                                <!--                                    <div class="control__indicator"></div>-->
                                <!--                                </label>-->
                                <span class="ml-auto"><a href="#" class="forgot-pass">Forgot Password</a></span>
                            </div>

                            <input type="button" value="Log In" class="login-btn btn text-white btn-block btn-primary" style="background-color: #487391;border-color: #368cc8;">
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    $(function () {

        $('.login-btn').on('click', function () {

            let email = $('#username').val();
            let password = $('#password').val();

            if (email == '' || password == '')
            {
                toastr.warning('Please enter your username and password.');
                return false;
            }

            Swal.fire({
                title: 'Please Wait',
                text: 'We are validating your credentials',
                imageUrl: '<?=base_url('upload_system_files/vendor/images/ycl_anime_500kb.gif')?>',
                imageAlt: 'Loading...',
                showCancelButton: false,
                showConfirmButton: false,
                allowOutsideClick: false
            });

            $.post( "<?=base_url('admin/login/verify')?>",
                {
                    email: email,
                    password: password
                })
                .done(function( data ) {

                    data = JSON.parse(data);
                    if (data.status == 'success')
                    {
                        Swal.fire({
                            title: 'Done!',
                            text: 'We are redirecting you',
                            icon: 'success',
                            showCancelButton: false,
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });

                        setTimeout(() => {
                            window.location = '<?=base_url('admin/dashboard')?>'
                        }, 1000);

                    }else{
                        Swal.fire(
                            'Unable To Login',
                            data.msg,
                            'error'
                        )
                    }

                })
                .fail(function () {
                    Swal.fire(
                        'Unable To Login',
                        'Network Error',
                        'error'
                    )
                });
        });

    });
</script>
