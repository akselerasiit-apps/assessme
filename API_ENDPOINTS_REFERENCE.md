# API Endpoints Reference - Assessment Interface

## 📡 **Base URL**
```
http://127.0.0.1:8001
```

---

## 🔐 **Authentication**
All endpoints require authenticated session (Laravel Sanctum/Session).

---

## 📋 **ASSESSMENT ENDPOINTS**

### **1. Get Activities by Level**
Retrieve activities for specific GAMO and level.

```http
GET /assessments/{assessment}/gamo/{gamo}/activities?level={level}
```

**Parameters:**
- `assessment` (path, required): Assessment ID
- `gamo` (path, required): GAMO Objective ID
- `level` (query, optional): Capability level (2-5), default: 2

**Response:**
```json
{
  "success": true,
  "activities": [
    {
      "id": 45,
      "code": "EDM01.02.01",
      "activity_name": "Monitor governance system performance | Pantau kinerja sistem tata kelola",
      "activity_text_en": "Monitor governance system...",
      "activity_text_id": "Pantau sistem tata kelola...",
      "level": 2,
      "document_requirement": "Performance dashboard",
      "evidence_count": 3,
      "answer": {
        "capability_rating": "L",
        "notes": "Good progress",
        "updated_at": "2025-01-21T10:30:00"
      }
    }
  ]
}
```

**Example:**
```javascript
$.get(`/assessments/8/gamo/1/activities?level=2`, function(response) {
    console.log(response.activities);
});
```

---

### **2. Get Activity Detail**
Get detailed information for a single activity.

```http
GET /assessments/{assessment}/gamo/{gamo}/activity/{activity_id}
```

**Parameters:**
- `assessment` (path, required): Assessment ID
- `gamo` (path, required): GAMO Objective ID
- `activity_id` (path, required): Activity ID

**Response:**
```json
{
  "success": true,
  "activity": {
    "id": 45,
    "code": "EDM01.02.01",
    "activity_name": "Monitor governance system performance | Pantau kinerja sistem tata kelola",
    "level": 2,
    "document_requirement": "Performance dashboard",
    "evidence_count": 3,
    "answer": {
      "capability_rating": "L",
      "notes": "Good progress"
    }
  }
}
```

---

### **3. Get PBC by Level**
Get Prepared By Client (PBC) document requirements with status.

```http
GET /assessments/{assessment}/gamo/{gamo}/pbc?level={level}
```

**Parameters:**
- `assessment` (path, required): Assessment ID
- `gamo` (path, required): GAMO Objective ID
- `level` (query, optional): Capability level (2-5), default: 2

**Response:**
```json
{
  "success": true,
  "level": 2,
  "activities": [
    {
      "id": 45,
      "code": "EDM01.02.01",
      "name": "Monitor governance system performance",
      "translated_text": "Pantau kinerja sistem tata kelola",
      "level": 2,
      "evidence_count": 3,
      "status": "complete",
      "rating": "L",
      "notes": "Good progress"
    }
  ]
}
```

**Status Values:**
- `complete`: Has evidence AND has rating
- `partial`: Has evidence but NO rating
- `rated`: Has rating but NO evidence
- `pending`: No evidence AND no rating

**Example:**
```javascript
$.get(`/assessments/8/gamo/1/pbc?level=3`, function(response) {
    response.activities.forEach(activity => {
        console.log(`${activity.code}: ${activity.status}`);
    });
});
```

---

### **4. Get Summary Statistics**
Get assessment summary with statistics per level.

```http
GET /assessments/{assessment}/gamo/{gamo}/summary
```

**Parameters:**
- `assessment` (path, required): Assessment ID
- `gamo` (path, required): GAMO Objective ID

**Response:**
```json
{
  "success": true,
  "summary": {
    "gamo_name": "EDM01 - Ensure Governance Framework Setting and Maintenance",
    "total_activities": 30,
    "assessed": 15,
    "not_assessed": 15,
    "compliance_percentage": "50.00"
  },
  "levels": {
    "2": {
      "total": 8,
      "assessed": 4,
      "not_assessed": 4,
      "na": 1,
      "n": 1,
      "p": 1,
      "l": 1,
      "f": 0,
      "compliance": "37.50"
    },
    "3": { /* ... */ },
    "4": { /* ... */ },
    "5": { /* ... */ }
  },
  "totals": {
    "total": 30,
    "assessed": 15,
    "not_assessed": 15,
    "na": 3,
    "n": 3,
    "p": 4,
    "l": 3,
    "f": 2,
    "compliance": "50.00"
  }
}
```

**Rating Counts:**
- `na`: Not Applicable
- `n`: Not Achieved
- `p`: Partially Achieved
- `l`: Largely Achieved
- `f`: Fully Achieved

**Example:**
```javascript
$.get(`/assessments/8/gamo/1/summary`, function(response) {
    $('#statTotalActivities').text(response.summary.total_activities);
    $('#statAssessed').text(response.summary.assessed);
    $('#statCompliance').text(response.summary.compliance_percentage + '%');
});
```

---

## 📁 **EVIDENCE ENDPOINTS**

### **5. Upload Evidence**
Upload evidence (file or URL) for an activity.

```http
POST /assessments/{assessment}/activity/{activity}/evidence
```

**Parameters:**
- `assessment` (path, required): Assessment ID
- `activity` (path, required): Activity ID

**Request Body (multipart/form-data):**
```javascript
// For File Upload
{
  "evidence_type": "file",
  "evidence_name": "Performance Dashboard Report",
  "evidence_description": "Monthly performance metrics",
  "evidence_file": [File object]
}

// For URL Upload
{
  "evidence_type": "url",
  "evidence_name": "External Documentation",
  "evidence_description": "Link to external resource",
  "evidence_url": "https://example.com/document.pdf"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Evidence uploaded successfully",
  "evidence": {
    "id": 123,
    "evidence_name": "Performance Dashboard Report",
    "evidence_description": "Monthly performance metrics",
    "evidence_type": "file",
    "evidence_file": "evidence_1705825200_report.pdf",
    "file_size": 245678,
    "uploaded_at": "2025-01-21T10:30:00"
  }
}
```

**Example (File):**
```javascript
let formData = new FormData();
formData.append('evidence_type', 'file');
formData.append('evidence_name', 'Dashboard Report');
formData.append('evidence_description', 'Monthly metrics');
formData.append('evidence_file', fileInput.files[0]);

$.ajax({
    url: `/assessments/8/activity/45/evidence`,
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
        console.log('Uploaded:', response.evidence);
    }
});
```

**Example (URL):**
```javascript
$.post(`/assessments/8/activity/45/evidence`, {
    evidence_type: 'url',
    evidence_name: 'External Doc',
    evidence_description: 'Link to resource',
    evidence_url: 'https://example.com/doc.pdf'
}, function(response) {
    console.log('Saved:', response.evidence);
});
```

---

### **6. Get Evidence List**
Get all evidence for a specific activity.

```http
GET /assessments/{assessment}/activity/{activity}/evidence
```

**Parameters:**
- `assessment` (path, required): Assessment ID
- `activity` (path, required): Activity ID

**Response:**
```json
{
  "success": true,
  "evidence": [
    {
      "id": 123,
      "evidence_name": "Performance Dashboard Report",
      "evidence_description": "Monthly performance metrics",
      "evidence_type": "file",
      "evidence_file": "evidence_1705825200_report.pdf",
      "evidence_url": null,
      "file_size": 245678,
      "activity": {
        "id": 45,
        "code": "EDM01.02.01",
        "activity_name": "Monitor governance system..."
      },
      "uploaded_by": {
        "id": 1,
        "name": "John Doe"
      },
      "uploaded_at": "2025-01-21T10:30:00"
    }
  ]
}
```

**Example:**
```javascript
$.get(`/assessments/8/activity/45/evidence`, function(response) {
    response.evidence.forEach(ev => {
        console.log(`${ev.evidence_name} (${ev.evidence_type})`);
    });
});
```

---

### **7. Download Evidence**
Download evidence file.

```http
GET /evidence/{evidence}/download
```

**Parameters:**
- `evidence` (path, required): Evidence ID

**Response:**
File download stream (application/octet-stream)

**Example:**
```html
<a href="/evidence/123/download" class="btn btn-primary" download>
    Download Evidence
</a>
```

**JavaScript Example:**
```javascript
window.location.href = `/evidence/${evidenceId}/download`;
```

---

## 📝 **ASSESSMENT ANSWER ENDPOINTS**

### **8. Save Assessment Answer**
Save or update capability rating for an activity.

```http
POST /assessments/{assessment}/answer
```

**Parameters:**
- `assessment` (path, required): Assessment ID

**Request Body (JSON):**
```json
{
  "activity_id": 45,
  "capability_rating": "L",
  "notes": "Good progress on this activity"
}
```

**Rating Values:**
- `F`: Fully Achieved (5.0)
- `L`: Largely Achieved (3.75)
- `P`: Partially Achieved (2.5)
- `N`: Not Achieved (1.25)
- `N/A`: Not Applicable (0)

**Response:**
```json
{
  "success": true,
  "message": "Assessment saved successfully",
  "answer": {
    "id": 234,
    "activity_id": 45,
    "capability_rating": "L",
    "notes": "Good progress on this activity",
    "updated_at": "2025-01-21T10:30:00"
  }
}
```

**Example:**
```javascript
$.post(`/assessments/8/answer`, {
    activity_id: 45,
    capability_rating: 'L',
    notes: 'Good progress'
}, function(response) {
    console.log('Rating saved:', response.answer);
});
```

---

## 📊 **REPORTING ENDPOINTS**

### **9. Get Average Score**
Get average capability scores per level.

```http
GET /assessments/{assessment}/average
```

**Parameters:**
- `assessment` (path, required): Assessment ID

**Response:**
```json
{
  "success": true,
  "averages": {
    "2": 3.5,
    "3": 3.2,
    "4": 2.8,
    "5": 2.1,
    "overall": 2.9
  }
}
```

---

### **10. Get Notes List**
Get all notes/comments for an assessment.

```http
GET /assessments/{assessment}/notes
```

**Parameters:**
- `assessment` (path, required): Assessment ID

**Response:**
```json
{
  "success": true,
  "notes": [
    {
      "activity_id": 45,
      "activity_code": "EDM01.02.01",
      "activity_name": "Monitor governance system...",
      "notes": "Good progress on this activity",
      "updated_at": "2025-01-21T10:30:00"
    }
  ]
}
```

---

### **11. Get History Log**
Get assessment activity history.

```http
GET /assessments/{assessment}/history
```

**Parameters:**
- `assessment` (path, required): Assessment ID

**Response:**
```json
{
  "success": true,
  "history": [
    {
      "action": "rating_updated",
      "activity_code": "EDM01.02.01",
      "old_rating": "P",
      "new_rating": "L",
      "user": "John Doe",
      "timestamp": "2025-01-21T10:30:00"
    },
    {
      "action": "evidence_uploaded",
      "activity_code": "EDM01.02.01",
      "evidence_name": "Dashboard Report",
      "user": "John Doe",
      "timestamp": "2025-01-21T09:15:00"
    }
  ]
}
```

---

## 🚨 **ERROR RESPONSES**

All endpoints return error responses in this format:

```json
{
  "success": false,
  "message": "Error message here",
  "errors": {
    "field_name": ["Validation error message"]
  }
}
```

**Common HTTP Status Codes:**
- `200 OK`: Successful request
- `201 Created`: Resource created successfully
- `400 Bad Request`: Invalid request data
- `401 Unauthorized`: Authentication required
- `403 Forbidden`: Insufficient permissions
- `404 Not Found`: Resource not found
- `422 Unprocessable Entity`: Validation errors
- `500 Internal Server Error`: Server error

**Example Error:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "evidence_name": ["The evidence name field is required."],
    "evidence_file": ["The evidence file field is required when evidence type is file."]
  }
}
```

---

## 🔧 **AJAX HELPER FUNCTIONS**

### **Global AJAX Setup**
```javascript
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
```

### **Error Handler**
```javascript
function handleAjaxError(xhr) {
    if (xhr.responseJSON) {
        let errors = xhr.responseJSON.errors || {};
        let messages = [];
        
        for (let field in errors) {
            messages.push(errors[field].join(', '));
        }
        
        toastr.error(messages.join('<br>'), 'Error');
    } else {
        toastr.error('An error occurred. Please try again.', 'Error');
    }
}
```

### **Complete AJAX Call Example**
```javascript
$.ajax({
    url: `/assessments/8/gamo/1/activities?level=2`,
    type: 'GET',
    dataType: 'json',
    beforeSend: function() {
        // Show loading indicator
        $('#loadingSpinner').show();
    },
    success: function(response) {
        if (response.success) {
            console.log('Activities:', response.activities);
            renderActivitiesTable(response.activities);
        } else {
            toastr.error(response.message, 'Error');
        }
    },
    error: function(xhr) {
        handleAjaxError(xhr);
    },
    complete: function() {
        // Hide loading indicator
        $('#loadingSpinner').hide();
    }
});
```

---

## 📚 **REQUEST EXAMPLES**

### **Complete Assessment Workflow**

```javascript
// 1. Load activities for Level 2
$.get('/assessments/8/gamo/1/activities?level=2', function(response) {
    renderActivitiesTable(response.activities);
});

// 2. Rate an activity
$.post('/assessments/8/answer', {
    activity_id: 45,
    capability_rating: 'L',
    notes: 'Good progress'
}, function(response) {
    toastr.success('Rating saved successfully');
});

// 3. Upload evidence
let formData = new FormData();
formData.append('evidence_type', 'file');
formData.append('evidence_name', 'Dashboard Report');
formData.append('evidence_description', 'Monthly metrics');
formData.append('evidence_file', fileInput.files[0]);

$.ajax({
    url: '/assessments/8/activity/45/evidence',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
        toastr.success('Evidence uploaded successfully');
    }
});

// 4. Check PBC status
$.get('/assessments/8/gamo/1/pbc?level=2', function(response) {
    response.activities.forEach(activity => {
        console.log(`${activity.code}: ${activity.status}`);
    });
});

// 5. View summary
$.get('/assessments/8/gamo/1/summary', function(response) {
    console.log('Compliance:', response.summary.compliance_percentage + '%');
    console.log('Assessed:', response.summary.assessed);
});
```

---

## 🧪 **TESTING WITH cURL**

### **Get Activities**
```bash
curl -X GET "http://127.0.0.1:8001/assessments/8/gamo/1/activities?level=2" \
  -H "Accept: application/json" \
  --cookie "laravel_session=YOUR_SESSION_COOKIE"
```

### **Upload Evidence (File)**
```bash
curl -X POST "http://127.0.0.1:8001/assessments/8/activity/45/evidence" \
  -H "Accept: application/json" \
  -F "evidence_type=file" \
  -F "evidence_name=Test Document" \
  -F "evidence_description=Test upload" \
  -F "evidence_file=@/path/to/file.pdf" \
  --cookie "laravel_session=YOUR_SESSION_COOKIE"
```

### **Save Rating**
```bash
curl -X POST "http://127.0.0.1:8001/assessments/8/answer" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"activity_id": 45, "capability_rating": "L", "notes": "Test note"}' \
  --cookie "laravel_session=YOUR_SESSION_COOKIE"
```

---

**Generated:** January 21, 2025  
**API Version:** 1.0  
**Laravel Version:** 10+
