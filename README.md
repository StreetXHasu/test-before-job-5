### HTTP service for URL shortening, similar to Bitly and other services.

No UI is required, just a JSON API service.

**Features:**

* Save a short representation of a given URL
* Redirect to the original URL using the previously saved short representation

**Requirements:**

* Programming language: PHP
* Provide instructions for running the application
* No restrictions on technologies used – any database can be used for persistence
* Code must be published on GitHub

**Advanced features:**
* URL validation with proper link format checking
* Ability to set custom links, allowing users to create human-readable codes
* Tests written (aim for 70%+ coverage)
* Optionally, you can create a simple UI and deploy the service to free hosting (Google Cloud, AWS, etc.)

---

## Run Instructions

1. Install dependencies:
   ```bash
   composer install
   ```
2. Configure .env (for example, for SQLite or another DB)
3. Run migrations:
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
4. Start the server:
   ```bash
   symfony server:start
   ```
   or
   ```bash
   php -S 127.0.0.1:8000 -t public
   ```

## Main API Routes

- **GET /api/links** — get a list of all links
- **POST /api/links** — add a new link
  - Request body (JSON):
    ```json
    { "url": "https://example.com", "customCode": "my-code" }
    ```
    customCode — optional
- **GET /api/links/{shortCode}** — get a single link by shortCode or customCode
- **GET /{shortCode}** — redirect to the original URL by shortCode or customCode

## Usage Example

1. Create a link:
   ```bash
   curl -X POST http://127.0.0.1:8000/api/links -H 'Content-Type: application/json' -d '{"url": "https://example.com"}'
   ```
2. Get all links:
   ```bash
   curl http://127.0.0.1:8000/api/links
   ```
3. Get a single link:
   ```bash
   curl http://127.0.0.1:8000/api/links/{shortCode}
   ```
4. Follow a short link (redirect):
   Open in browser: http://127.0.0.1:8000/{shortCode}

## API Documentation and Tests via Bruno

The `bruno-api-doc` folder contains collections for API testing in [Bruno](https://www.usebruno.com/). Simply import the .bru files into Bruno and test all methods.
