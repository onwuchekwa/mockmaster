<?php 
    // register.php
    include('master/Examination.php');
    $exam = new Examination;
    $exam->candidateSessionPrivate();
    $pageName = "Candidate Profile";
    include('header.php');

    $exam->query = "SELECT * FROM candidate WHERE candidateId = '".$_SESSION['candidateId']."'";

    $result = $exam->query_result();
?>

<div class="row">
    <div class="col-md-3"></div>
        <div class="col-md-6 card-container">
            <div class="card card-margin">
                <div class="card-header"><h4>Candidate Profile</h4></div>
                <div class="card-body">
                    <span id="message"></span>
                    <form method="post" id="userProfileForm">
                        <?php foreach($result as $row) { ?>
                        <script>
                            $(document).ready(function() {
                                $('#candidateGender').val("<?php echo $row['candidateGender']; ?>");
                                $('#candidateCountry').val('Ghana');
                            });
                        </script>                        
                        <div class="form-group">
                            <label>First name</label>
                            <input type="text" id="candidateFirstname" name="candidateFirstname" class="form-control" maxlength="44" value="<?php echo $row['candidateFirstname']; ?>" />
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" id="candidateLastname" name="candidateLastname" class="form-control" maxlength="44" value="<?php echo $row['candidateLastname']; ?>" />
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select id="candidateGender" name="candidateGender" class="form-control">
                                <option value="">Select your gender</option>
                                <option value="f">Female</option>
                                <option value="m">Male</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" id="candidateAddress" name="candidateAddress" class="form-control" maxlength="99" value="<?php echo $row['candidateAddress']; ?>" />
                        </div>
                        <div class="form-group">
                            <label>Residential Country</label>
                            <input type="text" id="candidateCountry" name="candidateCountry" class="form-control" data-parsley-error-message="You must live in Ghana to edit your profile" />
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" id="candidatePhone" name="candidatePhone" class="form-control" maxlength="10" data-parsley-error-message="This values should be numbers only" value="<?php echo $row['candidatePhone']; ?>" />
                        </div>
                        <div class="form-group">
                        <input type="hidden" name="page" value="profile" />
                        <input type="hidden" name="action" value="profile" />
                        <input type="submit" name="userProfile" id="userProfile" value="Update my Profile" class="btn btn-info" />
                        </div>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    <div class="col-md-3"></div>
</div>

<script>
    $(document).ready(function() {
        $('#userProfileForm').parsley();

        $('#userProfileForm').on('submit', function(e) {
            e.preventDefault();
            $('#candidateFirstname').attr('required', 'required');
            $('#candidateFirstname').attr('data-parsley-pattern', '^[a-zA-Z- ]+$');
            $('#candidateLastname').attr('required', 'required');
            $('#candidateLastname').attr('data-parsley-pattern', '^[a-zA-Z- ]+$');
            $('#candidateGender').attr('required', 'required');
            $('#candidateAddress').attr('required', 'required');
            $('#candidateCountry').attr('required', 'required');
            $('#candidateCountry').attr('data-parsley-equalto', 'Ghana');
            $('#candidatePhone').attr('required', 'required');
            $('#candidatePhone').attr('data-parsley-pattern', '^[0-9]+$');

            if($('#userProfileForm').parsley().validate()) {
                $.ajax({
                    url: "user_ajax_action.php",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    beforeSend: function() {
                        $('#userProfile').attr('disabled', 'disabled');
                        $('#userProfile').val('please wait...')
                    },
                    success: function(data) {
                        if(data.success) {
                            location.reload(true);
                        } else {
                            $('#message').html('<span class="alert alert-success d-block">'+data.error+'</span>');
                        }
                        $('#userProfile').attr('disabled', false);
                        $('#userProfile').val('Update my Profile');
                    }
                });
            }
        });
    });
</script>

<?php include('master/footer.php') ?>