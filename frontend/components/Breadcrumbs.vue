<template>
  <nav class="text-sm text-gray-500 mb-6" aria-label="Хлебные крошки">
    <ol class="flex items-center space-x-2">
      <li>
        <NuxtLink to="/" class="hover:text-blue-600 transition-colors">
          Главная
        </NuxtLink>
      </li>
      
      <li v-for="(crumb, index) in breadcrumbs" :key="index" class="flex items-center">
        <Icon name="heroicons:chevron-right" class="h-4 w-4 mx-2 text-gray-400" />
        <NuxtLink 
          v-if="crumb.link && index < breadcrumbs.length - 1"
          :to="crumb.link"
          class="hover:text-blue-600 transition-colors"
        >
          {{ crumb.name }}
        </NuxtLink>
        <span v-else class="text-gray-900 font-medium">
          {{ crumb.name }}
        </span>
      </li>
    </ol>
  </nav>
</template>

<script setup>
defineProps({
  breadcrumbs: {
    type: Array,
    default: () => []
  }
})
</script>
