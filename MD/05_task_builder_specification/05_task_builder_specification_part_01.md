# المرحلة 05 — Task Builder Specification
# 05_task_builder_specification_part_01.md

Version: 1.0

Status: Approved

Part: 01 of 03

Depends On

- 01_project_overview.md
- 02_business_rules.md
- 02.5_system_architecture.md
- 03_database_design_part_01.md
- 03_database_design_part_02.md
- 03_database_design_part_03.md
- 03_database_design_part_04.md
- 04.1_hr_workflows.md
- 04.2_consultant_workflows.md
- 04.3_system_workflows_part_01.md
- 04.3_system_workflows_part_02.md
- 04.3_system_workflows_part_03.md
- 04.5_edge_cases.md

---

# مقدمة

يمثل نظام المهام (Task System) أحد أهم أجزاء النظام، لأنه المسؤول عن تعريف الأعمال التي يجب على الاستشاريين تنفيذها وتسجيلها.

لا يعتمد النظام على مهام ثابتة مبرمجة داخل النظام، وإنما يعتمد على مفهوم:

```text
Dynamic Task Builder
```

أي أن مسؤول الـ HR يستطيع إنشاء وتعديل وإدارة أنواع مختلفة من المهام بدون الحاجة إلى تعديل الكود البرمجي.

---

# الهدف من Task Builder

إنشاء نظام مرن يسمح للإدارة بتعريف:

- المهام اليومية.
- المهام حسب الحاجة.
- النماذج الخاصة بالاستلام والفحص.
- الحقول المطلوبة لكل مهمة.
- طريقة إدخال البيانات.
- قواعد التحقق.
- تأثير المهمة على تقييم الأداء.

---

# ==========================================================
# Section 01 — Task Builder Concept
# ==========================================================

## تعريف

Task Builder هو المحرك المسؤول عن إنشاء تعريفات المهام داخل النظام.

المهمة ليست مجرد Checkbox.

بل هي كيان يحتوي على:

```text
Task Definition

+

Input Fields

+

Validation Rules

+

Business Rules

+

Performance Rules
```

---

# مثال

مهمة:

```text
استلام أعمال الأرضيات
```

يمكن أن تحتوي على:

```text
اسم المهمة:

استلام أعمال الأرضيات


نوع المهمة:

Event Task


الحقول:

- هل تم الاستلام؟
- رقم البند
- تاريخ الاستلام
- الملاحظات
- صور الموقع
```

---

# ==========================================================
# Section 02 — Task Lifecycle
# ==========================================================

## الهدف

تحديد دورة حياة المهمة منذ إنشائها حتى استخدامها داخل النظام.

---

# Workflow 01 — Create Task

## Trigger

HR يقوم بإنشاء مهمة جديدة.

---

## System Flow

```text
فتح Task Builder

↓

إدخال بيانات المهمة الأساسية

↓

اختيار نوع المهمة

↓

إضافة الحقول

↓

تحديد قواعد التحقق

↓

حفظ المهمة
```

---

## Result

يتم إنشاء Task Definition جديدة.

---

# Workflow 02 — Review Task

## الهدف

مراجعة المهمة قبل نشرها.

---

## System Flow

```text
إنشاء المهمة

↓

حالة Draft

↓

مراجعة البيانات

↓

اعتماد المهمة
```

---

# Task Status

الحالات الأساسية:

```text
Draft

Active

Inactive

Archived
```

---

# Workflow 03 — Publish Task

## Trigger

اعتماد HR للمهمة.

---

## System Flow

```text
تغيير الحالة

↓

Draft

↓

Active

↓

ظهور المهمة للاستشاريين
```

---

# Workflow 04 — Disable Task

## Trigger

HR يقوم بإيقاف مهمة.

---

## System Flow

```text
Active

↓

Inactive

↓

عدم ظهورها في المهام الجديدة

↓

الحفاظ على السجلات القديمة
```

---

# ==========================================================
# Section 03 — Task Types
# ==========================================================

## الهدف

تحديد أنواع المهام التي يدعمها النظام.

---

# Type 01 — Daily Task

## التعريف

مهام يجب تنفيذها بشكل يومي.

---

## Characteristics

```text
تظهر يومياً

تحسب ضمن نسبة الإنجاز اليومية

مرتبطة بيوم العمل
```

---

## Example

```text
فحص السلامة اليومية

مراجعة العمال

متابعة النظافة
```

---

## Performance Impact

تؤثر على:

```text
Daily Completion Percentage
```

---

# Type 02 — Event Task

## التعريف

مهام تظهر عند حدوث عملية أو طلب معين.

---

## Characteristics

```text
لا تظهر يومياً

لا تسبب نقصاً إذا لم تستخدم

تسجل عند حدوث الحدث
```

---

## Example

```text
استلام الخرسانة

استلام العزل

استلام الأرضيات
```

---

## Performance Impact

لا تدخل في نسبة المهام اليومية.

ولكن يتم حساب:

```text
Additional Tasks Count
```

---

# Type 03 — Custom Form Task

## التعريف

مهمة تحتاج نموذجاً كاملاً وليس اختياراً فقط.

---

## Example

```text
تقرير فحص موقع
```

يحتوي:

```text
رقم التقرير

التاريخ

الملاحظات

الصور

النتيجة
```

---

# ==========================================================
# Section 04 — Task Basic Structure
# ==========================================================

## الهدف

تعريف المكونات الأساسية لأي مهمة.

---

# Task Definition

كل مهمة تحتوي على:

```text
Basic Information

+

Configuration

+

Fields

+

Rules
```

---

# Basic Information

تشمل:

```text
Task Name

Description

Task Type

Status

Priority
```

---

# Configuration

تشمل:

```text
Is Required

Is Daily

Allow Multiple Responses

Need Approval

Performance Weight
```

---

# Target Assignment (Site & Consultant)

تحدد نطاق ظهور المهمة:

```text
Site Assignment: (All Sites / Specific Sites)

Consultant Assignment: (All Consultants / Specific Consultants)
```

---

# Example

```text
Task Name:

Safety Inspection


Type:

Daily Task


Required:

Yes


Target Consultants:

All Consultants (or Specific Consultants)


Performance Weight:

10%
```

---

# ==========================================================
# Section 05 — Task Metadata
# ==========================================================

## الهدف

إضافة معلومات تساعد النظام في التعامل مع المهمة.

---

# Metadata Examples

## Display Information

```text
Title

Description

Icon

Order
```

---

## Behavior Information

```text
Task Type

Required Status

Visibility Rules
```

---

## Performance Information

```text
Included In Performance

Weight

Category
```

---

# ==========================================================
# Section 06 — Task Activation Rules
# ==========================================================

## الهدف

تحديد متى تظهر المهمة للاستشاري.

---

# Rule 01 — Active Task Only

يتم عرض:

```text
Active Tasks
```

فقط.

---

# Rule 02 — Historical Preservation

عند تعطيل مهمة:

```text
New Records:

No


Old Records:

Remain Available
```

---

# Rule 03 — Future Changes Only

أي تعديل جديد:

يطبق على الاستخدامات المستقبلية.

ولا يغير البيانات التاريخية.

---

# ==========================================================
# Section 07 — Task Categories
# ==========================================================

## الهدف

تنظيم المهام داخل النظام.

---

# Example Categories

```text
Safety

Quality

Inspection

Documentation

Engineering
```

---

# Benefits

تساعد في:

- التقارير.
- البحث.
- التصفية.
- التحليل.

---

# ==========================================================
# Section 08 — Task Builder Rules
# ==========================================================

## Rule 01

لا يمكن إنشاء مهمة بدون:

```text
Name

Type
```

---

## Rule 02

المهمة اليومية يجب أن تحتوي على:

```text
Performance Configuration
```

---

## Rule 03

المهمة التي تحتاج إدخال بيانات يجب أن تحتوي على:

```text
At Least One Field
```

---

## Rule 04

لا يتم حذف مهمة مستخدمة سابقاً.

يتم:

```text
Disable

Archive
```

---

# نهاية الجزء الأول

تم توثيق:

- مفهوم Task Builder.
- دورة حياة المهمة.
- أنواع المهام.
- الهيكل الأساسي.
- Metadata.
- قواعد التفعيل.

---

# الجزء التالي

الملف:

```text
05_task_builder_specification_part_02.md
```

سيغطي:

- Dynamic Fields Engine.
- أنواع الحقول.
- خصائص الحقول.
- Validation Rules.
- Conditional Fields.
- Field Dependencies.
```
