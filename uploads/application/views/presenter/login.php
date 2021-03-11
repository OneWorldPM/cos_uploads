<title>COS ePoster Submission Site</title>

<div class="content" style="padding: unset;">
    <div class="container">
        <div class="jumbotron" style="padding: unset;">
            <img class="" src="<?=base_url().'./upload_system_files/vendor/images/COS_banner_login.svg'?>" alt="banner" style="width:100%;">
        </div>

        <div class="row pt-5">
            <div class="col-md-6 order-md-2">
                <img src="<?=base_url('upload_system_files/vendor/')?>images/undraw_filing_system_b5d2_with_cos_banner.svg" alt="Folder image" class="img-fluid">
            </div>
            <div class="col-md-6 contents">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <h3>Sign in to the</h3>
                            <h4><strong>COS ePoster Submission Site</strong></h4>
                            <p class="mb-4">Username is your registered email and default password is your first name (password is case sensitive)</p>
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

            $.post( "<?=base_url('login/verify')?>",
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
                            window.location = '<?=base_url('dashboard')?>'
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
