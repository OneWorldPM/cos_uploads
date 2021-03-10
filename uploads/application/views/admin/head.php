<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?=base_url('upload_system_files/vendor/')?>fonts/icomoon/style.css">

    <link rel="stylesheet" href="<?=base_url('upload_system_files/vendor/')?>css/owl.carousel.min.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?=base_url('upload_system_files/vendor/')?>css/bootstrap.min.css">

    <!-- Style -->
    <link rel="stylesheet" href="<?=base_url('upload_system_files/vendor/')?>css/style.css">

    <link rel="icon" href="<?=base_url('upload_system_files/vendor/')?>images/ycl_Icon.png">


    <!-- JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
    <script src="<?=base_url('upload_system_files/vendor/')?>js/popper.min.js"></script>
    <script src="<?=base_url('upload_system_files/vendor/')?>js/bootstrap.min.js"></script>
    <script src="<?=base_url('upload_system_files/vendor/')?>js/main.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw==" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g==" crossorigin="anonymous" />

    <script src="https://kit.fontawesome.com/fd91b3535c.js" crossorigin="anonymous"></script>
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <a class="navbar-brand" href="#"><img src="<?=base_url('upload_system_files/vendor/images/ycl_Icon.png')?>" width="40px"> COS FUS | Admin Panel</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <?php if($this->session->userdata('admin_login_status')): ?>
    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?=base_url('admin/dashboard')?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?=base_url('admin/presenters')?>"><i class="fas fa-chalkboard-teacher"></i> Presenters</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?=base_url('admin/categories')?>"><i class="far fa-clone"></i> Categories</a>
            </li>
            <li class="nav-item">
                <span class="nav-link" style="cursor: pointer;" data-toggle="modal" data-target="#loadPresentationsModal" data-backdrop="static" data-keyboard="false"><i class="fas fa-upload"></i> Load Data</span>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?=base_url('admin/load_history')?>"><i class="fas fa-tasks"></i> Load History</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?=base_url('admin/email')?>"><i class="fas fa-envelope"></i> Email</a>
            </li>
        </ul>
        <ul class="navbar-nav dropdown-menu-right">
            <li class="nav-item">
                <span class="nav-link text-white"><strong style="color: white !important;"><?=$_SESSION['fullname']?></strong></span>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <strong>Account</strong>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <span class="change-pass-btn dropdown-item" style="cursor: pointer;">
                        <strong>Change password</strong>
                    </span>
                    <a href="<?=base_url('admin/logout')?>" class="dropdown-item">
                        <div class="dropdown-divider"></div>
                        <strong>Logout</strong>
                    </a>
                </div>
            </li>
        </ul>
    </div>
    <?php endif; ?>
</nav>
