<template>
  <div>
    <!-- Хлебные крошки -->
    <Breadcrumbs :breadcrumbs="breadcrumbs" />
    <!-- Основная информация о товаре -->
    <div v-if="pending" class="animate-pulse">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="h-96 bg-gray-300 rounded-lg"></div>
        <div class="space-y-4">
          <div class="h-8 bg-gray-300 rounded w-3/4"></div>
          <div class="h-6 bg-gray-300 rounded w-1/2"></div>
          <div class="h-4 bg-gray-300 rounded w-full"></div>
          <div class="h-4 bg-gray-300 rounded w-2/3"></div>
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
    
    <div v-else-if="product" class="grid grid-cols-1 lg:grid-cols-2 gap-8">

      <!-- Галерея изображений -->
      <div class="space-y-4">
        <!-- Главное изображение -->
        <div class="relative">
          <img 
            :src="selectedImageUrl || primaryImageUrl || '/images/placeholder.jpg'"
            :alt="product.title"
            class="w-full h-96 object-cover rounded-lg shadow-lg"
          >
          <!-- Бейдж скидки -->
          <div v-if="product.old_price && product.old_price > product.price" 
               class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
            -{{ Math.round(((product.old_price - product.price) / product.old_price) * 100) }}%
          </div>
        </div>
        
        <!-- Миниатюры -->
        <div v-if="product.images && product.images.length > 1" class="flex space-x-2 overflow-x-auto">
          <button 
            v-for="(image, index) in product.images" 
            :key="image.id"
            @click="selectedImage = image"
            :class="selectedImage?.id === image.id ? 'ring-2 ring-blue-500' : 'ring-1 ring-gray-200'"
            class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden"
          >
            <img 
              :src="image.webp_thumb || image.thumb || image.url" 
              :alt="`${product.title} - изображение ${index + 1}`"
              class="w-full h-full object-cover"
            >
          </button>
        </div>
      </div>
      
      <!-- Информация о товаре -->
      <div class="space-y-6">
        <!-- Заголовок и категория -->
        <div>
          <div v-if="product.categories && product.categories.length > 0" class="mb-2">
            <NuxtLink 
              :to="`/categories/${product.categories[0].slug}`"
              class="inline-block bg-blue-100 text-blue-700 text-sm px-3 py-1 rounded-full hover:bg-blue-200 transition-colors"
            >
              {{ product.categories[0].name }}
            </NuxtLink>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ product.title }}</h1>
          <p v-if="product.sku" class="text-sm text-gray-500">Артикул: {{ product.sku }}</p>
        </div>
        
        <!-- Цена -->
        <div class="flex items-center space-x-4">
          <span class="text-3xl font-bold text-gray-900">
            {{ formatPrice(product.price) }} ₽
          </span>
          <span v-if="product.old_price && product.old_price > product.price" 
                class="text-xl text-gray-500 line-through">
            {{ formatPrice(product.old_price) }} ₽
          </span>
        </div>
        
        <!-- Краткое описание -->
        <div v-if="product.description">
          <p class="text-gray-600 leading-relaxed">{{ product.description }}</p>
        </div>
        
        <!-- Варианты товара -->
        <div v-if="product.variants && product.variants.length > 0" class="space-y-4">
          <h3 class="text-lg font-semibold text-gray-900">Варианты товара</h3>
          <div class="grid grid-cols-2 gap-3">
            <div 
              v-for="variant in product.variants" 
              :key="variant.id"
              @click="selectVariant(variant)"
              :class="selectedVariant?.id === variant.id ? 'ring-2 ring-blue-500 bg-blue-50' : 'ring-1 ring-gray-200'"
              class="p-3 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
            >
              <div class="text-sm font-medium text-gray-900">{{ variant.sku }}</div>
              <div class="text-sm text-gray-600">{{ formatPrice(variant.price) }} ₽</div>
              <div class="text-xs text-gray-500">
                В наличии: {{ variant.stock_quantity }}
              </div>
            </div>
          </div>
        </div>
        
        <!-- Количество и добавление в корзину -->
        <div class="space-y-4">
          <div class="flex items-center space-x-4">
            <label class="text-sm font-medium text-gray-700">Количество:</label>
            <div class="flex items-center border border-gray-300 rounded-lg">
              <button 
                @click="decreaseQuantity"
                :disabled="quantity <= 1"
                class="px-3 py-2 text-gray-600 hover:text-gray-800 disabled:opacity-50"
              >
                <Icon name="heroicons:minus" class="h-4 w-4" />
              </button>
              <input 
                v-model.number="quantity"
                type="number"
                min="1"
                :max="maxQuantity"
                class="w-16 text-center border-0 focus:ring-0"
              >
              <button 
                @click="increaseQuantity"
                :disabled="quantity >= maxQuantity"
                class="px-3 py-2 text-gray-600 hover:text-gray-800 disabled:opacity-50"
              >
                <Icon name="heroicons:plus" class="h-4 w-4" />
              </button>
            </div>
            <span class="text-sm text-gray-500">
              Доступно: {{ maxQuantity }}
            </span>
          </div>
          
          <div class="flex space-x-3">
            <button 
              @click="addToCart"
              :disabled="!canAddToCart"
              class="flex-1 btn-primary py-3 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Icon name="heroicons:shopping-cart" class="h-5 w-5 inline mr-2" />
              {{ canAddToCart ? 'Добавить в корзину' : 'Нет в наличии' }}
            </button>
            
            <button 
              @click="toggleFavorite"
              :class="isFavorite ? 'text-red-500 bg-red-50 border-red-300' : 'text-gray-400 border-gray-300'"
              class="p-3 border rounded-lg hover:bg-gray-50 transition-colors"
            >
              <Icon name="heroicons:heart" class="h-5 w-5" :class="{ 'fill-current': isFavorite }" />
            </button>
          </div>
        </div>
        
        <!-- Характеристики -->
        <div v-if="product.attributes && product.attributes.length > 0" class="space-y-3">
          <h3 class="text-lg font-semibold text-gray-900">Характеристики</h3>
          <div class="grid grid-cols-1 gap-2">
            <div 
              v-for="attr in product.attributes" 
              :key="attr.id"
              class="flex justify-between py-2 border-b border-gray-100"
            >
              <span class="text-gray-600">{{ attr.name }}:</span>
              <span class="text-gray-900 font-medium">{{ attr.value }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Подробное описание -->
    <div v-if="product && product.description" class="mt-12">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Описание товара</h2>
      <div class="prose max-w-none" v-html="product.description"></div>
    </div>
    
    <!-- Похожие товары -->
    <div v-if="relatedProducts && relatedProducts.length > 0" class="mt-16">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">Похожие товары</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <ProductCard 
          v-for="relatedProduct in relatedProducts" 
          :key="relatedProduct.id" 
          :product="relatedProduct"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
const { getProduct } = useApi()
const route = useRoute()
const cartStore = useCartStore()

const productSlug = route.params.slug

// Реактивные данные
const selectedImage = ref(null)
const selectedVariant = ref(null)
const quantity = ref(1)
const isFavorite = ref(false)

// Загрузка товара
const { data: productData, pending, error, refresh } = await useLazyAsyncData(`product-${productSlug}`, async () => {
  const { data, error: apiError } = await getProduct(productSlug)
  if (apiError) throw apiError
  return data
}, {
  server: false
})

const product = computed(() => productData.value)

// Хлебные крошки
const breadcrumbs = computed(() => {
  if (!product.value) return []
  
  const crumbs = []
  
  if (product.value.categories && product.value.categories.length > 0) {
    crumbs.push({
      name: product.value.categories[0].name,
      link: `/categories/${product.value.categories[0].slug}`
    })
  }
  
  crumbs.push({ name: product.value.title })
  
  return crumbs
})

// SEO
useHead(() => ({
  title: product.value ? `${product.value.title} - Nuxt Shop` : 'Товар - Nuxt Shop',
  meta: [
    { 
      name: 'description', 
      content: product.value?.description || product.value?.description || 'Описание товара' 
    }
  ]
}))

// Вычисляемые свойства
const primaryImageUrl = computed(() => {
  if (product.value?.images && product.value.images.length > 0) {
    const primaryImage = product.value.images[0]
    return primaryImage.webp_large || primaryImage.large || primaryImage.url
  }
  return null
})

const selectedImageUrl = computed(() => {
  if (selectedImage.value) {
    return selectedImage.value.webp_large || selectedImage.value.large || selectedImage.value.url
  }
  return null
})

const maxQuantity = computed(() => {
  if (selectedVariant.value) {
    return selectedVariant.value.stock_quantity || 0
  }
  return product.value?.stock_quantity || 0
})

const canAddToCart = computed(() => {
  return maxQuantity.value > 0
})

const relatedProducts = computed(() => {
  // Здесь можно добавить логику для получения похожих товаров
  return []
})

// Методы
const formatPrice = (price) => {
  return new Intl.NumberFormat('ru-RU').format(price)
}

const selectVariant = (variant) => {
  selectedVariant.value = variant
  quantity.value = 1
}

const increaseQuantity = () => {
  if (quantity.value < maxQuantity.value) {
    quantity.value++
  }
}

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--
  }
}

const addToCart = () => {
  if (!canAddToCart.value) return
  
  const itemToAdd = {
    ...product.value,
    variant: selectedVariant.value,
    quantity: quantity.value
  }
  
  cartStore.addItem(itemToAdd)
  
  // Показываем уведомление
  console.log(`Товар "${product.value.title}" добавлен в корзину`)
}

const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value
  // Здесь можно добавить логику сохранения в избранное
  console.log(`Товар ${isFavorite.value ? 'добавлен в' : 'удален из'} избранное`)
}

// Инициализация
onMounted(() => {
  if (product.value) {
    // Устанавливаем первое изображение как выбранное
    if (product.value.images && product.value.images.length > 0) {
      selectedImage.value = product.value.images[0]
    }
    
    // Устанавливаем первый вариант как выбранный
    if (product.value.variants && product.value.variants.length > 0) {
      selectedVariant.value = product.value.variants[0]
    }
    
    // Загружаем состояние избранного
    if (process.client) {
      const favorites = JSON.parse(localStorage.getItem('favorites') || '[]')
      isFavorite.value = favorites.includes(product.value.id)
    }
  }
})

// Следим за изменениями продукта
watch(product, (newProduct) => {
  if (newProduct && newProduct.images && newProduct.images.length > 0) {
    selectedImage.value = newProduct.images[0]
  }
}, { immediate: true })

// Сохранение состояния избранного
watch(isFavorite, (newValue) => {
  if (process.client && product.value) {
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]')
    if (newValue) {
      if (!favorites.includes(product.value.id)) {
        favorites.push(product.value.id)
      }
    } else {
      const index = favorites.indexOf(product.value.id)
      if (index > -1) {
        favorites.splice(index, 1)
      }
    }
    localStorage.setItem('favorites', JSON.stringify(favorites))
  }
})
</script>
