1. run 'docker-compose up --build'
2. run 'docker exec -it php_cli bash'
3. run 'composer install'
4. import 'bookdb.sql' in src folder to 'bookdb' database in phpMyAdmin http://localhost:8081
5. run 'cd api-docs' + 'npx serve' to view api-docs site   


# Project Setup Instructions

Follow these steps to get the project up and running locally.

1. **Build and start the Docker containers:**

   Run the following command to build and start the containers:

   ```bash
   docker-compose up --build

2. **Install PHP dependencies:

   ```bash
   docker exec -it php_cli bash
   composer install

3. **Import the database:

Import the bookdb.sql file located in the src folder to the bookdb database using phpMyAdmin at [http://localhost:8081](http://localhost:8081)


4. **View the API documentation:

   ```bash
   cd api-docs
   npx serve
   
This will start the server and you can view the API documentation at http://localhost:5000.
