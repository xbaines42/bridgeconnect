# BridgeConnect – Homeless Resource Platform

## Overview
BridgeConnect is a full-stack web application designed to connect individuals experiencing homelessness with essential community resources such as shelters, food programs, medical services, hygiene centers, and job support.

The platform provides real-time information and role-based functionality, allowing different types of users to interact with the system efficiently.

---

## Problem
Individuals experiencing homelessness often struggle to find accurate, up-to-date information about available resources such as shelter beds, food drives, and support services.

---

## Solution
BridgeConnect provides a centralized platform where:
- Users can quickly find nearby resources
- Shelter providers can update availability in real time
- Volunteers and admins can manage and maintain resource listings

---

## Features

### 🔍 Resource Search & Filtering
- Search by name, location, or keyword
- Filter by type (shelter, food, medical, hygiene, job support)

### 🏠 Real-Time Shelter Availability
- Shelter providers update available bed counts
- Users see the most accurate availability instantly

### 🗺️ Directions Integration
- One-click access to Google Maps directions

### 🔐 Authentication System
- Secure login and account creation
- Role-based access control

### 👥 Role-Based Dashboards
- **Person in Need**
  - Browse and filter resources
  - View availability
  - Get directions

- **Shelter Provider**
  - Update bed counts in real time

- **Volunteer**
  - Add new resources

- **Admin**
  - Full system control (add/delete/manage resources)

---

## Tech Stack

- **Frontend:** HTML, CSS
- **Backend:** PHP
- **Database:** MySQL
- **Server:** MAMP (Apache + MySQL)
- **Tools:** GitHub, Trello (Agile project tracking)

---

## Installation & Setup (MAMP)

### 1. Move Project Folder
Place the `bridgeconnect` folder into:
/Applications/MAMP/htdocs/
---

### 2. Start MAMP
- Open MAMP
- Start:
  - Apache
  - MySQL

---

### 3. Install Database
Open in your browser:
http://localhost:8888/bridgeconnect/install.php
This will automatically:
- Create the database
- Create all tables
- Insert demo users and resources

---

### 4. Run the Application

Go to:
http://localhost:8888/bridgeconnect
---

## Demo Login Accounts

| Role               | Email                | Password |
|--------------------|---------------------|----------|
| Person in Need     | user@test.com       | 1234     |
| Shelter Provider   | provider@test.com   | 1234     |
| Volunteer          | volunteer@test.com  | 1234     |
| Admin              | admin@test.com      | 1234     |

---

## How to Use

### Person in Need
- Login
- Search or filter resources
- View shelter availability
- Click "Get Directions"

### Shelter Provider
- Login
- Update available bed counts

### Volunteer
- Login
- Add new resources

### Admin
- Login
- Add or delete resources
- Manage the entire system

---

## Project Management (Agile)

- User stories created and prioritized
- Sprint planning conducted using Trello
- Tasks tracked across:
  - To-Do
  - In Progress
  - Done

---

## Future Improvements

- Mobile-friendly responsive design
- Notifications for resource updates
- Favorites/bookmark system
- API integration for real-time data
- Deployment to a live server

---

## Authors

-Xavier Baines 
-Kamari Johnson
-Christopher Crosby 


---
