<template>
  <div class="card hover:shadow-lg transition-shadow group">
    <!-- Изображение товара -->
    <div class="relative h-48 bg-gray-200 overflow-hidden">
      <img 
        v-if="product.image" 
        :src="product.image.webp_thumb || product.image.thumb || product.image.url" 
        :alt="product.title || product.name"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      >
      <div v-else class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
        <Icon name="heroicons:photo" class="h-12 w-12 text-gray-500" />
      </div>
      
      <!-- Бейдж скидки -->
      <div v-if="product.discount" class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-medium">
        -{{ product.discount }}%
      </div>
      
      <!-- Кнопка избранного -->
      <button 
        @click.prevent="toggleFavorite"
        class="absolute top-2 right-2 p-2 bg-white rounded-full shadow-md hover:bg-gray-50 transition-colors"
        :class="{ 'text-red-500': isFavorite, 'text-gray-400': !isFavorite }"
      >
        <Icon name="heroicons:heart" class="h-5 w-5" :class="{ 'fill-current': isFavorite }" />
      </button>
    </div>
    
    <!-- Информация о товаре -->
    <div class="p-4">
      <h3 class="text-lg font-semibold mb-2 group-hover:text-blue-600 transition-colors line-clamp-2">
        {{ product.title || product.name }}
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
      
      <p v-if="product.description" class="text-gray-600 text-sm mb-3 line-clamp-2">
        {{ product.description }}
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
          ({{ product.reviews_count || 0 }})
        </span>
      </div>
      
      <!-- Цена -->
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-2">
          <span class="text-xl font-bold text-gray-900">
            {{ formatPrice(product.price) }} ₽
          </span>
          <span v-if="product.old_price" class="text-sm text-gray-500 line-through">
            {{ formatPrice(product.old_price) }} ₽
          </span>
        </div>
        
        <div v-if="product.stock_quantity !== undefined" class="text-sm">
          <span v-if="product.stock_quantity > 0" class="text-green-600">
            В наличии: {{ product.stock_quantity }}
          </span>
          <span v-else class="text-red-600">
            Нет в наличии
          </span>
        </div>
      </div>
      
      <!-- Кнопки действий -->
      <div class="flex space-x-2">
        <NuxtLink 
          :to="`/products/${product.slug}`"
          class="flex-1 btn-secondary text-center py-2 text-sm"
        >
          Подробнее
        </NuxtLink>
        
        <button 
          @click="addToCart"
          class="flex-1 btn-primary py-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <Icon name="heroicons:shopping-cart" class="h-4 w-4 inline mr-1" />
          В корзину
        </button>
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

const primaryImage = computed(() => {
  if (props.product.image) {
    return props.product.image;
  }
  if (props.product.images && props.product.images.length > 0) {
    return props.product.images[0];
  }
  return null;
});

const isFavorite = ref(false)

// Методы
const formatPrice = (price) => {
  return new Intl.NumberFormat('ru-RU').format(price || 0)
}

const addToCart = () => {
  cartStore.addItem(props.product)
  
  // Показываем уведомление (можно добавить toast)
  console.log(`Товар "${props.product.title || props.product.name}" добавлен в корзину`)
}

const toggleFavorite = () => {
  isFavorite.value = !isFavorite.value
  // Здесь можно добавить логику сохранения в избранное
  console.log(`Товар ${isFavorite.value ? 'добавлен в' : 'удален из'} избранное`)
}

// Загрузка состояния избранного при монтировании
onMounted(() => {
  // Здесь можно загрузить состояние избранного из localStorage или API
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
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>