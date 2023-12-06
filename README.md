# Routes

-   GET `/api/users` - list all users
    -   Response:
    ```php
      {
          "success": boolean,
          "data": [...users]
      }
    ```
-   POST `/api/users` - create user
    -   Expected input:
        ```php
            {
                "first_name": string,
                "last_name": string,
                "email": string,
                "password": string,
                "address": string // optional
            }
        ```
    -   Response:
        ```php
        {
            "success": boolean,
            "data": created_user
        }
        ```
-   PUT `/api/users/{user_id}` - update user with id `user_id` (only with API key)

    -   **Use Token from `/api/users/auth` as a Bearer token in headers.**
    -   Expected input:

        ```php
            {
                "first_name": string,
                "last_name": string,
                "email": string,
                "password": string,
                "address": string // optional
            }
        ```

    -   Response:
        ```php
        {
            "success": boolean,
            "data": updated_user
        }
        ```

-   DELETE `/api/users/{user_id}` - delete user with id `user_id` (only with API key)
    -   **Use Token from `/api/users/auth` as a Bearer token in headers.**
    -   Reponse:
        ```php
        {
            "success": boolean,
            "message": string
        }
        ```
-   POST `/api/users/auth` - authenticate - returns an API key
    -   Expected input:
        ```php
        {
            "email": string,
            "password": string,
            "token_name": string
        }
        ```
    -   Response:
        ```php
        {
            "success": boolean,
            "data": string // generated token
        }
        ```
-   GET `/api/user` - returns authenticated user (only with API key)
    -   **Use Token from `/api/users/auth` as a Bearer token in headers.**
    -   Response:
        ```php
        {
            ...authenticated_user
        }
        ```

### Notes

- Laravel v10, requires a minimum PHP version of 8.1

- uses MySQL, `test_task` DB for 'production', `test_task_test` for testing environment.

- **User object:**

    ```php
    {
    "id": integer,
    "first_name": string,
    "last_name": string,
    "email": string,
    "created_at": string, // timestamp
    "updated_at": string, // timestamp
    "address": string | null
    }
    ```
