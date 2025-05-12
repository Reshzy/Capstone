# 🛠 SVP System Laravel Development Roadmap

This roadmap outlines the phases, tools, and Laravel packages that will be used to develop the Web-Based Small Value Procurement (SVP) system for Cagayan State University.

---

## 🚀 Phase 1: Project Setup and Architecture

- [x] Install Laravel via Composer
- [x] Configure `.env` for database and mail
- [x] Set up version control (Git) and repository

**Packages:**
- `laravel/breeze` or `laravel/jetstream` – for user authentication and scaffolding
- `barryvdh/laravel-debugbar` – for debugging
- `spatie/laravel-permission` – for role and permission management

---

## 🔐 Phase 2: User Management & Role-Based Access

- [ ] Implement login, registration, and password reset
- [ ] Define user roles: Requestor, Approver, Procurement Officer, Admin
- [ ] Restrict access to pages based on roles

**Packages:**
- `spatie/laravel-permission` – to handle RBAC
- `laravel/ui` or `breeze/jetstream` – for simple auth scaffolding

---

## 📝 Phase 3: Purchase Request (PR) Module

- [ ] Create PR submission form
- [ ] Enable file uploads for PPMP documents
- [ ] Store PR data in `purchase_requests` table

**Packages:**
- `laravelcollective/html` – for building forms
- `intervention/image` – for document upload validation (if needed)

---

## 🧮 Phase 4: Budget Approval Workflow

- [ ] Build PR approval queue for Budget Officers
- [ ] Allow status updates (Approved/Rejected)
- [ ] Attach approved budget amount

---

## 📋 Phase 5: BAC Evaluation and Supplier Canvassing

- [ ] Create interfaces for BAC members to evaluate specs
- [ ] Build RFQ generation feature
- [ ] Link supplier database to RFQ items

**Packages:**
- `maatwebsite/excel` – for exporting/importing supplier data and quotations
- `dompdf/dompdf` or `barryvdh/laravel-dompdf` – to generate printable RFQs and AOQs

---

## 🛍️ Phase 6: Document Generation (AOQ, PO, DV)

- [ ] Automate generation of Abstracts of Quotation, Purchase Orders, Disbursement Vouchers
- [ ] Implement print/export buttons for each document

---

## 📦 Phase 7: Supplier Management Module

- [ ] Add/edit/delete supplier entries
- [ ] Tag suppliers by procurement category
- [ ] Track supplier performance history

---

## 🖥️ Phase 8: Admin Dashboard & Reporting

- [ ] Create dashboards for all user roles
- [ ] Add tables for tracking pending, approved, and rejected PRs
- [ ] Build reporting feature with filters by date, department, category

**Packages:**
- `yajra/laravel-datatables` – for searchable, paginated data tables
- `spatie/laravel-activitylog` – for audit trail logging
- `barryvdh/laravel-dompdf` – for report PDF export

---

## 🔔 Phase 9: Notification System

- [ ] Implement email notifications for key actions (PR submitted, approved, rejected)
- [ ] Add in-app notifications using Laravel Broadcast or Pusher (optional)

**Packages:**
- `laravel-notification-channels/webpush` – for real-time in-app notifications
- `pusher/pusher-php-server` – for event broadcasting (optional)

---

## 🔍 Phase 10: Testing and Security

- [ ] Write unit tests for models and controllers
- [ ] Test form validation, file uploads, and access control
- [ ] Conduct penetration testing and ensure HTTPS is enabled

**Packages:**
- `phpunit/phpunit` – built-in Laravel testing
- `fzaninotto/faker` – for seeding mock data

---

## 🌐 Phase 11: Deployment and Optimization

- [ ] Deploy to production server (DigitalOcean, shared hosting, etc.)
- [ ] Run migrations and seeders
- [ ] Set up caching, queue workers, and daily backups

**Tools:**
- Laravel Forge / Envoyer (optional for CI/CD)
- `spatie/laravel-backup` – for automated backups

---

## ✅ Optional Enhancements

- Multi-campus module support
- Supplier ratings and historical performance analytics
- SMS integration for urgent procurement alerts
