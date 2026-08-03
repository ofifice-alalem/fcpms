<template>
  <GuestLayout>
    <div class="space-y-6">
      <!-- 1. Header Section -->
      <div class="text-center space-y-2">
        <h2 class="text-2xl font-black text-slate-800 dark:text-white">تسجيل الدخول للنظام</h2>
        <p class="text-xs text-slate-500 dark:text-slate-400">
          أدخل بيانات الاعتماد الخاصة بك للوصول لبوابة FCPMS
        </p>
      </div>

      <!-- 2. Authentication Login Form -->
      <form @submit.prevent="submit" class="space-y-4">
        <!-- Email Input Field (FM-004: Required *) -->
        <SpatialInput
          v-model="form.email"
          type="email"
          label="البريد الإلكتروني"
          placeholder="example@company.com"
          :required="true"
          :error="form.errors.email"
          prefixIcon="📧"
        />

        <!-- Password Input Field (FM-004: Required *) -->
        <SpatialInput
          v-model="form.password"
          type="password"
          label="كلمة المرور"
          placeholder="••••••••"
          :required="true"
          :error="form.errors.password"
          prefixIcon="🔒"
        />

        <!-- Remember Me Checkbox -->
        <div class="flex items-center justify-between pt-1">
          <SpatialCheckbox
            v-model="form.remember"
            label="تذكر جلسة الدخول"
          />
        </div>

        <!-- Submit Action Button (AC-004: Loading state & double submit prevention) -->
        <div class="pt-2">
          <SpatialButton
            type="submit"
            variant="primary"
            size="lg"
            block
            icon="🔑"
            :loading="form.processing"
          >
            تسجيل الدخول الآن
          </SpatialButton>
        </div>
      </form>

      <!-- Feedback Error Toast (FB-002 & BR-004: Blocked Account Alert) -->
      <SpatialToast
        v-if="toast.show"
        :type="toast.type"
        :title="toast.title"
        :message="toast.message"
        @dismiss="toast.show = false"
      />
    </div>
  </GuestLayout>
</template>

<script setup>
/**
 * Login.vue - صفحة تسجيل الدخول الموحدة للنظام (Phase 4 - Step 1)
 * LY-001: GuestLayout Glassmorphism Structure
 * FM-004: Required indicator on form fields
 * FM-007: Inline validation errors mapping
 * AC-004: Loading state during login POST request
 * BR-004: Inactive accounts blocking feedback
 */
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import SpatialInput from '@/Components/Spatial/SpatialInput.vue'
import SpatialCheckbox from '@/Components/Spatial/SpatialCheckbox.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialToast from '@/Components/Spatial/SpatialToast.vue'

const toast = ref({ show: false, type: 'error', title: '', message: '' })

// Inertia Form State Management
const form = useForm({
  email: '',
  password: '',
  remember: false
})

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
    onError: (errors) => {
      toast.value = {
        show: true,
        type: 'error',
        title: 'فشل تسجيل الدخول',
        message: errors.email || errors.password || 'تعذر التحقق من بيانات الدخول، يرجى التأكد من البريد وكلمة المرور.'
      }
    }
  })
}
</script>
