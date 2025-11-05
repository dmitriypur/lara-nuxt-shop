<template>
  <div class="card flex flex-col md:flex-row hover:shadow-lg transition-shadow">
    <!-- Изображение товара -->
    <div class="relative w-full md:w-48 h-48 bg-gray-200 overflow-hidden flex-shrink-0">
      <img 
        v-if="primaryImage" 
        :src="primaryImage.webp_thumb || primaryImage.thumb || primaryImage.url" 
        :alt="product.title || product.name"
        class="w-full h-full object-cover"
      >
      <div v-else class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
        <Icon name="heroicons:photo" class="h-12 w-12 text-gray-500" />
      </div>
      
      <!-- Бейдж скидки -->
      <div v-if="product.discount" class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-medium">
        -{{ product.discount }}%
      </div>
    </div>
    
    <!-- Информация о товаре -->
    <div class="flex-1 p-6">
      <div class="flex flex-col md:flex-row md:justify-between h-full">
        <!-- Основная информация -->
        <div class="flex-1 mb-4 md:mb-0 md:mr-6">
          <h3 class="text-xl font-semibold mb-2 hover:text-blue-600 transition-colors">
            <NuxtLink :to="`/products/${product.slug}`">
              {{ product.title || product.name }}
            </NuxtLink>
          </h3>
          
          <!-- Категория -->
          <div v-if="product.category" class="mb-2">
            <NuxtLink 
              :to="`/categories/${product.category.slug}`"
              class="inline-block bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded hover:bg-blue-200 transition-colors"
            >
              {{ product.category.name }}
            </NuxtLink>
          </div>
          
          <p v-if="product.description" v-html="product.description" class="text-gray-600 mb-3 line-clamp-3">
          </p>
          
          <!-- Рейтинг -->
          <div v-if="product.rating" class="flex items-center mb-3">
            <div class="flex items-center">
              <Icon 
                v-for="star in 5" 
                :key="star"
                name="heroicons:star" 
                class="h-4 w-4"
                :class="star <= product.rating ? 'text-yellow-400 fill-current' : 'text-gray-300'"
              />
            </div>
            <span class="text-sm text-gray-600 ml-2">
              {{ product.rating }} ({{ product.reviews_count || 0 }} отзывов)
            </span>
          </div>
          
          <!-- Характеристики -->
          <div v-if="product.features" class="flex flex-wrap gap-2 mb-3">
            <span 
              v-for="feature in product.features.slice(0, 3)" 
              :key="feature"
              class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-sm"
            >
              {{ feature }}
            </span>
          </div>
        </div>
        
        <!-- Цена и действия -->
        <div class="flex flex-col justify-between md:w-48">
          <!-- Наличие -->
          <div class="mb-3">
            <span v-if="product.stock_quantity > 0" class="text-green-600 text-sm font-medium">
              ✓ В наличии: {{ product.stock_quantity }} шт.
            </span>
            <span v-else class="text-red-600 text-sm font-medium">
              ✗ Нет в наличии
            </span>
          </div>
          
          <!-- Цена -->
          <div class="mb-4">
            <div class="flex items-center space-x-2 mb-1">
              <span class="text-2xl font-bold text-gray-900">
                {{ formatPrice(currentPrice) }} ₽
              </span>
            </div>
            <div v-if="product.discount" class="flex items-center space-x-2">
              <span class="text-lg text-gray-500 line-through">
                {{ formatPrice(product.price) }} ₽
              </span>
              <span class="text-green-600 font-medium">
                Экономия: {{ formatPrice(product.price - currentPrice) }} ₽
              </span>
            </div>
          </div>
          
          <!-- Кнопки действий -->
          <div class="space-y-2">
            <button 
              @click="addToCart"
              :disabled="!product.stock_quantity || product.stock_quantity <= 0"
              class="w-full btn-primary py-3 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Icon name="heroicons:shopping-cart" class="h-5 w-5 inline mr-2" />
              В корзину
            </button>
            
            <div class="flex space-x-2">
              <NuxtLink 
                :to="`/products/${product.slug}`"
                class="flex-1 btn-secondary text-center py-2"
              >
                Подробнее
              </NuxtLink>
              
              <button 
                @click="toggleFavorite"
                class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                :class="{ 'text-red-500 border-red-300 bg-red-50': isFavorite, 'text-gray-400': !isFavorite }"
              >
                <Icon name="heroicons:heart" class="h-5 w-5" :class="{ 'fill-current': isFavorite }" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const cartStore = useCartStore()

// Вычисляемые свойства
const currentPrice = computed(() => {
  if (props.product.discount) {
    return props.product.price * (1 - props.product.discount / 100)
  }
  return props.product.price
})

const primaryImage = computed(() => {
  return props.product.images && props.product.images.length > 0 
    ? props.product.images[0] 
    : null
})

const isFavorite = ref(false)

// Методы
const formatPrice = (price) => {
  return new Intl.NumberFormat('ru-RU').format(price)
}

const addToCart = () => {
  cartStore.addItem(props.product)
  console.log(`Товар "${props.product.title || props.product.name}" добавлен в корзину`)
}

const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value
  console.log(`Товар ${isFavorite.value ? 'добавлен в' : 'удален из'} избранное`)
}

// Загрузка состояния избранного при монтировании
onMounted(() => {
  if (process.client) {
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]')
    isFavorite.value = favorites.includes(props.product.id)
  }
})

// Сохранение состояния избранного
watch(isFavorite, (newValue) => {
  if (process.client) {
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]')
    if (newValue) {
      if (!favorites.includes(props.product.id)) {
        favorites.push(props.product.id)
      }
    } else {
      const index = favorites.indexOf(props.product.id)
      if (index > -1) {
        favorites.splice(index, 1)
      }
    }
    localStorage.setItem('favorites', JSON.stringify(favorites))
  }
})
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>