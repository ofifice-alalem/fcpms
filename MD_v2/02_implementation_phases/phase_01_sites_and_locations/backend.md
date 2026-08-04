# ⚙️ Backend Implementation: Phase 01 — إدارة المواقع الميدانية (Sites & Locations)

---

## 1. Overview & Scope
- **اسم المرحلة**: `phase_01_sites_and_locations`
- **الهدف التنفيذي**: بناء وإدارة دليل المواقع الميدانية والمنشآت التابعة للمشروع، وتخزين الرموز الفريدة والمدن والحالات التشغيلية للمواقع.
- **التزام المعمارية**:
  - الاتزام المباشر بنمط `Repository-Service Pattern` وحالة `Strict Mode` الموثقة في `00_architecture_and_rules/architecture.md`.
  - الامتثال لقواعد البيانات الحاكمة في `00_architecture_and_rules/database.md#sites-module`.
  - تطبيق قواعد الأعمال الخاصة بالمواقع: `BR-020`, `BR-021`, `BR-022`, `BR-023` من `00_architecture_and_rules/business_rules.md`.

---

## 2. Models & Database Entities
- **النموذج الرئيسي**: `App\Models\Site`
- **جدول البيانات**: `sites`
- **الحقول المعالجة**:
  - `id`: `bigint PK`
  - `code`: `varchar UNIQUE` (رمز فريد للموقع الميداني - BR-020)
  - `name`: `varchar` (اسم الموقع/المنشأة)
  - `address`: `text NULL` (العنوان التفصيلي)
  - `city`: `varchar NULL` (المدينة/المنطقة)
  - `status`: `enum('active', 'inactive')` (الحالة التشغيلية للموقع - BR-021)
  - `notes`: `text NULL` (ملاحظات هندسية/ميدانية)
  - `created_at`, `updated_at`: `timestamps`
- **العلاقات (Relationships)**:
  - `siteVisits()`: `hasMany(SiteVisit::class)`
  - `taskSiteAssignments()`: `hasMany(TaskSiteAssignment::class)`

---

## 3. Repository Layer (`App\Repositories`)
- **الواجهة**: `App\Repositories\Contracts\SiteRepositoryInterface`
- **التطبيق**: `App\Repositories\Eloquent\SiteRepository`
- **الدوال المحددة**:
  ```php
  public function getAllPaginated(array $filters, int $perPage = 15): LengthAwarePaginator;
  public function findByCode(string $code): ?Site;
  public function getActiveSites(): Collection;
  public function createSite(array $data): Site;
  public function updateSite(Site $site, array $data): bool;
  public function toggleStatus(Site $site): bool;
  ```

---

## 4. Service Layer (`App\Services`)
- **الفئة**: `App\Services\SiteService`
- **منطق الأعمال وقواعد BRs**:
  - **BR-020**: التحقق الفوري من عدم تكرار `code` الموقع عبر النظام قبل الحفظ.
  - **BR-021**: عند تحويل الموقع إلى `inactive` يتم منع إسناد أي مهام جديدة له.
  - **BR-022**: حظر إغلاق/حذف موقع مركب لديه زيارات ميدانية معلقة أو جارية.

---

## 5. Controllers & Form Requests
- **الـ Controller**: `App\Http\Controllers\Admin\SiteController`
- **طلبات التحقق (Form Requests)**:
  - `App\Http\Requests\Admin\StoreSiteRequest`
  - `App\Http\Requests\Admin\UpdateSiteRequest`
- **قواعد التحقق (Validation Rules Example)**:
  ```php
  // StoreSiteRequest
  public function rules(): array
  {
      return [
          'code'    => ['required', 'string', 'max:50', 'unique:sites,code'],
          'name'    => ['required', 'string', 'max:255'],
          'address' => ['nullable', 'string', 'max:500'],
          'city'    => ['required', 'string', 'max:100'],
          'status'  => ['required', 'in:active,inactive'],
          'notes'   => ['nullable', 'string', 'max:1000'],
      ];
  }
  ```

---

## 6. Authorization & Policies
- **السياسة المعتمدة**: `App\Policies\SitePolicy`
- **الصلاحيات (Spatie Permissions)**:
  - `view-sites`: عرض قائمة المواقع والتفاصيل.
  - `create-sites`: إضافة موقع ميداني جديد.
  - `edit-sites`: تعديل بيانات وصلاحية موقع قائم.
  - `delete-sites`: تعطيل أو حذف الموقع الميداني.

---

## 7. Testing Standards
- **ملف الاختبار**: `tests/Feature/Admin/SiteManagementTest.php`
- **الحالات المغطاة**:
  - `test_can_list_sites_with_pagination_and_search()`
  - `test_cannot_create_site_with_duplicate_code()`
  - `test_status_toggle_restricts_task_assignments()`
