import { defineStore } from 'pinia'
import { useToastStore } from './toast'

export const useCartStore = defineStore('cart', () => {
  // Состояние
  const items = ref([])
  const isOpen = ref(false)
  const toast = useToastStore()

  // Геттеры
  const itemsCount = computed(() => {
    return items.value.reduce((total, item) => total + item.quantity, 0)
  })

  const totalPrice = computed(() => {
    return items.value.reduce((total, item) => total + (item.price * item.quantity), 0)
  })

  const isEmpty = computed(() => items.value.length === 0)

  // Действия
  const addItem = (product, quantity = 1) => {
    // Нормализуем входной объект товара (поддержка title/name и images)
    const id = product.id
    const name = product.title || product.name || 'Товар'
    const price = Number(product.price) || 0
    const image = product.image
      || (product.images?.[0]?.thumb || product.images?.[0]?.url)
      || null
    // Если когда‑то будем передавать опции (например, { size: 'XL' }),
    // то учитываем их при уникальности позиции.
    const options = product.options || product.selectedOptions || null

    const existingItem = items.value.find(item => item.id === id && JSON.stringify(item.options) === JSON.stringify(options))

    if (existingItem) {
      existingItem.quantity += quantity
    } else {
      items.value.push({
        id,
        name,
        price,
        image,
        quantity,
        options
      })
    }

    // Тост об успешном добавлении
    if (process.client) {
      const qtyLabel = quantity > 1 ? ` (×${quantity})` : ''
      toast.add('success', `Товар «${name}» добавлен в корзину${qtyLabel}`)
    }
  }

  const removeItem = (productId, options = null) => {
    const index = items.value.findIndex(item => item.id === productId && JSON.stringify(item.options) === JSON.stringify(options))
    if (index > -1) {
      items.value.splice(index, 1)
    }
  }

  const updateQuantity = (productId, quantity, options = null) => {
    const item = items.value.find(item => item.id === productId && JSON.stringify(item.options) === JSON.stringify(options))
    if (item) {
      if (quantity <= 0) {
        removeItem(productId, options)
      } else {
        item.quantity = quantity
      }
    }
  }

  const clearCart = () => {
    items.value = []
  }

  const toggleCart = () => {
    isOpen.value = !isOpen.value
  }

  const openCart = () => {
    isOpen.value = true
  }

  const closeCart = () => {
    isOpen.value = false
  }

  // Сохранение в localStorage
  const saveToStorage = () => {
    if (process.client) {
      localStorage.setItem('cart', JSON.stringify(items.value))
    }
  }

  // Загрузка из localStorage
  const loadFromStorage = () => {
    if (process.client) {
      const saved = localStorage.getItem('cart')
      if (saved) {
        items.value = JSON.parse(saved)
      }
    }
  }

  // Автосохранение при изменении корзины
  watch(items, saveToStorage, { deep: true })

  return {
    // Состояние
    items: readonly(items),
    isOpen: readonly(isOpen),
    
    // Геттеры
    itemsCount,
    totalPrice,
    isEmpty,
    
    // Действия
    addItem,
    removeItem,
    updateQuantity,
    clearCart,
    toggleCart,
    openCart,
    closeCart,
    loadFromStorage
  }
})