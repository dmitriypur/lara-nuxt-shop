<template>
  <div>
    <!-- Хлебные крошки -->
    <Breadcrumbs :breadcrumbs="[
      { name: 'Товары' }
    ]" />
    
    <!-- Заголовок страницы -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-4">Все товары</h1>
      <p class="text-gray-600">Широкий ассортимент товаров по выгодным ценам</p>
    </div>

    <!-- Результаты -->
    <div class="flex justify-between items-center mb-6">
      <p class="text-gray-600">
        <span v-if="!pending">Найдено {{ totalProducts }} товаров</span>
        <span v-else>Загрузка...</span>
      </p>
      
      <!-- Переключатель вида -->
      <div class="flex space-x-2">
        <button 
          @click="viewMode = 'grid'"
          :class="viewMode === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
          class="p-2 rounded"
        >
          <Icon name="heroicons:squares-2x2" class="h-5 w-5" />
        </button>
        <button 
          @click="viewMode = 'list'"
          :class="viewMode === 'list' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
          class="p-2 rounded"
        >
          <Icon name="heroicons:list-bullet" class="h-5 w-5" />
        </button>
      </div>
    </div>

    <!-- Товары -->
    <div v-if="pending" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="n in 8" :key="n" class="card animate-pulse">
        <div class="h-48 bg-gray-300"></div>
        <div class="p-4">
          <div class="h-4 bg-gray-300 rounded mb-2"></div>
          <div class="h-3 bg-gray-300 rounded w-2/3 mb-2"></div>
          <div class="h-6 bg-gray-300 rounded w-1/3"></div>
        </div>
      </div>
    </div>
    
    <div v-else-if="error" class="text-center py-12">
      <Icon name="heroicons:exclamation-triangle" class="h-12 w-12 text-red-500 mx-auto mb-4" />
      <h3 class="text-lg font-semibold text-gray-900 mb-2">Ошибка загрузки</h3>
      <p class="text-gray-600 mb-4">{{ error.message }}</p>
      <button @click="refresh()" class="btn-primary">
        Попробовать снова
      </button>
    </div>
    
    <div v-else-if="products.length === 0" class="text-center py-12">
      <Icon name="heroicons:magnifying-glass" class="h-12 w-12 text-gray-400 mx-auto mb-4" />
      <h3 class="text-lg font-semibold text-gray-900 mb-2">Товары не найдены</h3>
      <p class="text-gray-600">Каталог пуст или товары временно недоступны.</p>
    </div>
    
    <div v-else>
      <!-- Сетка товаров -->
      <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <ProductCard v-for="product in products" :key="product.id" :product="product" />
      </div>
      
      <!-- Список товаров -->
      <div v-else class="space-y-4">
        <ProductListItem v-for="product in products" :key="product.id" :product="product" />
      </div>
    </div>

    <!-- Пагинация -->
    <div v-if="totalPages > 1" class="mt-12 flex justify-center">
      <nav class="flex space-x-2">
        <button 
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage <= 1"
          class="px-3 py-2 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
        >
          Назад
        </button>
        
        <button 
          v-for="page in visiblePages" 
          :key="page"
          @click="goToPage(page)"
          :class="page === currentPage ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'"
          class="px-3 py-2 border border-gray-300 rounded-md"
        >
          {{ page }}
        </button>
        
        <button 
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage >= totalPages"
          class="px-3 py-2 border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
        >
          Вперед
        </button>
      </nav>
    </div>
  </div>
</template>

<script setup>
const { getProducts } = useApi()
const route = useRoute()
const router = useRouter()

// SEO
useHead({
  title: 'Все товары - Nuxt Shop',
  meta: [
    { name: 'description', content: 'Все товары интернет-магазина Nuxt Shop. Широкий ассортимент по выгодным ценам.' }
  ]
})

// Реактивные данные (фильтры убраны, оставляем только пагинацию/вид)
const currentPage = ref(parseInt(route.query.page) || 1)
const viewMode = ref('grid')

// Категории временно не используются

// Загрузка товаров (без фильтров)
const { data: productsData, pending, error, refresh } = await useLazyAsyncData('products', async () => {
  const { data } = await getProducts({
    page: currentPage.value,
    per_page: 12
  })
  return data
}, {
  server: false,
  watch: [currentPage]
})

// Вычисляемые свойства
const products = computed(() => productsData.value?.data || [])
const totalProducts = computed(() => productsData.value?.total || 0)
const totalPages = computed(() => productsData.value?.last_page || 1)

const visiblePages = computed(() => {
  const pages = []
  const start = Math.max(1, currentPage.value - 2)
  const end = Math.min(totalPages.value, currentPage.value + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Методы: только пагинация и обновление URL

const goToPage = (page) => {
  currentPage.value = page
  updateURL()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const updateURL = () => {
  const query = {
    page: currentPage.value > 1 ? currentPage.value : undefined
  }
  
  // Удаляем undefined значения
  Object.keys(query).forEach(key => {
    if (query[key] === undefined) {
      delete query[key]
    }
  })
  
  router.push({ query })
}
</script>
