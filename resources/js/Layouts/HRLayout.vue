<template>
  <SpatialWindow>
    <!-- Sticky Top Glass Header (v3.0) -->
    <header class="sticky top-0 z-40 backdrop-blur-2xl bg-white/70 dark:bg-[#13131a]/70 border border-black/10 dark:border-white/10 px-6 py-4 rounded-[24px] flex items-center justify-between shadow-md">
      <div class="flex items-center gap-4">
        <div class="flex items-center gap-3">
          <div class="w-3 h-3 rounded-full bg-primary animate-pulse"></div>
          <h1 class="text-xl font-black text-slate-900 dark:text-white">
            <slot name="title">FCPMS</slot>
          </h1>
          <span class="text-xs px-2.5 py-1 rounded-full bg-primary/20 text-primary font-bold hidden sm:inline-block">
            إدارة الموارد البشرية
          </span>
        </div>
      </div>

      <!-- Header Controls -->
      <div class="flex items-center gap-3">
        <!-- Theme Toggle -->
        <button
          @click="toggleTheme"
          class="spatial-icon-btn"
          title="تبديل الثيم ☀️ / 🌙"
        >
          <span v-if="isDark" class="text-amber-400 text-lg">☀️</span>
          <span v-else class="text-slate-700 text-lg">🌙</span>
        </button>

        <!-- Logout Action -->
        <SpatialButton size="sm" variant="destructive" icon="🚪" @click="showLogoutModal = true">
          تسجيل الخروج
        </SpatialButton>
      </div>
    </header>

    <!-- Main Layout Container (Sidebar + Content) -->
    <div class="flex flex-1 gap-6 overflow-hidden min-h-[calc(100vh-8rem)]">
      <!-- HR Navigation Sidebar (RTL: right side) -->
      <SpatialSidebar
        :items="navItems"
        :userName="user?.name || 'مسؤول HR'"
        userRole="إدارة الموارد البشرية"
      />

      <!-- Dynamic Content Body -->
      <main class="flex-1 overflow-y-auto space-y-6 custom-scroll p-1">
        <slot />
      </main>
    </div>

    <!-- Logout Confirmation Modal -->
    <SpatialModal :show="showLogoutModal" title="تأكيد تسجيل الخروج" @close="showLogoutModal = false">
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
 * HRLayout.vue - تخطيط الموارد البشرية المطابق لـ Design System v3.0
 */
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import SpatialWindow from '@/Components/Spatial/SpatialWindow.vue'
import SpatialSidebar from '@/Components/Spatial/SpatialSidebar.vue'
import SpatialButton from '@/Components/Spatial/SpatialButton.vue'
import SpatialModal from '@/Components/Spatial/SpatialModal.vue'
import { useTheme } from '@/Composables/useTheme'

const { isDark, toggleTheme } = useTheme()
const page = usePage()
const user = computed(() => page.props.auth?.user)

const showLogoutModal = ref(false)

const navItems = computed(() => [
  { name: 'لوحة التحكم', href: route('hr.consultants.index'), icon: '📊', active: route().current('hr.consultants.index') },
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
