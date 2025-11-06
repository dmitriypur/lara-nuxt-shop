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
        <div v-if="relatedProducts && relatedProducts.length > 1" class="space-y-4">
          <h3 class="text-lg font-semibold text-gray-900">Варианты</h3>
          <div class="flex flex-wrap gap-3">
            <NuxtLink
              v-for="related in relatedProducts"
              :key="related.id"
              :to="`/products/${related.slug}`"
              :class="product.id === related.id ? 'ring-2 ring-blue-500 bg-blue-50' : 'ring-1 ring-gray-200'"
              class="p-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2 text-decoration-none"
            >
              <img 
                v-if="related.images && related.images.length > 0"
                :src="related.images[0].thumb"
                :alt="related.title"
                class="w-10 h-10 object-cover rounded-md"
              >
              <span class="text-sm font-medium text-gray-800">{{ related.title }}</span>
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

const { data: product, pending, error, refresh } = useAsyncData(
  `product-${route.params.slug}`,
  () => $fetch(`/api/products/${route.params.slug}`),
  { watch: [() => route.params.slug] }
);

const { data: relatedProducts } = useAsyncData(
  `related-products-${route.params.slug}`,
  () => {
    if (!product.value?.id) return Promise.resolve([]);
    return $fetch(`/api/products/${product.value.id}/related`);
  },
  { watch: [product] }
);

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
