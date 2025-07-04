<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags for proper HTML5 document structure -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Manufacturer API Example - POST Requests</title>
    
    <!-- Bootstrap CSS for responsive design and styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS for additional styling -->
    <style>
        /* Custom styling for the input form */
        .input-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        /* Styling for result cards */
        .manufacturer-card {
            transition: transform 0.2s ease-in-out;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Hover effect for cards */
        .manufacturer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        
        /* Loading spinner styling */
        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        
        /* Error message styling */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
        
        /* Success message styling */
        .success-message {
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <!-- Main container with Bootstrap responsive classes -->
    <div class="container-fluid py-4">
        <!-- Page header with title and description -->
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fas fa-car"></i> Car Manufacturer API Demo - POST Method
                </h1>
                <p class="text-center text-muted mb-4">
                    This page demonstrates how to use the Car Manufacturer API POST endpoint to retrieve a specific manufacturer by its ID
                </p>
            </div>
        </div>

        <!-- Input form container with gradient background -->
        <div class="row">
            <div class="col-12">
                <div class="input-container">
                    <h3 class="mb-4"><i class="fas fa-search"></i> Get Specific Manufacturer by ID</h3>
                    
                    <!-- Instructions for the user -->
                    <div class="alert alert-light" role="alert">
                        <h6><i class="fas fa-info-circle"></i> How to use:</h6>
                        <ul class="mb-0">
                            <li>Enter a single manufacturer ID (number)</li>
                            <li>Click "Fetch Manufacturer" to send POST request to the API</li>
                            <li>The API will return data for the specified manufacturer</li>
                        </ul>
                    </div>
                    
                    <!-- Form for ID input -->
                    <form id="postForm">
                        <div class="row">
                            <!-- Text input for manufacturer ID -->
                            <div class="col-md-8 mb-3">
                                <label for="manufacturerId" class="form-label">Manufacturer ID:</label>
                                <input type="number" class="form-control" id="manufacturerId"
                                       placeholder="e.g., 5" min="1">
                                <small class="form-text text-light">
                                    Enter a manufacturer ID (positive number)
                                </small>
                            </div>
                            
                            <!-- Button to fetch manufacturer -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="button" class="btn btn-success" id="fetchBtn">
                                        <i class="fas fa-paper-plane"></i> Fetch Manufacturer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Loading spinner that appears during API calls -->
        <div class="loading" id="loadingSpinner">
            <div class="spinner-border text-primary" aria-label="Loading">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Sending POST request to API...</p>
        </div>

        <!-- Container for displaying API response information -->
        <div id="responseInfo" class="mb-3" style="display: none;">
            <div class="alert alert-info">
                <!-- Information about the POST request results -->
                <div class="row">
                    <div class="col-md-6">
                        <strong>Requested ID:</strong> <span id="requestedId">None</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Found:</strong> <span id="foundCount">0</span> manufacturer(s)
                    </div>
                </div>
            </div>
        </div>

        <!-- Container where manufacturer results will be displayed -->
        <div id="results" class="row">
            <!-- Results will be populated here by JavaScript -->
        </div>

        <!-- Container for displaying API URL and JSON data for learning -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-code"></i> API Details (for learning)</h5>
                    </div>
                    <div class="card-body">
                        <!-- Display the API endpoint being called -->
                        <div class="mb-3">
                            <label class="form-label"><strong>API Endpoint:</strong></label>
                            <div class="border p-2 bg-light">
                                <code id="apiEndpoint">No request made yet</code>
                            </div>
                        </div>
                        
                        <!-- Display the POST data being sent -->
                        <div class="mb-3">
                            <label class="form-label"><strong>POST Data Sent:</strong></label>
                            <textarea class="form-control" id="postData" rows="5" readonly
                                     placeholder="POST request data will appear here..."></textarea>
                        </div>
                        
                        <!-- Display the raw JSON response from API -->
                        <div class="mb-3">
                            <label class="form-label"><strong>JSON Response:</strong></label>
                            <textarea class="form-control" id="jsonResponse" rows="10" readonly
                                     placeholder="API response will appear here..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery library for DOM manipulation and AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JavaScript for interactive components -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // API base URL - change this to match your server setup
        const API_BASE_URL = 'index.php';

        // jQuery document ready function - runs when page is fully loaded
        $(document).ready(function() {
            // Event handler for "Fetch Manufacturer" button click
            $('#fetchBtn').click(function() {
                // Send POST request to API with the entered ID
                fetchManufacturerById();
            });
            
            // Event handler for Enter key press in ID input field
            $('#manufacturerId').keypress(function(e) {
                // If Enter key (keyCode 13) is pressed
                if (e.which == 13) {
                    // Prevent form submission
                    e.preventDefault();
                    // Trigger fetch function instead
                    $('#fetchBtn').click();
                }
            });
        });

        /**
         * Main function to fetch a manufacturer by ID using POST request
         * Sends the entered ID to the API and processes the response
         */
        function fetchManufacturerById() {
            // Get the ID value from the input field
            const manufacturerId = parseInt($('#manufacturerId').val());
            
            // Validate the ID input
            if (!manufacturerId || manufacturerId <= 0 || isNaN(manufacturerId)) {
                showError('Please enter a valid manufacturer ID (positive number)');
                return;
            }
            
            // Show loading spinner while request is being made
            $('#loadingSpinner').show();
            // Hide previous results
            $('#results').empty();
            $('#responseInfo').hide();
            
            // Prepare the POST data object with the single ID in an array
            const postData = {
                ids: [manufacturerId]  // Array containing the single ID
            };
            
            // Display the API endpoint for educational purposes
            $('#apiEndpoint').text('POST ' + API_BASE_URL);
            
            // Display the POST data being sent for educational purposes
            $('#postData').val(JSON.stringify(postData, null, 2));
            
            // Make AJAX POST request to the API
            $.ajax({
                url: API_BASE_URL,  // API endpoint URL
                method: 'POST',  // HTTP method
                dataType: 'json',  // Expected response data type
                contentType: 'application/json',  // Content type of the request
                data: JSON.stringify(postData),  // Convert data object to JSON string
                
                // Success callback - runs when API returns successful response
                success: function(response) {
                    // Hide loading spinner
                    $('#loadingSpinner').hide();
                    
                    // Display raw JSON response for learning
                    $('#jsonResponse').val(JSON.stringify(response, null, 2));
                    
                    // Check if API response indicates success
                    if (response.success) {
                        // Display the manufacturer data
                        displayResults(response.data);
                        // Show response information with the requested ID
                        showResponseInfo(response, manufacturerId);
                        // Show success message
                        if (response.data.length > 0) {
                            showSuccess(`Successfully retrieved manufacturer with ID ${manufacturerId}`);
                        } else {
                            showError(`No manufacturer found with ID ${manufacturerId}`);
                        }
                    } else {
                        // Show error message if API returns error
                        showError('API returned an error: ' + (response.message || 'Unknown error'));
                    }
                },
                
                // Error callback - runs when AJAX request fails
                error: function(xhr, status, error) {
                    // Hide loading spinner
                    $('#loadingSpinner').hide();
                    
                    // Try to parse error response if it's JSON
                    let errorMessage = 'Failed to send POST request: ' + error;
                    try {
                        const errorResponse = JSON.parse(xhr.responseText);
                        if (errorResponse.message) {
                            errorMessage = errorResponse.message;
                        }
                    } catch (e) {
                        // If response is not JSON, use default error message
                    }
                    
                    // Display error message to user
                    showError(errorMessage);
                    
                    // Show error details in JSON response area
                    $('#jsonResponse').val('Error: ' + errorMessage);
                }
            });
        }

        /**
         * Function to display manufacturer results as Bootstrap cards
         * Takes array of manufacturer objects and creates HTML elements
         * @param {Array} manufacturers - Array of manufacturer objects from API
         */
        function displayResults(manufacturers) {
            const resultsContainer = $('#results');
            
            // Check if any results were returned
            if (manufacturers.length === 0) {
                resultsContainer.html(`
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-search"></i>
                            <h5>No manufacturer found</h5>
                            <p>The requested manufacturer ID might not exist in the database.</p>
                        </div>
                    </div>
                `);
                return;
            }
            
            // Clear previous results
            resultsContainer.empty();
            
            // Since we're fetching only one manufacturer, get the first (and only) result
            const manufacturer = manufacturers[0];
            
            // Create a single card for the manufacturer result
            const card = `
                <div class="col-lg-8 col-xl-6 mx-auto mb-4">
                    <div class="card manufacturer-card h-100">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-industry"></i> ${escapeHtml(manufacturer.name)}
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="card-text">
                                        <strong><i class="fas fa-globe"></i> Country:</strong><br>
                                        <span class="text-primary">${escapeHtml(manufacturer.country)}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="card-text">
                                        <strong><i class="fas fa-calendar"></i> Established:</strong><br>
                                        <span class="text-primary">${manufacturer.established_year}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <p class="card-text">
                                        <strong><i class="fas fa-user-tie"></i> CEO:</strong><br>
                                        <span class="text-primary">${escapeHtml(manufacturer.ceo)}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center text-muted">
                            <strong>Manufacturer ID: ${manufacturer.id}</strong>
                        </div>
                    </div>
                </div>
            `;
            
            // Add the card to the results container
            resultsContainer.append(card);
        }

        /**
         * Function to display response information
         * Shows details about the POST request and results
         * @param {Object} response - The API response object
         * @param {number} requestedId - The ID that was requested
         */
        function showResponseInfo(response, requestedId) {
            $('#responseInfo').show();
            
            // Display requested ID and found count
            $('#requestedId').text(requestedId);
            $('#foundCount').text(response.found_count || response.data.length);
        }

        /**
         * Function to display success messages to user
         * Creates an alert box with success styling
         * @param {string} message - The success message to display
         */
        function showSuccess(message) {
            const successHtml = `
                <div class="col-12">
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <strong>Success:</strong> ${escapeHtml(message)}
                    </div>
                </div>
            `;
            $('#results').prepend(successHtml);
            
            // Auto-hide the success message after 5 seconds
            setTimeout(() => {
                $('.success-message').fadeOut();
            }, 5000);
        }

        /**
         * Function to display error messages to user
         * Creates an alert box with error styling
         * @param {string} message - The error message to display
         */
        function showError(message) {
            const errorHtml = `
                <div class="col-12">
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Error:</strong> ${escapeHtml(message)}
                    </div>
                </div>
            `;
            $('#results').html(errorHtml);
        }

        /**
         * Function to escape HTML characters to prevent XSS attacks
         * Replaces dangerous characters with HTML entities
         * @param {string} text - The text to escape
         * @returns {string} - The escaped HTML-safe text
         */
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
