<template>
  <footer class="bg-gray-900 text-white mt-16">
    <div class="container mx-auto px-4 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- О компании -->
        <div>
          <div class="flex items-center space-x-2 mb-4">
            <Icon name="heroicons:shopping-bag" class="h-8 w-8 text-blue-400" />
            <span class="text-xl font-bold">Nuxt Shop</span>
          </div>
          <p class="text-gray-400 mb-4">
            Современный интернет-магазин с широким ассортиментом товаров и быстрой доставкой.
          </p>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-white transition-colors">
              <Icon name="heroicons:envelope" class="h-5 w-5" />
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition-colors">
              <Icon name="heroicons:phone" class="h-5 w-5" />
            </a>
          </div>
        </div>

        <!-- Каталог -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Каталог</h3>
          <ul class="space-y-2">
            <li>
              <NuxtLink to="/catalog" class="text-gray-400 hover:text-white transition-colors">
                Каталог
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/products" class="text-gray-400 hover:text-white transition-colors">
                Все товары
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/categories" class="text-gray-400 hover:text-white transition-colors">
                Категории
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/catalog" class="text-gray-400 hover:text-white transition-colors">
                Новинки
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/catalog" class="text-gray-400 hover:text-white transition-colors">
                Популярные
              </NuxtLink>
            </li>
          </ul>
        </div>

        <!-- Информация -->
        <!-- <div>
          <h3 class="text-lg font-semibold mb-4">Информация</h3>
          <ul class="space-y-2">
            <li>
              <NuxtLink to="/about" class="text-gray-400 hover:text-white transition-colors">
                О нас
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/delivery" class="text-gray-400 hover:text-white transition-colors">
                Доставка
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/payment" class="text-gray-400 hover:text-white transition-colors">
                Оплата
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/returns" class="text-gray-400 hover:text-white transition-colors">
                Возврат
              </NuxtLink>
            </li>
          </ul>
        </div> -->

        <!-- Контакты -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Контакты</h3>
          <div class="space-y-3">
            <div class="flex items-center space-x-3">
              <Icon name="heroicons:map-pin" class="h-5 w-5 text-gray-400" />
              <span class="text-gray-400">г. Москва, ул. Примерная, 123</span>
            </div>
            <div class="flex items-center space-x-3">
              <Icon name="heroicons:phone" class="h-5 w-5 text-gray-400" />
              <span class="text-gray-400">+7 (999) 123-45-67</span>
            </div>
            <div class="flex items-center space-x-3">
              <Icon name="heroicons:envelope" class="h-5 w-5 text-gray-400" />
              <span class="text-gray-400">info@nuxtshop.ru</span>
            </div>
            <div class="flex items-center space-x-3">
              <Icon name="heroicons:clock" class="h-5 w-5 text-gray-400" />
              <span class="text-gray-400">Пн-Пт: 9:00-18:00</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Нижняя часть -->
      <div class="border-t border-gray-800 mt-8 pt-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <p class="text-gray-400 text-sm">
            © {{ currentYear }} Nuxt Shop. Все права защищены.
          </p>
          <div class="flex space-x-6 mt-4 md:mt-0">
            <!-- <NuxtLink to="/privacy" class="text-gray-400 hover:text-white text-sm transition-colors">
              Политика конфиденциальности
            </NuxtLink>
            <NuxtLink to="/terms" class="text-gray-400 hover:text-white text-sm transition-colors">
              Условия использования
            </NuxtLink> -->
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
const currentYear = new Date().getFullYear()
</script>