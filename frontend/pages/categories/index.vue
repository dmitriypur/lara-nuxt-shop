<template>
  <div>
    <!-- Хлебные крошки -->
    <Breadcrumbs :breadcrumbs="[
      { name: 'Категории' }
    ]" />
    
    <!-- Заголовок страницы -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-4">Категории товаров</h1>
      <p class="text-gray-600">Выберите интересующую вас категорию</p>
    </div>

    <!-- Поиск категорий -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-8">
      <div class="max-w-md">
        <label class="block text-sm font-medium text-gray-700 mb-2">Поиск категории</label>
        <div class="relative">
          <input 
            v-model="searchQuery"
            type="text" 
            placeholder="Введите название категории..."
            class="input-field pl-10"
            @input="debouncedSearch"
          >
          <Icon name="heroicons:magnifying-glass" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
        </div>
      </div>
    </div>

    <!-- Категории -->
    <div v-if="pending" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="n in 6" :key="n" class="card animate-pulse">
        <div class="h-48 bg-gray-300 rounded-t-lg"></div>
        <div class="p-4">
          <div class="h-5 bg-gray-300 rounded mb-2"></div>
          <div class="h-3 bg-gray-300 rounded w-2/3 mb-3"></div>
          <div class="h-8 bg-gray-300 rounded w-1/2"></div>
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
    
    <div v-else-if="filteredCategories.length === 0" class="text-center py-12">
      <Icon name="heroicons:magnifying-glass" class="h-12 w-12 text-gray-400 mx-auto mb-4" />
      <h3 class="text-lg font-semibold text-gray-900 mb-2">Категории не найдены</h3>
      <p class="text-gray-600 mb-4">Попробуйте изменить параметры поиска</p>
      <button @click="searchQuery = ''" class="btn-primary">
        Сбросить поиск
      </button>
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div 
          v-for="category in filteredCategories" 
          :key="category.id" 
          class="card hover:shadow-lg transition-shadow duration-200 cursor-pointer"
          @click="goToCategory(category)"
        >
        <!-- Изображение категории -->
        <div class="h-48 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-t-lg flex items-center justify-center">
          <Icon 
            v-if="!category.image" 
            name="heroicons:tag" 
            class="h-16 w-16 text-blue-500" 
          />
          <img 
            v-else 
            :src="category.image" 
            :alt="category.name"
            class="h-full w-full object-cover rounded-t-lg"
          >
        </div>
        
        <!-- Информация о категории -->
        <div class="p-4">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ category.name }}</h3>
          <p v-if="category.description" class="text-gray-600 text-sm mb-3 line-clamp-2">
            {{ category.description }}
          </p>
          
          <!-- Количество товаров -->
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">
              {{ category.products_count || 0 }} товаров
            </span>
            <button class="btn-primary text-sm px-4 py-2">
              Перейти
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Пустое состояние если нет категорий -->
    <div v-if="!pending && !error && categories.length === 0" class="text-center py-12">
      <Icon name="heroicons:tag" class="h-12 w-12 text-gray-400 mx-auto mb-4" />
      <h3 class="text-lg font-semibold text-gray-900 mb-2">Категории не найдены</h3>
      <p class="text-gray-600 mb-4">В данный момент категории не добавлены</p>
      <NuxtLink to="/catalog" class="btn-primary">
        Перейти в каталог
      </NuxtLink>
    </div>
  </div>
</template>

<script setup>
const { getCategories } = useApi()

// SEO
useHead({
  title: 'Категории товаров - Nuxt Shop',
  meta: [
    { name: 'description', content: 'Категории товаров интернет-магазина Nuxt Shop. Выберите интересующую вас категорию.' }
  ]
})

// Реактивные данные
const searchQuery = ref('')

// Загрузка категорий
const { data: categoriesData, pending, error, refresh } = await useLazyAsyncData('categories', async () => {
  const { data, error: apiError } = await getCategories()
  if (apiError) throw apiError
  return data
}, {
  server: false
})

const categories = computed(() => categoriesData.value?.data || [])

// Фильтрация категорий по поиску
const filteredCategories = computed(() => {
  if (!searchQuery.value) return categories.value
  
  return categories.value.filter(category => 
    category.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    (category.description && category.description.toLowerCase().includes(searchQuery.value.toLowerCase()))
  )
})

// Debounced поиск
const debouncedSearch = useDebounceFn(() => {
  // Поиск происходит автоматически через computed свойство
}, 300)

// Переход к категории
const goToCategory = (category) => {
  navigateTo(`/categories/${category.slug}`)
}
</script>
