<template>
  <div>
    <!-- Хлебные крошки -->
    <Breadcrumbs :breadcrumbs="breadcrumbs" />

    <!-- Индикатор загрузки -->
    <div v-if="pending" class="animate-pulse">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="h-96 bg-gray-300 rounded-lg"></div>
        <div class="space-y-4">
          <div class="h-8 bg-gray-300 rounded w-3/4"></div>
          <div class="h-6 bg-gray-300 rounded w-1/2"></div>
          <div class="h-10 bg-gray-300 rounded w-full mt-6"></div>
        </div>
      </div>
    </div>

    <!-- Сообщение об ошибке -->
    <div v-else-if="error" class="text-center py-12">
      <Icon name="heroicons:exclamation-triangle" class="h-12 w-12 text-red-500 mx-auto mb-4" />
      <h3 class="text-lg font-semibold text-gray-900 mb-2">Ошибка загрузки товара</h3>
      <p class="text-gray-600 mb-4">Не удалось загрузить информацию о товаре. Пожалуйста, попробуйте еще раз.</p>
      <button @click="refresh()" class="btn-primary">
        Попробовать снова
      </button>
    </div>

    <!-- Основной контент страницы товара -->
    <div v-if="product" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Галерея изображений -->
      <div class="space-y-4">
        <div class="relative">
          <img 
            :src="selectedImage?.url || primaryImageUrl || '/images/placeholder.jpg'"
            :alt="product.title"
            class="w-full h-96 object-cover rounded-lg shadow-lg"
          >
          <div v-if="product.old_price && product.old_price > product.price" 
               class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
            -{{ Math.round(((product.old_price - product.price) / product.old_price) * 100) }}%
          </div>
        </div>
        <div v-if="product.images && product.images.length > 1" class="flex space-x-2 overflow-x-auto pb-2">
          <button 
            v-for="image in product.images" 
            :key="image.id"
            @click="selectedImage = image"
            :class="selectedImage?.id === image.id ? 'ring-2 ring-blue-500' : 'ring-1 ring-gray-200'"
            class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden focus:outline-none"
          >
            <img 
              :src="image.thumb"
              :alt="`${product.title} - изображение`"
              class="w-full h-full object-cover"
            >
          </button>
        </div>
      </div>

      <!-- Информация о товаре -->
      <div class="space-y-6">
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
        
        <div class="flex items-center space-x-4">
          <span class="text-3xl font-bold text-gray-900">{{ formatPrice(product.price) }} ₽</span>
          <span v-if="product.old_price && product.old_price > product.price" class="text-xl text-gray-500 line-through">
            {{ formatPrice(product.old_price) }} ₽
          </span>
        </div>

        <!-- Варианты -->
        <div v-if="variantsToShow && variantsToShow.length > 0" class="space-y-4">
          <h3 class="text-lg font-semibold text-gray-900">Варианты</h3>
          <div class="flex flex-wrap gap-3">
            <NuxtLink
              v-for="variant in variantsToShow"
              :key="variant.id"
              :to="`/products/${variant.slug}`"
              :class="product.id === variant.id ? 'ring-2 ring-blue-500 bg-blue-50' : 'ring-1 ring-gray-200'"
              class="p-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2 text-decoration-none"
            >
              <template v-if="getVariantThumb(variant)">
                <img 
                  :src="getVariantThumb(variant)"
                  :alt="variant.title"
                  class="w-10 h-10 object-cover rounded-md"
                >
              </template>
              <template v-else>
                <div class="w-10 h-10 rounded-md bg-gray-100 flex items-center justify-center text-gray-400">
                  <Icon name="heroicons:photo" class="w-6 h-6" />
                </div>
              </template>
              <span class="text-sm font-medium text-gray-800">{{ variant.title }}</span>
            </NuxtLink>
          </div>
        </div>

        <div class="flex items-center space-x-4 pt-4">
          <button @click="addToCart" class="btn-primary flex-1">
            <Icon name="heroicons:shopping-cart" class="h-5 w-5 inline mr-2" />
            Добавить в корзину
          </button>
        </div>
      </div>
    </div>

    <!-- Описание и похожие товары -->
    <div class="mt-12">
      <div v-if="product && product.description" class="prose max-w-none mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Описание товара</h2>
        <div v-html="product.description"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useCartStore } from '@/stores/cart';

const route = useRoute();
const cartStore = useCartStore();

// Используем единый композабл для API с базовым URL из runtimeConfig
const { getProduct, apiCall } = useApi()

// Загружаем товар по slug и разворачиваем ответ { data: { ... } } -> объект товара
const { data: productData, pending, error, refresh } = await useLazyAsyncData(
  `product-${route.params.slug}`,
  async () => {
    const { data, error } = await getProduct(route.params.slug)
    if (error) throw error
    return data
  },
  { server: false, watch: [() => route.params.slug] }
)

// Приводим к удобному виду
const product = computed(() => productData.value?.data || null)

// Группа связанных товаров (родитель + его варианты) из backend-эндпоинта
const { data: relatedData } = await useLazyAsyncData(
  `related-products-${route.params.slug}`,
  async () => {
    const slug = product.value?.slug || route.params.slug
    if (!slug) return { data: [] }
    const { data } = await apiCall(`/v1/products/${slug}/related`)
    return data
  },
  { server: false, watch: [product, () => route.params.slug] }
)

const variantsToShow = computed(() => {
  const currentId = product.value?.id
  const group = relatedData.value?.data || []
  return group.filter((v) => v.id !== currentId)
})

const getVariantThumb = (v) => v?.images?.[0]?.thumb || v?.images?.[0]?.url || null

const selectedImage = ref(null);

// Инициализация и обновление выбранного изображения
watch(
  product,
  (newProduct) => {
    if (newProduct?.images?.[0]) {
      selectedImage.value = newProduct.images[0];
    } else {
      selectedImage.value = null;
    }
  },
  { immediate: true }
);

const primaryImageUrl = computed(() => product.value?.images?.[0]?.url || null);

const breadcrumbs = computed(() => {
  if (!product.value) return [];
  const crumbs = [{ name: 'Каталог', path: '/products' }];
  if (product.value.categories?.[0]) {
    crumbs.push({ 
      name: product.value.categories[0].name, 
      path: `/categories/${product.value.categories[0].slug}` 
    });
  }
  crumbs.push({ name: product.value.title, path: '' });
  return crumbs;
});

const formatPrice = (price) => new Intl.NumberFormat('ru-RU').format(price);

const addToCart = () => {
  if (product.value) {
    cartStore.addItem({
      id: product.value.id,
      title: product.value.title,
      price: product.value.price,
      image: primaryImageUrl.value,
      quantity: 1,
    });
    alert('Товар добавлен в корзину!');
  }
};
</script>
