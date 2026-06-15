# PRD Summary: M-Core Freelance (Sistem Manajemen Freelance Project)

## Product Vision
A mobile-first freelance project management system with microservices architecture focusing on project tracking, milestone management, and automated invoicing.

## Target Users
- **Freelancers:** Manage projects, clients, milestones, and payments (Restricted CRUD).
- **Admins:** Full system oversight and logging (Full CRUD).

## Core Feature Requirements (Mobile UI Focus)
1.  **Authentication:** Login & Registration (JWT-based).
2.  **Dashboard:** Responsive UI for project overview, upcoming deadlines, and financial status.
3.  **Project Management:** CRUD for projects and clients. Pipeline tracking (Pitching, In Progress, Review, Completed).
4.  **Milestone & Deadline Tracking:** Short-term targets within projects.
5.  **Financial Module & Invoice Generator:** Tracking values, payment status (Unpaid, Partially Paid, Paid), and PDF/viewable invoice generation.
6.  **Navigation:** Clean, intuitive mobile navigation (Bottom Nav or Sidebar).

## Design Direction
- **Style:** Clean, professional, and modern.
- **Color Palette:** Professional blues/indigo for trust, with status-specific accents (e.g., green for paid, amber for in-progress).
- **Typography:** Sans-serif for readability (e.g., Inter or Roboto).
- **Layout:** Card-based interfaces for project and milestone lists to handle dense information on mobile screens.
