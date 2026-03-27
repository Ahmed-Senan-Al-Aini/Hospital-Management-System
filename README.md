# Hospital Management System

A comprehensive system for managing daily operations in hospitals and medical centers, built using **PHP** following the **MVC** (Model-View-Controller) architectural pattern and **MySQL** database. The system provides an easy-to-use interface for managing patients, visits, prescriptions, and pharmaceutical inventory.

---

## 🚀 Key Features

The system offers comprehensive coverage of the most important aspects of medical administration:
### 1. Login
- Email : ``` admin@hospital.com ```.
- Password  : ``` admin123 ```

### 1. Dashboard
- Real-time statistics on daily prescriptions, pending cases, and inventory status.
- Charts and quick alerts for medicines that are low in stock or nearing expiry.

### 2. Patient Management
- Comprehensive patient data registration (national ID, date of birth, blood type, etc.).
- Historical record for each patient, including previous visits and received prescriptions.

### 3. Visits & Diagnosis Management
- Registration of patient visits and documentation of medical diagnoses for each case.
- Linking visits to issued prescriptions.

### 4. Pharmacy & Pharmaceutical Inventory
- Complete management of medicines (add, edit, delete).
- Tracking of inventory movement (add quantities, dispense, adjust).
- Smart alert system for low stock and critical stock.
- Tracking of expiry dates.

#### Inventory Flow Diagram

![Inventory Flow Diagram](https://private-us-east-1.manuscdn.com/sessionFile/dHsWHXhrZYjAVZ7nK7b4Qr/sandbox/BTP5xl9QxqA5cr3FahUmrN-images_1774636614449_na1fn_L2hvbWUvdWJ1bnR1L2hvc3BpdGFsX21hbmFnZW1lbnRfc3lzdGVtL2ludmVudG9yeV9mbG93.png?Policy=eyJTdGF0ZW1lbnQiOlt7IlJlc291cmNlIjoiaHR0cHM6Ly9wcml2YXRlLXVzLWVhc3QtMS5tYW51c2Nkbi5jb20vc2Vzc2lvbkZpbGUvZEhzV0hYaHJaWWpBVlo3bks3YjRRci9zYW5kYm94L0JUUDV4bDlReHFBNWNyM0ZhaFVtck4taW1hZ2VzXzE3NzQ2MzY2MTQ0NDlfbmExZm5fTDJodmJXVXZkV0oxYm5SMUwyaHZjM0JwZEdGc1gyMWhibUZuWlcxbGJuUmZjM2x6ZEdWdEwybHVkbVZ1ZEc5eWVWOW1iRzkzLnBuZyIsIkNvbmRpdGlvbiI6eyJEYXRlTGVzc1RoYW4iOnsiQVdTOkVwb2NoVGltZSI6MTc5ODc2MTYwMH19fV19&Key-Pair-Id=K2HSFNDJXOU9YS&Signature=mJxNyRvXaal47z~unCCgC-EUtKg7Tbusz~cKu1ic3dGqNZ-N-fYjBzxL--cxa2nelAdqFyU9SXpcV3IQpvOmNvTBCy-ErQmWtSoxkZBrYHaoEcbfHlWNgYwTvbHVwWycpbBWmhrSE1438XzGjAGVj~m0kyryaxx624vcmndAG7fNpLD3b1m9lacpRSq819NO6bjiMVFBVHOENZQk1ofxypD-gKX9uGMSkzDMsS83UQl5iGZ32qYlHul7IMCA6tApv37f2z3tRtpOG~igcIlHV0FCiDp6e5-NuyNC8xqCANTlg37M-k~Pm40QPsfe-Yr31ipvAqUufzOiTjNtzG54UA__)

### 5. Prescriptions
- Creation of electronic prescriptions linked to doctors and patients.
- Tracking prescription status (pending, dispensed, cancelled).
- Ability to print prescriptions professionally.

### 6. Reports & Analytics
- Reports on medicine consumption over a specified period.
- Reports on expired or soon-to-expire medicines.
- Reports on inventory movement and daily/monthly prescriptions.

---

## 🛠 Technologies Used

The system is built using modern and stable technologies:

| Category | Technologies Used |
| :--- | :--- |
| **Core Language** | PHP (MVC Architecture) |
| **Database** | MySQL |
| **Frontend** | Bootstrap 5, CSS3, FontAwesome |
| **Scripting** | jQuery, JavaScript (AJAX) |
| **Security** | CSRF Protection, Password Hashing (Bcrypt), Role-Based Access Control (RBAC) |

#### MVC Architecture Diagram

![MVC Architecture Diagram](https://private-us-east-1.manuscdn.com/sessionFile/dHsWHXhrZYjAVZ7nK7b4Qr/sandbox/BTP5xl9QxqA5cr3FahUmrN-images_1774636614449_na1fn_L2hvbWUvdWJ1bnR1L2hvc3BpdGFsX21hbmFnZW1lbnRfc3lzdGVtL212Y19hcmNoaXRlY3R1cmU.png?Policy=eyJTdGF0ZW1lbnQiOlt7IlJlc291cmNlIjoiaHR0cHM6Ly9wcml2YXRlLXVzLWVhc3QtMS5tYW51c2Nkbi5jb20vc2Vzc2lvbkZpbGUvZEhzV0hYaHJaWWpBVlo3bks3YjRRci9zYW5kYm94L0JUUDV4bDlReHFBNWNyM0ZhaFVtck4taW1hZ2VzXzE3NzQ2MzY2MTQ0NDlfbmExZm5fTDJodmJXVXZkV0oxYm5SMUwyaHZjM0JwZEdGc1gyMWhibUZuWlcxbGJuUmZjM2x6ZEdWdEwyMTJZMTloY21Ob2FYUmxZM1IxY21VLnBuZyIsIkNvbmRpdGlvbiI6eyJEYXRlTGVzc1RoYW4iOnsiQVdTOkVwb2NoVGltZSI6MTc5ODc2MTYwMH19fV19&Key-Pair-Id=K2HSFNDJXOU9YS&Signature=BWqit0Lm3YSbbKhcCDAkoIsMRslMGdbXVy-jCuRfqPBMPcQobdiL6yUqJLVlKs-ovtIC3qllOd8OxGTDPKT-dmgtEaCJjCBR2LWOPZ4TtFuiY15~o1NSrKDufrPdGhT0K-Ocv2m8vXcLT2zU27d-cii6LjYOGOrksUwXTyGxWPs1uJPxoGmT9twnmRHzlLrUxrAn0-P6-X-fNNvE7GhtHr3Fa1fGIj8h76j~iXbASML4KH-Dy4-ka7iX5QosZEp84zTI-lgW71Uv79lOQF3Rv8pzISD6j8JKRtyXMNpXG508C6Wb5yKrYlYgLgpItE4DLRkgpu62Q0~rOTCAsbNYkQ__)

---

## 💻 System Requirements

- **PHP**: Version 8.0 or higher.
- **MySQL**: Version 5.7 or higher.
- **Web Server**: Apache (e.g., XAMPP or WAMP) or Nginx.
- **PDO Extension**: Must be enabled in PHP for database connection.

---

## ⚙️ Installation Steps

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/Ahmed-Senan-Al-Aini/Hospital-Management-System.git
   ```

2. **Database Setup**:
   - Create a new database named `hospital_system`.
   - Import the SQL file located at: `config/Hospital_Management_System.sql`.

3. **Configuration**:
   - Open `config/database.php` and modify the connection details (host, username, password) to match your local environment.

4. **Run the Application**:
   - Place the project folder in your web server's document root (e.g., `htdocs` for XAMPP).
   - Access the system via your browser: `http://localhost/Hospital-Management-System`.

---

## 🔐 User Roles

The system supports a role-based access control (RBAC) system to ensure data privacy:
- **Admin**: Full permissions over all sections and reports.
- **Doctor**: Manages visits and creates prescriptions.
- **Pharmacist**: Manages inventory and dispenses prescriptions.
- **Secretary**: Registers patients and organizes appointments.

#### Authentication Flow Diagram

![Authentication Flow Diagram](https://private-us-east-1.manuscdn.com/sessionFile/dHsWHXhrZYjAVZ7nK7b4Qr/sandbox/BTP5xl9QxqA5cr3FahUmrN-images_1774636614449_na1fn_L2hvbWUvdWJ1bnR1L2hvc3BpdGFsX21hbmFnZW1lbnRfc3lzdGVtL2F1dGhfZmxvdw.png?Policy=eyJTdGF0ZW1lbnQiOlt7IlJlc291cmNlIjoiaHR0cHM6Ly9wcml2YXRlLXVzLWVhc3QtMS5tYW51c2Nkbi5jb20vc2Vzc2lvbkZpbGUvZEhzV0hYaHJaWWpBVlo3bks3YjRRci9zYW5kYm94L0JUUDV4bDlReHFBNWNyM0ZhaFVtck4taW1hZ2VzXzE3NzQ2MzY2MTQ0NDlfbmExZm5fTDJodmJXVXZkV0oxYm5SMUwyaHZjM0JwZEdGc1gyMWhibUZuWlcxbGJuUmZjM2x6ZEdWdEwyRjFkR2hmWm14dmR3LnBuZyIsIkNvbmRpdGlvbiI6eyJEYXRlTGVzc1RoYW4iOnsiQVdTOkVwb2NoVGltZSI6MTc5ODc2MTYwMH19fV19&Key-Pair-Id=K2HSFNDJXOU9YS&Signature=JruK~hgxSnq4eNfoHRf2dfDhbLcuVE1C3w63nLfRoH4hkWtj5-v1AGo3j5eoLOm1yT91Q8teiAB4HjCby5eFy1GES5i-EwThYCXg5DGgIu2E3r3zMP2L~UM-9r8cWhV06wqA0STx3H-w4xTpE0WeSIpzxAlb24t2fofZMUOOlztLcnIU6tAqx4hKy3-0WBUhsO0XH2hToFH4ikJEqdPrlk6Skx7LDkb-vwFr1bUILnmfgdnwc4tcqGgHU5eGyJCOVNM16LiKIl821MT9vC2ZaZKCNHgbTIb8TJz-8LlArsHxC~Dc7HcAif~3yxJ5VO2O0~1z-oG~e05rHfzlEeXnig__)

---

## 📂 Project Structure

```text
├── config/          # Configuration files and database schema
├── controllers/     # Application logic and request handling
├── core/            # Core functionalities (Router, Auth, Session, etc.)
├── models/          # Database interaction and data models
├── public/          # Public assets (CSS, JS, Fonts)
├── storage/         # Logs and temporary files
├── views/           # User interface templates
└── index.php        # Main entry point
```

---

## 📄 License

This project is licensed under the **MIT License**. You are free to use and modify it.

---

## 📧 Contact

If you have any questions or suggestions, feel free to contact the project owner via GitHub.

---
