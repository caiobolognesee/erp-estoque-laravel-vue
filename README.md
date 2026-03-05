# Stock ERP – Laravel + Vue

A simple stock ERP system built with Laravel (API) and Vue 3 (Frontend).

This project allows:

- Product creation and listing
- Registering purchases (stock in + weighted average cost update)
- Registering sales (stock out + total and profit calculation)
- Viewing purchase and sales history
- Fully dockerized environment

---

## 🏗 Tech Stack

- Laravel 12 (REST API)
- Vue 3 + Vite
- MySQL 8
- Docker + Docker Compose

---

## 📁 Project Structure

```
erp-estoque-laravel-vue/
│
├── docker-compose.yml
│
├── backend/
│   ├── Dockerfile
│   └── (Laravel API)
│
└── frontend/
    ├── Dockerfile
    └── (Vue 3 application)
```

---

## 🚀 Running the Project

### 1️⃣ Build and start containers

```bash
docker compose up -d --build
```

### 2️⃣ Install backend dependencies

```bash
docker compose exec backend composer install
```

### 3️⃣ Generate application key

```bash
docker compose exec backend php artisan key:generate
```

### 4️⃣ Run migrations

```bash
docker compose exec backend php artisan migrate
```

---

## 🌐 Access URLs

Frontend:
```
http://localhost:5173
```

Backend:
```
http://localhost:8000
```

---

## 📌 API Endpoints

### Products
- `POST /api/products`
- `GET /api/products`

### Purchases
- `POST /api/purchases`
- `GET /api/purchases`

### Sales
- `POST /api/sales`
- `GET /api/sales`

---

## 💼 Business Rules

### Purchase
- Increases product stock
- Recalculates weighted average cost:

```
new_avg_cost =
((old_stock * old_avg_cost) + (incoming_qty * unit_price))
/ (old_stock + incoming_qty)
```

---

### Sale
- Validates available stock
- Decreases stock
- Calculates:

```
total  = sum(quantity * unit_price)
profit = sum(quantity * (unit_price - cost_at_sale))
```

`cost_at_sale` is stored for historical accuracy.

---

## 🧪 Example Sale Response

```json
{
  "id": 1,
  "customer": "Caio Bolognese",
  "total": 199.80,
  "profit": 99.80,
  "created_at": "2026-03-03T17:30:00Z",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "unit_price": 199.80,
      "cost_at_sale": 50.00
    }
  ]
}
```

---

## 📦 Docker Notes

- Backend and frontend are fully decoupled
- MySQL runs in its own container
- Backend connects to MySQL via service name `mysql`

---

## ✨ Improvements (Future Work)

- Authentication layer
- Product editing and deletion
- Pagination
- Unit tests
- Better UI styling

---

## 👨‍💻 Author

Caio Bolognese