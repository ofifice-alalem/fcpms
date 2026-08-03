<template>
  <aside
    class="relative z-30 flex flex-col transition-all duration-300 rounded-3xl border border-white/20 dark:border-white/10 bg-white/60 dark:bg-slate-900/60 backdrop-blur-2xl shadow-xl h-full"
    :class="[isCollapsed ? 'w-20' : 'w-72']"
  >
    <!-- Brand / Header -->
    <div class="p-6 flex items-center justify-between border-b border-slate-200/50 dark:border-slate-800/50">
      <div v-if="!isCollapsed" class="flex items-center space-x-3 space-x-reverse">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white font-black text-xl shadow-md">
          F
        </div>
        <div>
          <h1 class="font-black text-base text-slate-800 dark:text-white leading-tight">FCPMS</h1>
          <span class="text-[10px] text-indigo-500 font-bold uppercase tracking-wider">نظام الإشراف الميداني</span>
        </div>
      </div>
      <button
        @click="isCollapsed = !isCollapsed"
        class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-indigo-500 transition-colors mx-auto"
      >
        <span v-if="isCollapsed">❯</span>
        <span v-else>❮</span>
      </button>
    </div>

    <!-- Navigation Items -->
    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
      <template v-for="item in items" :key="item.name">
        <a
          :href="item.href || '#'"
          class="flex items-center px-4 py-3 rounded-xl transition-all duration-200 group relative"
          :class="[
            item.active
              ? 'bg-gradient-to-r from-indigo-500/20 to-purple-500/20 text-indigo-600 dark:text-indigo-400 font-bold border border-indigo-500/30'
              : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-slate-200'
          ]"
        >
          <span class="text-xl leading-none flex items-center justify-center w-6 h-6">{{ item.icon }}</span>
          <span v-if="!isCollapsed" class="mr-3 text-sm">{{ item.name }}</span>

          <!-- Badge -->
          <span
            v-if="item.badge && !isCollapsed"
            class="mr-auto px-2 py-0.5 text-[10px] font-bold rounded-full bg-indigo-500 text-white"
          >
            {{ item.badge }}
          </span>
        </a>
      </template>
    </nav>

    <!-- User Footer -->
    <div class="p-4 border-t border-slate-200/50 dark:border-slate-800/50">
      <div class="flex items-center" :class="[isCollapsed ? 'justify-center' : 'space-x-3 space-x-reverse']">
        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold">
          {{ userInitials }}
        </div>
        <div v-if="!isCollapsed" class="truncate">
          <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate">{{ userName }}</p>
          <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase font-semibold">{{ userRole }}</p>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
/**
 * SpatialSidebar.vue - شريط التنقل الجانبي الذكي
 */
import { ref, computed } from 'vue'

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
