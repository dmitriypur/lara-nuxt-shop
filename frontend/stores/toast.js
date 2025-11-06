import { defineStore } from 'pinia'

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([])
  let nextId = 1

  const add = (type = 'info', message = '', duration = 3000) => {
    const id = nextId++
    const toast = { id, type, message }
    toasts.value.push(toast)
    if (duration > 0) {
      setTimeout(() => remove(id), duration)
    }
    return id
  }

  const remove = (id) => {
    const idx = toasts.value.findIndex(t => t.id === id)
    if (idx !== -1) toasts.value.splice(idx, 1)
  }

  const clear = () => {
    toasts.value = []
  }

  return {
    toasts: readonly(toasts),
    add,
    remove,
    clear
  }
})