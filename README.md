# Perfect Gifts (PHP + MySQL + Bootstrap)

Major Project-ready demo eCommerce for Gift Shop.

## Tech
- PHP (PDO, prepared statements)
- MySQL (8 tables with FKs)
- Bootstrap 5 (responsive UI)
- Session Cart + Order history
- Admin Panel + Reports
- Payment Flow (Demo Success)

## Setup (XAMPP)
1. Copy folder `giftshop_php` into: `xampp/htdocs/`
2. Open phpMyAdmin:
   - Create DB: `giftshop`
   - Import:
     - `sql/schema.sql`
     - `sql/seed.sql`
3. Open: `http://localhost/giftshop_php/`
4. Admin Panel: `http://localhost/giftshop_php/admin/`

### Default Admin
- Email: `admin@demo.com`
- Password: `Admin@123`

## Folder structure
- `config/` app + DB config
- `includes/` reusable navbar/footer (like components)
- `admin/` admin pages + reports
- `sql/` schema + seed
- `assets/` css/js
- `images/` images
