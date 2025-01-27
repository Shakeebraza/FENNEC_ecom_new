<?php
require_once("../../global.php");
include_once('../header.php');

// Ensure role in [1,3,4]
$role = $_SESSION['role'] ?? 0;
if (!in_array($role, [1,3,4])) {
    header("Location: {$urlval}admin/logout.php");
    exit;
}
$isAdmin = in_array($role, [1,3]); // role=1 or 3 => can add/edit/delete

?>
<style>
/* ... your modal styling, etc. ... */
</style>

<div class="page-container">
    <div class="main-content">
        <div class="section__content section__content--p30">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-5">
                            <h3 class="title-5 mb-4">Locations Table</h3>
                            
                            <!-- Button Group for Adding Locations -->
                            <?php if ($isAdmin): ?>
                            <div class="btn-group" role="group" aria-label="Add Location Buttons">
                                <!-- Add Country -->
                                <button type="button" class="btn btn-success mr-3"
                                        data-toggle="modal" data-target="#addCountryModal">
                                    Add Country
                                </button>
                                <!-- Add City -->
                                <button type="button" class="btn btn-success mr-3"
                                        data-toggle="modal" data-target="#addCityModal">
                                    Add City
                                </button>
                                <!-- Add Area -->
                                <button type="button" class="btn btn-success mr-3"
                                        data-toggle="modal" data-target="#addAreaModal">
                                    Add Area
                                </button>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info">
                                You have read-only access to Locations.
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive table-responsive-data2">
                            <table id="userTable" class="table table-data2">
                                <thead>
                                    <tr>
                                        <th>Country</th>
                                        <th>City</th>
                                        <th>Area</th>
                                        <!-- Show Action column only if Admin or Super Admin -->
                                        <?php if ($isAdmin): ?>
                                            <th>Action</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody><!-- Filled by DataTables --></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// If $isAdmin => show popup code to add/edit 
// If moderator => you can show them, but with disabled fields
include_once('popup.php');
include_once('../footer.php');
?>

<script>
console.log("jQuery is available: ", typeof $ !== 'undefined');

// 1. Deleting location
function deleteLocation(areaId) {
    <?php if (!$isAdmin): ?>
        alert('You do not have permission to delete locations.');
        return;
    <?php endif; ?>

    if (confirm("Are you sure you want to delete this location?")) {
        $.ajax({
            url: '<?=$urlval?>admin/ajax/location/delete-location.php',
            type: 'POST',
            data: { area_id: areaId },
            success: function(response) {
                alert(response.message);
                $('#userTable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                alert("An error occurred: " + xhr.responseText);
            }
        });
    }
}

// 2. Editing location
function editLocation(areaId) {
    <?php if (!$isAdmin): ?>
        alert('You do not have permission to edit locations.');
        return;
    <?php endif; ?>

    // Then run your existing AJAX to fetch location details
    $.ajax({
        url: '<?=$urlval?>admin/ajax/location/get-location-details.php',
        type: 'POST',
        data: { area_id: areaId },
        success: function(response) {
            if (response && response.success) {
                const data = response.data;
                // Fill your edit form fields
                $('#countryNameedit').val(data.country_name);
                $('#countryid').val(data.country_id);
                $('#countryLongitude').val(data.country_longitude);
                $('#countryLatitude').val(data.country_latitude);

                $('#cityNameedit').val(data.city_name);
                $('#cityId').val(data.city_id);
                $('#cityLongitude').val(data.city_longitude);
                $('#cityLatitude').val(data.city_latitude);

                $('#areaNameedit').val(data.area_name);
                $('#aeraid').val(data.area_id);
                $('#areaLongitude').val(data.area_longitude);
                $('#areaLatitude').val(data.area_latitude);

                // Open modal
                $('#editPopup').modal('show');
            } else {
                alert("Failed to load location details: " + (response.message || "Unknown error"));
            }
        },
        error: function(xhr) {
            alert("An error occurred: " + xhr.responseText);
        }
    });
}

// 3. Saving location changes
function saveLocation() {
    <?php if (!$isAdmin): ?>
        alert('You do not have permission to save location changes.');
        return;
    <?php endif; ?>

    const locationData = {
        country_id:        $('#countryid').val(),
        country_name:      $('#countryNameedit').val(),
        country_longitude: $('#countryLongitude').val(),
        country_latitude:  $('#countryLatitude').val(),
        city_id:           $('#cityId').val(),
        city_name:         $('#cityNameedit').val(),
        city_longitude:    $('#cityLongitude').val(),
        city_latitude:     $('#cityLatitude').val(),
        area_id:           $('#aeraid').val(),
        area_name:         $('#areaNameedit').val(),
        area_longitude:    $('#areaLongitude').val(),
        area_latitude:     $('#areaLatitude').val()
    };

    $.ajax({
        url: '<?=$urlval?>admin/ajax/location/update-location.php',
        type: 'POST',
        data: locationData,
        success: function(response) {
            if (response.success) {
                alert('Location updated successfully!');
                $('#editPopup').modal('hide');
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('An error occurred: ' + xhr.responseText);
        }
    });
}

$(document).ready(function() {
    var table = $('#userTable').DataTable({
        "processing": true,
        "serverSide": true,
        "searching": false,
        "ajax": {
            "url": "<?php echo $urlval; ?>admin/ajax/location/fetchlocation.php",
            "type": "POST"
        },
        "columns": [
            { "data": "country" },
            { "data": "city" },
            { "data": "aera" },
            <?php if ($isAdmin): ?>
            { "data": "actions" }
            <?php endif; ?>
        ]
    });

    // If isAdmin => we load the "Add" modals. If not => do nothing or hide them.
    <?php if ($isAdmin): ?>
    // Fill <select> for city/country
    $('#addCityModal').on('show.bs.modal', function() {
        $.ajax({
            url: '<?=$urlval?>admin/ajax/location/get-country.php',
            type: 'GET',
            success: function(data) {
                var countries = JSON.parse(data);
                var countrySelect = $('#countrySelect');
                countrySelect.empty();
                countrySelect.append('<option value="">Select Country</option>');
                countries.forEach(function(country) {
                    countrySelect.append('<option value="' + country.id + '">' + country.name + '</option>');
                });
            },
            error: function() {
                alert('Error loading countries.');
            }
        });
    });

    $('#addAreaModal').on('show.bs.modal', function() {
        $.ajax({
            url: '<?=$urlval?>admin/ajax/location/get_cities.php',
            type: 'GET',
            success: function(data) {
                var cities = JSON.parse(data);
                var citySelect = $('#citySelect');
                citySelect.empty();
                citySelect.append('<option value="">Select City</option>');
                cities.forEach(function(city) {
                    citySelect.append('<option value="' + city.id + '">' + city.name + '</option>');
                });
            },
            error: function() {
                alert('Error loading cities.');
            }
        });
    });

    $('#addCountryForm').submit(function(e) {
        e.preventDefault();
        var countryData = {
            name: $('#countryName').val(),
            latitude: $('#latitude').val(),
            longitude: $('#longitude').val()
        };
        $.post('<?=$urlval?>admin/ajax/location/add_country.php', countryData, function(response) {
            if (response.success) {
                $('#addCountryModal').modal('hide');
                table.ajax.reload();
            }
        });
    });

    $('#addCityForm').submit(function(e) {
        e.preventDefault();
        var cityData = {
            name: $('#cityName').val(),
            latitude: $('#latitudeCity').val(),
            longitude: $('#longitudeCity').val(),
            country_id: $('#countrySelect').val()
        };
        $.post('<?=$urlval?>admin/ajax/location/add_city.php', cityData, function(response) {
            if (response.success) {
                $('#addCityModal').modal('hide');
                table.ajax.reload();
            }
        });
    });

    $('#addAreaForm').submit(function(e) {
        e.preventDefault();
        var areaData = {
            name: $('#areaName').val(),
            latitude: $('#latitudeArea').val(),
            longitude: $('#longitudeArea').val(),
            city_id: $('#citySelect').val()
        };
        $.post('<?=$urlval?>admin/ajax/location/add_area.php', areaData, function(response) {
            if (response.success) {
                $('#addAreaModal').modal('hide');
                table.ajax.reload();
            }
        });
    });
    <?php endif; // end isAdmin ?>

    $('.close').on('click', function() {
        $('#editPopup').modal('hide');
    });
});
</script>
</body>
</html>
