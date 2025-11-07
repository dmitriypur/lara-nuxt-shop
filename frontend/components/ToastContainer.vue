<template>
  <div class="fixed top-4 right-4 z-50 space-y-2">
    <transition-group name="toast-fade" tag="div">
      <div
        v-for="t in toastStore.toasts"
        :key="t.id"
        :class="toastClass(t.type)"
        role="alert"
      >
        <div class="flex items-start">
          <div class="mr-2 mt-0.5">
            <Icon v-if="t.type==='success'" name="heroicons:check-circle" class="w-5 h-5" />
            <Icon v-else-if="t.type==='error'" name="heroicons:exclamation-circle" class="w-5 h-5" />
            <Icon v-else name="heroicons:information-circle" class="w-5 h-5" />
          </div>
          <div class="flex-1">{{ t.message }}</div>
          <button @click="toastStore.remove(t.id)" class="ml-3 text-white/80 hover:text-white">
            <Icon name="heroicons:x-mark" class="w-5 h-5" />
          </button>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
import { useToastStore } from '@/stores/toast'
const toastStore = useToastStore()

const toastClass = (type) => {
  const base = 'min-w-[260px] max-w-[360px] px-4 py-3 rounded-lg shadow-lg text-white'
  if (type === 'success') return base + ' bg-green-600'
  if (type === 'error') return base + ' bg-red-600'
  return base + ' bg-gray-800'
}
</script>

<style>
.toast-fade-enter-active, .toast-fade-leave-active {
  transition: all .2s ease;
}
.toast-fade-enter-from, .toast-fade-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}
</style>