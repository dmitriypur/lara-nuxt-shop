<template>
  <div>
    <!-- Хлебные крошки -->
    <div class="container mx-auto px-4 pt-6">
      <Breadcrumbs :breadcrumbs="[]" />
    </div>
    
    <!-- Hero секция -->
    <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-20 mb-12">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">
          Добро пожаловать в Nuxt Shop
        </h1>
        <p class="text-xl md:text-2xl mb-8 opacity-90">
          Лучшие товары по выгодным ценам с быстрой доставкой
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <NuxtLink to="/catalog" class="btn-primary bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 text-lg">
            Перейти в каталог
          </NuxtLink>
          <NuxtLink to="/products" class="btn-secondary bg-transparent border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-3 text-lg">
            Все товары
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Преимущества -->
    <section class="mb-16">
      <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="text-center">
            <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <Icon name="heroicons:truck" class="h-8 w-8 text-blue-600" />
            </div>
            <h3 class="text-xl font-semibold mb-2">Быстрая доставка</h3>
            <p class="text-gray-600">Доставляем заказы по всей России в кратчайшие сроки</p>
          </div>
          
          <div class="text-center">
            <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <Icon name="heroicons:shield-check" class="h-8 w-8 text-green-600" />
            </div>
            <h3 class="text-xl font-semibold mb-2">Гарантия качества</h3>
            <p class="text-gray-600">Все товары проходят строгий контроль качества</p>
          </div>
          
          <div class="text-center">
            <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
              <Icon name="heroicons:heart" class="h-8 w-8 text-purple-600" />
            </div>
            <h3 class="text-xl font-semibold mb-2">Поддержка 24/7</h3>
            <p class="text-gray-600">Наша команда всегда готова помочь вам</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Популярные категории -->
    <section class="mb-16">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-12">
          <h2 class="text-3xl font-bold">Популярные категории</h2>
          <NuxtLink to="/categories" class="text-blue-600 hover:text-blue-700 font-medium">
            Все категории →
          </NuxtLink>
        </div>

        <div v-if="pending" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="category in categories.data" :key="category.id" class="card animate-pulse">
            <div class="h-48 bg-gray-300">{{ category.name }}</div>
            <div class="p-4">
              <div class="h-4 bg-gray-300 rounded mb-2"></div>
              <div class="h-3 bg-gray-300 rounded w-2/3"></div>
            </div>
          </div>
        </div>
        
        <div v-else-if="error" class="text-center py-8">
          <p class="text-red-600">Ошибка загрузки категорий: {{ error.message }}</p>
        </div>
        
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <NuxtLink 
            v-for="category in categories.data" 
            :key="category.id" 
            :to="`/categories/${category.slug}`"
            class="card hover:shadow-lg transition-shadow group"
          >
            <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
              <Icon name="heroicons:squares-2x2" class="h-16 w-16 text-white" />
            </div>
            <div class="p-4">
              <h3 class="text-lg font-semibold group-hover:text-blue-600 transition-colors">
                {{ category.name }}
              </h3>
              <p class="text-gray-600 text-sm mt-1">
                {{ category.products_count }} товаров
              </p>
            </div>
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Популярные товары -->
    <section class="mb-16">
      <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
          <h2 class="text-3xl font-bold">Популярные товары</h2>
          <NuxtLink to="/catalog" class="text-blue-600 hover:text-blue-700 font-medium">
            Смотреть все →
          </NuxtLink>
        </div>
        
        <div v-if="productsPending" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="n in 4" :key="n" class="card animate-pulse">
            <div class="h-48 bg-gray-300"></div>
            <div class="p-4">
              <div class="h-4 bg-gray-300 rounded mb-2"></div>
              <div class="h-3 bg-gray-300 rounded w-1/2 mb-2"></div>
              <div class="h-6 bg-gray-300 rounded w-1/3"></div>
            </div>
          </div>
        </div>
        
        <div v-else-if="productsError" class="text-center py-8">
          <p class="text-red-600">Ошибка загрузки товаров: {{ productsError.message }}</p>
        </div>
      
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <ProductCard 
            v-for="product in products.data" 
            :key="product.id" 
            :product="product"
          />
        </div>
      </div>
    </section>

    <!-- CTA секция -->
    <section class="bg-gray-100 py-16">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Готовы начать покупки?</h2>
        <p class="text-xl text-gray-600 mb-8">
          Присоединяйтесь к тысячам довольных покупателей
        </p>
        <NuxtLink to="/catalog" class="btn-primary px-8 py-3 text-lg">
          Начать покупки
        </NuxtLink>
      </div>
    </section>
  </div>
</template>

<script setup>
// SEO
useHead({
  title: 'Главная - Nuxt Shop',
  meta: [
    { name: 'description', content: 'Интернет-магазин Nuxt Shop - лучшие товары по выгодным ценам с быстрой доставкой' }
  ]
})

const { getCategories, getProducts } = useApi()

// Загрузка категорий только на клиенте
const { data: categoriesData, pending, error } = await useLazyAsyncData('categories', async () => {
  const result = await getCategories()
  return result.data
}, {
  server: false
})

const categories = computed(() => {
  console.log('Categories data:', categoriesData.value)
  return categoriesData.value || []
})

// Загрузка популярных товаров только на клиенте
const { data: productsData, pending: productsPending, error: productsError } = await useLazyAsyncData('popular-products', async () => {
  const result = await getProducts({ limit: 8, popular: true })
  return result.data
}, {
  server: false
})

const products = computed(() => {
  console.log('Products data:', productsData.value)
  return productsData.value || []
})
</script>