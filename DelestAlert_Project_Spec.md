# DelestAlert — Full Project Specification for Implementation

## 1. Project Title

**DelestAlert — Intelligent Electricity Outage Prediction and Notification System for Cameroon**

---

## 2. Project Overview

DelestAlert is a digital platform designed to help users in Cameroon:

- view electricity outages currently in progress;
- view planned electricity outages;
- visualize affected areas on a map;
- receive outage and restoration notifications;
- report electricity outages;
- consult outage history;
- receive intelligent outage predictions;
- allow electricity providers to publish and update outage-related information;
- allow administrators to manage users, locations, outages, reports, notifications, and prediction supervision.

The system combines:

- a user-facing application;
- a backend system;
- a database;
- Google Maps API;
- an AI/prediction component;
- a notification service.

The main objective is to reduce the uncertainty caused by electricity outages by centralizing outage information, community reports, official provider information, geographic visualization, and predictive analytics.

---

# 3. Main Objectives

The system must:

1. Inform users about ongoing electricity outages.
2. Inform users about scheduled outages.
3. Display affected zones geographically.
4. Allow registered clients to report outages.
5. Notify clients when an outage affects one of their saved locations.
6. Notify clients when electricity is restored.
7. Store outage history for consultation and analysis.
8. Allow electricity providers to declare incidents and scheduled outages.
9. Allow administrators to manage the platform.
10. Use historical and operational data to predict possible future outages.
11. Estimate probable outage duration where enough data is available.
12. Identify zones with a high outage risk.

---

# 4. Actors

## 4.1 Abstract Actor: User

`User` is an abstract actor representing common functionality shared by human users of DelestAlert.

Specialized actors:

- Visitor
- Client
- Administrator

Generalization:

```text
                    <<abstract>>
                       User
                    /   |    \
                   /    |     \
             Visitor  Client  Administrator
```

---

## 4.2 Visitor

A Visitor is a person who has not yet authenticated or may not yet have an account.

Main capabilities:

- view public outage information;
- view current outages;
- view scheduled outages;
- view outage map;
- view general network status;
- create an account;
- log in.

---

## 4.3 Client

A Client is a registered authenticated user.

Main capabilities:

- inherit common User capabilities;
- manage profile;
- manage saved locations;
- configure notification preferences;
- report an outage;
- view report history;
- view outage history;
- receive outage notifications;
- receive restoration notifications;
- view personalized predictions;
- log out.

---

## 4.4 Administrator

An Administrator is an authorized platform manager.

Main capabilities:

- inherit common User capabilities;
- manage users;
- manage zones/localities;
- manage outages;
- manage reports;
- validate or reject reports;
- view statistics;
- supervise predictions;
- manage notifications;
- configure system settings.

---

## 4.5 Provider

The Provider represents an electricity provider or authorized official source, for example an electricity company.

The Provider is independent from the User inheritance hierarchy.

Main capabilities:

- transmit network data;
- publish scheduled outages;
- declare electrical incidents;
- update outage status;
- signal electricity restoration.

---

## 4.6 AI Prediction Engine

The AI engine is responsible for prediction-related processing.

Depending on the final architecture, it can be:

- an internal module of DelestAlert; or
- a separate AI service.

Main responsibilities:

- analyze historical outage data;
- analyze incident data;
- analyze user reports;
- detect high-risk zones;
- predict possible outages;
- estimate probable outage duration;
- calculate confidence/risk score.

---

## 4.7 Google Maps API

Google Maps API is an external service used for:

- displaying maps;
- geocoding addresses;
- reverse geocoding;
- displaying outage markers;
- displaying affected zones;
- showing user saved locations;
- resolving geographic coordinates.

---

## 4.8 Notification Service

An external or internal notification service may be used to send:

- push notifications;
- email notifications;
- SMS notifications.

---

# 5. Main Use Cases

## 5.1 Common User Use Cases

```text
User ---------------------> Consult scheduled outages
User ---------------------> Consult current outages
User ---------------------> Consult outage map
User ---------------------> Consult network status
```

---

## 5.2 Visitor Use Cases

```text
Visitor ------------------> Create account
Visitor ------------------> Log in
```

---

## 5.3 Client Use Cases

```text
Client -------------------> Manage profile
Client -------------------> Manage saved locations
Client -------------------> Report an outage
Client -------------------> Consult outage history
Client -------------------> Consult report history
Client -------------------> Configure notifications
Client <------------------- Receive outage notification
Client <------------------- Receive restoration notification
Client -------------------> Consult personalized predictions
Client -------------------> Log out
```

---

## 5.4 Administrator Use Cases

```text
Administrator ------------> Manage users
Administrator ------------> Manage zones
Administrator ------------> Manage outages
Administrator ------------> Manage reports
Administrator ------------> Validate reports
Administrator ------------> Reject reports
Administrator ------------> Consult statistics
Administrator ------------> Supervise predictions
Administrator ------------> Manage notifications
Administrator ------------> Configure system
```

---

## 5.5 Provider Use Cases

Avoid using the repeated verb "provide" in UI/use-case naming.

Use:

```text
Provider -----------------> Transmit network data
Provider -----------------> Publish scheduled outages
Provider -----------------> Declare electrical incidents
Provider -----------------> Update outage status
Provider -----------------> Signal electricity restoration
```

---

# 6. Detailed Use Cases

## 6.1 Manage Profile

Parent use case:

**Manage Profile**

Specific operations:

- view profile;
- update personal information;
- change password;
- delete account.

If saved location management already exists as a separate use case, location editing should not be duplicated inside Manage Profile.

---

## 6.2 Manage Reports

Parent use case:

**Manage Reports**

Specific operations:

- consult reports;
- view report details;
- validate a report;
- reject a report;
- update report status.

Recommended report statuses:

```text
PENDING
UNDER_REVIEW
CONFIRMED
REJECTED
RESOLVED
```

Avoid permanently deleting confirmed operational reports unless required. Keeping them is useful for analytics, statistics, audit history, and AI training data.

---

# 7. Core Functional Modules

The application should be separated into the following modules.

## 7.1 Authentication Module

Features:

- registration;
- login;
- logout;
- password reset;
- token/session management;
- role-based access control;
- account status management.

Roles:

```text
VISITOR
CLIENT
ADMIN
PROVIDER
```

`VISITOR` may not need to exist in the database because it represents an unauthenticated person.

---

## 7.2 User Management Module

Features:

- create account;
- view account;
- update profile;
- change password;
- delete/deactivate account;
- administrator user search;
- administrator user activation/deactivation;
- role management.

---

## 7.3 Location Module

A Client can save one or more locations.

Examples:

- Home
- Office
- School
- Shop

Each saved location should contain:

```text
id
user_id
label
address
latitude
longitude
city
district
region
is_primary
created_at
updated_at
```

---

## 7.4 Outage Module

An outage represents an electricity interruption.

Outage types:

```text
SCHEDULED
UNPLANNED
PREDICTED
```

Outage statuses:

```text
PLANNED
ONGOING
RESOLVED
CANCELLED
```

Suggested fields:

```text
id
title
description
type
status
source
region
city
district
latitude
longitude
affected_radius
scheduled_start
expected_end
actual_start
actual_end
estimated_duration_minutes
created_by
created_at
updated_at
```

---

## 7.5 Incident Module

An incident is declared by a Provider.

Suggested fields:

```text
id
provider_id
title
description
incident_type
severity
region
city
district
latitude
longitude
occurred_at
status
created_at
updated_at
```

Suggested severity values:

```text
LOW
MEDIUM
HIGH
CRITICAL
```

---

## 7.6 User Report Module

A Client can report a suspected or observed electricity outage.

Suggested fields:

```text
id
user_id
description
latitude
longitude
address
reported_at
status
validated_by
validated_at
rejection_reason
created_at
updated_at
```

Optional:

- image attachment;
- number of confirmations from nearby users.

---

## 7.7 Notification Module

Notification types:

```text
OUTAGE_STARTED
OUTAGE_PREDICTED
OUTAGE_SCHEDULED
OUTAGE_UPDATED
POWER_RESTORED
SYSTEM_MESSAGE
```

Notification channels:

```text
PUSH
EMAIL
SMS
```

Suggested notification fields:

```text
id
user_id
type
title
message
channel
status
outage_id
sent_at
read_at
created_at
```

Notification preferences:

```text
user_id
push_enabled
email_enabled
sms_enabled
scheduled_outage_alerts
predicted_outage_alerts
restoration_alerts
minimum_risk_threshold
```

---

# 8. Google Maps Integration

Google Maps API should be used only through the System/backend or application integration layer.

The Client must not directly access internal database resources.

For current outage consultation:

```text
Client -> System
System -> DBMS
System -> Google Maps API
System -> Client
```

Main mapping features:

- display outage markers;
- display affected area;
- center map on user's current or saved location;
- geocode provider-entered addresses;
- reverse geocode user-selected map coordinates;
- group nearby outage markers;
- display outage information when a marker is selected.

Marker information may include:

```text
Outage title
Status
Outage type
Start time
Estimated end time
Affected zone
Source
```

---

# 9. AI / Prediction Module

## 9.1 Purpose

The prediction engine estimates whether an electricity outage is likely to occur in a specific zone and optionally estimates its duration.

## 9.2 Possible Input Data

The AI may consume:

- historical outages;
- outage frequency by zone;
- outage duration by zone;
- current incidents;
- scheduled maintenance;
- provider network data;
- user outage reports;
- time of day;
- day of week;
- seasonal patterns;
- geographic zone;
- recent outage density.

If weather data is later added, it can also become an AI feature input.

---

## 9.3 Prediction Output

A prediction should contain:

```text
zone
latitude
longitude
predicted_start
predicted_end
estimated_duration_minutes
outage_probability
risk_level
confidence_score
generated_at
model_version
```

Suggested risk levels:

```text
LOW
MEDIUM
HIGH
CRITICAL
```

---

## 9.4 Prediction Business Rule

Do not present every model output as a certain outage.

Example:

```text
Probability < 40%   -> LOW
40% - 59%           -> MEDIUM
60% - 79%           -> HIGH
80%+                -> CRITICAL
```

These thresholds should be configurable.

---

# 10. Sequence Flow — Consult Current Outages

Participants:

```text
Client
System
DBMS
Google Maps API
```

Flow:

```text
Client -> System
Click "Current Outages"

System -> DBMS
Search current outages

DBMS -> DBMS
Execute query

DBMS --> System
Return matching outages

System -> System
Process results
```

Decision:

```text
Are current outages found?
```

If NO:

```text
System --> Client
Display "No current outage"
```

If YES:

```text
System -> Google Maps API
Send outage coordinates

Google Maps API -> Google Maps API
Process coordinates

Google Maps API --> System
Return map data

System -> System
Validate map data
```

If map data is valid:

```text
System --> Client
Display current outages on map
```

If invalid:

```text
System --> Client
Display map loading error
```

---

# 11. Activity Flow — Consult Current Outages

Swimlanes:

```text
CLIENT | SYSTEM | DBMS | GOOGLE MAPS API
```

Flow:

```text
START
  |
[CLIENT]
Click "Current Outages"
  |
[SYSTEM]
Search current outages
  |
[DBMS]
Execute query
  |
[SYSTEM]
Process results
  |
<Current outages found?>
  |
  +-- NO --> [CLIENT]
  |          Display "No current outage"
  |          END
  |
  +-- YES --> [SYSTEM]
               Send outage coordinates
                 |
              [GOOGLE MAPS API]
               Process coordinates
                 |
              [SYSTEM]
               Validate map data
                 |
              <Map data valid?>
                 |
                 +-- NO --> [CLIENT]
                 |          Display error message
                 |          END
                 |
                 +-- YES --> [CLIENT]
                            Display current outages on map
                            END
```

---

# 12. Sequence Flow — Predict an Outage

Participants:

```text
Client
System
DBMS
AI
```

Flow:

```text
Client -> System
Click "Predictions"

System -> DBMS
Retrieve historical and operational data

DBMS -> DBMS
Execute query

DBMS --> System
Return historical data

System -> System
Validate data
```

Decision:

```text
Is there enough data?
```

If NO:

```text
System --> Client
Display "Insufficient data to generate prediction"
```

If YES:

```text
System -> AI
Send data for prediction

AI -> AI
Analyze data

AI -> AI
Generate outage prediction

AI --> System
Return prediction result

System -> System
Process result
```

Decision:

```text
Is prediction available?
```

If YES:

```text
System --> Client
Display predicted zone, time, duration, probability and risk
```

If NO:

```text
System --> Client
Display prediction error message
```

---

# 13. Activity Flow — Predict an Outage

Swimlanes:

```text
CLIENT | SYSTEM | DBMS | AI
```

Flow:

```text
START
  |
[CLIENT]
Click "Predictions"
  |
[SYSTEM]
Request historical data
  |
[DBMS]
Execute query
  |
[SYSTEM]
Validate data
  |
<Enough data?>
  |
  +-- NO --> [CLIENT]
  |          Display "Insufficient data"
  |          END
  |
  +-- YES --> [SYSTEM]
               Send data to AI
                 |
              [AI]
               Analyze data
                 |
              [AI]
               Generate prediction
                 |
              [SYSTEM]
               Process prediction result
                 |
              <Prediction available?>
                 |
                 +-- NO --> [CLIENT]
                 |          Display error message
                 |          END
                 |
                 +-- YES --> [CLIENT]
                            Display outage prediction
                            END
```

---

# 14. Sequence Flow — Declare an Incident

Participants:

```text
Provider
System
DBMS
```

Flow:

```text
Provider -> System
Click "Declare Incident"

System --> Provider
Display incident form

Provider -> System
Enter incident information

Provider -> System
Click "Declare"

System -> System
Validate entered data
```

Decision:

```text
Are data valid?
```

If NO:

```text
System --> Provider
Display validation errors
```

Provider may correct the information and submit again.

If YES:

```text
System -> DBMS
Save incident

DBMS -> DBMS
Execute insert query

DBMS --> System
Return save result

System -> System
Verify save result
```

Decision:

```text
Was the incident saved?
```

If YES:

```text
System --> Provider
Display "Incident declared successfully"
```

If NO:

```text
System --> Provider
Display error message
```

---

# 15. Activity Flow — Declare an Incident

Swimlanes:

```text
PROVIDER | SYSTEM | DBMS
```

Flow:

```text
START
  |
[PROVIDER]
Click "Declare Incident"
  |
[SYSTEM]
Display declaration form
  |
[PROVIDER]
Enter incident information
  |
[PROVIDER]
Click "Declare"
  |
[SYSTEM]
Validate data
  |
<Data valid?>
  |
  +-- NO --> [SYSTEM]
  |          Identify validation errors
  |            |
  |          [PROVIDER]
  |          Display/correct invalid information
  |            |
  |          Return to validation
  |
  +-- YES --> [SYSTEM]
               Send save request
                 |
              [DBMS]
               Execute save query
                 |
              [SYSTEM]
               Verify save result
                 |
              <Incident saved?>
                 |
                 +-- NO --> [PROVIDER]
                 |          Display error message
                 |          END
                 |
                 +-- YES --> [PROVIDER]
                            Display "Incident declared successfully"
                            END
```

---

# 16. Suggested Database Entities

Minimum entities:

```text
User
Role
SavedLocation
Outage
Incident
OutageReport
Prediction
Notification
NotificationPreference
Zone
Provider
AuditLog
```

---

# 17. Suggested Database Relationships

```text
User 1 ----- N SavedLocation

User 1 ----- N OutageReport

User 1 ----- N Notification

User 1 ----- 1 NotificationPreference

Provider 1 ----- N Incident

Provider 1 ----- N Outage

Zone 1 ----- N Outage

Zone 1 ----- N Incident

Zone 1 ----- N Prediction

Outage 1 ----- N Notification

OutageReport N ----- 1 Zone

Prediction N ----- 1 Zone

Administrator 1 ----- N validated OutageReport
```

---

# 18. Suggested User Entity

```json
{
  "id": "uuid",
  "firstName": "string",
  "lastName": "string",
  "email": "string",
  "phone": "string",
  "passwordHash": "string",
  "role": "CLIENT | ADMIN | PROVIDER",
  "status": "ACTIVE | SUSPENDED | DEACTIVATED",
  "createdAt": "datetime",
  "updatedAt": "datetime"
}
```

---

# 19. Suggested Outage Entity

```json
{
  "id": "uuid",
  "title": "string",
  "description": "string",
  "type": "SCHEDULED | UNPLANNED | PREDICTED",
  "status": "PLANNED | ONGOING | RESOLVED | CANCELLED",
  "source": "PROVIDER | USER_REPORT | AI | ADMIN",
  "zoneId": "uuid",
  "latitude": 4.0511,
  "longitude": 9.7679,
  "affectedRadiusKm": 2.5,
  "scheduledStart": "datetime|null",
  "expectedEnd": "datetime|null",
  "actualStart": "datetime|null",
  "actualEnd": "datetime|null",
  "estimatedDurationMinutes": 120,
  "createdAt": "datetime",
  "updatedAt": "datetime"
}
```

---

# 20. Suggested Incident Entity

```json
{
  "id": "uuid",
  "providerId": "uuid",
  "title": "Transformer failure",
  "description": "string",
  "incidentType": "TECHNICAL_FAILURE",
  "severity": "HIGH",
  "zoneId": "uuid",
  "latitude": 4.0511,
  "longitude": 9.7679,
  "occurredAt": "datetime",
  "status": "OPEN | IN_PROGRESS | RESOLVED",
  "createdAt": "datetime",
  "updatedAt": "datetime"
}
```

---

# 21. Suggested Prediction Entity

```json
{
  "id": "uuid",
  "zoneId": "uuid",
  "predictedStart": "datetime",
  "predictedEnd": "datetime|null",
  "estimatedDurationMinutes": 90,
  "probability": 0.78,
  "riskLevel": "HIGH",
  "confidenceScore": 0.82,
  "modelVersion": "v1.0",
  "generatedAt": "datetime"
}
```

---

# 22. API Requirements

The backend should expose REST endpoints or equivalent APIs.

## Authentication

```text
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
POST   /api/auth/forgot-password
POST   /api/auth/reset-password
```

## Profile

```text
GET    /api/profile
PATCH  /api/profile
PATCH  /api/profile/password
DELETE /api/profile
```

## Saved Locations

```text
GET    /api/locations
POST   /api/locations
GET    /api/locations/:id
PATCH  /api/locations/:id
DELETE /api/locations/:id
```

## Outages

```text
GET    /api/outages/current
GET    /api/outages/scheduled
GET    /api/outages/history
GET    /api/outages/:id
POST   /api/outages
PATCH  /api/outages/:id
```

## Reports

```text
POST   /api/reports
GET    /api/reports/my
GET    /api/admin/reports
GET    /api/admin/reports/:id
PATCH  /api/admin/reports/:id/status
```

## Incidents

```text
POST   /api/provider/incidents
GET    /api/provider/incidents
GET    /api/provider/incidents/:id
PATCH  /api/provider/incidents/:id
```

## Predictions

```text
GET    /api/predictions
GET    /api/predictions/:zoneId
POST   /api/predictions/generate
```

## Notifications

```text
GET    /api/notifications
PATCH  /api/notifications/:id/read
GET    /api/notification-preferences
PATCH  /api/notification-preferences
```

## Admin

```text
GET    /api/admin/users
PATCH  /api/admin/users/:id/status

GET    /api/admin/zones
POST   /api/admin/zones
PATCH  /api/admin/zones/:id
DELETE /api/admin/zones/:id

GET    /api/admin/statistics
GET    /api/admin/predictions
```

---

# 23. Suggested Screens

## Public / Visitor

1. Splash screen
2. Landing/Home screen
3. Current outages
4. Scheduled outages
5. Outage map
6. Login
7. Registration
8. Forgot password

## Client

1. Client dashboard
2. Current outages
3. Scheduled outages
4. Outage map
5. Predictions
6. Report outage
7. My reports
8. Outage history
9. Saved locations
10. Notifications
11. Notification settings
12. Profile

## Provider

1. Provider dashboard
2. Declare incident
3. Incident list
4. Publish scheduled outage
5. Outage list
6. Update outage
7. Signal restoration
8. Network data entry/import

## Administrator

1. Admin dashboard
2. User management
3. Zone management
4. Outage management
5. Report management
6. Report validation
7. Prediction supervision
8. Notification management
9. Statistics
10. System settings

---

# 24. Client Dashboard

The Client dashboard may contain:

- current network status;
- nearby ongoing outages;
- scheduled outages affecting saved locations;
- latest prediction;
- risk indicator;
- quick report button;
- unread notification count;
- mini map.

---

# 25. Administrator Dashboard

Recommended cards:

```text
Total users
Active clients
Current outages
Scheduled outages
Open incidents
Pending reports
Validated reports
Predictions generated today
High-risk zones
Notifications sent
```

Recommended charts:

- outages by month;
- outages by region;
- average outage duration;
- reports by status;
- predicted vs actual outages;
- outage frequency by zone.

---

# 26. Provider Dashboard

Recommended information:

- ongoing incidents;
- scheduled outages;
- unresolved incidents;
- recently restored zones;
- incident severity summary;
- outage publication history.

Quick actions:

```text
Declare incident
Publish scheduled outage
Update outage status
Signal restoration
```

---

# 27. Report-Outage Flow

Suggested form:

```text
Location
Latitude
Longitude
Description
Observed start time
Optional photo
```

Flow:

```text
Client submits report
        |
System validates data
        |
System stores report as PENDING
        |
Admin receives pending report
        |
Admin reviews report
        |
   +----+----+
   |         |
Confirm     Reject
   |         |
CONFIRMED   REJECTED
   |
May contribute to outage detection / AI
```

---

# 28. Outage Notification Logic

When a new outage is created or predicted:

```text
1. Determine affected geographic zone.
2. Find Clients with saved locations inside the affected area.
3. Read notification preferences.
4. Create notification records.
5. Send enabled notification channels.
6. Record delivery status.
```

For restoration:

```text
Outage status changes to RESOLVED
        |
Find previously notified affected Clients
        |
Send POWER_RESTORED notification
```

---

# 29. Prediction Workflow

```text
Collect data
    |
Clean data
    |
Aggregate by zone/time
    |
Create model features
    |
Run prediction model
    |
Calculate outage probability
    |
Estimate duration
    |
Assign risk level
    |
Store prediction
    |
Notify affected Clients if threshold reached
```

---

# 30. AI Model Strategy

For the first implementation, avoid unnecessary complexity.

A progressive strategy is recommended.

## Version 1 — Rule/Statistical Baseline

Use historical statistics such as:

- outage count per zone;
- recent incident count;
- average duration;
- day/time frequency;
- recent user report count.

Generate a risk score.

This is suitable for an MVP even before a large dataset exists.

## Version 2 — Machine Learning

When enough historical data exists, train a supervised model.

Possible models:

- Logistic Regression;
- Random Forest;
- Gradient Boosting;
- XGBoost.

Tasks:

1. classification: outage likely / not likely;
2. regression: estimated outage duration.

The AI service should expose a clean API so the model can later be replaced without changing the rest of the application.

---

# 31. Recommended Prediction API Contract

Request:

```json
{
  "zoneId": "uuid",
  "timestamp": "datetime",
  "historicalOutages": [],
  "recentIncidents": [],
  "recentReports": [],
  "networkIndicators": {}
}
```

Response:

```json
{
  "predictionAvailable": true,
  "probability": 0.78,
  "riskLevel": "HIGH",
  "predictedStart": "datetime",
  "estimatedDurationMinutes": 90,
  "confidence": 0.82,
  "modelVersion": "v1.0"
}
```

---

# 32. Suggested Architecture

A practical architecture is:

```text
                Mobile/Web Client
                       |
                       v
                Backend REST API
                       |
       +---------------+----------------+
       |               |                |
       v               v                v
     DBMS        Google Maps API    Notification Service
       |
       v
 Historical / operational data
       |
       v
   AI Prediction Service
```

The Client must communicate with the backend System.

The backend System is responsible for:

- authorization;
- business logic;
- database access;
- AI service calls;
- Google Maps integration where applicable;
- notification triggering.

---

# 33. Suggested Technology Stack

This section is a recommendation and can be changed.

## Option A — Mobile-first

Frontend:

```text
Flutter
Dart
Provider / Riverpod / Bloc
Google Maps Flutter
```

Backend:

```text
Node.js
NestJS or Express
TypeScript
```

Database:

```text
PostgreSQL
PostGIS recommended for geographic queries
```

AI:

```text
Python
FastAPI
scikit-learn / XGBoost
pandas
```

Notifications:

```text
Firebase Cloud Messaging
Email provider
Optional SMS provider
```

Maps:

```text
Google Maps Platform
```

## Option B — Web + Mobile

Web frontend:

```text
Vue 3 or React
TypeScript
```

Mobile:

```text
Flutter
```

Same backend/database/AI stack as above.

---

# 34. Geographic Queries

If PostgreSQL + PostGIS is used, the backend should support:

- find outages inside radius;
- find users/saved locations inside affected area;
- find closest outage;
- cluster outages by zone;
- calculate distance between saved location and outage.

Example conceptual query:

```text
Find all saved client locations within X kilometers
of an outage latitude/longitude.
```

This is important for notification targeting.

---

# 35. Security Requirements

The implementation must include:

- hashed passwords using bcrypt/Argon2;
- JWT or secure session authentication;
- refresh token strategy if JWT is used;
- role-based authorization;
- input validation;
- request rate limiting;
- API error handling;
- database parameterization/ORM;
- protection against injection;
- secure environment variables;
- API key protection;
- server-side role validation;
- audit logging for sensitive admin/provider actions.

Never expose:

- database credentials;
- Google API secret keys;
- AI service secrets;
- SMS/email secrets;
- JWT secrets

inside the frontend source code.

---

# 36. Authorization Rules

Examples:

```text
Visitor:
- read public outage data
- register
- login

Client:
- all public reads
- manage own profile
- manage own locations
- submit own reports
- read own notifications

Provider:
- manage only provider-authorized incidents/outages
- signal restoration
- publish scheduled outages

Administrator:
- manage all users
- manage zones
- review reports
- manage outages
- inspect predictions
- access statistics
```

---

# 37. Validation Rules

Examples:

## Registration

```text
Email must be valid.
Email must be unique.
Phone must be valid if required.
Password must satisfy security rules.
```

## Incident

```text
Title required.
Description required.
Location required.
Occurrence time required.
Severity required.
```

## User report

```text
Location required.
Description required.
Coordinates must be valid.
```

## Outage

```text
Affected zone required.
Type required.
Status required.
Scheduled start required for SCHEDULED outage.
```

---

# 38. Error Handling

The backend should use consistent error responses.

Example:

```json
{
  "success": false,
  "code": "VALIDATION_ERROR",
  "message": "Invalid incident data",
  "errors": {
    "location": "Location is required"
  }
}
```

Success example:

```json
{
  "success": true,
  "message": "Incident declared successfully",
  "data": {}
}
```

---

# 39. Audit Logging

Important actions should be logged:

- admin validates report;
- admin rejects report;
- provider declares incident;
- provider publishes outage;
- provider signals restoration;
- admin suspends user;
- admin changes system configuration.

Suggested fields:

```text
id
actor_id
actor_role
action
entity_type
entity_id
metadata
created_at
```

---

# 40. Testing Requirements

The project should include:

## Unit Tests

- authentication;
- outage services;
- report validation;
- incident creation;
- notification targeting;
- prediction service;
- role authorization.

## Integration Tests

- API + database;
- AI service communication;
- Google Maps/geocoding integration abstraction;
- notification service integration abstraction.

## End-to-End Tests

Critical scenarios:

```text
Register -> Login -> Save location
Client -> View current outage map
Client -> Report outage
Admin -> Validate report
Provider -> Declare incident
Provider -> Signal restoration
System -> Notify affected clients
Client -> View prediction
```

---

# 41. Seed Data

Codex should generate development seed data containing:

- one administrator;
- one provider;
- several clients;
- Cameroon regions/cities/zones;
- sample current outages;
- sample scheduled outages;
- sample resolved outages;
- sample incidents;
- sample user reports;
- sample predictions;
- sample notifications.

Never use production passwords in seed data.

---

# 42. Cameroon-Specific Initial Geography

The system should support Cameroon geography.

At minimum, design the schema so it can represent:

```text
Region
City
District / Locality
Latitude
Longitude
```

Do not hard-code the application to only one city.

---

# 43. Recommended Project Structure

If using NestJS backend:

```text
backend/
├── src/
│   ├── auth/
│   ├── users/
│   ├── locations/
│   ├── zones/
│   ├── outages/
│   ├── incidents/
│   ├── reports/
│   ├── predictions/
│   ├── notifications/
│   ├── providers/
│   ├── admin/
│   ├── maps/
│   ├── audit/
│   ├── common/
│   └── main.ts
├── test/
├── .env.example
└── README.md
```

AI service:

```text
ai-service/
├── app/
│   ├── api/
│   ├── models/
│   ├── services/
│   ├── schemas/
│   ├── preprocessing/
│   └── main.py
├── training/
├── tests/
├── requirements.txt
└── README.md
```

Flutter app:

```text
mobile/
├── lib/
│   ├── core/
│   ├── models/
│   ├── services/
│   ├── repositories/
│   ├── providers/
│   ├── screens/
│   │   ├── auth/
│   │   ├── home/
│   │   ├── outages/
│   │   ├── maps/
│   │   ├── predictions/
│   │   ├── reports/
│   │   ├── notifications/
│   │   └── profile/
│   ├── widgets/
│   └── main.dart
└── pubspec.yaml
```

---

# 44. Environment Variables

Example:

```env
DATABASE_URL=

JWT_SECRET=
JWT_REFRESH_SECRET=

GOOGLE_MAPS_API_KEY=

AI_SERVICE_URL=

FCM_PROJECT_ID=
FCM_CLIENT_EMAIL=
FCM_PRIVATE_KEY=

EMAIL_PROVIDER_API_KEY=
SMS_PROVIDER_API_KEY=
```

Create `.env.example`.

Never commit `.env`.

---

# 45. Main Business Rules

1. Only authenticated Clients can submit outage reports.
2. Only Providers can officially declare incidents unless an Admin is explicitly allowed.
3. Only authorized Providers/Admins can create official scheduled outages.
4. Client reports must not automatically become official outages.
5. Admin validation can increase the confidence of a report.
6. Several geographically close reports may be grouped.
7. AI predictions must be labeled as predictions, not confirmed outages.
8. Confirmed Provider incidents have higher authority than unverified Client reports.
9. A resolved outage should trigger restoration notifications to affected Clients.
10. Notifications must respect Client preferences.
11. Users should receive alerts primarily for relevant saved/current locations.
12. Historical data must remain available for analysis after an outage is resolved.

---

# 46. Source Priority

If conflicting information exists, the system may use this priority:

```text
1. Confirmed Provider information
2. Administrator-validated information
3. Multiple corroborated Client reports
4. Single Client report
5. AI prediction
```

AI prediction is predictive evidence, not official confirmation.

---

# 47. MVP Scope

Codex should build the project progressively.

## MVP Phase 1

Implement:

- authentication;
- roles;
- profile;
- saved locations;
- current outages;
- scheduled outages;
- outage history;
- map display;
- client outage reports;
- provider incident declaration;
- admin report validation;
- basic notifications.

## MVP Phase 2

Implement:

- AI prediction service;
- prediction dashboard;
- risk scores;
- prediction history;
- estimated duration;
- automatic prediction notifications.

## MVP Phase 3

Implement:

- advanced statistics;
- geographic clustering;
- provider data imports;
- richer notification channels;
- prediction accuracy monitoring.

---

# 48. Required Initial User Stories

## Visitor

```text
As a Visitor,
I want to view current outages,
so that I can know which areas are affected.

As a Visitor,
I want to create an account,
so that I can receive personalized alerts.
```

## Client

```text
As a Client,
I want to save my home location,
so that I receive relevant outage notifications.

As a Client,
I want to report an outage,
so that the system can capture local incidents.

As a Client,
I want to view predicted outages,
so that I can prepare in advance.
```

## Provider

```text
As a Provider,
I want to declare an electrical incident,
so that users can be informed.

As a Provider,
I want to publish a scheduled outage,
so that affected clients can prepare.

As a Provider,
I want to signal restoration,
so that clients know electricity has returned.
```

## Administrator

```text
As an Administrator,
I want to review user reports,
so that false or duplicate information is controlled.

As an Administrator,
I want to view outage statistics,
so that I can monitor platform activity.
```

---

# 49. Acceptance Criteria — Current Outages

```text
Given current outages exist
When a user opens "Current Outages"
Then the system returns active outages
And displays affected zones on the map.

Given no current outages exist
When a user opens "Current Outages"
Then the system displays "No current outage".

Given the map service fails
When outage data is available
Then the application must still display outage information
And show a map-loading error instead of crashing.
```

---

# 50. Acceptance Criteria — Prediction

```text
Given enough historical data exists
When the Client opens Predictions
Then the system sends relevant data to the AI service
And returns a probability/risk prediction.

Given insufficient data exists
When a prediction is requested
Then the system displays an insufficient-data message.

Predictions must clearly display that they are forecasts
and not confirmed outages.
```

---

# 51. Acceptance Criteria — Declare Incident

```text
Given an authenticated Provider
When the Provider opens "Declare Incident"
Then the system displays the incident form.

Given valid incident information
When the Provider submits the form
Then the incident is stored
And a success message is displayed.

Given invalid information
When the Provider submits the form
Then validation errors are displayed
And nothing is saved.
```

---

# 52. Implementation Instructions for Codex

Codex should:

1. Read this specification before coding.
2. Build the project modularly.
3. Keep business logic out of UI components.
4. Use DTO/schema validation for all API inputs.
5. Use role-based access guards.
6. Create migrations/schema definitions instead of ad-hoc database creation.
7. Create `.env.example`.
8. Never hard-code secrets.
9. Add seed data.
10. Add tests for critical flows.
11. Add API documentation.
12. Keep external services behind interfaces/adapters:
    - Maps service;
    - AI service;
    - notification service.
13. Make the AI module replaceable.
14. Make notification channels configurable.
15. Keep all geographic data latitude/longitude compatible.
16. Implement pagination for long lists.
17. Implement search/filtering in Admin and Provider lists.
18. Use consistent API response and error formats.
19. Include loading, empty, success, and error states in the UI.
20. Do not treat AI predictions as confirmed outages.
21. Prefer clean, maintainable code over unnecessary complexity.

---

# 53. Important UI States

Every major list/screen must support:

```text
Loading
Success
Empty
Error
Offline / retry when appropriate
```

Examples:

Current outages:

```text
Loading...
No current outage
Current outages loaded
Unable to load outages
```

Predictions:

```text
Generating prediction...
Prediction available
Insufficient data
Prediction service unavailable
```

---

# 54. Suggested Development Order

Codex should implement in this order:

```text
1. Project setup
2. Database schema
3. Authentication and roles
4. User/profile management
5. Zones and locations
6. Outage CRUD
7. Current/scheduled/history endpoints
8. Map integration
9. Client outage reporting
10. Admin report management
11. Provider incident management
12. Notification preferences
13. Notification engine
14. AI service interface
15. Baseline prediction engine
16. Prediction screens
17. Dashboards/statistics
18. Automated tests
19. Documentation
20. Deployment configuration
```

---

# 55. Definition of Done

A feature is considered complete only when:

- backend endpoint works;
- authorization is applied;
- input is validated;
- database migration/schema exists;
- frontend screen is connected;
- loading/error/empty states are handled;
- tests exist for important logic;
- documentation is updated.

---

# 56. Final Product Vision

DelestAlert should ultimately provide a simple experience:

```text
User opens the application
        |
Sees current electricity situation
        |
Checks nearby outages on a map
        |
Receives warnings before possible outages
        |
Receives official scheduled outage information
        |
Can report a local outage
        |
Receives a restoration notification
```

At the same time:

```text
Provider publishes operational information
        |
Administrator validates community information
        |
Database builds historical knowledge
        |
AI analyzes patterns
        |
System predicts risk
        |
Relevant Clients are notified
```

The implementation must preserve a clear distinction between:

- **official outage information**;
- **user-reported information**;
- **AI-predicted information**.

This distinction is essential for trust, correctness, and future expansion of DelestAlert.
