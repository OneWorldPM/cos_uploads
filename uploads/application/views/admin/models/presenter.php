<!-- Load Presenter Modal -->
<style>
  .input-group-text{
      width:200px;
  }
</style>
<div class="modal fade" id="modalEditPresenter" tabindex="-1" role="dialog" aria-labelledby="loadPresentationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loadPresentationsModalLabel">Edit Presenter Details</h5>
            </div>
            <div class="modal-body">

                <form id="form-update-presenter" action="<?= base_url().'admin/presenters/update_presenter/'?>" method="POST">
                    <div class="form-group">
                        <div class="input-group-prepend" hidden>
                            <div class="input-group-text ">ID</div>
                            <input  type="text" name="presenter_id" class="form-control  modal-presenter-id" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Name Prefix</div>
                            <input  type="text" name="name_prefix" class="form-control  modal-name_prefix" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text ">First Name</div>
                            <input  type="text" name="first_name" class="form-control modal-first_name" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text ">Last Name</div>
                            <input  type="text" name="last_name" class="form-control modal-last_name" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text ">Email</div>
                            <input  type="text" name="email" class="form-control modal-email" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text ">Password</div>
                            <input  type="text" name="password" class="form-control modal-password" value="" >
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group-prepend">
                            <input type="submit" name="button-save" class="form-control btn btn-primary" id = "button-update" value="Save Changes">
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary text-white" data-dismiss="modal"><i class="far fa-times-circle"></i> Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function(){
        $('#form-update-presenter').submit(function(e){

            e.preventDefault();
            var formData = new FormData($(this)[0]);
            var form = $(this);
            var url = form.attr('action');

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to Save Changes",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.isConfirmed) {

            $.ajax({
                type: "POST",
                url: url,
                data:  formData,
                contentType: false,
                cache: false,
                processData:false,
                // serializes the form's elements.
                success: function(success)
                {
                    if (success)
                    {
                        Swal.fire(
                            'Done!',
                           '<span><i class="fas fa-check-circle" style="color: green;"></i></span><br>Success',
                            'success'
                        ).then(()=>{
                            loadPresenters();
                            $('#presenterTableBody').data.reload();
                            $('#modalEditPresenter').modal('hide');
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
            });
        });
    });

</script>
