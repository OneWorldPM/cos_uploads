<title>Dashboard - COS ePoster Submission Site</title>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <a class="navbar-brand" href="#"><img src="<?=base_url('upload_system_files/vendor/images/ycl_Icon.png')?>" width="40px"> COS ePoster Submission Site</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
        <ul class="navbar-nav mr-auto">
<!--            <li class="nav-item active">-->
<!--                <a class="nav-link" href="--><?//=base_url('dashboard')?><!--">Dashboard</a>-->
<!--            </li>-->
        </ul>
        <ul class="navbar-nav dropdown-menu-right">
            <li class="nav-item">
                <span class="nav-link mr-3"><strong style="color: white !important;"><?=$_SESSION['name_prefix']?> <?=$_SESSION['fullname']?></strong></span>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="/support/submit_ticket/" target="_blank"><i class="far fa-life-ring"></i> Support</a>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <strong><i class="fas fa-tools"></i> Account</strong>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <span class="change-pass-btn dropdown-item" style="cursor: pointer;">
                        <strong><i class="fas fa-lock"></i> Change password</strong>
                    </span>
                    <a href="<?=base_url('logout')?>" class="dropdown-item">
                        <div class="dropdown-divider"></div>
                        <strong><i class="fas fa-sign-out-alt"></i> Logout</strong>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<main role="main" style="margin-top: 70px;margin-left: 20px;margin-right: 20px;">
    <div class="row">
        <div class="col-md-12">
            <h3><i class="fas fa-chalkboard-teacher"></i> Your Presentations</h3>
            <p>Your presentations are listed here; you can upload your files using the <!--<button id="example-upload-btn" class="btn btn-sm btn-info"><i class="fas fa-upload"></i> Upload</button>-->upload button.</p>
            <p>You may upload the following file types:  Microsoft PowerPoint (.ppt, .pptx), Video Files (.mp4, .mp3, .mv4, .mpg)</p>

            <div id="lastUpdatedAlert" class="alert alert-warning alert-dismissible fade show" role="alert" style="display:none;">
                Presentations list was last loaded on <strong><span id="lastUpdated"></span></strong> by admin
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

        </div>

        <div class="col-md-12">
            <table id="presentationTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                <tr>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Presentation Title</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody id="presentationTableBody">
                <!-- Will be filled by JQuery AJAX -->
                </tbody>

            </table>
        </div>

    </div>

    <hr>
</main>

<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.23/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css" crossorigin="anonymous" />


<script>
    $(document).ready(function() {

        loadPresentations();

        $('#example-upload-btn').on('click', function () {
            toastr.warning('You need to click one of the similar buttons listed below to upload files.');
        });

        $('.change-pass-btn').on('click', function () {
            $('#changePasswordModal').modal('show');
        });

        $('#presentationTable').on('click', '.upload-btn', function () {

            let user_id = $(this).attr('user-id');
            let presentation_id = $(this).attr('presentation-id');
            let presentation_name = $(this).attr('presentation-name');
            let session_name = $(this).attr('session-name');

            showUploader(user_id, presentation_id, session_name, presentation_name);
        });

        $('#presentationTable').on('click', '.details-btn', function () {

            let user_id = $(this).attr('user-id');
            let presentation_id = $(this).attr('presentation-id');
            let presentation_name = $(this).attr('presentation-name');
            let session_name = $(this).attr('session-name');

            showUploader(user_id, presentation_id, session_name, presentation_name);
        });


    } );



    function loadPresentations() {
        $.get( "<?=base_url('dashboard/getPresentationList')?>", function(response) {
            response = JSON.parse(response);

            if ( $.fn.DataTable.isDataTable('#presentationTable') ) {
                $('#presentationTable').DataTable().destroy();
            }

            $('#tblRemittanceList tbody').empty();

            $('#presentationTableBody').html('');
            $.each(response.data, function(i, presentation) {

                let statusBadge = (presentation.uploadStatus)?'<span class="badge badge-success"><i class="fas fa-check-circle"></i> '+presentation.uploadStatus+' File(s) uploaded</span>':'<span class="badge badge-warning"><i class="fas fa-exclamation-circle"></i> No Uploads</span>';
                let uploadBtn = '<button class="upload-btn btn btn-sm btn-info" session-name="'+presentation.session_name+'" presentation-name="'+presentation.name+'" user-id="<?=$this->session->userdata('user_id')?>" presentation-id="'+presentation.id+'"><i class="fas fa-upload"></i> Upload</button>';
                let detailsBtn = '<button class="details-btn btn btn-sm btn-primary text-white" session-name="'+presentation.session_name+'" presentation-name="'+presentation.name+'" user-id="<?=$this->session->userdata('user_id')?>" presentation-id="'+presentation.id+'"><i class="fas fa-info-circle"></i> Details</button>';

                $('#presentationTableBody').append('' +
                    '<tr>\n' +
                    '  <td>\n' +
                    '    '+statusBadge+'\n' +
                    '  </td>\n' +
                    '  <td>'+presentation.session_name+'</td>\n' +
                    '  <td>'+presentation.name+'</td>\n' +
                    '  <td>\n' +
                    '    '+uploadBtn+'\n' +
                    '    '+detailsBtn+'\n' +
                    '  </td>\n' +
                    '</tr>');
            });

            $('#presentationTable').DataTable({
                searching: false,
                initComplete: function() {
                    $(this.api().table().container()).find('input').attr('autocomplete', 'off');
                }
            });

            $('#lastUpdated').text(formatDateTime(response.data[0].created_on, false));
            $('#lastUpdatedAlert').show();
        })
            .fail(function(response) {
                $('#sessionsTable').DataTable();
                toastr.error("Unable to load your presentations data");
            });
    }

    function formatDateTime(datetimeStr, include_year = true) {
        let lastUpdatedDate = new Date(datetimeStr);
        let year = new Intl.DateTimeFormat('en', { year: 'numeric' }).format(lastUpdatedDate);
        let month = new Intl.DateTimeFormat('en', { month: 'long' }).format(lastUpdatedDate);
        let day = new Intl.DateTimeFormat('en', { day: '2-digit' }).format(lastUpdatedDate);
        let time = lastUpdatedDate.toLocaleTimeString('en-US', { hour: 'numeric', hour12: true, minute: 'numeric' });

        return ((include_year)?year+' ':'')+month+', '+day+'th '+time;
    }

</script>

