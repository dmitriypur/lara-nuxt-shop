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
    <div v-if="currentProduct" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
      <!-- Галерея изображений -->
      <div class="space-y-4">
        <div class="relative">
          <img 
            :src="selectedImage?.url || primaryImageUrl || '/images/placeholder.jpg'"
            :alt="currentProduct.title"
            class="w-full h-96 object-cover rounded-lg shadow-lg"
          >
          <div v-if="currentProduct.old_price && currentProduct.old_price > currentProduct.price" 
               class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
            -{{ Math.round(((currentProduct.old_price - currentProduct.price) / currentProduct.old_price) * 100) }}%
          </div>
        </div>
        <div v-if="currentProduct.images && currentProduct.images.length > 1" class="flex space-x-2 overflow-x-auto pb-2">
          <button 
            v-for="image in currentProduct.images" 
            :key="image.id"
            @click="selectedImage = image"
            :class="selectedImage?.id === image.id ? 'ring-2 ring-blue-500' : 'ring-1 ring-gray-200'"
            class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden focus:outline-none"
          >
            <img 
              :src="image.thumb"
              :alt="`${currentProduct.title} - изображение`"
              class="w-full h-full object-cover"
            >
          </button>
        </div>
      </div>

      <!-- Информация о товаре -->
      <div class="space-y-6">
        <div>
          <div v-if="currentProduct.categories && currentProduct.categories.length > 0" class="mb-2">
            <NuxtLink 
              :to="`/categories/${currentProduct.categories[0].slug}`"
              class="inline-block bg-blue-100 text-blue-700 text-sm px-3 py-1 rounded-full hover:bg-blue-200 transition-colors"
            >
              {{ currentProduct.categories[0].name }}
            </NuxtLink>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ currentProduct.title }}</h1>
          <p v-if="currentProduct.sku" class="text-sm text-gray-500">Артикул: {{ currentProduct.sku }}</p>
        </div>
        
        <div class="flex items-center space-x-4">
          <span class="text-3xl font-bold text-gray-900">{{ formatPrice(currentProduct.price) }} ₽</span>
          <span v-if="currentProduct.old_price && currentProduct.old_price > currentProduct.price" class="text-xl text-gray-500 line-through">
            {{ formatPrice(currentProduct.old_price) }} ₽
          </span>
        </div>

        <!-- Варианты (ссылки), оставляем как было -->
        <div v-if="variantsToShow && variantsToShow.length > 0" class="space-y-4">
          <h3 class="text-lg font-semibold text-gray-900">Варианты</h3>
          <div class="flex flex-wrap gap-3">
            <NuxtLink
              v-for="variant in variantsToShow"
              :key="variant.id"
              :to="`/products/${variant.slug}`"
              :class="currentProduct.id === variant.id ? 'ring-2 ring-blue-500 bg-blue-50' : 'ring-1 ring-gray-200'"
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
        <!-- Атрибуты (характеристики) товара: выбор опций -->
        <div v-if="attributesGroups && Object.keys(attributesGroups).length" class="space-y-6">
          <div v-for="(values, attrName) in attributesGroups" :key="attrName">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ attrName }}</h3>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="val in values"
                :key="val.id"
                @click="selectAttribute(attrName, val)"
                :class="[
                  'px-3 py-2 rounded-md border text-sm transition-colors',
                  isSelected(attrName, val)
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'bg-white text-gray-800 border-gray-300 hover:bg-gray-50'
                ]"
              >
                {{ val.value }}
              </button>
            </div>
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
      <div v-if="currentProduct && currentProduct.description" class="prose max-w-none mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Описание товара</h2>
        <div v-html="currentProduct.description"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, reactive } from 'vue';
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

// Текущий выбранный вариант/товар
const selectedVariant = ref(null)
const currentProduct = computed(() => selectedVariant.value || product.value)

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

// Варианты/опции товара (родитель + его активные варианты)
const productOptions = computed(() => relatedData.value?.data || [])

// Список вариантов для отображения (без текущего товара)
const variantsToShow = computed(() => {
  const group = productOptions.value || []
  const currentId = currentProduct.value?.id
  return group.filter(v => v.id !== currentId)
})

// Группы атрибутов базового товара (варианты выбора)
// Упрощаем: бекенд даёт attributes.display (selected-группы, либо base-группы как фолбэк)
const attributesGroups = computed(() => currentProduct.value?.attributes?.display || {})


const getVariantThumb = (v) => v?.images?.[0]?.thumb || v?.images?.[0]?.url || null

const selectedImage = ref(null);

// Инициализация и обновление выбранного изображения
// Следим за текущим продуктом (с учетом выбранного варианта) и обновляем выбранное изображение
watch(
  currentProduct,
  (newProduct) => {
    if (newProduct?.images?.[0]) {
      selectedImage.value = newProduct.images[0];
    } else {
      selectedImage.value = null;
    }
  },
  { immediate: true }
);

const primaryImageUrl = computed(() => currentProduct.value?.images?.[0]?.url || null);

const breadcrumbs = computed(() => {
  if (!currentProduct.value) return [];
  const crumbs = [{ name: 'Каталог', path: '/products' }];
  if (currentProduct.value.categories?.[0]) {
    crumbs.push({ 
      name: currentProduct.value.categories[0].name, 
      path: `/categories/${currentProduct.value.categories[0].slug}` 
    });
  }
  crumbs.push({ name: currentProduct.value.title, path: '' });
  return crumbs;
});

const formatPrice = (price) => new Intl.NumberFormat('ru-RU').format(price);

// Выбор опций атрибутов для корзины
const selectedOptions = ref({})

const initDefaultSelections = () => {
  const groups = attributesGroups.value || {}
  const defaults = {}
  for (const [attrName, values] of Object.entries(groups)) {
    if (Array.isArray(values) && values.length > 0) {
      defaults[attrName] = values[0]
    }
  }
  selectedOptions.value = defaults
}

watch(attributesGroups, () => {
  // Переинициализируем выбор при смене товара/варианта
  initDefaultSelections()
}, { immediate: true })

const isSelected = (attrName, val) => {
  const v = selectedOptions.value?.[attrName]
  return v && v.id === val.id
}

const selectAttribute = (attrName, val) => {
  selectedOptions.value = {
    ...selectedOptions.value,
    [attrName]: val
  }
}

const addToCart = () => {
  if (!currentProduct.value) return
  const p = currentProduct.value
  const payload = {
    id: p.id,
    title: p.title,
    price: p.price,
    images: p.images,
    // Передаём выбранные опции в корзину; cart store учитывает options в уникальности позиции
    options: selectedOptions.value && Object.keys(selectedOptions.value).length ? selectedOptions.value : null
  }
  cartStore.addItem(payload, 1)
  alert('Товар добавлен в корзину!')
}
</script>
