<template>
  <div>
    <!-- Хлебные крошки -->
    <Breadcrumbs :breadcrumbs="[
      { name: 'Корзина' }
    ]" />
    
    <!-- Заголовок -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold mb-4">Корзина</h1>
      <nav class="text-sm text-gray-600">
        <NuxtLink to="/" class="hover:text-blue-600">Главная</NuxtLink>
        <span class="mx-2">/</span>
        <span>Корзина</span>
      </nav>
    </div>

    <!-- Пустая корзина -->
    <div v-if="cartStore.isEmpty" class="text-center py-16">
      <Icon name="heroicons:shopping-cart" class="h-24 w-24 text-gray-300 mx-auto mb-6" />
      <h2 class="text-2xl font-semibold text-gray-900 mb-4">Ваша корзина пуста</h2>
      <p class="text-gray-600 mb-8">Добавьте товары в корзину, чтобы оформить заказ</p>
      <NuxtLink to="/catalog" class="btn-primary px-8 py-3">
        Перейти в каталог
      </NuxtLink>
    </div>

    <!-- Корзина с товарами -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Список товаров -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-sm border">
          <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
              <h2 class="text-xl font-semibold">Товары в корзине</h2>
              <button 
                @click="clearCart" 
                class="text-red-600 hover:text-red-700 text-sm font-medium"
              >
                Очистить корзину
              </button>
            </div>
          </div>
          
          <div class="divide-y divide-gray-200">
            <div 
              v-for="item in cartStore.items" 
              :key="item.id + ':' + JSON.stringify(item.options || {})"
              class="p-6 flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-4"
            >
              <!-- Изображение товара -->
              <div class="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                <img 
                  v-if="item.image" 
                  :src="item.image" 
                  :alt="item.name"
                  class="w-full h-full object-cover"
                >
                <div v-else class="w-full h-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center">
                  <Icon name="heroicons:photo" class="h-8 w-8 text-gray-500" />
                </div>
              </div>
              
              <!-- Информация о товаре -->
              <div class="flex-1 min-w-0">
                <h3 class="text-lg font-medium text-gray-900 mb-1">
                  {{ item.name }}
                </h3>
                <p class="text-gray-600 text-sm">
                  Цена за единицу: {{ formatPrice(item.price) }} ₽
                </p>
                <div v-if="item.options" class="mt-2 text-xs text-gray-600 flex flex-wrap gap-2">
                  <span
                    v-for="(opt, name) in item.options"
                    :key="name"
                    class="px-2 py-1 rounded border border-gray-200 bg-gray-100 text-gray-700"
                  >
                    {{ name }}: {{ opt?.value ?? opt }}
                  </span>
                </div>
              </div>
              
              <!-- Количество -->
              <div class="flex items-center space-x-3">
                <button 
                  @click="updateQuantity(item.id, item.quantity - 1, item.options)"
                  class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50"
                  :disabled="item.quantity <= 1"
                >
                  <Icon name="heroicons:minus" class="h-4 w-4" />
                </button>
                
                <span class="w-12 text-center font-medium">{{ item.quantity }}</span>
                
                <button 
                  @click="updateQuantity(item.id, item.quantity + 1, item.options)"
                  class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50"
                >
                  <Icon name="heroicons:plus" class="h-4 w-4" />
                </button>
              </div>
              
              <!-- Общая стоимость -->
              <div class="text-right">
                <p class="text-lg font-semibold text-gray-900">
                  {{ formatPrice(item.price * item.quantity) }} ₽
                </p>
              </div>
              
              <!-- Кнопка удаления -->
              <button 
                @click="removeItem(item.id, item.options)"
                class="text-red-600 hover:text-red-700 p-2"
              >
                <Icon name="heroicons:trash" class="h-5 w-5" />
              </button>
            </div>
          </div>
        </div>
        
        <!-- Рекомендуемые товары -->
        <div class="mt-8">
          <h3 class="text-xl font-semibold mb-4">Рекомендуем также</h3>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <ProductCard 
              v-for="product in recommendedProducts" 
              :key="product.id" 
              :product="product"
            />
          </div>
        </div>
      </div>
      
      <!-- Сводка заказа -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-sm border p-6 sticky top-4">
          <h3 class="text-xl font-semibold mb-6">Сводка заказа</h3>
          
          <div class="space-y-4 mb-6">
            <div class="flex justify-between">
              <span class="text-gray-600">Товары ({{ cartStore.itemsCount }} шт.)</span>
              <span class="font-medium">{{ formatPrice(cartStore.totalPrice) }} ₽</span>
            </div>
            
            <div class="flex justify-between">
              <span class="text-gray-600">Доставка</span>
              <span class="font-medium text-green-600">
                {{ deliveryCost > 0 ? formatPrice(deliveryCost) + ' ₽' : 'Бесплатно' }}
              </span>
            </div>
            
            <div v-if="discount > 0" class="flex justify-between text-green-600">
              <span>Скидка</span>
              <span class="font-medium">-{{ formatPrice(discount) }} ₽</span>
            </div>
            
            <hr class="border-gray-200">
            
            <div class="flex justify-between text-lg font-semibold">
              <span>Итого</span>
              <span>{{ formatPrice(totalWithDelivery) }} ₽</span>
            </div>
          </div>
          
          <!-- Промокод -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Промокод</label>
            <div class="flex space-x-2">
              <input 
                v-model="promoCode"
                type="text" 
                placeholder="Введите промокод"
                class="flex-1 input-field"
              >
              <button 
                @click="applyPromoCode"
                :disabled="!promoCode || promoCodeApplied"
                class="btn-secondary px-4 disabled:opacity-50"
              >
                {{ promoCodeApplied ? '✓' : 'Применить' }}
              </button>
            </div>
            <p v-if="promoCodeError" class="text-red-600 text-sm mt-1">
              {{ promoCodeError }}
            </p>
            <p v-if="promoCodeApplied" class="text-green-600 text-sm mt-1">
              Промокод применен!
            </p>
          </div>
          
          <!-- Способ доставки -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Способ доставки</label>
            <div class="space-y-2">
              <label class="flex items-center">
                <input 
                  v-model="deliveryMethod" 
                  type="radio" 
                  value="pickup" 
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                >
                <span class="ml-2 text-sm">Самовывоз (бесплатно)</span>
              </label>
              <label class="flex items-center">
                <input 
                  v-model="deliveryMethod" 
                  type="radio" 
                  value="courier" 
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                >
                <span class="ml-2 text-sm">Курьерская доставка ({{ formatPrice(courierCost) }} ₽)</span>
              </label>
              <label class="flex items-center">
                <input 
                  v-model="deliveryMethod" 
                  type="radio" 
                  value="post" 
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                >
                <span class="ml-2 text-sm">Почта России ({{ formatPrice(postCost) }} ₽)</span>
              </label>
            </div>
          </div>
          
          <!-- Кнопка оформления заказа -->
          <NuxtLink to="/checkout" class="w-full btn-primary py-3 text-center block">
            Оформить заказ
          </NuxtLink>
          
          <!-- Продолжить покупки -->
          <NuxtLink to="/catalog" class="w-full btn-secondary py-3 text-center block mt-3">
            Продолжить покупки
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const cartStore = useCartStore()
const { getProducts } = useApi()

// SEO
useHead({
  title: 'Корзина - Nuxt Shop',
  meta: [
    { name: 'description', content: 'Корзина покупок в интернет-магазине Nuxt Shop' }
  ]
})

// Реактивные данные
const promoCode = ref('')
const promoCodeApplied = ref(false)
const promoCodeError = ref('')
const deliveryMethod = ref('pickup')
const discount = ref(0)

// Константы доставки
const courierCost = 300
const postCost = 200
const freeDeliveryThreshold = 3000

// Вычисляемые свойства
const deliveryCost = computed(() => {
  if (deliveryMethod.value === 'pickup') return 0
  if (cartStore.totalPrice >= freeDeliveryThreshold) return 0
  if (deliveryMethod.value === 'courier') return courierCost
  if (deliveryMethod.value === 'post') return postCost
  return 0
})

const totalWithDelivery = computed(() => {
  return cartStore.totalPrice + deliveryCost.value - discount.value
})

// Загрузка рекомендуемых товаров только на клиенте
const { data: recommendedData } = await useLazyAsyncData('recommended-products', async () => {
  const { data } = await getProducts({ limit: 4, recommended: true })
  return data
}, {
  server: false
})

const recommendedProducts = computed(() => recommendedData.value?.data || [])

// Методы
const formatPrice = (price) => {
  return new Intl.NumberFormat('ru-RU').format(price)
}

const updateQuantity = (productId, quantity) => {
  cartStore.updateQuantity(productId, quantity)
}

const removeItem = (productId) => {
  cartStore.removeItem(productId)
}

const clearCart = () => {
  if (confirm('Вы уверены, что хотите очистить корзину?')) {
    cartStore.clearCart()
  }
}

const applyPromoCode = async () => {
  if (!promoCode.value) return
  
  // Здесь должна быть логика проверки промокода через API
  // Пока что просто симуляция
  const validPromoCodes = {
    'SAVE10': 10, // 10% скидка
    'WELCOME': 500, // 500 рублей скидка
    'FREESHIP': 0 // бесплатная доставка
  }
  
  if (validPromoCodes.hasOwnProperty(promoCode.value.toUpperCase())) {
    const discountValue = validPromoCodes[promoCode.value.toUpperCase()]
    
    if (promoCode.value.toUpperCase() === 'SAVE10') {
      discount.value = Math.round(cartStore.totalPrice * 0.1)
    } else if (promoCode.value.toUpperCase() === 'FREESHIP') {
      // Логика бесплатной доставки
      deliveryMethod.value = 'pickup'
    } else {
      discount.value = discountValue
    }
    
    promoCodeApplied.value = true
    promoCodeError.value = ''
  } else {
    promoCodeError.value = 'Неверный промокод'
    promoCodeApplied.value = false
  }
}

// Загрузка корзины из localStorage при монтировании
onMounted(() => {
  cartStore.loadFromStorage()
})
</script>