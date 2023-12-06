# Routes

-   GET `/api/users` - list all users
    -   Response:
    ```js
      {
          "success": boolean,
          "data": [...users]
      }
    ```
-   POST `/api/users` - create user
    -   Expected input:
        ```js
            {
                "first_name": string,
                "last_name": string,
                "email": string,
                "password": string,
                "address": string // optional
            }
        ```
    -   Response:
        ```js
        {
            "success": boolean,
            "data": created_user
        }
        ```
-   PUT `/api/users/{user_id}` - update user with id `user_id` (only with API key)

    -   **Use Token from `/api/users/auth` as a Bearer token in headers.**
    -   Expected input:

        ```js
            {
                "first_name": string,
                "last_name": string,
                "email": string,
                "password": string,
                "address": string // optional
            }
        ```

    -   Response:
        ```js
        {
            "success": boolean,
            "data": updated_user
        }
        ```

-   DELETE `/api/users/{user_id}` - delete user with id `user_id` (only with API key)
    -   **Use Token from `/api/users/auth` as a Bearer token in headers.**
    -   Reponse:
        ```js
        {
            "success": boolean,
            "message": string
        }
        ```
-   POST `/api/users/auth` - authenticate - returns an API key
    -   Expected input:
        ```js
        {
            "email": string,
            "password": string,
            "token_name": string
        }
        ```
    -   Response:
        ```js
        {
            "success": boolean,
            "data": string // generated token
        }
        ```
-   GET `/api/user` - returns authenticated user (only with API key)
    -   **Use Token from `/api/users/auth` as a Bearer token in headers.**
    -   Response:
        ```js
        {
            ...authenticated_user
        }
        ```

### Notes

- Laravel v10, requires a minimum PHP version of 8.1

- uses MySQL, `test_task` DB for 'production', `test_task_test` for testing environment.

- **User object:**

    ```js
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
- Routes can also respond with an error object as well, if the input is incorrect (from laravel request validation):
    ```js
        "message": string, // error message
        "errors" { field: [...errors] } // all errors per field.
    ```
