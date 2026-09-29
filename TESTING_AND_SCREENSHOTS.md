# Testing and Screenshot Guide

## 1. Prepare the application

Install dependencies once:

```bash
composer install
npm install
```

Apply the database migrations and load the demo data:

```bash
php artisan migrate --seed
```

> To reset the local database completely, use `php artisan migrate:fresh --seed`. This deletes local database data.

## 2. Run automated tests

Run the full test suite:

```bash
php artisan test --compact
```

Run only the role and client-capability checks:

```bash
php artisan test --compact tests/Feature/RoleHierarchyTest.php
```

Run the community and bill-payment checks:

```bash
php artisan test --compact tests/Feature/CommunityAndBillingTest.php
```

Build the production frontend bundle:

```bash
npm run build
```

## 3. Start the application

```bash
php artisan serve --host=127.0.0.1 --port=8080
```

Open [http://127.0.0.1:8080](http://127.0.0.1:8080) in a browser.

## 4. Demo accounts

All demo accounts use the password `password`.

| Role | Email | Main items to capture |
| --- | --- | --- |
| Client | `client@delestalert.cm` | Personal dashboard, locations, report form, notifications, bills |
| Provider | `provider@delestalert.cm` | Provider dashboard, outage management, plus Client tabs |
| Administrator | `admin@delestalert.cm` | Administration dashboard, map settings, plus Client tabs |

You can also use the role buttons on the login page for quick demo access.

## 5. Suggested screenshots for the report

Take screenshots at desktop width and again at a narrow mobile width (about 390 px) for the responsive-design evidence.

1. Home page — overview of DelestAlert.
2. Login page — show the three one-click demo role buttons.
3. Client dashboard — personalised outage information and analytics.
4. Provider dashboard — operational analytics and the Client tabs in the sidebar.
5. Administrator dashboard — management controls and the Client tabs in the sidebar.
6. Outage management — Provider or Administrator publishing/updating an outage.
7. Community — posts, comments, reactions, and the contribution form.
8. Bill centre — unpaid bill and paid receipt state.
9. Map settings — Administrator switching between Leaflet and Google Maps.
10. French interface — switch the language selector to `FR` and capture a dashboard or outage page.

For each screenshot, keep the browser address bar visible if your report requires evidence of the page URL.
