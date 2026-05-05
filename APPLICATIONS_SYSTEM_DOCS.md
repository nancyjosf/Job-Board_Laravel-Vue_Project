# 📄 Applications System Documentation (Task #4)

## 📌 Overview
This module handles the core interaction between Candidates and Employers, including job applications and CV management.

## 🛠 Backend Components
- **Model:** `Application.php` (Relationships with User and Job)
- **Controller:** `ApplicationController.php`
- **Service Layer:** `ApplicationService.php` (Handles validation & upload logic)

## 📡 API Endpoints
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| POST | `/api/applications` | Create a new application (requires CV upload) |
| GET | `/api/my-applications` | Fetch current candidate's applications |
| PATCH | `/api/applications/{id}/status` | Update status (Employer only) |

## 📁 File Handling
- CVs are stored in `storage/app/public/cvs`.
- Supported formats: PDF, DOCX (Max size: 5MB).