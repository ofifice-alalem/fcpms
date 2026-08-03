# المرحلة 05 — Task Builder Specification
# 05_task_builder_specification_part_03.md

Version: 1.0

Status: Approved

Part: 03 of 03

Depends On

- 05_task_builder_specification_part_01.md
- 05_task_builder_specification_part_02.md
- 03_database_design_part_01.md
- 03_database_design_part_02.md
- 04.2_consultant_workflows.md
- 04.3_system_workflows_part_02.md
- 04.4_reporting_workflows_part_01.md

---

# مقدمة

يكمل هذا الجزء تصميم نظام المهام الديناميكي.

يركز على مرحلة استخدام المهمة بعد إنشائها، بداية من ظهورها للاستشاري، مروراً بتسجيل الإجابات، وانتهاءً بحساب تأثيرها على الأداء والتقارير.

يغطي هذا الجزء:

- Task Rendering Engine.
- Task Response System.
- Validation أثناء التنفيذ.
- Task Versioning.
- Performance Integration.
- أمثلة تشغيل كاملة.

---

# ==========================================================
# Engine 01 — Task Rendering Engine
# ==========================================================

## الهدف

تحويل تعريف المهمة المخزن داخل النظام إلى نموذج قابل للاستخدام من قبل الاستشاري.

---

# Workflow 01 — Load Task For Consultant

## Trigger

اختيار الاستشاري لموقع أو بدء تسجيل مهمة.

---

## System Flow

```text
اختيار الموقع

↓

قراءة المهام النشطة

↓

تحديد نوع المهمة

↓

تحميل تعريف المهمة

↓

تحميل الحقول

↓

تحميل القواعد

↓

إنشاء النموذج
```

---

## Result

ظهور المهمة بالشكل المناسب للاستشاري.

---

# Example

تعريف المهمة:

```text
Task:

استلام الأرضيات
```

النتيجة:

```text
☐ تم الاستلام


رقم البند:

[          ]


التاريخ:

[          ]


الملاحظات:

[          ]


الصور:

[ Upload ]
```

---

# ==========================================================
# Engine 02 — Task Execution Flow
# ==========================================================

## الهدف

تحديد دورة تنفيذ المهمة من قبل الاستشاري.

---

# Workflow 01 — Start Task

## Trigger

فتح المهمة.

---

## System Flow

```text
فتح المهمة

↓

إنشاء Task Response مؤقت

↓

تحميل الحقول

↓

السماح بالإدخال
```

---

# Workflow 02 — Fill Task Data

## Trigger

إدخال البيانات.

---

## System Flow

```text
إدخال قيمة

↓

تشغيل Validation

↓

حفظ القيمة مؤقتاً

↓

الانتقال للحقل التالي
```

---

# Workflow 03 — Submit Task

## Trigger

ضغط حفظ.

---

## System Flow

```text
استقبال البيانات

↓

فحص الحقول المطلوبة

↓

فحص القواعد

↓

حفظ الإجابات

↓

تحديث حالة المهمة
```

---

# Task Response Status

```text
Draft

Completed

Rejected

Approved
```

---

# ==========================================================
# Engine 03 — Task Response Storage
# ==========================================================

## الهدف

تخزين إجابات الاستشاري بطريقة مرنة.

---

# Concept

لا يتم إنشاء جدول مستقل لكل نوع مهمة.

مثال خاطئ:

```text
Concrete_Task_Table

Floor_Task_Table

Safety_Task_Table
```

لأن النظام سيصبح غير قابل للتوسع.

---

# التصميم الصحيح

يعتمد على:

```text
Task Definition

+

Field Definition

+

Task Response

+

Field Values
```

---

# Example

المهمة:

```text
Safety Inspection
```

الإجابات:

```text
هل تم الفحص؟

Yes


عدد العمال:

15


الملاحظات:

لا توجد مشاكل
```

يتم تخزينها كبيانات مرتبطة بتعريف الحقول.

---

# ==========================================================
# Engine 04 — Task Validation During Execution
# ==========================================================

## الهدف

منع حفظ بيانات غير صحيحة.

---

# Validation Level 01 — Field Validation

يتم فحص:

```text
Required

Data Type

Length

Format
```

---

# Validation Level 02 — Business Validation

مثال:

المهمة:

```text
استلام الخرسانة
```

القواعد:

```text
يجب وجود رقم الصبة

يجب وجود صورة

يجب تحديد النتيجة
```

---

# Validation Flow

```text
Submit Request

↓

Field Validation

↓

Business Validation

↓

Accept

or

Reject
```

---

# ==========================================================
# Engine 05 — Task Versioning Engine
# ==========================================================

## الهدف

الحفاظ على تاريخ المهمة عند حدوث تعديلات مستقبلية.

---

# المشكلة

مثال:

يناير:

```text
مهمة فحص السلامة

تحتوي:

Checkbox فقط
```

---

مارس:

HR يضيف:

```text
صورة إجبارية
```

---

السؤال:

هل تتغير السجلات القديمة؟

الإجابة:

لا.

---

# الحل

استخدام:

```text
Task Version
```

---

# Workflow

```text
تعديل المهمة

↓

إنشاء Version جديد

↓

الحفاظ على Version القديم

↓

استخدام الجديد في المهام القادمة
```

---

# Example

```text
Safety Inspection


Version 1

01-01-2026

Checkbox


Version 2

01-03-2026

Checkbox

+

Image
```

---

# ==========================================================
# Engine 06 — Task Performance Integration
# ==========================================================

## الهدف

ربط المهام مع نظام تقييم الأداء.

---

# Daily Tasks

تدخل في:

```text
Daily Completion Percentage
```

---

# Formula

```text
Completed Daily Tasks

-------------------------

Total Required Daily Tasks
```

× 100

---

# Example

المهام اليومية:

```text
10
```

تم تنفيذ:

```text
8
```

النتيجة:

```text
80%
```

---

# Additional Tasks

لا تدخل في النسبة اليومية.

لكن يتم حساب:

```text
Additional Tasks Count
```

---

# Example

استشاري:

```text
Daily Tasks:

95%


Additional Tasks:

12
```

---

# ==========================================================
# Engine 07 — Task Weight System
# ==========================================================

## الهدف

إعطاء أهمية مختلفة للمهام.

---

# Example

المهام:

```text
Safety Inspection

Weight: 50


Quality Check

Weight: 30


Documentation

Weight: 20
```

---

# Weighted Performance

يستخدم عندما تحتاج الإدارة إلى تقييم أكثر دقة.

---

# ==========================================================
# Engine 08 — Task Completion Rules
# ==========================================================

## Rule 01

المهمة اليومية المطلوبة يجب إكمالها قبل إغلاق اليوم.

---

## Rule 02

المهام الإضافية اختيارية.

---

## Rule 03

المهمة التي تحتوي على Required Fields لا يمكن حفظها ناقصة.

---

## Rule 04

تعديل إجابة قديمة يحتاج صلاحية.

---

## Rule 05

كل تعديل يسجل في Audit Log.

---

# ==========================================================
# Engine 09 — Complete Execution Example
# ==========================================================

# Scenario

استشاري يزور موقع:

```text
مشروع المستشفى
```

---

# Step 01

اختيار الموقع.

---

# Step 02

النظام يعرض:

## Daily Tasks

```text
☐ فحص السلامة

☐ مراجعة العمال

☐ متابعة النظافة
```

---

## Additional Tasks

```text
استلام الأرضيات

استلام الخرسانة
```

---

# Step 03

تنفيذ المهام اليومية.

النتيجة:

```text
3 / 3

100%
```

---

# Step 04

تنفيذ مهمة إضافية:

```text
استلام الأرضيات
```

إدخال:

```text
تم الاستلام

رقم البند

صور

ملاحظات
```

---

# Step 05

النظام يحفظ:

```text
Daily Completion:

100%


Additional Activities:

1
```

---

# ==========================================================
# Engine 10 — Task Builder Final Rules
# ==========================================================

## Rule 01

كل مهمة يجب أن تكون مستقلة عن البرمجة.

---

## Rule 02

أي نوع جديد من المهام يجب إنشاؤه من Task Builder.

---

## Rule 03

التاريخ القديم لا يتأثر بالتعديلات الجديدة.

---

## Rule 04

المهام اليومية والمهام الإضافية يتم حسابها بشكل منفصل.

---

## Rule 05

كل إجابة مرتبطة بنسخة المهمة المستخدمة وقت التنفيذ.

---

# نهاية المرحلة 05

تم اكتمال:

```text
05_task_builder_specification_part_01.md

05_task_builder_specification_part_02.md

05_task_builder_specification_part_03.md
```

---

# المرحلة التالية

المرحلة القادمة:

```text
06_ui_ux_specification.md
```

وستغطي:

- تصميم واجهات HR.
- تصميم واجهة الاستشاري.
- Dashboard.
- صفحات الإدارة.
- نظام التصميم.
- قواعد UX.
- حالات العرض المختلفة.
