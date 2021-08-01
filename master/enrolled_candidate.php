<?php
    // enrolled_candidate.php

    $pageName = 'Enrolled Candidate List';
    include('header.php');
?>

<br />
<nav aria-label="breadcrumb">
  	<ol class="breadcrumb">
    	<li class="breadcrumb-item"><a href="exam.php">Exam List</a></li>
    	<li class="breadcrumb-item active" aria-current="page">Enrolled Candidate List</li>
  	</ol>
</nav>
<div class="card card-margin">
<div class="card-header">
		<div class="row">
			<div class="col-md-9">
				<h3 class="panel-title">Enrolled Candidate List</h3>
			</div>
			<div class="col-md-3" align="right">
				
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="enroll_table" class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
                        <th>First Name</th>
                        <th>Last Name</th>
						<th>Gender</th>
						<th>Phone</th>
                        <th>Email Address</th>
                        <th>Email Status</th>
                        <th>Paid?</th>
						<th>Result</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

<script>
    $(document).ready(function(){
        var code = "<?php echo $_GET['code']; ?>";
        var dataTable = $('#enroll_table').DataTable({
            "processing" : true,
            "serverSide" : true,
            "order" : [],
            "ajax" : {
                url:"ajax_action.php",
                type:"POST",
                data:{action: 'fetch', page: 'exam_enroll', code: code},
            },
            "columnDefs" : [
                {
                    "targets" : [0],
                    "orderable" : false
                }
            ]
        });
    });
</script>

<?php
    include('footer.php');
?>