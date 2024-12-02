<?php
require_once("../../global.php");
include_once('../header.php');
?>
<style>
    .modal-content {
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.modal-header {
    background-color: #f7f7f7;
    border-bottom: 1px solid #ddd;
}

.modal-body {
    padding: 20px;
}

.close {
    color: #555;
    font-size: 1.5rem;
    opacity: 0.7;
}

.close:hover {
    opacity: 1;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}
.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
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
                <div class="btn-group" role="group" aria-label="Add Location Buttons">
                    <!-- Add Country -->
                    <a type="button" class="btn btn-success mr-3" data-toggle="modal" data-target="#addCountryModal">Add Country</a>

                    <!-- Add City -->
                    <a type="button" class="btn btn-success mr-3" data-toggle="modal" data-target="#addCityModal">Add City</a>

                    <!-- Add Area -->
                    <a type="button" class="btn btn-success mr-3" data-toggle="modal" data-target="#addAreaModal">Add Area</a>
                </div>
            </div>
                    <div class="table-responsive table-responsive-data2">
                        <table id="userTable" class="table table-data2">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>City</th>
                                    <th>Aera</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
</div>



<?php
include_once('popup.php');
include_once('../footer.php');
?>
<script>
    console.log("jQuery is available: ", typeof $ !== 'undefined');
// Define the deleteLocation function globally
function deleteLocation(areaId) {
    if (confirm("Are you sure you want to delete this location?")) {
        $.ajax({
            url: '<?=$urlval?>admin/ajax/location/delete-location.php', 
            type: 'POST', 
            data: { area_id: areaId },
            success: function(response) {
                alert(response.message);
                // Reload DataTable to reflect changes
                $('#userTable').DataTable().ajax.reload();
            },
            error: function(xhr) {
                alert("An error occurred: " + xhr.responseText);
            }
        });
    }
}
function editLocation(areaId) {
        // Fetch data for the popup
        $.ajax({
            url: '<?=$urlval?>admin/ajax/location/get-location-details.php',
            type: 'POST',
            data: { area_id: areaId },
            success: function(response) {
             if (response && response.success) {
            
                    const data = response.data;

                    console.log(data.country_name);
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

function saveLocation() {
    const locationData = {
        country_id: $('#countryid').val(),
        country_name: $('#countryNameedit').val(),
        country_longitude: $('#countryLongitude').val(),
        country_latitude: $('#countryLatitude').val(),
        city_id: $('#cityId').val(),
        city_name: $('#cityNameedit').val(),
        city_longitude: $('#cityLongitude').val(),
        city_latitude: $('#cityLatitude').val(),
        area_id: $('#aeraid').val(),
        area_name: $('#areaNameedit').val(),
        area_longitude: $('#areaLongitude').val(),
        area_latitude: $('#areaLatitude').val()
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
            "type": "POST",
            "data": function(d) {}
        },
        "columns": [
            { "data": "country" },
            { "data": "city" },
            { "data": "aera" },
            { "data": "actions" }
        ],
    });

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
                var citySelect = $('#citySelect');
                citySelect.empty();  
                citySelect.append('<option value="">Select City</option>');  

                var cities = JSON.parse(data);  
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
                
            }
        });
    });
    $('.close').on('click', function() {
    $('#editPopup').modal('hide');
});

});

</script>

</body>

</html>