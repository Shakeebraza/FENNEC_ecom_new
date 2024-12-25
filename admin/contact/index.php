<?php
require_once("../../global.php");
include_once('../header.php');
?>

<div class="page-container">

    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">

                        <h3 class="title-5 m-b-35">Contact Table</h3>
                        <div class="table-data__tool">
                            <div class="table-data__tool-right">
                            
                            
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive table-responsive-data2">
                        <table id="userTable" class="table table-data2">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once('../footer.php');
?>

<script>
$(document).ready(function() {
    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/contact/fetchcontact.php",
            "type": "POST"
        },
        "columns": [{
                "data": "checkbox"
            },
            {
                "data": "name"
            },
            {
                "data": "Code"
            },
            {
                "data": "Path"
            },
            {
                "data": "actions"
            }
        ],
    });

    // Delete language functionality
    $('#userTable').on('click', '.btn-danger', function() {
        var lanId = $(this).data('id');
        if (confirm('Are you sure you want to delete this language?')) {
            $.ajax({
                url: '<?php echo $urlval; ?>admin/ajax/contact/deletecon.php',
                type: 'POST',
                data: {
                    id: lanId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Contact deleted successfully!');
                        table.ajax.reload();
                    } else {
                        alert('Error deleting language: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while deleting the language.');
                }
            });
        }
    });
});
</script>

</body>
</html>
