# Rento - Vehicle Rental System (Backend API)

Rento is a comprehensive vehicle rental platform built with **Laravel**. It provides a robust API to manage two types of users: **Customers** (who want to rent cars) and **Agencies** (who manage their fleet and bookings).

## 🚀 Features

### 🔐 Authentication & Authorization
* **Multi-Role Auth**: Separate registration flows for Customers and Agencies.
* **Sanctum Integration**: Secure token-based authentication.
* **Profile Management**: Update personal info and profile photos for both roles.

### 👤 Customer Features
* **Browse & Search**: Find agencies and cars with advanced search filters.
* **Booking Flow**: Seamless car booking process from selection to confirmation.
* **Agency Discovery**: Explore different rental agencies and their specific fleets.

### 🏢 Agency Features
* **Fleet Management**: Full CRUD operations for cars, including soft deletes.
* **Dynamic Car Attributes**: Fetch predefined types, brands, models, fuel types, and transmissions.
* **Booking Management**: Monitor all incoming bookings or filter them by date.
* **Statistics**: Home dashboard for agency-specific insights.

---

## 🛠 Tech Stack

* **Framework:** Laravel 10+
* **Authentication:** Laravel Sanctum
* **Database:** MySQL / PostgreSQL
* **Architecture:** RESTful API with API Resources for consistent JSON responses.

---

## 🛣 API Endpoints (Quick Reference)

### 1. Authentication
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| POST | `/api/register/customer` | Register a new customer |
| POST | `/api/register/agency` | Register a new rental agency |
| POST | `/api/login` | Login and get Bearer Token |
| POST | `/api/logout` | Revoke token (Auth required) |

### 2. Customer Portal (`/api/customer/*`)
* `GET /home`: Get featured cars/agencies.
* `GET /agencies`: List all agencies.
* `GET /cars/{id}`: View specific car details.
* `POST /cars/{id}/book`: Request a booking.
* `POST /cars/book/confirm/{id}`: Finalize booking.

### 3. Agency Portal (`/api/agency/*`)
* `GET /cars`: View agency fleet.
* `POST /cars/store`: Add a new car to the system.
* `GET /bookings/{date}`: Check schedule for a specific day.
* `DELETE /cars/{id}`: Remove a car (Soft Delete).

---

## ⚙️ Installation & Setup

1.  **Clone the repository**
    ```bash
    git clone [https://github.com/your-username/rento-backend.git](https://github.com/your-username/rento-backend.git)
    cd rento-backend
    ```

2.  **Install dependencies**
    ```bash
    composer install
    ```

3.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Configure your database settings in the `.env` file.*

4.  **Run Migrations & Seeders**
    ```bash
    php artisan migrate --seed
    ```

5.  **Start the Server**
    ```bash
    php artisan serve
    ```

---

## 📱 Frontend Integration
The frontend for this project is built using **Flutter**. You can find the mobile application repository here:
👉 [Rento Flutter App](https://github.com/SamerZaina/Vehicle_Rental_App.git)

---

## 🤝 Contributors
**[Samer Zaina](https://github.com/SamerZaina)** & **[Roaaa Khalad](https://github.com/roaaabufoul)**.
