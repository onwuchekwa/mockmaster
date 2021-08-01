<?php
     $pageName = 'Change Password';
     include('header.php');
?>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6 card-container">
        <span id="message"></span>
        <div class="card card-margin">
            <div class="card-header">Change Password</div>
            <div class="card-body">
                <form method="POST" id="changePasswordForm">                   
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="adminPassword" name="adminPassword" class="form-control" />
                    </div>
                    <div class="form-group">
                        <label>Confirm password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" />
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="page" value="register" />
                        <input type="hidden" name="action" value="changePassword" />
                        <input type="submit" name="adminChangePwd" id="adminChangePwd" value="Change my password" class="btn btn-info" />
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

<script>
    $(document).ready(function(){        
        $('#changePasswordForm').parsley();

        $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault();            
            $('#adminPassword').attr('required', 'required');  
            $('#adminPassword').attr('data-parsley-minlength', '6');                  
            $('#confirmPassword').attr('required', 'required');
            $('#confirmPassword').attr('data-parsley-equalto', '#adminPassword');
            $('#confirmPassword').attr('data-parsley-minlength', '6');

            if($('#changePasswordForm').parsley().validate()) {
                $.ajax({
                    url: "ajax_action.php",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#adminChangePwd').attr('disabled', 'disabled');
                        $('#adminChangePwd').val('Please wait...');
                    },
                    success: function(data) {
                        if(data.success) {
                            alert(data.success);
						    location.reload(true);
                        }
                        $('#adminChangePwd').attr('disabled', false);
                        $('#adminChangePwd').val('Change my password');
                    }
                });
            }
        });
    });
</script>
<?php
    include('footer.php');
?>