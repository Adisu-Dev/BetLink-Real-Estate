# BetLink - Real Estate & Rental Property Management System

BetLink is a full-stack, enterprise-grade web application built to transform property listing, discovery, tour scheduling, and rental management in urban Ethiopia. It replaces informal and fragmented brokerage workflows with verified transparency, real-time appointment locking, and direct communication between owners, buyers, tenants, and agents.

---

## 🌟 Key Features

- **Verified Property Directory**: Detailed property listings with multimedia galleries, pricing, location filters, and verified amenities.
- **Automated Tour Scheduling**: Online appointment engine with database transaction locking (`lockForUpdate`) preventing overlapping booking collisions.
- **Multi-Role Portals**: Dedicated, responsive dashboards for:
  - **Buyer / Tenant**: Search, save favorites, book tours, track inquiries.
  - **Property Owner / Landlord**: Manage listings, view booking requests, analyze metrics.
  - **Real Estate Agent**: Lead tracking, property assignments, client management.
  - **System Admin**: Platform analytics, user roles, property verification, compliance.
- **Security & Trust**: Role-based access control (RBAC), JWT authentication, SMS/Email OTP verification, and CSRF/CORS protection.
- **Multi-Language Support**: English, Amharic, Afaan Oromoo, and Tigrinya locale support.

---

## 🛠️ Technology Stack

### Backend
- **Framework**: PHP 8.3 / Laravel 13
- **Database**: MySQL 8 (3NF Relational Schema with Row-level Locking)
- **Architecture**: RESTful API, Eloquent ORM, Form Request Validation, API Resource Transformers
- **Authentication**: JWT & Argon2id password hashing

### Frontend
- **Framework**: Vue.js 3 (Composition API `<script setup>`)
- **Build Tool**: Vite
- **State Management**: Pinia
- **Routing**: Vue Router 4
- **Styling**: Tailwind CSS & Modern Vanilla CSS Design Tokens
- **HTTP Client**: Axios with Interceptors

---

## 🚀 Getting Started

### Prerequisites
- PHP >= 8.2 & Composer
- Node.js >= 18 & npm
- MySQL Server >= 8.0

### 1. Backend Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### 2. Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

The web application will be accessible at `http://localhost:5173` and the API at `http://localhost:8000`.

---

## 👥 Author
- **Adisu Dereje** ([@Adisu-Dev](https://github.com/Adisu-Dev))
- Bahir Dar University / Bahir Dar Institute of Technology (BiT)
- Internship Project at **Qelem Meda Technologies PLC**
