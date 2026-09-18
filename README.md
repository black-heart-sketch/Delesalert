# DelestAlert

A Laravel 13 electricity-outage platform for Cameroon. It distinguishes official provider outages, client reports, and AI forecasts.

## Local start

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

The seeded accounts use Laravel's factory password (`password`):

| Role | Email |
| --- | --- |
| Admin | `admin@delestalert.cm` |
| Provider | `provider@delestalert.cm` |
| Client | `client@delestalert.cm` |

## Integrations

Set these only in `.env`, never in source control:

```env
MAP_PROVIDER=leaflet
GOOGLE_MAPS_API_KEY=browser-key-restricted-to-your-domain
AI_DRIVER=openrouter
OPENROUTER_API_KEY=your-openrouter-key
OPENROUTER_MODEL=openai/gpt-4o-mini
```

The public map configuration endpoint returns Google’s browser key only while Google Maps is selected. Restrict that key by HTTP referrer in Google Cloud. An admin can switch providers through `PUT /api/admin/map-configuration` with `{ "provider": "google" }` or `{ "provider": "leaflet" }`.

OpenRouter is optional. With no key, the prediction service uses its deterministic statistical baseline. With an OpenRouter key, the service asks the configured model for a probability and safely falls back to the baseline on service errors.

## API highlights

- `POST /api/auth/register`, `POST /api/auth/login`, `POST /api/auth/logout`
- Public: `GET /api/outages/current`, `/scheduled`, `/history`, `/map/configuration`
- Authenticated: locations, client reports, notification preferences, predictions
- Provider/admin: outage management and provider incidents
- Admin: users, zones, report reviews, prediction generation, map-provider setting

Use a bearer token from the authentication response. API responses follow `{ "success": true, "data": ... }`; validation errors are Laravel JSON validation responses.

## Verification

```bash
php artisan test
php artisan migrate:fresh --seed
```
