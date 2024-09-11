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

## Schools, Teachers, and Students

These routes are yet to be implemented. They will be accessible under the following prefixes:

- Schools: `/api/schools`
- Teachers: `/api/teachers`
- Students: `/api/students`

All these routes will require authentication using Bearer tokens.

## General Notes

1. All protected routes require a valid Bearer token obtained through the login process.
2. The Bearer token should be included in the Authorization header for all protected routes.
3. All responses will be in JSON format.
4. Errors will include appropriate HTTP status codes and error messages in the response body.
5. Only admin users can register new users through the `/api/register` endpoint.
6. User update and delete operations are restricted to admin users only.

For more detailed information about request and response formats, please refer to the API specification or contact the development team.