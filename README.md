# DEEP REST API Documentation

This document outlines the endpoints available in our DEEP REST API application and provides setup instructions.

## Setup Instructions

Before using the API, ensure you have completed the following steps:

1. **Install Docker**: Make sure Docker is installed on your system.

2. **Configure Environment Variables**:
   - Copy the example environment file:

     ```
     cp .env.example .env
     ```
   - Open the new `.env` file and update the MySQL, redis, mail server details according to your setup.

3. **Build and start the containers**:
   ```
   docker compose up -d --build
   ```

4. **Install Composer dependencies**:
   ```
   docker compose exec web composer install
   ```

5. **Check logs for admin credentials**:
   ```
   docker compose logs web
   ```
   This command will display the logs for DEEP API. Look for the output containing the admin user's credentials. Make sure to save these for logging in as an admin.

6. (Optional) Run tests:
   ```
   docker compose exec web php artisan test
   ```

## Authentication

Authentication for this API is handled using Bearer tokens. After a successful login, you'll receive a token that should be included in the Authorization header for all protected routes.

### Login

- **URL:** `/api/login`
- **Method:** `POST`
- **Description:** Authenticate a user and receive an access token
- **Required Fields:**
  - `email`
  - `password`
- **Response:** Returns user details and an access token upon successful authentication

### Register (Admin only)

- **URL:** `/api/register`
- **Method:** `POST`
- **Description:** Register a new user (only accessible to admin users)
- **Required Fields:**
  - `name`
  - `email`
  - `password`
  - `password_confirmation`
- **Authentication:** Required (Bearer Token of an admin user)
- **Response:** Returns user details and an access token upon successful registration

### Logout

- **URL:** `/api/logout`
- **Method:** `POST`
- **Description:** Invalidate the user's access token
- **Authentication:** Required (Bearer Token)
- **Headers:**
  - `Authorization: Bearer <your_access_token>`

## Using Authentication for Protected Routes

For all protected routes, you must include the Bearer token in the Authorization header of your HTTP request. Here's an example of how to structure your requests:

```
Authorization: Bearer <your_access_token>
```

Replace `<your_access_token>` with the actual token you received from the login process.

## User Management

All routes in this section require authentication. Some operations are restricted to admin users only.

### Update Password

- **URL:** `/api/users/password`
- **Method:** `PATCH`
- **Description:** Update the authenticated user's password
- **Required Fields:**
  - `current_password`
  - `new_password`
  - `new_password_confirmation`
- **Authentication:** Required (Bearer Token)

### Get User Roles

- **URL:** `/api/users/roles`
- **Method:** `GET`
- **Description:** Retrieve available user roles
- **Authentication:** Required (Bearer Token)

### List Users

- **URL:** `/api/users`
- **Method:** `GET`
- **Description:** Retrieve a list of users
- **Authentication:** Required (Bearer Token)

### Get User

- **URL:** `/api/users/{id}`
- **Method:** `GET`
- **Description:** Retrieve details of a specific user
- **Authentication:** Required (Bearer Token)

### Create User

- **URL:** `/api/users`
- **Method:** `POST`
- **Description:** Create a new user
- **Authentication:** Required (Bearer Token)

### Update User (Admin only)

- **URL:** `/api/users/{id}`
- **Method:** `PUT/PATCH`
- **Description:** Update an existing user
- **Authentication:** Required (Bearer Token of an admin user)

### Delete User (Admin only)

- **URL:** `/api/users/{id}`
- **Method:** `DELETE`
- **Description:** Delete a user
- **Authentication:** Required (Bearer Token of an admin user)


## Schools
All routes in this section require authentication using Bearer tokens.

### List Schools
- **URL:** `/api/schools`
- **Method:** `GET`
- **Description:** Retrieve a list of schools
- **Authentication:** Required (Bearer Token)

### Get School
- **URL:** `/api/schools/{id}`
- **Method:** `GET`
- **Description:** Retrieve details of a specific school
- **Authentication:** Required (Bearer Token)

### Create School
- **URL:** `/api/schools`
- **Method:** `POST`
- **Description:** Create a new school
- **Authentication:** Required (Bearer Token)
- **Required Fields:**
  - School Data:
    - `title`: String
    - `address`: String
    - `description`: String
    - `phone_number`: String
    - `founding_year`: Integer
    - `student_capacity`: Integer
    - `photo`: Nullable, image file (jpg, png, jpeg), max size 2048 KB
    - `website`: Nullable, valid URL
  - User Data:
    - `name`: String
    - `email`: String
    - `password`: String
    - `password_confirmation`: String
    - `role`: String (should be 'school' or non-admin role)

### Update School
- **URL:** `/api/schools/{id}`
- **Method:** `PUT/PATCH`
- **Description:** Update an existing school
- **Authentication:** Required (Bearer Token)

### Delete School
- **URL:** `/api/schools/{id}`
- **Method:** `DELETE`
- **Description:** Delete a school
- **Authentication:** Required (Bearer Token)

### Search Schools
- **URL:** `/api/schools/search`
- **Method:** `GET`
- **Description:** Search for schools based on various criteria
- **Authentication:** Required (Bearer Token)
- **Request Body:**
  - `search` (optional): String
- **Response:** Returns a list of schools matching the search criteria

## Students
All routes in this section require authentication using Bearer tokens.

### List Students
- **URL:** `/api/students`
- **Method:** `GET`
- **Description:** Retrieve a list of students
- **Authentication:** Required (Bearer Token)

### Get Student
- **URL:** `/api/students/{id}`
- **Method:** `GET`
- **Description:** Retrieve details of a specific student
- **Authentication:** Required (Bearer Token)

### Create Student
- **URL:** `/api/students`
- **Method:** `POST`
- **Description:** Create a new student
- **Authentication:** Required (Bearer Token)
- **Required Fields:**
  - `name`: String (max 255 characters)
  - `photo`: Nullable, image file (jpg, png, jpeg), max size 2048 KB
  - `email`: String, valid email (max 255 characters, unique in users table)
  - `password`: String (min 8 characters)
  - `password_confirmation`: String (must match password)
  - `school_ids`: Array (at least one school must be selected)
  - `school_ids.*`: Existing school ID
  - `role`: String (must be 'student')

### Update Student
- **URL:** `/api/students/{id}`
- **Method:** `PUT/PATCH`
- **Description:** Update an existing student
- **Authentication:** Required (Bearer Token)

### Delete Student
- **URL:** `/api/students/{id}`
- **Method:** `DELETE`
- **Description:** Delete a student
- **Authentication:** Required (Bearer Token)

### Search Students
- **URL:** `/api/students/search`
- **Method:** `GET`
- **Description:** Search for students based on name
- **Authentication:** Required (Bearer Token)
- **Request Body:**
  - `search` (optional): String
- **Response:** Returns a list of students matching the search criteria

## Teachers
All routes in this section require authentication using Bearer tokens.

### List Teachers
- **URL:** `/api/teachers`
- **Method:** `GET`
- **Description:** Retrieve a list of teachers
- **Authentication:** Required (Bearer Token)

### Get Teacher
- **URL:** `/api/teachers/{id}`
- **Method:** `GET`
- **Description:** Retrieve details of a specific teacher
- **Authentication:** Required (Bearer Token)

### Create Teacher
- **URL:** `/api/teachers`
- **Method:** `POST`
- **Description:** Create a new teacher
- **Authentication:** Required (Bearer Token)
- **Required Fields:**
  - `name`: String (max 255 characters)
  - `photo`: Nullable, image file (jpg, png, jpeg), max size 2048 KB
  - `phone_number`: Nullable, String (max 20 characters)
  - `bio`: Nullable, String
  - `email`: String, valid email (max 255 characters, unique in users table)
  - `password`: String (min 8 characters)
  - `password_confirmation`: String (must match password)
  - `school_ids`: Array (at least one school must be selected)
  - `school_ids.*`: Existing school ID
  - `role`: String (must be 'teacher')

### Update Teacher
- **URL:** `/api/teachers/{id}`
- **Method:** `PUT/PATCH`
- **Description:** Update an existing teacher
- **Authentication:** Required (Bearer Token)

### Delete Teacher
- **URL:** `/api/teachers/{id}`
- **Method:** `DELETE`
- **Description:** Delete a teacher
- **Authentication:** Required (Bearer Token)

### Search Teachers
- **URL:** `/api/teachers/search`
- **Method:** `GET`
- **Description:** Search for teachers based on name
- **Authentication:** Required (Bearer Token)
- **Request Body:**
  - `search` (optional): String
- **Response:** Returns a list of teachers matching the search criteria



## Projects
All routes in this section require authentication using Bearer tokens.

### List Projects
- **URL:** `/api/projects`
- **Method:** `GET`
- **Description:** Retrieve a list of projects
- **Authentication:** Required (Bearer Token)

### Create Project
- **URL:** `/api/projects`
- **Method:** `POST`
- **Description:** Create a new project
- **Authentication:** Required (Bearer Token)
- **Body:** JSON object containing project details

### Get Project
- **URL:** `/api/projects/{id}`
- **Method:** `GET`
- **Description:** Retrieve details of a specific project
- **Authentication:** Required (Bearer Token)

### Update Project
- **URL:** `/api/projects/{id}`
- **Method:** `PUT`
- **Description:** Update details of a specific project
- **Authentication:** Required (Bearer Token)
- **Body:** JSON object containing updated project details

### Delete Project
- **URL:** `/api/projects/{id}`
- **Method:** `DELETE`
- **Description:** Delete a specific project
- **Authentication:** Required (Bearer Token)

### List Project Programs
- **URL:** `/api/projects/{id}/programs`
- **Method:** `GET`
- **Description:** Retrieve a list of programs associated with a specific project
- **Authentication:** Required (Bearer Token)

### List Completed Programs in Project
- **URL:** `/api/projects/{id}/completed-programs`
- **Method:** `GET`
- **Description:** Retrieve a list of completed programs in a specific project
- **Authentication:** Required (Bearer Token)

## Programs
All routes in this section require authentication using Bearer tokens.

### List Programs
- **URL:** `/api/programs`
- **Method:** `GET`
- **Description:** Retrieve a list of programs
- **Authentication:** Required (Bearer Token)

### Create Program
- **URL:** `/api/programs`
- **Method:** `POST`
- **Description:** Create a new program
- **Authentication:** Required (Bearer Token)
- **Body:** JSON object containing program details

### Get Program
- **URL:** `/api/programs/{id}`
- **Method:** `GET`
- **Description:** Retrieve details of a specific program
- **Authentication:** Required (Bearer Token)

### Update Program
- **URL:** `/api/programs/{id}`
- **Method:** `PUT`
- **Description:** Update details of a specific program
- **Authentication:** Required (Bearer Token)
- **Body:** JSON object containing updated program details

### Delete Program
- **URL:** `/api/programs/{id}`
- **Method:** `DELETE`
- **Description:** Delete a specific program
- **Authentication:** Required (Bearer Token)

### List Program Projects
- **URL:** `/api/programs/{id}/projects`
- **Method:** `GET`
- **Description:** Retrieve a list of projects associated with a specific program
- **Authentication:** Required (Bearer Token)

### List Completed Projects for Program
- **URL:** `/api/programs/{id}/completed-projects`
- **Method:** `GET`
- **Description:** Retrieve a list of projects where this program is completed
- **Authentication:** Required (Bearer Token)

## Project-Program Operations
All routes in this section require authentication using Bearer tokens.

### Attach Program to Project
- **URL:** `/api/projects/{project_id}/programs/{program_id}`
- **Method:** `POST`
- **Description:** Attach a program to a project
- **Authentication:** Required (Bearer Token)

### Detach Program from Project
- **URL:** `/api/projects/{project_id}/programs/{program_id}`
- **Method:** `DELETE`
- **Description:** Detach a program from a project
- **Authentication:** Required (Bearer Token)

### Mark Program as Complete in Project
- **URL:** `/api/projects/{project_id}/programs/{program_id}/complete`
- **Method:** `PATCH`
- **Description:** Mark a program as complete within a project
- **Authentication:** Required (Bearer Token)

### Mark Program as Incomplete in Project
- **URL:** `/api/projects/{project_id}/programs/{program_id}/incomplete`
- **Method:** `PATCH`
- **Description:** Mark a program as incomplete within a project
- **Authentication:** Required (Bearer Token)

### Attach Students to Program in Project
- **URL:** `/api/projects/{project_id}/programs/{program_id}/students`
- **Method:** `POST`
- **Description:** Attach students to a program within a project
- **Authentication:** Required (Bearer Token)
- **Body:** JSON array of student IDs