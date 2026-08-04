<template>
  <aside
    class="border-l border-black/10 dark:border-white/10 bg-white/40 dark:bg-slate-900/40 backdrop-blur-2xl flex flex-col py-6 px-4 transition-all duration-300 shrink-0 z-30 rounded-[30px] shadow-xl my-4"
    :class="[isCollapsed ? 'w-20' : 'w-64']"
  >
    <!-- Brand / Header -->
    <div class="flex items-center gap-3 px-2 mb-6 pb-4 border-b border-black/10 dark:border-white/10">
      <div class="w-10 h-10 rounded-2xl bg-primary flex items-center justify-center text-white font-black text-lg shadow-lg shadow-primary/30 shrink-0">
        ف
      </div>
      <div v-if="!isCollapsed" class="truncate">
        <span class="font-black text-base text-slate-900 dark:text-white block">FCPMS</span>
        <span class="text-[11px] font-bold text-slate-500 dark:text-white/50 block">نظام أداء الاستشاريين</span>
      </div>
      <button
        @click="isCollapsed = !isCollapsed"
        class="spatial-icon-btn !w-8 !h-8 mr-auto shrink-0"
        title="طي / توسيع القائمة"
      >
        <span class="text-xs font-bold">{{ isCollapsed ? '❯' : '❮' }}</span>
      </button>
    </div>

    <!-- Navigation Items -->
    <nav class="flex-1 flex flex-col gap-1.5 custom-scroll overflow-y-auto">
      <div v-if="!isCollapsed" class="text-[11px] font-black text-slate-400 dark:text-white/40 uppercase tracking-widest px-3 my-1">
        القائمة الرئيسية
      </div>

      <template v-for="item in items" :key="item.name">
        <Link
          :href="item.href || '#'"
          class="flex items-center gap-3 px-3.5 py-3 rounded-[16px] text-sm font-bold transition-all"
          :class="[
            item.active
              ? 'bg-primary text-white shadow-md shadow-primary/30'
              : 'text-slate-600 dark:text-white/70 hover:bg-black/5 dark:hover:bg-white/10'
          ]"
        >
          <span class="text-lg leading-none shrink-0">{{ item.icon }}</span>
          <span v-if="!isCollapsed" class="truncate">{{ item.name }}</span>

          <span
            v-if="item.badge && !isCollapsed"
            class="mr-auto px-2 py-0.5 text-[10px] font-black rounded-full bg-white/20 text-white"
          >
            {{ item.badge }}
          </span>
        </Link>
      </template>
    </nav>

    <!-- User Profile Footer -->
    <div class="mt-4 pt-4 border-t border-black/10 dark:border-white/10">
      <div class="flex items-center gap-3 px-2" :class="{ 'justify-center': isCollapsed }">
        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white font-black text-sm shadow-md shrink-0">
          {{ userInitials }}
        </div>
        <div v-if="!isCollapsed" class="truncate">
          <p class="text-xs font-black text-slate-900 dark:text-white truncate">{{ userName }}</p>
          <p class="text-[10px] font-bold text-slate-500 dark:text-white/50 truncate">{{ userRole }}</p>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
/**
 * SpatialSidebar.vue - شريط التنقل الجانبي المطابق لـ Design System v3.0
 */
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  items: { type: Array, default: () => [] },
  userName: { type: String, default: 'مستخدم النظام' },
  userRole: { type: String, default: 'استشاري' }
})

const isCollapsed = ref(false)

const userInitials = computed(() => {
  return props.userName.charAt(0) || 'م'
})
</script>
