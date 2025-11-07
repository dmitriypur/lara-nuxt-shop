import { defineNuxtPlugin } from '#app'
import { useCartStore } from '@/stores/cart'

export default defineNuxtPlugin(() => {
  // Инициализация корзины из localStorage на клиенте
  const cart = useCartStore()
  cart.loadFromStorage()
})