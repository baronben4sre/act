Personal Finance Tracker
Overview

This project is a personal finance tracking tool designed to consolidate financial data into a single, long-lived database, independent of banks or third-party applications.

It supports:

	* Credit card transactions

	* Checking account transactions

	* Investment tracking with reference index comparison

The goal is to ensure data continuity over time, even when banks, applications, or export formats change, device fail, all leading to data loss.

Transactions are imported from CSV files, classified into predefined groups and subcategories, and aggregated for long-term analysis. Visualization is provided through Grafana dashboards, enabling clear insight into spending patterns and investment performance over time.

**Key Concepts**

	* Single source of truth: all financial data stored in a local database

	* Batch-first processing: automation for recurring classifications

	* Manual correction UI: lightweight web interface for one-off classification

	* Time-series normalization: fill-forward logic ensures continuity for analytics

	* Benchmarking: investment performance compared against reference indices

	* Spending review over the time

	* Automated data backup

**Features**

    * Multi-account support

	* Credit cards

	* Checking accounts

	* Investment positions

    * Transaction classification

	* Predefined groups and subcategories

        * Batch classification for recurring patterns
	
	* Web interface for one-off corrections

    * Investment analytics

	* Reference index import

	* Relative performance comparison over time

    * Visualization

	* Grafana dashboards for:

	    * Spending by category

	    * Subcategory breakdown

	    * Investment evolution

    * Asynchronous web UI

	* AJAX-based submission

	* No page reload during classification

     * Security considerations

	* XSS prevention using htmlspecialchars()

	* SQL injection protection via prepared statements

	* Application logging for traceability

Technology Stack

	* Backend: PHP

	* Database: MySQL

	* Frontend: HTML, JavaScript (ES6, AJAX)

	* Visualization: Grafana and Grafonet

	* Processing: Python, Shell scripts for batch ingestion and classification

Requirements

	* PYTHON 3 + Modules: yfinance; mysql.connector; json; requests; fnmatch; pymysql
	* PHP 7+
	* MySQL
	* Apache or Nginx
	* JavaScript
	* Grafana
	* rsync (optional)

Setup Overview

	1. Clone the repository:
		git clone https://github.com/your-user/bank-tracker.git
		cd bank-tracker

	2. Configure environment variables:
		export ACTBASE='/opt/act'
		export ACTDATA='/opt/act/data'
		export ACTLOG='/opt/act/log'
		export ACTEXTRACSV='rsync_login@your_repo'
		export DB_HOST="your_host"
		export DB_USER="your_username"
		export DB_PASSWORD="your_password"
		export DB_NAME="your_db"

	3. Export transaction CSV files from your bank portals.

	4. Create an account:
		sh
		act/bin/act-add-account

	4. Import the latest CSV and classify:
		sh
		act/bin/act-import-last-csv
	   or setup a cronjob to pickup with rsync the CSV file from the downloaded directory configured by ACTEXTRACSV environment variable.

	5. Optional: Classify transactions:
		Place the PHP files under your web root (/var/www/html)
		Open in browser:
			http://your-server/classify.php
 		Select 'Unspecified' in the dropdown and filter.

	6. Setup a cronjob to copy the backup directory in a backup device:
		
**Logging**

	Application logs are stored in:
		/var/www/html/logs/act.log

These logs are intended for debugging, auditability, and traceability of automated processing.

**Future Enhancements**

	* Cloud deployment (Ongoing)
	* Extended investment analytics
	* Additional dashboard templates

**Contributing**

This project is primarily a personal learning and experimentation platform.
Suggestions, issues, and pull requests are welcome.

**License**

This project is licensed under the MIT License — see the LICENSE file for details.

