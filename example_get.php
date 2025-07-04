<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags for proper HTML5 document structure -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Manufacturer API Example - GET Requests Only</title>
    
    <!-- Bootstrap CSS for responsive design and styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS for additional styling -->
    <style>
        /* Custom styling for the search form */
        .search-container {
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
    </style>
</head>
<body>
    <!-- Main container with Bootstrap responsive classes -->
    <div class="container-fluid py-4">
        <!-- Page header with title and description -->
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fas fa-car"></i> Car Manufacturer API Demo
                </h1>
                <p class="text-center text-muted mb-4">
                    This page demonstrates how to use the Car Manufacturer API with GET requests only
                </p>
            </div>
        </div>

        <!-- Search form container with gradient background -->
        <div class="row">
            <div class="col-12">
                <div class="search-container">
                    <h3 class="mb-4"><i class="fas fa-search"></i> Search & Filter Manufacturers</h3>
                    
                    <!-- Form for search and filter inputs -->
                    <form id="searchForm">
                        <div class="row">
                            <!-- Text search field for manufacturer name -->
                            <div class="col-md-4 mb-3">
                                <label for="searchName" class="form-label">Search by Name:</label>
                                <input type="text" class="form-control" id="searchName"
                                       placeholder="e.g., Toyota, BMW, Tesla...">
                            </div>
                            
                            <!-- Dropdown for country selection -->
                            <div class="col-md-4 mb-3">
                                <label for="searchCountry" class="form-label">Country:</label>
                                <select class="form-select" id="searchCountry">
                                    <!-- Default option to show all countries -->
                                    <option value="">All Countries</option>
                                    <!-- Options will be populated dynamically from API -->
                                </select>
                            </div>
                            
                            <!-- Text search field for CEO name -->
                            <div class="col-md-4 mb-3">
                                <label for="searchCEO" class="form-label">Search by CEO:</label>
                                <input type="text" class="form-control" id="searchCEO"
                                       placeholder="e.g., Elon Musk, Mary Barra...">
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Number input for specific establishment year -->
                            <div class="col-md-3 mb-3">
                                <label for="exactYear" class="form-label">Exact Year:</label>
                                <input type="number" class="form-control" id="exactYear"
                                       placeholder="e.g., 1937" min="1800" max="2030">
                            </div>
                            
                            <!-- Number input for minimum year range -->
                            <div class="col-md-3 mb-3">
                                <label for="yearFrom" class="form-label">From Year:</label>
                                <input type="number" class="form-control" id="yearFrom"
                                       placeholder="e.g., 1900" min="1800" max="2030">
                            </div>
                            
                            <!-- Number input for maximum year range -->
                            <div class="col-md-3 mb-3">
                                <label for="yearTo" class="form-label">To Year:</label>
                                <input type="number" class="form-control" id="yearTo"
                                       placeholder="e.g., 2000" min="1800" max="2030">
                            </div>
                            
                            <!-- Dropdown for results per page -->
                            <div class="col-md-3 mb-3">
                                <label for="limitResults" class="form-label">Results per page:</label>
                                <select class="form-select" id="limitResults">
                                    <option value="10">10 results</option>
                                    <option value="20" selected>20 results</option>
                                    <option value="50">50 results</option>
                                    <option value="100">100 results</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Action buttons for search and reset -->
                            <div class="col-md-12 mb-3 d-flex justify-content-center">
                                <!-- Button to trigger search with current parameters -->
                                <button type="button" class="btn btn-light me-2" id="searchBtn">
                                    <i class="fas fa-search"></i> Search
                                </button>
                                <!-- Button to clear all search parameters -->
                                <button type="button" class="btn btn-outline-light" id="resetBtn">
                                    <i class="fas fa-refresh"></i> Reset
                                </button>
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
            <p class="mt-2">Fetching manufacturer data...</p>
        </div>

        <!-- Container for displaying API response information -->
        <div id="responseInfo" class="mb-3" style="display: none;">
            <div class="alert alert-info">
                <!-- Information about current search results and pagination -->
                <div class="row">
                    <div class="col-md-6">
                        <strong>Results:</strong> <span id="resultCount">0</span> manufacturers found
                    </div>
                    <div class="col-md-6">
                        <strong>Page:</strong> <span id="currentPage">1</span> of <span id="totalPages">1</span>
                    </div>
                </div>
                <!-- Display active filters for user reference -->
                <div class="mt-2">
                    <strong>Active Filters:</strong> <span id="activeFilters">None</span>
                </div>
            </div>
        </div>

        <!-- Pagination controls -->
        <div id="paginationTop" class="mb-3" style="display: none;">
            <nav aria-label="Manufacturer pagination">
                <ul class="pagination justify-content-center" id="paginationList">
                    <!-- Pagination buttons will be generated dynamically -->
                </ul>
            </nav>
        </div>

        <!-- Container where manufacturer results will be displayed -->
        <div id="results" class="row">
            <!-- Results will be populated here by JavaScript -->
        </div>

        <!-- Bottom pagination (duplicate for user convenience) -->
        <div id="paginationBottom" class="mt-4" style="display: none;">
            <nav aria-label="Manufacturer pagination">
                <ul class="pagination justify-content-center" id="paginationListBottom">
                    <!-- Same pagination as top, for easy navigation -->
                </ul>
            </nav>
        </div>

        <!-- Container for displaying API URL and JSON response for learning -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-code"></i> API Details (for learning)</h5>
                    </div>
                    <div class="card-body">
                        <!-- Display the actual API URL being called -->
                        <div class="mb-3">
                            <label class="form-label"><strong>API URL Called:</strong></label>
                            <div class="border p-2 bg-light">
                                <code id="apiUrl">No request made yet</code>
                            </div>
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
        // Global variable to store current page number for pagination
        let currentPage = 1;
        
        // Global variable to store current search parameters
        let currentParams = {};
        
        // API base URL - change this to match your server setup
        const API_BASE_URL = 'index.php';

        // jQuery document ready function - runs when page is fully loaded
        $(document).ready(function() {
            // Load initial data when page first loads
            loadManufacturers();
            
            // Populate the country dropdown with unique countries from API
            populateCountryDropdown();
            
            // Event handler for search button click
            $('#searchBtn').click(function() {
                // Reset to page 1 when new search is performed
                currentPage = 1;
                // Trigger the search with current form values
                loadManufacturers();
            });
            
            // Event handler for reset button click
            $('#resetBtn').click(function() {
                // Clear all form inputs to default values
                resetForm();
                // Reset pagination to first page
                currentPage = 1;
                // Load all manufacturers without filters
                loadManufacturers();
            });
            
            // Event handler for Enter key press in search fields
            $('#searchForm input').keypress(function(e) {
                // If Enter key (keyCode 13) is pressed
                if (e.which == 13) {
                    // Prevent form submission
                    e.preventDefault();
                    // Trigger search instead
                    $('#searchBtn').click();
                }
            });
        });

        /**
         * Main function to load manufacturers from API
         * Builds query parameters from form inputs and makes AJAX request
         */
        function loadManufacturers() {
            // Show loading spinner while request is being made
            $('#loadingSpinner').show();
            // Hide previous results
            $('#results').empty();
            $('#responseInfo').hide();
            $('#paginationTop, #paginationBottom').hide();
            
            // Build query parameters object from form inputs
            const params = buildQueryParams();
            // Store current parameters for pagination
            currentParams = params;
            
            // Construct the full API URL with query string
            const queryString = $.param(params);
            const fullUrl = API_BASE_URL + (queryString ? '?' + queryString : '');
            
            // Display the API URL for educational purposes
            $('#apiUrl').text(fullUrl);
            
            // Make AJAX GET request to the API
            $.ajax({
                url: fullUrl,  // Full URL with query parameters
                method: 'GET',  // HTTP method
                dataType: 'json',  // Expected response data type
                
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
                        // Update pagination info and controls
                        updatePaginationInfo(response.pagination);
                        // Show response information
                        showResponseInfo(response);
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
                    let errorMessage = 'Failed to load data: ' + error;
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
         * Function to build query parameters object from form inputs
         * Returns an object with all non-empty form values
         */
        function buildQueryParams() {
            const params = {};
            
            // Get manufacturer name search term
            const name = $('#searchName').val().trim();
            if (name) params.name = name;
            
            // Get selected country
            const country = $('#searchCountry').val();
            if (country) params.country = country;
            
            // Get CEO search term
            const ceo = $('#searchCEO').val().trim();
            if (ceo) params.ceo = ceo;
            
            // Get exact year filter
            const exactYear = $('#exactYear').val();
            if (exactYear) params.established_year = exactYear;
            
            // Get year range filters
            const yearFrom = $('#yearFrom').val();
            if (yearFrom) params.year_from = yearFrom;
            
            const yearTo = $('#yearTo').val();
            if (yearTo) params.year_to = yearTo;
            
            // Get pagination parameters
            params.page = currentPage;
            params.limit = $('#limitResults').val();
            
            return params;
        }

        /**
         * Function to display manufacturer results as Bootstrap cards
         * Takes array of manufacturer objects and creates HTML elements
         */
        function displayResults(manufacturers) {
            const resultsContainer = $('#results');
            
            // Check if any results were returned
            if (manufacturers.length === 0) {
                resultsContainer.html(`
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-search"></i>
                            <h5>No manufacturers found</h5>
                            <p>Try adjusting your search criteria or filters.</p>
                        </div>
                    </div>
                `);
                return;
            }
            
            // Clear previous results
            resultsContainer.empty();
            
            // Loop through each manufacturer and create a card
            manufacturers.forEach(function(manufacturer) {
                const card = `
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card manufacturer-card h-100">
                            <div class="card-header bg-primary text-white">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-industry"></i> ${escapeHtml(manufacturer.name)}
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong><i class="fas fa-globe"></i> Country:</strong><br>
                                    ${escapeHtml(manufacturer.country)}
                                </p>
                                <p class="card-text">
                                    <strong><i class="fas fa-calendar"></i> Established:</strong><br>
                                    ${manufacturer.established_year}
                                </p>
                                <p class="card-text">
                                    <strong><i class="fas fa-user-tie"></i> CEO:</strong><br>
                                    ${escapeHtml(manufacturer.ceo)}
                                </p>
                            </div>
                            <div class="card-footer text-muted">
                                <small>ID: ${manufacturer.id}</small>
                            </div>
                        </div>
                    </div>
                `;
                
                // Add the card to the results container
                resultsContainer.append(card);
            });
        }

        /**
         * Function to update pagination information and controls
         * Creates pagination buttons based on current page and total pages
         */
        function updatePaginationInfo(pagination) {
            // Update pagination information display
            $('#currentPage').text(pagination.page);
            $('#totalPages').text(pagination.total_pages);
            $('#resultCount').text(pagination.total);
            
            // Only show pagination if there are multiple pages
            if (pagination.total_pages > 1) {
                createPaginationButtons(pagination);
                $('#paginationTop, #paginationBottom').show();
            } else {
                $('#paginationTop, #paginationBottom').hide();
            }
        }

        /**
         * Function to create pagination button elements
         * Generates Previous, numbered pages, and Next buttons
         */
        function createPaginationButtons(pagination) {
            const paginationHtml = [];
            
            // Previous button
            if (pagination.page > 1) {
                paginationHtml.push(`
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="goToPage(${pagination.page - 1})">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    </li>
                `);
            } else {
                paginationHtml.push(`
                    <li class="page-item disabled">
                        <span class="page-link"><i class="fas fa-chevron-left"></i> Previous</span>
                    </li>
                `);
            }
            
            // Calculate which page numbers to show
            let startPage = Math.max(1, pagination.page - 2);
            let endPage = Math.min(pagination.total_pages, pagination.page + 2);
            
            // Show first page if not in range
            if (startPage > 1) {
                paginationHtml.push(`
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="goToPage(1)">1</a>
                    </li>
                `);
                if (startPage > 2) {
                    paginationHtml.push(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
            }
            
            // Show page numbers in range
            for (let i = startPage; i <= endPage; i++) {
                if (i === pagination.page) {
                    paginationHtml.push(`
                        <li class="page-item active">
                            <span class="page-link">${i}</span>
                        </li>
                    `);
                } else {
                    paginationHtml.push(`
                        <li class="page-item">
                            <a class="page-link" href="#" onclick="goToPage(${i})">${i}</a>
                        </li>
                    `);
                }
            }
            
            // Show last page if not in range
            if (endPage < pagination.total_pages) {
                if (endPage < pagination.total_pages - 1) {
                    paginationHtml.push(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
                paginationHtml.push(`
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="goToPage(${pagination.total_pages})">${pagination.total_pages}</a>
                    </li>
                `);
            }
            
            // Next button
            if (pagination.page < pagination.total_pages) {
                paginationHtml.push(`
                    <li class="page-item">
                        <a class="page-link" href="#" onclick="goToPage(${pagination.page + 1})">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                `);
            } else {
                paginationHtml.push(`
                    <li class="page-item disabled">
                        <span class="page-link">Next <i class="fas fa-chevron-right"></i></span>
                    </li>
                `);
            }
            
            // Update both pagination areas (top and bottom)
            $('#paginationList, #paginationListBottom').html(paginationHtml.join(''));
        }

        /**
         * Function to navigate to a specific page
         * Updates current page and reloads data
         */
        function goToPage(page) {
            currentPage = page;
            loadManufacturers();
            // Scroll to top for better user experience
            $('html, body').animate({scrollTop: 0}, 500);
        }

        /**
         * Function to display response information
         * Shows filters applied and result summary
         */
        function showResponseInfo(response) {
            $('#responseInfo').show();
            
            // Display active filters
            if (response.filters_applied && response.filters_applied.length > 0) {
                const filters = response.filters_applied
                    .filter(filter => filter !== 'page' && filter !== 'limit' && filter !== 'sort' && filter !== 'order')
                    .join(', ');
                $('#activeFilters').text(filters || 'None');
            } else {
                $('#activeFilters').text('None');
            }
        }

        /**
         * Function to populate country dropdown with unique countries
         * Makes API call to get all manufacturers and extract unique countries
         */
        function populateCountryDropdown() {
            $.ajax({
                url: API_BASE_URL + '?limit=100',  // Get more results to capture all countries
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Extract unique countries from the response
                        const countries = [...new Set(response.data.map(m => m.country))].sort();
                        
                        // Populate the dropdown
                        const countrySelect = $('#searchCountry');
                        countries.forEach(function(country) {
                            countrySelect.append(`<option value="${country}">${country}</option>`);
                        });
                    }
                },
                error: function() {
                    // If failed to load countries, add some common ones manually
                    const commonCountries = ['Japan', 'Germany', 'USA', 'France', 'Italy', 'UK', 'South Korea'];
                    const countrySelect = $('#searchCountry');
                    commonCountries.forEach(function(country) {
                        countrySelect.append(`<option value="${country}">${country}</option>`);
                    });
                }
            });
        }

        /**
         * Function to reset all form inputs to default values
         * Clears search fields and resets dropdowns
         */
        function resetForm() {
            $('#searchName').val('');
            $('#searchCountry').val('');
            $('#searchCEO').val('');
            $('#exactYear').val('');
            $('#yearFrom').val('');
            $('#yearTo').val('');
            $('#limitResults').val('20');
        }

        /**
         * Function to display error messages to user
         * Creates an alert box with error styling
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
         */
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>
