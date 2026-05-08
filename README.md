<div align="center">
  
<div>
  <img src="public/images/logo/logolinetextW2.svg" alt="Estatra Logo" width="120">
</div>

## Estatra - Property & Tenant Management for House/Flat Owners

[![Laravel][Laravel.com]][Laravel-url]
[![Tailwind][TailwindCSS.com]][Tailwind-url]
[![Alpine][Alpine.js]][Alpine-url]
[![Livewire][Livewire.com]][Livewire-url]

</div>

## ✨ About The Project

Estatra makes it simple for house owners to manage rentals. Track rents, bills, expenses, and maintenance in one place with an intuitive dashboard.

### 🎯 Key Features

- **Property & Unit Management**: Add houses, apartments, and individual rental units.
- **Tenant & Lease Tracking**: Store tenant details and manage active leases.
- **Rent Payments**: Record and track rental payments for each tenant.
- **Property Expenses**:  Log expenses like utilities, repairs, and upkeep.
- **Maintenance Logs**: Keep track of maintenance requests and completed work.

## ⚡ Quick Start

```bash
# Clone the repository
git clone https://github.com/tasFiquejim/estatra.git
cd estatra

# Install dependencies
composer install
npm install

# Configure environment
cp .env.example .env
php artisan key:generate

# Run migrations and seed demo data
php artisan migrate --seed

# Start development server
php artisan serve
npm run dev
```

## 🔑 Demo Credentials

After seeding, log in with the pre-created demo account:

| Field    | Value                |
|----------|----------------------|
| Email    | `demo@estatra.com`   |
| Password | `password`           |

The demo account includes **3 properties**, **12 units**, **8 active tenants**, **3 months of rent history**, and maintenance/expense records — ready to explore all features.

## 🛠️ Built With

- **Framework**: Laravel 12+
- **Frontend**: TailwindCSS + AlpineJS
- **Real-time**: Laravel Livewire
- **Database**: MySQL
- **Authentication**: Laravel Breeze

## 📝 License

Distributed under the MIT License. See [LICENSE](LICENSE) for more information.

---

<div align="center">
  
### ⭐️ If this project helped you, please consider giving it a star!

</div>

<!-- MARKDOWN LINKS & IMAGES -->
[Laravel.com]: https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white
[Laravel-url]: https://laravel.com
[TailwindCSS.com]: https://img.shields.io/badge/Tailwind-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white
[Tailwind-url]: https://tailwindcss.com
[Alpine.js]: https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=black
[Alpine-url]: https://alpinejs.dev
[Livewire.com]: https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white
[Livewire-url]: https://laravel-livewire.com
