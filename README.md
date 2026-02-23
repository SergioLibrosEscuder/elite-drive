<p align="center">
<img src="public/images/logo/elitedrive-logo-square.png" width="300" alt="Elite Drive Logo">
</p>


## 1. About the project

Elite Drive is a comprehensive web platform designed for managing and booking high-end vehicle rentals. The project was launched with the goal of digitizing the rental experience, offering an intuitive customer interface and a robust control panel for fleet management.

The system allows users to register, verify their identity via email, manage their personal profile, and make real-time bookings. Administrators, on the other hand, have tools to monitor vehicle status, manage customers, and validate booking transactions.


## 2. Tech Stack

Main Frameworks and tools:

- [![Laravel](https://img.shields.io/badge/laravel-v11-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
- [![Vue.js](https://img.shields.io/badge/vue.js-v3-%2335495e.svg?style=for-the-badge&logo=vuedotjs&logoColor=%234FC08D)](https://vuejs.org/)
- [![Bootstrap](https://img.shields.io/badge/bootstrap-v5.3.8-%238511FA.svg?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
- [![Supabase](https://img.shields.io/badge/Supabase-PostgreSQL-%233ECF8E.svg?style=for-the-badge&logo=supabase&logoColor=white)](https://supabase.com/)


## 3. Prerequisites

- PHP 8.2
- Composer
- Node.js & NPM
- Docker

## 4. Dependencies

- [Pinia](https://pinia.vuejs.org/) - State Management
- [jsPDF](https://www.npmjs.com/package/jspdf) - PDF generation
- [Swiper](https://swiperjs.com/) - Modern sliders
- [EmailJS](https://www.emailjs.com/) - Email services


## 5. Installation and Configuration

Steps to clone and launch the project:

1. Clone repository:
```bash
git clone https://github.com/SergioLibrosEscuder/elite-drive.git
```
2. Install dependencies for PHP:
``` bash
composer install
```
3. Install dependencies for JS:
```bash
npm install
```
4. Configure the environment:
```bash
cp .env.example .env
```
- (Make sure to configure it correctly) <br><br>

5. Generate the app key
```bash
php artisan key:generate
```
6. Execute:
```bash
php artisan serve
```
```bash
npm run dev
```
- (Execute them in separated terminals)


## 6. Main Features

- **Auth:** Registration and login with email confirmation via signed URLs.
- **User Profile:** Personal data management with real-time validation.
- **Bookings:** Real-time vehicle availability and booking system.
- **Admin Panel:** Complete CRUD for fleet, bookings, and customer oversight.


## 7. Database Structure

![Database Structure](public/images/db_diagram/supabase-schema-evyujthtrhdgvuymnaot.png)

## 8. Authors

<div align="center">
  <table>
    <tr>
      <td align="center">
        <a href="https://github.com/SergioLibrosEscuder">
          <img src="https://avatars.githubusercontent.com/u/238160125?v=4&s=120" width="120" alt="Sergio Libros"><br>
          <b>Sergio Libros</b>
        </a>
      </td>
      <td align="center">
        <a href="https://github.com/AlumnoAbastos">
          <img src="https://avatars.githubusercontent.com/u/238160364?v=4&s=120" width="120" alt="Guillermo Soto"><br>
          <b>Guillermo Soto</b>
        </a>
      </td>
      <td align="center">
        <a href="https://github.com/IvanRequenaPuig">
          <img src="https://avatars.githubusercontent.com/u/150246848?v=4&s=120" width="120" alt="Iván Requena"><br>
          <b>Iván Requena</b>
        </a>
      </td>
    </tr>
  </table>
</div>
