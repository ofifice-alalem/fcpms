<template>
  <!-- LY-001: Consistent Layout Structure (Sidebar + Header + Content) -->
  <!-- LY-004: Responsive mobile-first grid layout -->
  <SpatialWindow>
    <div class="flex h-[calc(100vh-4rem)] w-full gap-6 overflow-hidden">
      <!-- NV-001: HR Navigation Sidebar -->
      <SpatialSidebar
        :items="navItems"
        :userName="user?.name || 'مسؤول HR'"
        userRole="إدارة الموارد البشرية"
      />

      <!-- Main Content Container -->
      <div class="flex-1 flex flex-col min-w-0 overflow-hidden space-y-6">
        <!-- Header Bar -->
        <header class="flex items-center justify-between p-4 rounded-2xl border border-white/20 dark:border-white/10 bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl shadow-lg">
          <!-- Page Title Slot / Breadcrumb -->
          <div>
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">
              <slot name="title">لوحة التحكم التنفيذية</slot>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">مرحباً بك في نظام FCPMS للإشراف الميداني</p>
          </div>

          <!-- Header Controls -->
          <div class="flex items-center space-x-3 space-x-reverse">
            <!-- Theme Switcher Button -->
            <SpatialIconBtn @click="toggleTheme" variant="secondary" title="تبديل المظهر">
              <span>{{ isDark ? '☀️' : '🌙' }}</span>
            </SpatialIconBtn>

            <!-- User Logout Modal Trigger -->
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

    <!-- FB-001 & Logout Modal Confirmation -->
    <SpatialModal :show="showLogoutModal" title="تاكيد تسجيل الخروج" @close="showLogoutModal = false">
      <p class="text-sm text-slate-600 dark:text-slate-300">هل أنت تأكد من رغبتك في تسجيل الخروج من نظام FCPMS؟</p>
      <template #footer>
        <SpatialButton variant="ghost" @click="showLogoutModal = false">إلغاء</SpatialButton>
        <SpatialButton variant="destructive" @click="confirmLogout">تسجيل الخروج الآن</SpatialButton>
      </template>
    </SpatialModal>
  </SpatialWindow>
</template>

<script setup>
/**
 * HRLayout.vue - تخطيط إدارة الموارد البشرية والمدراء
 * LY-001: الهيكل الموحد (Sidebar + Header + Content)
 * NV-001: روابط التنقل الإدارية الشاملة
 * PM-001: تقييد الوصول حسب أدوار الموارد البشرية HR
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

// NV-001: HR Navigation Sidebar Items
const navItems = computed(() => [
  { name: 'لوحة التحكم', href: route('hr.consultants.index'), icon: '📊', active: route().current('hr.consultants.*') },
  { name: 'الاستشاريون الميدانيون', href: route('hr.consultants.index'), icon: '👨‍💼', active: route().current('hr.consultants.*') },
  { name: 'المواقع الميدانية', href: route('hr.sites.index'), icon: '🏗️', active: route().current('hr.sites.*') },
  { name: 'بناء قوالب المهام', href: route('hr.tasks.index'), icon: '📋', active: route().current('hr.tasks.*') },
  { name: 'تقارير الأداء الميداني', href: route('reports.performance'), icon: '📈', active: route().current('reports.performance') },
  { name: 'سجل نشاط المواقع', href: route('reports.site-activity'), icon: '🗺️', active: route().current('reports.site-activity') },
])

const confirmLogout = () => {
  router.post('/logout')
}
</script>
