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

    <!-- Фильтры и поиск -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Поиск -->
        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-2">Поиск</label>
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Введите название товара..."
              class="input-field pl-10"
              @input="debouncedSearch"
            >
            <Icon name="heroicons:magnifying-glass" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
          </div>
        </div>

        <!-- Категория -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Категория</label>
          <select v-model="selectedCategory" class="input-field" @change="applyFilters">
            <option value="">Все категории</option>
            <option v-for="category in categories" :key="category.id" :value="category.slug">
              {{ category.name }}
            </option>
          </select>
        </div>

        <!-- Сортировка -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Сортировка</label>
          <select v-model="sortBy" class="input-field" @change="applyFilters">
            <option value="name">По названию</option>
            <option value="price_asc">Цена: по возрастанию</option>
            <option value="price_desc">Цена: по убыванию</option>
            <option value="rating">По рейтингу</option>
            <option value="created_at">Новинки</option>
          </select>
        </div>
      </div>

      <!-- Дополнительные фильтры -->
      <div class="mt-6 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Цена -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Цена</label>
            <div class="flex space-x-2">
              <input 
                v-model="priceFrom"
                type="number" 
                placeholder="От"
                class="input-field"
                @input="debouncedFilter"
              >
              <input 
                v-model="priceTo"
                type="number" 
                placeholder="До"
                class="input-field"
                @input="debouncedFilter"
              >
            </div>
          </div>

          <!-- В наличии -->
          <div class="flex items-center">
            <input 
              v-model="inStock"
              type="checkbox" 
              id="inStock"
              class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              @change="applyFilters"
            >
            <label for="inStock" class="ml-2 text-sm text-gray-700">
              Только в наличии
            </label>
          </div>

          <!-- Сброс фильтров -->
          <div class="flex items-end">
            <button @click="resetFilters" class="btn-secondary w-full">
              Сбросить фильтры
            </button>
          </div>
        </div>
      </div>
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
      <p class="text-gray-600 mb-4">Попробуйте изменить параметры поиска</p>
      <button @click="resetFilters" class="btn-primary">
        Сбросить фильтры
      </button>
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
const { getProducts, getCategories } = useApi()
const route = useRoute()
const router = useRouter()

// SEO
useHead({
  title: 'Все товары - Nuxt Shop',
  meta: [
    { name: 'description', content: 'Все товары интернет-магазина Nuxt Shop. Широкий ассортимент по выгодным ценам.' }
  ]
})

// Реактивные данные
const searchQuery = ref(route.query.search || '')
const selectedCategory = ref(route.query.category || '')
const sortBy = ref(route.query.sort || 'title')
const priceFrom = ref(route.query.price_from || '')
const priceTo = ref(route.query.price_to || '')
const inStock = ref(route.query.in_stock === 'true')
const currentPage = ref(parseInt(route.query.page) || 1)
const viewMode = ref('grid')

// Загрузка категорий
const { data: categoriesData } = await useLazyAsyncData('categories', async () => {
  const { data } = await getCategories()
  return data
}, {
  server: false
})
const categories = computed(() => categoriesData.value?.data || [])

// Загрузка товаров
const { data: productsData, pending, error, refresh } = await useLazyAsyncData('products', async () => {
  const params = {
    page: currentPage.value,
    per_page: 12,
    search: searchQuery.value,
    category: selectedCategory.value,
    sort: sortBy.value,
    price_from: priceFrom.value,
    price_to: priceTo.value,
    in_stock: inStock.value
  }
  
  // Удаляем пустые параметры
  Object.keys(params).forEach(key => {
    if (params[key] === '' || params[key] === false) {
      delete params[key]
    }
  })
  
  const { data } = await getProducts(params)
  return data
}, {
  server: false,
  watch: [currentPage, searchQuery, selectedCategory, sortBy, priceFrom, priceTo, inStock]
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

// Debounced функции
const debouncedSearch = useDebounceFn(() => {
  currentPage.value = 1
  updateURL()
}, 500)

const debouncedFilter = useDebounceFn(() => {
  currentPage.value = 1
  applyFilters()
}, 500)

// Методы
const applyFilters = () => {
  currentPage.value = 1
  updateURL()
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  sortBy.value = 'name'
  priceFrom.value = ''
  priceTo.value = ''
  inStock.value = false
  currentPage.value = 1
  updateURL()
}

const goToPage = (page) => {
  currentPage.value = page
  updateURL()
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

const updateURL = () => {
  const query = {
    page: currentPage.value > 1 ? currentPage.value : undefined,
    search: searchQuery.value || undefined,
    category: selectedCategory.value || undefined,
    sort: sortBy.value !== 'title' ? sortBy.value : undefined,
    price_from: priceFrom.value || undefined,
    price_to: priceTo.value || undefined,
    in_stock: inStock.value || undefined
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
