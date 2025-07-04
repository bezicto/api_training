# Car Manufacturer API (index.php)

A comprehensive REST API for managing car manufacturer data with full CRUD (Create, Read, Update, Delete) operations.

## Features

- **GET**: Retrieve manufacturers with filtering, sorting, and pagination
- **POST**: Create new manufacturers or get manufacturers by specific IDs
- **PUT**: Update existing manufacturers
- **DELETE**: Delete manufacturers by ID
- Cross-Origin Resource Sharing (CORS) enabled
- Input validation and error handling
- JSON responses with consistent structure

## Database Schema

The API expects a `car_manufacturer` table with the following structure:

```sql
CREATE TABLE car_manufacturer (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(50) NOT NULL,
    established_year INT NOT NULL,
    ceo VARCHAR(100) NOT NULL
);
```
You may use **car_manufacturer.sql** to bulk insert data into table above. Use **db_config.php** to set your MariaDB database connection.

## API Endpoints

###
###
###
###
### 1. GET /manufacturers

Retrieve manufacturers with optional filtering, sorting, and pagination.

**Query Parameters:**
- `name`: Filter by manufacturer name (partial match)
- `country`: Filter by country (partial match)
- `ceo`: Filter by CEO name (partial match)
- `established_year`: Filter by exact establishment year
- `year_from`: Filter by minimum establishment year
- `year_to`: Filter by maximum establishment year
- `sort`: Sort field (name, country, established_year, ceo)
- `order`: Sort order (ASC or DESC)
- `page`: Page number (default: 1)
- `limit`: Results per page (default: 20, max: 100)

**Example:**
```
GET /index.php?country=Japan&sort=name&order=ASC&page=1&limit=10
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Toyota",
            "country": "Japan",
            "established_year": 1937,
            "ceo": "Akio Toyoda"
        }
    ],
    "pagination": {
        "page": 1,
        "limit": 10,
        "total": 25,
        "total_pages": 3
    },
    "filters_applied": ["country", "sort", "order", "page", "limit"]
}
```

###
###
###
###
### 2. POST /manufacturers

**Two functionalities:**

#### A. Get manufacturers by specific IDs (backward compatibility)

**Request Body:**
```json
{
    "ids": [1, 3, 5]
}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Toyota",
            "country": "Japan",
            "established_year": 1937,
            "ceo": "Akio Toyoda"
        }
    ],
    "requested_ids": [1, 3, 5],
    "found_count": 1
}
```

#### B. Create a new manufacturer

**Request Body:**
```json
{
    "name": "Tesla",
    "country": "USA",
    "established_year": 2003,
    "ceo": "Elon Musk"
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "message": "Manufacturer created successfully",
    "data": {
        "id": 15,
        "name": "Tesla",
        "country": "USA",
        "established_year": 2003,
        "ceo": "Elon Musk"
    }
}
```

###
###
###
###
### 3. PUT /manufacturers

Update an existing manufacturer. You can update any combination of fields.

**Query Parameter:**
- `id`: The ID of the manufacturer to update

**Request Body (partial update allowed):**
```json
{
    "name": "Tesla Inc.",
    "ceo": "Elon Musk"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Manufacturer updated successfully",
    "data": {
        "id": 15,
        "name": "Tesla Inc.",
        "country": "USA",
        "established_year": 2003,
        "ceo": "Elon Musk"
    }
}
```

###
###
###
###
### 4. DELETE /manufacturers

Delete a manufacturer by ID.

**Query Parameter:**
- `id`: The ID of the manufacturer to delete

**Example:**
```
DELETE /index.php?id=15
```

**Response:**
```json
{
    "success": true,
    "message": "Manufacturer deleted successfully",
    "deleted_data": {
        "id": 15,
        "name": "Tesla Inc.",
        "country": "USA",
        "established_year": 2003,
        "ceo": "Elon Musk"
    }
}
```

## Error Responses

All errors follow a consistent format:

```json
{
    "error": true,
    "message": "Error description"
}
```

**Common HTTP Status Codes:**
- `400`: Bad Request (invalid input)
- `404`: Not Found (manufacturer doesn't exist)
- `409`: Conflict (duplicate manufacturer name)
- `422`: Unprocessable Entity (validation failed)
- `500`: Internal Server Error

## Validation Rules

### Creating/Updating Manufacturers:

- **name**: Required (for creation), max 100 characters, must be unique
- **country**: Required (for creation), max 50 characters
- **established_year**: Required (for creation), must be between 1800 and current year
- **ceo**: Required (for creation), max 100 characters

## Example Files

1. **example_get.php**: Demonstrates GET requests with filtering and pagination
2. **example_post.php**: Demonstrates POST requests for getting manufacturers by ID
3. **example_crud.php**: Comprehensive testing interface for all CRUD operations

## Backward Compatibility

The enhanced API maintains full backward compatibility:
- All existing GET requests continue to work unchanged
- POST requests with `{"ids": [1, 2, 3]}` format continue to work for fetching specific manufacturers
- `example_get.php` and `example_post.php` remain fully functional

## Usage Examples

### JavaScript/jQuery

```javascript
// Create a new manufacturer
$.ajax({
    url: 'index.php',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({
        name: 'BYD',
        country: 'China',
        established_year: 1995,
        ceo: 'Wang Chuanfu'
    }),
    success: function(response) {
        console.log('Created:', response.data);
    }
});

// Update a manufacturer
$.ajax({
    url: 'index.php?id=5',
    method: 'PUT',
    contentType: 'application/json',
    data: JSON.stringify({
        ceo: 'New CEO Name'
    }),
    success: function(response) {
        console.log('Updated:', response.data);
    }
});

// Delete a manufacturer
$.ajax({
    url: 'index.php?id=5',
    method: 'DELETE',
    success: function(response) {
        console.log('Deleted:', response.deleted_data);
    }
});
```

### cURL Examples

```bash
# Create manufacturer
curl -X POST http://localhost/index.php \
  -H "Content-Type: application/json" \
  -d '{"name":"BYD","country":"China","established_year":1995,"ceo":"Wang Chuanfu"}'

# Update manufacturer
curl -X PUT "http://localhost/index.php?id=5" \
  -H "Content-Type: application/json" \
  -d '{"ceo":"New CEO Name"}'

# Delete manufacturer
curl -X DELETE "http://localhost/index.php?id=5"
```

## Security Features

- SQL injection prevention through prepared statements
- Input validation and sanitization
- HTML escaping in responses
- Duplicate name prevention
- Existence validation before updates/deletes

## Testing

Use `example_crud.php` for interactive testing of all API features through a web interface.
