# 🚀 Trainee Task Management System

A comprehensive web application designed to manage and track tasks assigned to trainees within a company. This project was developed as part of a technical assessment, requiring robust backend logic, interactive features, and strict adherence to a legacy environment constraint.

## ✨ Live Demo & Repository

| Type | Link | Notes |
| :--- | :--- | :--- |
| **Live Demo** |- | *Fully deployed on Railway.app* |
| **GitHub Repo** | [https://github.com/kamu-data](https://github.com/ridwanariefm/trainee-task/) | *Source code and documentation* |

**Admin Access:**
* **Username:** `superadmin`
* **Password:** `rahasia123` *(Sesuai yang dibuat di terminal)*

---

## 🛠️ Tech Stack & Environment

This project successfully met the unique challenge of developing modern features on a specific legacy stack, demonstrating adaptability and deep framework knowledge.

| Component | Technology | Version |
| :--- | :--- | :--- |
| **Backend Framework** | Laravel | 5.8 |
| **Language** | PHP | 7.4 (Forced via Docker/Composer) |
| **Database** | MySQL | Latest |
| **Frontend** | Bootstrap 4, jQuery, AJAX | |
| **Deployment** | Docker, Railway.app | |

---

## 💎 Key Features Implemented

The application offers powerful features to manage user roles, data flow, and task deadlines efficiently.

### 1. Advanced Authentication & Security
* **Multi-Authentication Guard:** Implemented custom Guards to maintain separate, secure sessions for **Admin** (username-based login) and **Trainee** (email-based login).
* **UUID Primary Keys:** Utilized Universally Unique Identifiers (UUID) instead of standard auto-incrementing integers for all primary keys, enhancing security and data integrity.

### 2. Dynamic Data Management
* **Server-Side DataTables (AJAX):** Engineered an interactive dashboard using jQuery DataTables with server-side processing for real-time sorting, filtering, and searching tasks without page reloads.
* **Bulk Data Handling:** Integrated `Maatwebsite/Excel` for two critical features:
    * **Import:** Bulk import of trainee data with strict validation.
    * **Export:** Dynamic export of task lists, filtered by current status (e.g., export only 'Late' tasks).

### 3. Task Automation & Workflow
* **Automated Status Update (Cron Job):** Configured and executed Laravel Scheduler (Cron Job) via the deployment pipeline to run daily, automatically marking any overdue tasks as 'Late'.
* **Mass Actions:** Implemented efficient mass deletion functionality for task records using AJAX requests and checkbox selections.
* **Inline Editing:** Enabled quick status updates (Done, Pending, Late) directly from the dashboard view via modal forms.

---

## ⚙️ Installation & Setup (Local Development)

Follow these steps to run the project on your local machine using XAMPP/Docker:

1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/kamu-data](https://github.com/kamu-data)
    cd trainee-task-system
    ```

2.  **Install dependencies:**
    ```bash
    composer install
    ```

3.  **Configure Environment:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    * *Edit the `.env` file to configure your local MySQL database credentials.*

4.  **Database Migration & Seeding:**
    ```bash
    php artisan migrate:fresh --seed
    ```

5.  **Start the local server:**
    ```bash
    php artisan serve
    ```

The application will be accessible at `http://127.0.0.1:8000`.

## 🧑‍💻 Author

| Name | LinkedIn |
| :--- | :--- |
| Ridwan Arief Mutaqin | https://www.linkedin.com/in/ridwan-arief-mutaqin-b99b29316/ |
