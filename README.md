# digiProduct

**digiProduct** is a personal portfolio-based e-commerce web application for showcasing and selling digital products and freelance services.  
This project is designed to demonstrate full-stack development skills using modern web technologies.

---

## 🚀 Tech Stack

### Backend
- Laravel

### Frontend
- Vue.js
- Inertia.js
- Tailwind CSS

### Other
- Docker (optional)
- PostgreSQL

---

## 🛍️ Project Type
**E-commerce Portfolio Website**

This application combines a public-facing portfolio website with an admin dashboard for managing products, content, and orders.

---

## ✨ Main Features

### 🌐 Public Website (Portfolio)
- Home page
- Our Work
- About Us
- Articles / Blog
- Shop (digital product placement)
- Hire Us (freelance order request)

### 🔐 Admin Dashboard
- Dashboard overview
- Product management
- Article/content management
- Order management
- Website settings:
  - Content management
  - Home slider (image upload)
  - Company slider (client logo / image)
  - User testimonials
- Product analytics

---

## 🧑‍💻 Installation & Setup

### Option 1: Using Docker
If the project contains a `docker-compose.yml` or `Dockerfile`, simply run:

```bash
docker-compose up -d
```

### Option 2: Manual Setup (Local Development)
Clone the repository

```bash
git clone <repository-url>
cd digiProduct
```

Install backend dependencies

```bash
composer install
```
Install frontend dependencies

```bash
npm install
```
Setup environment variables

```bash
cp .env.example .env
```
Configure your database credentials in `.env`

Generate application key

```bash
php artisan key:generate
```
Run database seeders

```bash
php artisan db:seed
```
Start the development servers

```bash
php artisan serve
npm run dev
```
## ⚙️ Environment Configuration
Copy `.env.example` to `.env`

Update the following values based on your database server:

- `DB_DATABASE`

- `DB_USERNAME`

- `DB_PASSWORD`

## 👤 Author
Herlian Aliyasa Almaj Duddin

## 📄 License
This project is proprietary and does not use an open-source license.
All rights reserved.