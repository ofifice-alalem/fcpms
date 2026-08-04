# 🎨 Frontend Implementation: Phase 01 — إدارة المواقع الميدانية (Sites & Locations)

---

## 1. Overview & UI Objectives
- **اسم المرحلة**: `phase_01_sites_and_locations`
- **الهدف البصري**: توفير واجهة إدارة مواقع فائقة الدقة بأسلوب **Spatial UI v3.0** تتيح تصفية المواقع حسب المدن، البحث الحي، إضافة موقع جديد عبر Drawer أو Modal، وعرض تفاصيل الموقع ببطاقة شفافة.
- **الالتزام بالقواعد البصرية**:
  - مطابقة الأبعاد والـ Glassmorphic Gradients المعتمدة في `01_design_system_and_components/design_rules.md`.
  - الاعتماد على المكونات التفاعلية الموثقة في `01_design_system_and_components/components_catalog.html`.

---

## 2. Page Layout & Structure
- **مسار الملف في المشروع**: `resources/views/pages/sites/index.blade.php`
- **مكونات الهيكل الأساسي (Layout Components)**:
  - **Header**: يحتوي على عنوان الصفحة `إدارة المواقع الميدانية` + زر الإضافة `+ إضافة موقع جديد`.
  - **Sidebar Navigation**: يبرز تبويب `المواقع الميدانية` كـ Active Tab.
  - **Filter Bar**: يحتوي على حقل البحث الحي `spatial-input` + قائمة منسدلة اختيار مفرد `spatial-dropdown-trigger` بارتفاع 56px (h-14) لاختيار المدينة.

---

## 3. UI Components Used (From `components_catalog.html`)

### 3.1 Data Grid Table (`SpatialTable`)
- **العناصر المعروضة**:
  - `رمز الموقع (#CODE)`
  - `اسم المنشأة/الموقع`
  - `المدينة`
  - `العنوان`
  - `الحالة التشغيلية` (شارة `status-pill completed` للنشط، `status-pill overdue` للموقوف).
  - `زر الإجراءات`: زر الاستكشاف 👁️ لعرض Modal التفاصيل، وزر التعديل ✏️ لفتح الـ Drawer.

### 3.2 Floating Bulk Action Bar (`#bulkBarFloating`)
- يظهر تلقائياً عند تحديد صفوف من جدول المواقع لتأدية إجراءات مجمعة (طباعة أكواد المواقع، تصدير البيانات إلى Excel، أو تعطيل المواقع المحددة).

### 3.3 Add/Edit Site Drawer (`#siteFormDrawer`)
- لوحة جانبية تنزلق بسلاسة من اليسار (`w-[480px]`) تحتوي على:
  - حقل رمز الموقع (مع شارة التحقق `✓ صحيح`).
  - حقل اسم الموقع (`spatial-input h-14`).
  - قائمة اختيار المدينة المخصصة (`spatial-dropdown-menu` ببارتفاع 56px).
  - مفتاح التبديل للحالة التشغيلية (`spatial-switch`).
  - زر الحفظ الفاخر `spatial-button`.

### 3.4 Site Details Modal (`#siteDetailModal`)
- نافذة منبثقة زجاجية (`spatial-modal-card`) تستعرض إحصائيات الموقع، عدد الزيارات الميدانية، المهام المسندة، والخريطة/العنوان التفصيلي.

---

## 4. Micro-Interactions & Form Validation Rules
1. **Validation Feedback**:
   - إظهار رسالة `validation-msg success` عند كتابة رمز موقع غير مكرر.
   - إظهار رسالة `validation-msg error` عند إدخال رمز مستخدم سابقاً.
2. **Dropdown Interaction**:
   - القائمة المنسدلة للـ `City Select` تعتمد كلاس `spatial-dropdown-trigger` مع السهم الفيكتوري المحدد بمقاس `1.125rem` فقط لتعطي مظهر زجاجي متناسق 100%.

---

## 5. JavaScript Logic Functions
```javascript
// استدعاء المكونات التفاعلية المعتمدة في الكتالوج
function openAddSiteDrawer() { openDrawer('siteFormDrawer'); }
function viewSiteDetails(siteId) { openModal('siteDetailModal'); }
function filterSitesByCity(cityName) { filterTableByColumn('city', cityName); }
```
