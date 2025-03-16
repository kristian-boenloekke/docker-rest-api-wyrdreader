1. run 'docker-compose up --build'
2. run 'docker exec -it php_cli bash'
3. run 'composer install'
4. import 'bookdb.sql' in src folder to 'bookdb' database in phpMyAdmin http://localhost:8081
5. run 'cd api-docs' + 'npx serve' to view api-docs site   


# Project Setup Instructions

Follow the steps below to set up and run the project locally.

## 1. Build and Start Docker Containers

Run the following command to build and start the Docker containers:

```bash
docker-compose up --build
This will build and start the required services as defined in the docker-compose.yml file.

2. Install PHP Dependencies
Next, enter the PHP container and install the required PHP dependencies using Composer:

bash
Copy
Edit
docker exec -it php_cli bash
composer install
This will install the necessary PHP dependencies for the project.

3. Import the Database
Import the database by following these steps:

Open phpMyAdmin in your browser.
Create a new database named bookdb.
Import the bookdb.sql file located in the src folder into the bookdb database.
4. View the API Documentation
To view the API documentation, navigate to the api-docs directory and run:

bash
Copy
Edit
cd api-docs
npx serve
This will start a local server and you can access the API documentation at http://localhost:5000.
