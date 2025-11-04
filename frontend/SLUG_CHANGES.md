# Изменения для работы со slug вместо ID

## Переименованные файлы
- `pages/categories/[id].vue` → `pages/categories/[slug].vue`

## Обновленные API методы
- `getCategory(id)` → `getCategory(slug)`
- `getProductsByCategory(categoryId, params)` → `getProductsByCategory(categorySlug, params)`

## Обновленные компоненты

### 1. Страница категории `[slug].vue`
- `categoryId` → `categorySlug`
- API вызовы используют slug вместо ID
- Кэш ключи обновлены для работы со slug

### 2. Страница списка категорий `index.vue`
- `goToCategory(categoryId)` → `goToCategory(category)`
- Переход по `category.slug` вместо `category.id`

### 3. Главная страница `index.vue`
- Ссылки на категории используют `category.slug`

### 4. Страница каталога `catalog/index.vue`
- Фильтр категорий использует `category.slug` как значение

### 5. Компоненты товаров
- `ProductCard.vue` и `ProductListItem.vue`
- Ссылки на категории используют `product.category.slug`

## API роуты (backend)
Backend уже настроен для работы со slug:
- `GET /v1/categories/{slug}` - получение категории
- `GET /v1/categories/{slug}/products` - товары категории

## Преимущества использования slug
1. **SEO-friendly URLs** - понятные URL для поисковиков
2. **Читаемость** - `/categories/electronics` вместо `/categories/1`
3. **Стабильность** - URL не меняется при изменении ID в базе
4. **Пользовательский опыт** - легче запомнить и поделиться ссылкой

## Примеры URL
- `/categories/electronics` - категория "Электроника"
- `/categories/clothing` - категория "Одежда"
- `/categories/home-garden` - категория "Дом и сад"
