export const useApi = () => {
  const config = useRuntimeConfig()
  const baseURL = config.public.apiBase

  // Базовая функция для API запросов
  const apiCall = async (endpoint, options = {}) => {
    const defaultOptions = {
      baseURL,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    }

    // Объединяем опции
    const finalOptions = {
      ...defaultOptions,
      ...options,
      headers: {
        ...defaultOptions.headers,
        ...options.headers
      }
    }

    try {
      const data = await $fetch(endpoint, finalOptions)
      return { data, error: null }
    } catch (error) {
      console.error('API Error:', error)
      return { data: null, error }
    }
  }

  // Методы для работы с категориями
  const getCategories = () => {
    return apiCall('/v1/categories')
  }

  const getCategory = (slug) => {
    return apiCall(`/v1/categories/${slug}`)
  }

  // Методы для работы с товарами
  const getProducts = (params = {}) => {
    const query = new URLSearchParams(params).toString()
    const endpoint = query ? `/v1/products?${query}` : '/v1/products'
    return apiCall(endpoint)
  }

  const getProduct = (slug) => {
    return apiCall(`/v1/products/${slug}`)
  }

  const getProductsByCategory = (categorySlug, params = {}) => {
    const query = new URLSearchParams(params).toString()
    const endpoint = query 
      ? `/v1/categories/${categorySlug}/products?${query}` 
      : `/v1/categories/${categorySlug}/products`
    return apiCall(endpoint)
  }

  const searchProducts = (searchTerm, params = {}) => {
    const searchParams = { search: searchTerm, ...params }
    const query = new URLSearchParams(searchParams).toString()
    return apiCall(`/v1/products/search?${query}`)
  }

  // Методы для работы с заказами
  const createOrder = (orderData) => {
    return apiCall('/orders', {
      method: 'POST',
      body: orderData
    })
  }

  const getOrders = () => {
    return apiCall('/orders')
  }

  const getOrder = (id) => {
    return apiCall(`/orders/${id}`)
  }

  // Методы для работы с пользователями
  const register = (userData) => {
    return apiCall('/auth/register', {
      method: 'POST',
      body: userData
    })
  }

  const login = (credentials) => {
    return apiCall('/auth/login', {
      method: 'POST',
      body: credentials
    })
  }

  const logout = () => {
    return apiCall('/auth/logout', {
      method: 'POST'
    })
  }

  const getProfile = () => {
    return apiCall('/auth/profile')
  }

  const updateProfile = (profileData) => {
    return apiCall('/auth/profile', {
      method: 'PUT',
      body: profileData
    })
  }

  return {
    // Базовые методы
    apiCall,
    
    // Категории
    getCategories,
    getCategory,
    
    // Товары
    getProducts,
    getProduct,
    getProductsByCategory,
    searchProducts,
    
    // Заказы
    createOrder,
    getOrders,
    getOrder,
    
    // Аутентификация
    register,
    login,
    logout,
    getProfile,
    updateProfile
  }
}