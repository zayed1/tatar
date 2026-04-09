# عودة التتار - Tatar Wars Game

## Overview
Strategic war game (Travian-style) with PHP backend and React Native mobile app.

## Branch Structure

| Branch | Purpose | Auto-deploys to |
|--------|---------|-----------------|
| `main` | Production PHP game + API | Railway (Docker) |
| `mobi` | Mobile app (React Native) | Replit → TestFlight/App Store |

**IMPORTANT**: These are two separate codebases in separate branches. Never merge `mobi` into `main` or vice versa.

## Tech Stack

### Backend (main branch)
- **Language**: PHP 8.1 (no framework)
- **Database**: MySQL on Railway
- **Server**: Apache (Docker: `php:8.1-apache`)
- **Deployment**: Railway auto-deploys from `main`

### Mobile App (mobi branch)
- **Framework**: React Native (Expo SDK 54)
- **Navigation**: React Navigation (bottom tabs + stack)
- **Language**: JavaScript (not TypeScript)
- **Deployment**: Replit auto-pulls from `mobi` branch
- **Structure**: `artifacts/mobile/` directory

**IMPORTANT**: Keep Replit config files intact in `mobi` branch:
- `.replit`, `.replitignore`, `pnpm-workspace.yaml`, `scripts/`, `server/`
- The build script at `artifacts/mobile/scripts/build.js` uses `App.js` as entry point

## Key Directories

```
/ (main branch)
├── api/                    # REST API for mobile app (10 endpoints)
│   ├── config.php          # DB connection, token auth, JSON helpers
│   ├── login.php           # POST: authenticate, return token
│   ├── data.php            # GET: resources, gold, notifications
│   ├── messages.php        # CRUD: inbox, sent, send, delete
│   ├── reports.php         # List/read/delete battle reports
│   ├── profile.php         # Player profile data
│   ├── statistics.php      # Rankings (6 tabs)
│   ├── chat.php            # Global + alliance chat
│   ├── alliance.php        # Alliance info, members, diplomacy
│   ├── build.php           # Buildings list + troop data
│   └── plus.php            # Plus features + gold packages
├── core-f/
│   ├── config-f/s1.php     # ALL game config (DB, game speed, packages, payments)
│   ├── mod-f/              # Models (business logic)
│   ├── ph-f/               # Views (phtml templates)
│   │   └── layout/
│   │       ├── game.phtml  # Main game layout (authenticated)
│   │       └── form.phtml  # Public pages layout (login, register)
│   ├── sql-f/              # Database layer (MysqlProvider)
│   ├── style-f/            # CSS, images, assets
│   │   └── default/
│   │       ├── mobile-app.css    # Mobile WebView styles
│   │       └── game-enhance.css  # Modern game UI enhancement
│   ├── ar.php              # Arabic translations
│   ├── en.php              # English translations
│   └── metadata.php        # Game data (buildings, troops, costs)
├── Dockerfile              # Docker build for Railway
├── docker-entrypoint.sh    # Apache startup with dynamic PORT
└── .htaccess               # URL rewriting, caching, GZIP
```

## Configuration

**ALL game settings** are in `core-f/config-f/s1.php`:
- Database connection (host, port, user, password)
- Game speed, map size, protection duration
- Plus/premium feature costs and durations
- Gold packages and payment methods
- Admin credentials

**DO NOT** move or split this file - the entire game depends on it.

## API Authentication

The mobile app uses token-based auth:
1. Login via `POST /api/login.php` → returns token
2. Token = `base64(playerId:sha256(playerId:passwordHash:SECRET))`
3. All API calls send `Authorization: Bearer <token>`
4. Token verified in `api/config.php` → `verifyToken()`
5. DB connections auto-close via `jsonError()`/`jsonSuccess()`

## Database

- MySQL on Railway (connection in s1.php)
- Tables: `p_players`, `p_villages`, `p_queue`, `p_msgs`, `p_rpts`, `p_alliances`, `g_chat`, `g_chat_alliance`, `g_settings`
- Resources stored as packed strings in `p_villages.resources`: `"type value max init rate pct,..."` (comma-separated, space-delimited)
- Buildings stored in `p_villages.buildings`: `"item_id level state,..."` 
- Troops in `p_villages.troops_num`: `"ownerid:tid count,tid count,..."`
- No ORM - raw `mysqli_*` queries via `MysqlProvider` class

## Mobile App Architecture (mobi branch)

```
artifacts/mobile/
├── App.js                  # Entry point, auth state, navigation
├── src/
│   ├── api/client.js       # 18 API functions + token management
│   ├── components/
│   │   ├── ResourceBar.js  # Top resource bar (auto-refresh 30s)
│   │   └── GameWebView.js  # WebView with injected CSS to hide chrome
│   └── screens/
│       ├── LoginScreen.js          # Native login
│       ├── FieldsScreen.js         # WebView: village1
│       ├── CityScreen.js           # WebView: village2
│       ├── MapScreen.js            # WebView: map
│       ├── MessagesScreen.js       # Native: inbox/sent/compose/reply
│       ├── ReportsScreen.js        # Native: 6 categories + detail
│       ├── ChatScreen.js           # Native: global + alliance (3s poll)
│       ├── ProfileScreen.js        # Native: stats, hero, villages
│       ├── StatisticsScreen.js     # Native: 6 ranking tabs
│       ├── AllianceScreen.js       # Native: members, diplomacy
│       ├── BuildingsScreen.js      # Native: building list + queue
│       ├── TroopsScreen.js         # Native: troop roster + training
│       ├── PlusScreen.js           # Native: plus features
│       ├── GoldShopScreen.js       # Native: gold packages
│       ├── MoreScreen.js           # Menu linking to all screens
│       └── WebPageScreen.js        # Generic WebView for any page
```

## Important Notes

- **RTL**: The app is Arabic (right-to-left). Use `flexDirection: 'row-reverse'`, `textAlign: 'right'`
- **PHP 8.1**: Use `??` null coalescing everywhere - the codebase had many null warnings
- **CSS Conflicts**: `compact.css` (legacy) conflicts with `mobile-app.css`. Use `body:not(.mobile-app)` to scope legacy rules
- **Mobile Detection**: PHP checks `$_GET['platform'] === 'app'` or user agent `TatarWarApp`
- **Game Layout**: `game.phtml` has inline critical CSS for mobile (cache-busting)
- **Passwords**: Stored as MD5 hashes (legacy - do not change without migration plan)
- **Queue System**: `p_queue.proc_type` controls task types (2=build, 7=train, etc.) - see `core-f/components.php` for constants
