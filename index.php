<?php

include_once 'db_config.php';

// Set JSON content type
header('Content-Type: application/json');

// Enable CORS for API access
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

// API response helper function
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit();
}

// Error response helper
function sendError($message, $status = 400) {
    sendResponse(['error' => true, 'message' => $message], $status);
}

// GET /manufacturers - Get all manufacturers with optional filtering
if ($method === 'GET') {
    try {
        // Build the base query
        $query = "SELECT * FROM car_manufacturer";
        $conditions = [];
        $params = [];
        $types = "";

        // Handle query parameters for filtering
        if (isset($_GET['country'])) {
            $conditions[] = "country LIKE ?";
            $params[] = '%' . $_GET['country'] . '%';
            $types .= "s";
        }

        if (isset($_GET['name'])) {
            $conditions[] = "name LIKE ?";
            $params[] = '%' . $_GET['name'] . '%';
            $types .= "s";
        }

        if (isset($_GET['ceo'])) {
            $conditions[] = "ceo LIKE ?";
            $params[] = '%' . $_GET['ceo'] . '%';
            $types .= "s";
        }

        if (isset($_GET['established_year'])) {
            $conditions[] = "established_year = ?";
            $params[] = (int)$_GET['established_year'];
            $types .= "i";
        }

        if (isset($_GET['year_from'])) {
            $conditions[] = "established_year >= ?";
            $params[] = (int)$_GET['year_from'];
            $types .= "i";
        }

        if (isset($_GET['year_to'])) {
            $conditions[] = "established_year <= ?";
            $params[] = (int)$_GET['year_to'];
            $types .= "i";
        }

        // Add WHERE clause if there are conditions
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        // Handle sorting
        $allowed_sort_fields = ['name', 'country', 'established_year', 'ceo'];
        $sort_field = isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sort_fields) ? $_GET['sort'] : 'name';
        $sort_order = isset($_GET['order']) && strtoupper($_GET['order']) === 'DESC' ? 'DESC' : 'ASC';
        $query .= " ORDER BY $sort_field $sort_order";

        // Handle pagination
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
        $offset = ($page - 1) * $limit;
        
        $query .= " LIMIT $limit OFFSET $offset";

        // Prepare and execute query
        $stmt = mysqli_prepare($conn, $query);
        
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $manufacturers = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $manufacturers[] = [
                'id' => (int)$row['id'],
                'name' => $row['name'],
                'country' => $row['country'],
                'established_year' => (int)$row['established_year'],
                'ceo' => $row['ceo']
            ];
        }

        // Get total count for pagination info
        $count_query = "SELECT COUNT(*) as total FROM car_manufacturer";
        if (!empty($conditions)) {
            $count_query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $count_stmt = mysqli_prepare($conn, $count_query);
        if (!empty($params)) {
            mysqli_stmt_bind_param($count_stmt, $types, ...$params);
        }
        
        mysqli_stmt_execute($count_stmt);
        $count_result = mysqli_stmt_get_result($count_stmt);
        $total = mysqli_fetch_assoc($count_result)['total'];

        $response = [
            'success' => true,
            'data' => $manufacturers,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => (int)$total,
                'total_pages' => ceil($total / $limit)
            ],
            'filters_applied' => array_keys($_GET)
        ];

        sendResponse($response);

    } catch (Exception $e) {
        sendError("Database error: " . $e->getMessage(), 500);
    }
}

// Helper function to validate manufacturer data
function validateManufacturerData($data, $isUpdate = false) {
    $errors = [];
    
    if (!$isUpdate && (!isset($data['name']) || empty(trim($data['name'])))) {
        $errors[] = "Name is required";
    }
    
    if (!$isUpdate && (!isset($data['country']) || empty(trim($data['country'])))) {
        $errors[] = "Country is required";
    }
    
    if (!$isUpdate && (!isset($data['established_year']) || !is_numeric($data['established_year']))) {
        $errors[] = "Established year is required and must be a number";
    }
    
    if (!$isUpdate && (!isset($data['ceo']) || empty(trim($data['ceo'])))) {
        $errors[] = "CEO is required";
    }
    
    // Validate data types and ranges if provided
    if (isset($data['established_year'])) {
        $year = (int)$data['established_year'];
        if ($year < 1800 || $year > date('Y')) {
            $errors[] = "Established year must be between 1800 and " . date('Y');
        }
    }
    
    if (isset($data['name']) && strlen(trim($data['name'])) > 100) {
        $errors[] = "Name must be less than 100 characters";
    }
    
    if (isset($data['country']) && strlen(trim($data['country'])) > 50) {
        $errors[] = "Country must be less than 50 characters";
    }
    
    if (isset($data['ceo']) && strlen(trim($data['ceo'])) > 100) {
        $errors[] = "CEO name must be less than 100 characters";
    }
    
    return $errors;
}

// POST /manufacturers - Handle both "get by IDs" and "create new manufacturer"
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendError("Invalid JSON input");
    }
    
    // Check if this is a "get by IDs" request (for backward compatibility)
    if (isset($input['ids']) && is_array($input['ids'])) {
        // Original functionality - Get manufacturers by specific IDs
        try {
            $ids = array_map('intval', $input['ids']);
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            
            $query = "SELECT * FROM car_manufacturer WHERE id IN ($placeholders) ORDER BY name";
            $stmt = mysqli_prepare($conn, $query);
            
            $types = str_repeat('i', count($ids));
            mysqli_stmt_bind_param($stmt, $types, ...$ids);
            mysqli_stmt_execute($stmt);
            
            $result = mysqli_stmt_get_result($stmt);
            $manufacturers = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $manufacturers[] = [
                    'id' => (int)$row['id'],
                    'name' => $row['name'],
                    'country' => $row['country'],
                    'established_year' => (int)$row['established_year'],
                    'ceo' => $row['ceo']
                ];
            }

            sendResponse([
                'success' => true,
                'data' => $manufacturers,
                'requested_ids' => $ids,
                'found_count' => count($manufacturers)
            ]);

        } catch (Exception $e) {
            sendError("Database error: " . $e->getMessage(), 500);
        }
    } else {
        // New functionality - Create a new manufacturer
        $errors = validateManufacturerData($input);
        
        if (!empty($errors)) {
            sendError("Validation failed: " . implode(", ", $errors), 422);
        }
        
        try {
            // Check if manufacturer with same name already exists
            $check_query = "SELECT id FROM car_manufacturer WHERE name = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, "s", $input['name']);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if (mysqli_num_rows($check_result) > 0) {
                sendError("A manufacturer with this name already exists", 409);
            }
            
            // Insert new manufacturer
            $query = "INSERT INTO car_manufacturer (name, country, established_year, ceo) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "ssis", $input['name'], $input['country'], $input['established_year'], $input['ceo']);
            mysqli_stmt_execute($stmt);
            
            $new_id = mysqli_insert_id($conn);
            
            // Return the created manufacturer
            $get_query = "SELECT * FROM car_manufacturer WHERE id = ?";
            $get_stmt = mysqli_prepare($conn, $get_query);
            mysqli_stmt_bind_param($get_stmt, "i", $new_id);
            mysqli_stmt_execute($get_stmt);
            $get_result = mysqli_stmt_get_result($get_stmt);
            $new_manufacturer = mysqli_fetch_assoc($get_result);
            
            sendResponse([
                'success' => true,
                'message' => 'Manufacturer created successfully',
                'data' => [
                    'id' => (int)$new_manufacturer['id'],
                    'name' => $new_manufacturer['name'],
                    'country' => $new_manufacturer['country'],
                    'established_year' => (int)$new_manufacturer['established_year'],
                    'ceo' => $new_manufacturer['ceo']
                ]
            ], 201);
            
        } catch (Exception $e) {
            sendError("Database error: " . $e->getMessage(), 500);
        }
    }
}

// PUT /manufacturers/{id} - Update a manufacturer
if ($method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        sendError("Invalid JSON input");
    }
    
    // Get ID from query parameter or URL path
    $id = null;
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    } elseif (isset($input['id'])) {
        $id = (int)$input['id'];
    }
    
    if (!$id || $id <= 0) {
        sendError("Valid manufacturer ID is required for update");
    }
    
    $errors = validateManufacturerData($input, true);
    
    if (!empty($errors)) {
        sendError("Validation failed: " . implode(", ", $errors), 422);
    }
    
    try {
        // Check if manufacturer exists
        $check_query = "SELECT * FROM car_manufacturer WHERE id = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) === 0) {
            sendError("Manufacturer not found", 404);
        }
        
        $existing = mysqli_fetch_assoc($check_result);
        
        // Check if name is being changed and if new name already exists
        if (isset($input['name']) && $input['name'] !== $existing['name']) {
            $name_check_query = "SELECT id FROM car_manufacturer WHERE name = ? AND id != ?";
            $name_check_stmt = mysqli_prepare($conn, $name_check_query);
            mysqli_stmt_bind_param($name_check_stmt, "si", $input['name'], $id);
            mysqli_stmt_execute($name_check_stmt);
            $name_check_result = mysqli_stmt_get_result($name_check_stmt);
            
            if (mysqli_num_rows($name_check_result) > 0) {
                sendError("A manufacturer with this name already exists", 409);
            }
        }
        
        // Build update query dynamically
        $update_fields = [];
        $update_values = [];
        $types = "";
        
        if (isset($input['name'])) {
            $update_fields[] = "name = ?";
            $update_values[] = $input['name'];
            $types .= "s";
        }
        
        if (isset($input['country'])) {
            $update_fields[] = "country = ?";
            $update_values[] = $input['country'];
            $types .= "s";
        }
        
        if (isset($input['established_year'])) {
            $update_fields[] = "established_year = ?";
            $update_values[] = (int)$input['established_year'];
            $types .= "i";
        }
        
        if (isset($input['ceo'])) {
            $update_fields[] = "ceo = ?";
            $update_values[] = $input['ceo'];
            $types .= "s";
        }
        
        if (empty($update_fields)) {
            sendError("No valid fields provided for update");
        }
        
        // Add ID to the end for WHERE clause
        $update_values[] = $id;
        $types .= "i";
        
        $query = "UPDATE car_manufacturer SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$update_values);
        mysqli_stmt_execute($stmt);
        
        // Return the updated manufacturer
        $get_query = "SELECT * FROM car_manufacturer WHERE id = ?";
        $get_stmt = mysqli_prepare($conn, $get_query);
        mysqli_stmt_bind_param($get_stmt, "i", $id);
        mysqli_stmt_execute($get_stmt);
        $get_result = mysqli_stmt_get_result($get_stmt);
        $updated_manufacturer = mysqli_fetch_assoc($get_result);
        
        sendResponse([
            'success' => true,
            'message' => 'Manufacturer updated successfully',
            'data' => [
                'id' => (int)$updated_manufacturer['id'],
                'name' => $updated_manufacturer['name'],
                'country' => $updated_manufacturer['country'],
                'established_year' => (int)$updated_manufacturer['established_year'],
                'ceo' => $updated_manufacturer['ceo']
            ]
        ]);
        
    } catch (Exception $e) {
        sendError("Database error: " . $e->getMessage(), 500);
    }
}

// DELETE /manufacturers/{id} - Delete a manufacturer
if ($method === 'DELETE') {
    // Get ID from query parameter
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if (!$id || $id <= 0) {
        sendError("Valid manufacturer ID is required for deletion");
    }
    
    try {
        // Check if manufacturer exists
        $check_query = "SELECT * FROM car_manufacturer WHERE id = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "i", $id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) === 0) {
            sendError("Manufacturer not found", 404);
        }
        
        $manufacturer = mysqli_fetch_assoc($check_result);
        
        // Delete the manufacturer
        $delete_query = "DELETE FROM car_manufacturer WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $id);
        mysqli_stmt_execute($delete_stmt);
        
        sendResponse([
            'success' => true,
            'message' => 'Manufacturer deleted successfully',
            'deleted_data' => [
                'id' => (int)$manufacturer['id'],
                'name' => $manufacturer['name'],
                'country' => $manufacturer['country'],
                'established_year' => (int)$manufacturer['established_year'],
                'ceo' => $manufacturer['ceo']
            ]
        ]);
        
    } catch (Exception $e) {
        sendError("Database error: " . $e->getMessage(), 500);
    }
}

// If no valid endpoint is matched
sendError("Invalid endpoint or method", 404);
