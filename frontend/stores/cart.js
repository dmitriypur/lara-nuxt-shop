import { defineStore } from 'pinia'

export const useCartStore = defineStore('cart', () => {
  // Состояние
  const items = ref([])
  const isOpen = ref(false)

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
    const existingItem = items.value.find(item => item.id === product.id)
    
    if (existingItem) {
      existingItem.quantity += quantity
    } else {
      items.value.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.image,
        quantity: quantity
      })
    }
  }

  const removeItem = (productId) => {
    const index = items.value.findIndex(item => item.id === productId)
    if (index > -1) {
      items.value.splice(index, 1)
    }
  }

  const updateQuantity = (productId, quantity) => {
    const item = items.value.find(item => item.id === productId)
    if (item) {
      if (quantity <= 0) {
        removeItem(productId)
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