<template>
  <header class="bg-white shadow-sm border-b">
    <div class="container mx-auto px-4">
      <div class="flex items-center justify-between h-16">
        <!-- Логотип -->
        <NuxtLink to="/" class="flex items-center space-x-2">
          <Icon name="heroicons:shopping-bag" class="h-8 w-8 text-blue-600" />
          <span class="text-xl font-bold text-gray-900">Nuxt Shop</span>
        </NuxtLink>

        <!-- Навигация -->
        <nav class="hidden md:flex items-center space-x-8">
          <NuxtLink to="/" class="text-gray-700 hover:text-blue-600 transition-colors">
            Главная
          </NuxtLink>
          <NuxtLink to="/catalog" class="text-gray-700 hover:text-blue-600 transition-colors">
            Каталог
          </NuxtLink>
          <NuxtLink to="/categories" class="text-gray-700 hover:text-blue-600 transition-colors">
            Категории
          </NuxtLink>
          <NuxtLink to="/products" class="text-gray-700 hover:text-blue-600 transition-colors">
            Товары
          </NuxtLink>
          <!-- <NuxtLink to="/about" class="text-gray-700 hover:text-blue-600 transition-colors">
            О нас
          </NuxtLink>
          <NuxtLink to="/contact" class="text-gray-700 hover:text-blue-600 transition-colors">
            Контакты
          </NuxtLink> -->
        </nav>

        <!-- Корзина и пользователь -->
        <div class="flex items-center space-x-4">
          <!-- Поиск -->
          <div class="hidden md:block relative">
            <input 
              type="text" 
              placeholder="Поиск товаров..."
              class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <Icon name="heroicons:magnifying-glass" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
          </div>

          <!-- Корзина -->
          <NuxtLink to="/cart" class="relative p-2 text-gray-700 hover:text-blue-600 transition-colors">
            <Icon name="heroicons:shopping-cart" class="h-6 w-6" />
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ cartStore.itemsCount }}
            </span>
          </NuxtLink>

          <!-- Пользователь -->
          <div class="relative">
            <button @click="toggleUserMenu" class="p-2 text-gray-700 hover:text-blue-600 transition-colors">
              <Icon name="heroicons:user" class="h-6 w-6" />
            </button>
            
            <!-- Выпадающее меню пользователя -->
            <div v-if="showUserMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
              <NuxtLink to="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Профиль
              </NuxtLink>
              <NuxtLink to="/orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Заказы
              </NuxtLink>
              <hr class="my-1">
              <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                Выйти
              </button>
            </div>
          </div>

          <!-- Мобильное меню -->
          <button @click="toggleMobileMenu" class="md:hidden p-2 text-gray-700">
            <Icon name="heroicons:bars-3" class="h-6 w-6" />
          </button>
        </div>
      </div>

      <!-- Мобильная навигация -->
      <div v-if="showMobileMenu" class="md:hidden border-t border-gray-200 py-4">
        <nav class="flex flex-col space-y-2">
          <NuxtLink to="/" class="text-gray-700 hover:text-blue-600 py-2">
            Главная
          </NuxtLink>
          <NuxtLink to="/catalog" class="text-gray-700 hover:text-blue-600 py-2">
            Каталог
          </NuxtLink>
          <NuxtLink to="/categories" class="text-gray-700 hover:text-blue-600 py-2">
            Категории
          </NuxtLink>
          <NuxtLink to="/products" class="text-gray-700 hover:text-blue-600 py-2">
            Товары
          </NuxtLink>
          <NuxtLink to="/about" class="text-gray-700 hover:text-blue-600 py-2">
            О нас
          </NuxtLink>
          <NuxtLink to="/contact" class="text-gray-700 hover:text-blue-600 py-2">
            Контакты
          </NuxtLink>
        </nav>
        
        <!-- Мобильный поиск -->
        <div class="mt-4 relative">
          <input 
            type="text" 
            placeholder="Поиск товаров..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
          <Icon name="heroicons:magnifying-glass" class="absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400" />
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
const cartStore = useCartStore()

const showUserMenu = ref(false)
const showMobileMenu = ref(false)

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const toggleMobileMenu = () => {
  showMobileMenu.value = !showMobileMenu.value
}

// Закрытие меню при клике вне его
onMounted(() => {
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
      showUserMenu.value = false
    }
  })
})
</script>