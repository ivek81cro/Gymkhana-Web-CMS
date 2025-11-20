# AJAX Endpoint Documentation

## `admin/ajax-get-gallery-images.php`

### Purpose
Fetches all images from a specific gallery for the image picker modal in article editor.

### Endpoint
```
POST /admin/ajax-get-gallery-images.php
```

### Authentication
Requires active admin session (`require_admin()`)

### Request Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `gallery_id` | integer | Yes | ID of the gallery to fetch images from |

### Request Example

**JavaScript (Fetch API):**
```javascript
fetch('ajax-get-gallery-images.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'gallery_id=5'
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log(data.images);
    } else {
        console.error(data.error);
    }
});
```

**jQuery:**
```javascript
$.post('ajax-get-gallery-images.php', {
    gallery_id: 5
}, function(data) {
    if (data.success) {
        console.log(data.images);
    }
}, 'json');
```

### Response Format

#### Success Response
```json
{
    "success": true,
    "images": [
        {
            "id": 123,
            "filename": "IMG_1234.jpg",
            "image_path": "uploads/gallery/IMG_1234.jpg",
            "display_name": "IMG_1234.jpg"
        },
        {
            "id": 124,
            "filename": "DSC_5678.jpg",
            "image_path": "uploads/gallery/DSC_5678.jpg",
            "display_name": "DSC_5678.jpg"
        }
    ]
}
```

#### Error Response - Missing Gallery ID
```json
{
    "success": false,
    "error": "Gallery ID is required"
}
```

#### Error Response - No Images Found
```json
{
    "success": true,
    "images": []
}
```

### Response Fields

| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | Indicates if request was successful |
| `images` | array | Array of image objects (empty if no images) |
| `error` | string | Error message (only present if `success: false`) |

#### Image Object Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Unique image ID from database |
| `filename` | string | Original filename (stored in database) |
| `image_path` | string | Relative path to image file (`uploads/gallery/` + filename) |
| `display_name` | string | Filename for display purposes (same as filename) |

### Database Query

```sql
SELECT id, filename 
FROM gallery_images 
WHERE gallery_id = :gallery_id
ORDER BY sort_order, id
```

### Usage in Application

This endpoint is called from `admin/novosti.php` when:
1. User selects a gallery from the dropdown
2. `handleGalleryChange()` JavaScript function triggers
3. Modal image picker is populated with gallery images
4. User clicks an image to select it for the article

### Error Handling

- **Authentication failure**: Redirects to login (handled by `require_admin()`)
- **Missing gallery_id**: Returns JSON error
- **Database error**: Caught by try-catch, returns JSON error
- **Invalid gallery_id**: Returns empty images array (not an error)

### Security Notes

- ✅ Requires admin authentication
- ✅ Uses PDO prepared statements (SQL injection protection)
- ✅ Gallery ID is cast to integer
- ✅ No user input is directly output (XSS protection)
- ⚠️ No CSRF token required (read-only operation)

### Example Integration

**HTML (in admin/novosti.php):**
```html
<select id="gallery_id" onchange="handleGalleryChange()">
    <option value="">-- Select Gallery --</option>
    <option value="5">My Gallery</option>
</select>

<div id="imagePickerContent"></div>
```

**JavaScript:**
```javascript
function handleGalleryChange() {
    const galleryId = document.getElementById('gallery_id').value;
    
    if (!galleryId) {
        return;
    }
    
    fetch('ajax-get-gallery-images.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'gallery_id=' + galleryId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayImages(data.images);
        } else {
            alert('Error: ' + data.error);
        }
    });
}

function displayImages(images) {
    const container = document.getElementById('imagePickerContent');
    container.innerHTML = '';
    
    images.forEach(img => {
        const div = document.createElement('div');
        div.innerHTML = `
            <img src="../${img.image_path}" 
                 onclick="selectImage('${img.image_path}')"
                 style="cursor: pointer; width: 150px;">
        `;
        container.appendChild(div);
    });
}
```

### File Location
```
Gymkhana-Web-CMS/
└── admin/
    └── ajax-get-gallery-images.php
```

### Dependencies
- `includes/config.php` - Database connection and helper functions
- Active admin session
- `gallery_images` table in database

### Related Files
- `admin/novosti.php` - Main consumer of this endpoint
- `admin/galerija-uredi.php` - Where images are uploaded

### Testing

**Manual Test:**
```bash
# Login to admin first, then:
curl -X POST http://localhost/admin/ajax-get-gallery-images.php \
  -H "Cookie: PHPSESSID=your_session_id" \
  -d "gallery_id=1"
```

**Expected Output:**
```json
{"success":true,"images":[...]}
```

### Changelog

| Date | Version | Changes |
|------|---------|---------|
| 2025-11-20 | 1.0 | Initial implementation |
| 2025-11-20 | 1.1 | Added documentation |

### Support
For issues or questions, contact: [@ivek81cro](https://github.com/ivek81cro)
