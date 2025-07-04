<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Manufacturer API - CRUD Operations</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .operation-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .result-container {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .success-message {
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fas fa-database"></i> Car Manufacturer API - CRUD Operations
                </h1>
                <p class="text-center text-muted mb-4">
                    Test Create, Read, Update, and Delete operations on the Car Manufacturer API
                </p>
            </div>
        </div>

        <!-- Create Operation -->
        <div class="row">
            <div class="col-12">
                <div class="operation-container">
                    <h3 class="mb-4"><i class="fas fa-plus"></i> Create New Manufacturer</h3>
                    
                    <form id="createForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="createName" class="form-label">Manufacturer Name *</label>
                                <input type="text" class="form-control" id="createName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="createCountry" class="form-label">Country *</label>
                                <input type="text" class="form-control" id="createCountry" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="createYear" class="form-label">Established Year *</label>
                                <input type="number" class="form-control" id="createYear" min="1800" max="2025" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="createCEO" class="form-label">CEO *</label>
                                <input type="text" class="form-control" id="createCEO" required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success" id="createBtn">
                            <i class="fas fa-plus"></i> Create Manufacturer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Operation -->
        <div class="row">
            <div class="col-12">
                <div class="operation-container">
                    <h3 class="mb-4"><i class="fas fa-edit"></i> Update Manufacturer</h3>
                    
                    <form id="updateForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="updateId" class="form-label">Manufacturer ID *</label>
                                <input type="number" class="form-control" id="updateId" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <button type="button" class="btn btn-info" id="loadBtn" style="margin-top: 32px;">
                                    <i class="fas fa-download"></i> Load Current Data
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="updateName" class="form-label">Manufacturer Name</label>
                                <input type="text" class="form-control" id="updateName">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="updateCountry" class="form-label">Country</label>
                                <input type="text" class="form-control" id="updateCountry">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="updateYear" class="form-label">Established Year</label>
                                <input type="number" class="form-control" id="updateYear" min="1800" max="2025">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="updateCEO" class="form-label">CEO</label>
                                <input type="text" class="form-control" id="updateCEO">
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning" id="updateBtn">
                            <i class="fas fa-save"></i> Update Manufacturer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Operation -->
        <div class="row">
            <div class="col-12">
                <div class="operation-container">
                    <h3 class="mb-4"><i class="fas fa-trash"></i> Delete Manufacturer</h3>
                    
                    <form id="deleteForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="deleteId" class="form-label">Manufacturer ID *</label>
                                <input type="number" class="form-control" id="deleteId" min="1" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <button type="button" class="btn btn-danger" id="deleteBtn" style="margin-top: 32px;">
                                    <i class="fas fa-trash"></i> Delete Manufacturer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results Container -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-terminal"></i> API Response</h5>
                    </div>
                    <div class="card-body">
                        <div id="results"></div>
                        <div class="result-container">
                            <label class="form-label"><strong>Last API Call:</strong></label>
                            <div class="border p-2 bg-light">
                                <code id="lastApiCall">No API call made yet</code>
                            </div>
                        </div>
                        <div class="result-container">
                            <label class="form-label"><strong>Raw JSON Response:</strong></label>
                            <textarea class="form-control" id="jsonResponse" rows="10" readonly
                                     placeholder="API response will appear here..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // CONFIGURATION
        // Define the base URL for all API calls - points to index.php which handles the API requests
        const API_BASE_URL = 'index.php';

        // DOCUMENT READY EVENT
        // jQuery function that waits for the DOM to be fully loaded before executing
        $(document).ready(function() {
            // EVENT HANDLERS - Attach click events to buttons using jQuery selectors
            
            // Bind click event to the Create button using its ID
            $('#createBtn').click(function() {
                createManufacturer(); // Call the function to create a new manufacturer
            });
            
            // Bind click event to the Load button for fetching existing manufacturer data
            $('#loadBtn').click(function() {
                loadManufacturerData(); // Call the function to load manufacturer data for editing
            });
            
            // Bind click event to the Update button
            $('#updateBtn').click(function() {
                updateManufacturer(); // Call the function to update manufacturer data
            });
            
            // Bind click event to the Delete button with confirmation dialog
            $('#deleteBtn').click(function() {
                // Show a confirmation dialog before proceeding with deletion
                // confirm() returns true if user clicks OK, false if Cancel
                if (confirm('Are you sure you want to delete this manufacturer? This action cannot be undone.')) {
                    deleteManufacturer(); // Only call delete function if user confirms
                }
            });
        });

        // FUNCTION: CREATE MANUFACTURER
        // This function handles creating a new manufacturer via POST request to the API
        function createManufacturer() {
            // Collect form data into a JavaScript object
            // .val() gets the value from input fields, .trim() removes leading/trailing whitespace
            const data = {
                name: $('#createName').val().trim(), // Get manufacturer name and remove extra spaces
                country: $('#createCountry').val().trim(), // Get country and remove extra spaces
                established_year: parseInt($('#createYear').val()), // Convert year string to integer
                ceo: $('#createCEO').val().trim() // Get CEO name and remove extra spaces
            };
            
            // CLIENT-SIDE VALIDATION
            // Check if all required fields have values (! means "not" or "false")
            if (!data.name || !data.country || !data.established_year || !data.ceo) {
                showError('All fields are required for creating a manufacturer'); // Display error message
                return; // Exit the function early if validation fails
            }
            
            // Update the display to show which API call is being made
            // .text() sets the text content of the element
            $('#lastApiCall').text('POST ' + API_BASE_URL + ' (Create)');
            
            // AJAX REQUEST - Asynchronous JavaScript and XML (actually JSON in this case)
            // This makes an HTTP request to the server without refreshing the page
            $.ajax({
                url: API_BASE_URL, // The endpoint URL to send the request to
                method: 'POST', // HTTP method - POST is used for creating new resources
                contentType: 'application/json', // Tell server we're sending JSON data
                data: JSON.stringify(data), // Convert JavaScript object to JSON string
                dataType: 'json', // Tell jQuery to expect JSON response from server
                
                // SUCCESS CALLBACK - This function runs if the request succeeds (HTTP 200)
                success: function(response) {
                    // Display the raw JSON response in the textarea for debugging
                    // JSON.stringify with null, 2 formats the JSON with 2-space indentation
                    $('#jsonResponse').val(JSON.stringify(response, null, 2));
                    
                    // Check if the API operation was successful based on response.success field
                    if (response.success) {
                        // Show success message including the new manufacturer's ID
                        showSuccess('Manufacturer created successfully! ID: ' + response.data.id);
                        // Clear all form fields after successful creation
                        // [0] gets the raw DOM element, .reset() is a native form method
                        $('#createForm')[0].reset();
                    } else {
                        // Show error message if API returned failure status
                        showError('Failed to create manufacturer: ' + response.message);
                    }
                },
                
                // ERROR CALLBACK - This function runs if the request fails (network error, HTTP error codes)
                error: function(xhr, status, error) {
                    handleAjaxError(xhr, error); // Call our custom error handling function
                }
            });
        }

        // FUNCTION: LOAD MANUFACTURER DATA
        // This function fetches existing manufacturer data for the update form
        function loadManufacturerData() {
            // Get the manufacturer ID from the input field and convert to integer
            const id = parseInt($('#updateId').val());
            
            // VALIDATION - Check if the ID is valid (greater than 0)
            // parseInt() can return NaN (Not a Number) if input is invalid
            if (!id || id <= 0) {
                showError('Please enter a valid manufacturer ID');
                return; // Exit function if validation fails
            }
            
            // Update the display to show current API call being made
            $('#lastApiCall').text('POST ' + API_BASE_URL + ' (Get by ID)');
            
            // AJAX REQUEST to fetch manufacturer data
            $.ajax({
                url: API_BASE_URL,
                method: 'POST', // Using POST method to send the ID in the request body
                contentType: 'application/json',
                // Send the ID in an array format as expected by the API
                // The API expects an 'ids' parameter with an array of IDs
                data: JSON.stringify({ids: [id]}),
                dataType: 'json',
                
                // SUCCESS CALLBACK - Process the fetched data
                success: function(response) {
                    // Display the raw JSON response for debugging
                    $('#jsonResponse').val(JSON.stringify(response, null, 2));
                    
                    // Check if data was successfully retrieved and contains results
                    // response.data should be an array with at least one manufacturer
                    if (response.success && response.data.length > 0) {
                        // Extract the first (and should be only) manufacturer from the array
                        const manufacturer = response.data[0];
                        
                        // Populate the update form fields with the existing data
                        // This allows users to see current values and modify only what they want
                        $('#updateName').val(manufacturer.name);
                        $('#updateCountry').val(manufacturer.country);
                        $('#updateYear').val(manufacturer.established_year);
                        $('#updateCEO').val(manufacturer.ceo);
                        
                        showSuccess('Manufacturer data loaded successfully');
                    } else {
                        // Show error if manufacturer was not found
                        showError('Manufacturer not found');
                        clearUpdateForm();
                    }
                },
                
                // ERROR CALLBACK
                error: function(xhr, status, error) {
                    handleAjaxError(xhr, error);
                    clearUpdateForm();
                }
            });
        }

        // FUNCTION: UPDATE MANUFACTURER
        // This function handles updating existing manufacturer data via PUT request
        function updateManufacturer() {
            // Get the manufacturer ID to update
            const id = parseInt($('#updateId').val());
            
            // VALIDATION - Ensure we have a valid ID
            if (!id || id <= 0) {
                showError('Please enter a valid manufacturer ID');
                return;
            }
            
            // BUILD DATA OBJECT - Only include fields that have values
            // This allows for partial updates (you don't need to update all fields)
            const data = {};
            
            // Check each field and only add to data object if it has a value
            // This conditional approach enables partial updates
            if ($('#updateName').val().trim()) data.name = $('#updateName').val().trim();
            if ($('#updateCountry').val().trim()) data.country = $('#updateCountry').val().trim();
            if ($('#updateYear').val()) data.established_year = parseInt($('#updateYear').val());
            if ($('#updateCEO').val().trim()) data.ceo = $('#updateCEO').val().trim();
            
            // VALIDATION - Ensure at least one field is provided for update
            // Object.keys() returns an array of the object's property names
            if (Object.keys(data).length === 0) {
                showError('Please provide at least one field to update');
                return; // Exit if no fields to update
            }
            
            // Update display to show current API call with the ID parameter
            $('#lastApiCall').text('PUT ' + API_BASE_URL + '?id=' + id);
            
            // AJAX REQUEST for updating data
            $.ajax({
                url: API_BASE_URL + '?id=' + id, // Include ID as query parameter in URL
                method: 'PUT', // HTTP PUT method is standard for updating existing resources
                contentType: 'application/json',
                data: JSON.stringify(data), // Send only the fields that need to be updated
                dataType: 'json',
                
                // SUCCESS CALLBACK
                success: function(response) {
                    // Display raw JSON response
                    $('#jsonResponse').val(JSON.stringify(response, null, 2));
                    
                    // Check if update was successful
                    if (response.success) {
                        showSuccess('Manufacturer updated successfully!');
                        // Optionally clear the form fields after successful update
                        clearUpdateForm();
                    } else {
                        // Show error message and clear form fields when update fails
                        showError('Failed to update manufacturer: ' + response.message);
                        clearUpdateForm(); // Clear form when no result/failure occurs
                    }
                },
                
                // ERROR CALLBACK
                error: function(xhr, status, error) {
                    handleAjaxError(xhr, error);
                    // Clear form fields when AJAX request fails entirely
                    clearUpdateForm();
                }
            });
        }

        // UTILITY FUNCTION: CLEAR UPDATE FORM
        // This function clears all the update form fields (except the ID field)
        function clearUpdateForm() {
            // Clear all update form fields except the ID field
            // We keep the ID so user doesn't have to re-enter it
            $('#updateName').val('');        // Clear manufacturer name field
            $('#updateCountry').val('');     // Clear country field
            $('#updateYear').val('');        // Clear established year field
            $('#updateCEO').val('');         // Clear CEO field
            
            // Note: We don't clear $('#updateId') so the user can try again with the same ID
        }

        // FUNCTION: DELETE MANUFACTURER
        // This function handles deletion of a manufacturer via DELETE request
        function deleteManufacturer() {
            // Get the manufacturer ID to delete
            const id = parseInt($('#deleteId').val());
            
            // VALIDATION - Check if ID is valid
            if (!id || id <= 0) {
                showError('Please enter a valid manufacturer ID');
                return;
            }
            
            // Update display to show current API call
            $('#lastApiCall').text('DELETE ' + API_BASE_URL + '?id=' + id);
            
            // AJAX REQUEST for deletion
            $.ajax({
                url: API_BASE_URL + '?id=' + id, // Include ID as query parameter
                method: 'DELETE', // HTTP DELETE method for removing resources
                dataType: 'json', // Expect JSON response (no request body needed for DELETE)
                
                // SUCCESS CALLBACK
                success: function(response) {
                    // Display raw JSON response
                    $('#jsonResponse').val(JSON.stringify(response, null, 2));
                    
                    // Check if deletion was successful
                    if (response.success) {
                        showSuccess('Manufacturer deleted successfully!');
                        // Clear the delete form after successful deletion
                        $('#deleteForm')[0].reset();
                    } else {
                        showError('Failed to delete manufacturer: ' + response.message);
                    }
                },
                
                // ERROR CALLBACK
                error: function(xhr, status, error) {
                    handleAjaxError(xhr, error);
                }
            });
        }

        // UTILITY FUNCTION: SHOW SUCCESS MESSAGE
        // Creates and displays a success message in the results container
        function showSuccess(message) {
            // Create HTML string using template literals (backticks allow multi-line strings)
            // Template literals also allow ${} for variable interpolation
            const successHtml = `
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <strong>Success:</strong> ${escapeHtml(message)}
                </div>
            `;
            // Insert the success message HTML into the results container
            // .html() replaces the entire content of the element
            $('#results').html(successHtml);
        }

        // UTILITY FUNCTION: SHOW ERROR MESSAGE
        // Creates and displays an error message in the results container
        function showError(message) {
            // Create HTML string with error styling
            const errorHtml = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Error:</strong> ${escapeHtml(message)}
                </div>
            `;
            // Insert the error message HTML into the results container
            $('#results').html(errorHtml);
        }

        // UTILITY FUNCTION: HANDLE AJAX ERRORS
        // This function processes and displays errors that occur during AJAX requests
        function handleAjaxError(xhr, error) {
            // Default error message in case we can't get specific details
            let errorMessage = 'Request failed: ' + error;
            
            try {
                // Try to parse the server response as JSON to get more specific error info
                // xhr.responseText contains the raw response from the server
                const errorResponse = JSON.parse(xhr.responseText);
                
                // If the server sent a specific error message, use that instead
                if (errorResponse.message) {
                    errorMessage = errorResponse.message;
                }
            } catch (e) {
                // If parsing fails (response isn't valid JSON), use the default error message
                // The catch block prevents JavaScript errors if the response isn't valid JSON
            }
            
            // Display error information in the JSON response textarea for debugging
            $('#jsonResponse').val('Error: ' + errorMessage);
            // Show the error message to the user using our error display function
            showError(errorMessage);
        }

        // UTILITY FUNCTION: ESCAPE HTML
        // This function prevents XSS (Cross-Site Scripting) attacks by escaping HTML characters
        function escapeHtml(text) {
            // Create a temporary div element
            const div = document.createElement('div');
            // Set the text content (this automatically escapes HTML characters)
            div.textContent = text;
            // Return the escaped HTML - innerHTML will contain the escaped version
            return div.innerHTML;
        }
    </script>
</body>
</html>
