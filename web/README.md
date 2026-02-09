# Credit Card Transaction Classifier

## Overview
This project is an **internal tool** designed to help professionals classify credit card transactions into predefined groups using a web interface. The system fetches transactions from a **MySQL database**, allows users to categorize them via a dynamic **HTML dropdown**, and submits classifications asynchronously via **AJAX**.

## Features
- 🔍 **Dynamic Filtering**: Users can filter transactions based on group selection.
- 📜 **Sortable Dropdown**: Groups are displayed in alphabetical order for easy selection.
- ⚡ **AJAX Submission**: Saves classifications without requiring a page reload.
- 🛡 **Security Enhancements**:
  - Input sanitization (`htmlspecialchars()`) prevents XSS attacks.
  - SQL injection protection via **prepared statements** (`bind_param()`).
  - Logs activity (`error_log()`) for debugging and audit trails.

## Requirements
- **PHP 7+**
- **MySQL**
- **Apache/Nginx**
- **JavaScript (ES6)**

## Setup Instructions
1. Clone the repository:
   ```sh
   git clone https://github.com/your-user/credit-card-classifier.git
   cd credit-card-classifier
Configure environment variables in your server:

sh
export MYSQL_HOST="your_host"
export MYSQL_USER="your_username"
export MYSQL_PASSWORD="your_password"
export MYSQL_NAME="your_db"
Ensure the database is properly set up:

sql
CREATE TABLE creditCard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operation DATE,
    libel VARCHAR(255),
    amount DECIMAL(10,2),
    groupid INT
);

CREATE TABLE groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255)
);
Place the PHP files inside your web root (/var/www/html/ or equivalent).

Open the browser and navigate to:

http://your-server/cptClassify.php
Usage
Use the dropdown to assign transactions to groups.

Click Submit to send the classification via AJAX (fetch()).

Logs are stored in /var/www/html/logs/cpt.log for debugging.

Security Considerations
✅ SQL Injection Protection: Uses mysqli_stmt::bind_param() for safe queries. 
✅ XSS Prevention: Outputs HTML using htmlspecialchars(). 
✅ Data Validation: Ensures only valid numeric filters are used. 
✅ Error Logging: Stores execution details in /logs/cpt.log.

Future Enhancements
🔄 Live Updates: Fetch new transactions dynamically without reloading.

📊 Analytics Dashboard: Display categorized transactions visually.

🔒 Role-Based Access: Restrict actions based on user roles.

Contributing
Feel free to submit pull requests or report issues in the repository.

License
This project is licensed under the MIT License.


