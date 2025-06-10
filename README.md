# Document-Entry-System

Document-Entry-System is a web application for uploading, tracking, and managing documents within an organization. Users can register, upload documents, view the status of their submissions, and see a list of their own uploaded documents. The system supports roles for administrators, responsible users (approvers), and regular users.

## Getting Started

1. **Download and Install XAMPP**  
   Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/) and install it on your machine.

2. **Set Up the Database**  
   - Start Apache and MySQL from the XAMPP control panel.
   - Open [phpMyAdmin](http://localhost/phpmyadmin).
   - Create a new database (e.g., `document_entry_system`).
   - Import the `schema.sql` and `seed.sql` files from the `database` directory into your new database using phpMyAdmin's "Import" feature.

3. **Configuration**  
   - All necessary configuration (such as database credentials) is hard coded for ease of portability in `app/config/config.php`.  
   - If you need to change database connection details, edit them directly in that file.

4. **Run the Application**  
   - Place the project folder in your XAMPP `htdocs` directory.
   - Access the app in your browser at [http://localhost/Document-Entry-System/public/](http://localhost/Document-Entry-System/public/).

## Project Structure

- `app/` – Application code (MVC: controllers, models, views, services, config, helpers)
- `database/` – SQL schema and seed data
- `public/` – Public web root (entry point, assets, uploads)
- `.env` – (Not used) Example environment variables
- `README.md`, `LICENSE` – Documentation and license