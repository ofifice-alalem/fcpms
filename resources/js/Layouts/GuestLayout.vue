<template>
  <!-- LY-004: Responsive full glass window background -->
  <SpatialWindow>
    <!-- Top Bar with Theme Toggle -->
    <div class="flex items-center justify-between w-full mb-8">
      <div class="flex items-center space-x-3 space-x-reverse">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-cyan-500 flex items-center justify-center text-white font-black text-2xl shadow-xl">
          F
        </div>
        <div>
          <h1 class="text-xl font-black text-slate-800 dark:text-white leading-tight">FCPMS</h1>
          <span class="text-xs text-indigo-500 font-bold uppercase tracking-wider">نظام الإشراف والرقابة الميدانية</span>
        </div>
      </div>

      <!-- Theme Switcher Icon Button -->
      <SpatialIconBtn @click="toggleTheme" variant="secondary" title="تبديل المظهر">
        <span>{{ isDark ? '☀️' : '🌙' }}</span>
      </SpatialIconBtn>
    </div>

    <!-- Centered Glass Login Container -->
    <div class="flex-1 flex items-center justify-center p-4">
      <div class="w-full max-w-md">
        <SpatialCard padding="large" :glow="true">
          <slot />
        </SpatialCard>
      </div>
    </div>
  </SpatialWindow>
</template>

<script setup>
/**
 * GuestLayout.vue - تخطيط الزوار والزوار غير المسجلين (Login/Auth)
 * LY-001: التناسق والتصميم الزجاجي الموحد
 * LY-004: التجاوب مع جميع الشاشات
 */
import SpatialWindow from '@/Components/Spatial/SpatialWindow.vue'
import SpatialCard from '@/Components/Spatial/SpatialCard.vue'
import SpatialIconBtn from '@/Components/Spatial/SpatialIconBtn.vue'
import { useTheme } from '@/Composables/useTheme'

const { isDark, toggleTheme } = useTheme()
</script>
