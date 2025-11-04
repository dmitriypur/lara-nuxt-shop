<template>
  <div class="min-h-screen bg-gray-50">
    <AppHeader />
    <main class="container mx-auto px-4 py-8">
      <NuxtPage />
    </main>
    <AppFooter />
  </div>
</template>

<script setup>
// Глобальные мета-теги
useHead({
  title: 'Nuxt Shop - Интернет-магазин',
  meta: [
    { name: 'description', content: 'Современный интернет-магазин на Nuxt 3' },
    { name: 'viewport', content: 'width=device-width, initial-scale=1' }
  ],
  link: [
    { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
  ]
})
</script>