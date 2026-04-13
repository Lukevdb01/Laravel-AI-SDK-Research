# Laravel AI SDK Research

## Requirements

- PHP 8.4+
- Composer
- Node.js 20+ and npm
- Docker (optional, for Qdrant and Ollama)

## Start The Project

1. Install PHP & Node dependencies:

```bash
npm install
composer install
```

2. Create your environment file and app key:

```bash
cp .env.example .env
php artisan key:generate
```

3. Add your API and vector DB settings in `.env`:

```env
GEMINI_API_KEY=your_gemini_api_key_here
QDRANT_HOST_URL=http://127.0.0.1:6333
OLLAMA_BASE_URL=http://localhost:11434
```

4. Create/start supporting AI services (Qdrant + Ollama):

```bash
docker compose up -d
```

5. Pull the embedding model used by the app:

```bash
docker exec -it $(docker ps -qf "ancestor=ollama/ollama:latest") ollama pull nomic-embed-text
```

If you are on Windows PowerShell and command substitution fails, run:

```powershell
$id = docker ps -qf "ancestor=ollama/ollama:latest"
docker exec -it $id ollama pull nomic-embed-text
```

6. Run database migrations and seed data:

```bash
php artisan migrate --seed
```

This seeder also generates embeddings through Ollama and stores vectors in Qdrant, so make sure both services are running before this step.

7. Start Laravel and Vite in one command:

```bash
composer run dev
```

8. Open the app:

```text
http://127.0.0.1:8000
```

## Useful Commands

- Migrate, refresh db and seed: `php artisan migrate:fresh --seed`
- Run tests: `composer test`
- Run linter fix: `composer run lint`
- Frontend type check: `npm run types:check`

## Notes

- Ollama is configured in Laravel via `OLLAMA_BASE_URL` in `.env` and is used by both the dashboard flow and database seeder for embeddings.
- Qdrant is configured in Laravel via `QDRANT_HOST_URL` in `.env`.
- Gemini is configured in Laravel via `GEMINI_API_KEY` in `.env`.