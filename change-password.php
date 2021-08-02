<?php 
    // register.php
    include('master/Examination.php');
    $exam = new Examination;
    $exam->candidateSessionPrivate();
    $pageName = "Change Password";
    include('header.php');
?>

<div class="row">
    <div class="col-md-3"></div>
        <div class="col-md-6 card-container">
            <div class="card card-margin">
                <div class="card-header"><h4>Change Password</h4></div>
                <div class="card-body">
                    <span id="message"></span>
                    <form method="post" id="changePasswordForm">                        
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" id="candidatePassword" name="candidatePassword" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" />
                        </div>                        
                        <div class="form-group">
                        <input type="hidden" name="page" value="changePassword" />
                        <input type="hidden" name="action" value="changePassword" />
                        <input type="submit" name="userChangePwd" id="userChangePwd" value="Change my Password" class="btn btn-info" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <div class="col-md-3"></div>
</div>

<script>
    $(document).ready(function() {        
        $('#changePasswordForm').parsley();

        $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault();
            $('#candidatePassword').attr('required', 'required');
            $('#candidatePassword').attr('data-parsley-minlength', '6');
            $('#confirmPassword').attr('required', 'required');
            $('#confirmPassword').attr('data-parsley-equalto', '#candidatePassword');
            $('#confirmPassword').attr('data-parsley-minlength', '6');

            if($('#changePasswordForm').parsley().validate()) {
                $.ajax({
                    url: "user_ajax_action.php",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#userChangePwd').attr('disabled', 'disabled');
                        $('#userChangePwd').val('please wait...')
                    },
                    success: function(data) {
                        if(data.success) {
                            alert(data.success);
						    location.reload(true);
                        }
                        $('#userChangePwd').attr('disabled', false);
                        $('#userChangePwd').val('Change my Password');
                    }
                });
            }
        });
    });
</script>

<?php include('master/footer.php') ?>