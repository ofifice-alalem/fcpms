# 🛣️ Route Definitions: Phase 01 — إدارة المواقع الميدانية (Sites & Locations)

---

## 1. Overview & Group Configuration
- **اسم المرحلة**: `phase_01_sites_and_locations`
- **المسار الرئيسي**: `/admin/sites`
- **مجموعة الـ Middleware**: `['web', 'auth', 'verified', 'permission:view-sites']`
- **الـ Controller المسؤول**: `App\Http\Controllers\Admin\SiteController`

---

## 2. Route Table Definition

| Method | URI | Route Name | Action Method | Required Permission | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `GET` | `/admin/sites` | `admin.sites.index` | `index` | `view-sites` | عرض جدول المواقع مع الفلترة والبحث |
| `POST` | `/admin/sites` | `admin.sites.store` | `store` | `create-sites` | إنشاء موقع ميداني جديد |
| `GET` | `/admin/sites/{site}` | `admin.sites.show` | `show` | `view-sites` | جلب تفاصيل موقع محدد للـ Modal |
| `PUT` | `/admin/sites/{site}` | `admin.sites.update` | `update` | `edit-sites` | تحديث بيانات موقع قائم |
| `PATCH` | `/admin/sites/{site}/toggle-status` | `admin.sites.toggle-status` | `toggleStatus` | `edit-sites` | تبديل حالة الموقع (`active`/`inactive`) |
| `DELETE`| `/admin/sites/{site}` | `admin.sites.destroy` | `destroy` | `delete-sites` | تعطيل/حذف الموقع الميداني |

---

## 3. API & AJAX Response Contracts

### 3.1 Success Response (`GET /admin/sites/{site}`):
```json
{
  "success": true,
  "data": {
    "id": 101,
    "code": "TR-S-01",
    "name": "موقع طرابلس المركزي - البرج أ",
    "city": "طرابلس",
    "address": "شارع النصر، طرابلس",
    "status": "active",
    "visits_count": 42,
    "created_at": "2026-08-04T12:00:00Z"
  }
}
```

### 3.2 Error Response (`422 Unprocessable Entity`):
```json
{
  "success": false,
  "message": "بيانات الموقع غير صالحة",
  "errors": {
    "code": ["رمز الموقع مستخدم سابقاً، يرجى أدخال رمز فريد"]
  }
}
```

---

## 4. Security & Throttling
- **Throttling**: `throttle:60,1` لحماية جميع مسارات الإضافة والتعديل.
- **CSRF Protection**: إجباري على كافة طلبات `POST`, `PUT`, `PATCH`, `DELETE`.
