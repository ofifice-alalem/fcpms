<template>
  <!-- LY-001: Consistent Layout Structure for Consultants -->
  <!-- LY-004: Responsive Mobile-first grid -->
  <SpatialWindow>
    <div class="flex h-[calc(100vh-4rem)] w-full gap-6 overflow-hidden">
      <!-- NV-001: Consultant Navigation Sidebar -->
      <SpatialSidebar
        :items="navItems"
        :userName="user?.name || 'استشاري ميداني'"
        userRole="استشاري إشراف ميداني"
      />

      <!-- Main Content Area -->
      <div class="flex-1 flex flex-col min-w-0 overflow-hidden space-y-6">
        <!-- Header Bar -->
        <header class="flex items-center justify-between p-4 rounded-2xl border border-white/20 dark:border-white/10 bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl shadow-lg">
          <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">
              <slot name="title">بوابة الاستشاري الميداني</slot>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">سجل اليوم والأعمال الميدانية المطلوبة</p>
          </div>

          <div class="flex items-center space-x-3 space-x-reverse">
            <!-- Theme Switcher Button -->
            <SpatialIconBtn @click="toggleTheme" variant="secondary" title="تبديل المظهر">
              <span>{{ isDark ? '☀️' : '🌙' }}</span>
            </SpatialIconBtn>

            <!-- User Logout Button Trigger -->
            <SpatialButton size="sm" variant="destructive" @click="showLogoutModal = true">
              تسجيل الخروج
            </SpatialButton>
          </div>
        </header>

        <!-- Dynamic Main Content Slot -->
        <main class="flex-1 overflow-y-auto p-2 pr-1 space-y-6">
          <slot />
        </main>
      </div>
    </div>

    <!-- Logout Modal -->
    <SpatialModal :show="showLogoutModal" title="تاكيد تسجيل الخروج" @close="showLogoutModal = false">
      <p class="text-sm text-slate-600 dark:text-slate-300">هل أنت تأكد من رغبتك في إنهاء الجلسة وتسجيل الخروج؟</p>
      <template #footer>
        <SpatialButton variant="ghost" @click="showLogoutModal = false">إلغاء</SpatialButton>
        <SpatialButton variant="destructive" @click="confirmLogout">تسجيل الخروج الآن</SpatialButton>
      </template>
    </SpatialModal>
  </SpatialWindow>
</template>

<script setup>
/**
 * ConsultantLayout.vue - تخطيط الاستشاري الميداني
 * LY-001: الهيكل الموحد المخصص للاستشاريين
 * NV-001: روابط العمل الميداني واليومي
 * PM-001: تقييد الوصول بحسب صلاحيات الاستشاري
 */
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import SpatialWindow from '@/Components/Spatial/SpatialWindow.vue'
import SpatialSidebar from '@/Components/Spatial/SpatialSidebar.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialIconBtn from '@/Components/Spatial/SpatialIconBtn.vue'
import SpatialModal from '@/Components/Spatial/SpatialModal.vue'
import { useTheme } from '@/Composables/useTheme'

const { isDark, toggleTheme } = useTheme()
const page = usePage()
const user = computed(() => page.props.auth?.user)

const showLogoutModal = ref(false)

// NV-001: Consultant Navigation Links
const navItems = computed(() => [
  { name: 'الرئيسية والسجل اليومي', href: route('consultant.dashboard'), icon: '🏠', active: route().current('consultant.dashboard') },
  { name: 'اختيار موقع ميداني', href: route('consultant.sites'), icon: '📍', active: route().current('consultant.sites') },
  { name: 'سجل مؤشرات الأداء', href: route('reports.performance'), icon: '📉', active: route().current('reports.performance') },
])

const confirmLogout = () => {
  router.post('/logout')
}
</script>
