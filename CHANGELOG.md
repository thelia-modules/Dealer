# Changelog

All notable changes to this module are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [4.0.4] - 2026-08-18

### Added

- Weekly opening-hours grid on the schedules tab: one row per weekday, saved as a whole in a
  single AJAX call (`POST /admin/module/Dealer/schedules/week`) instead of one row at a time with
  a full page reload per action.
- Unified exceptions list (closures and exceptional openings together, with badges and a human
  summary of the recurrence), supporting four recurrence modes: single date, date period, weekly
  (unbounded), and yearly (`recurring`, month/day only, year ignored).
- Customer preview block on the schedules tab, showing the upcoming pickup days/slots exactly as
  computed by `PickupSlotService` (`GET /admin/module/Dealer/schedules/preview`), refreshed after
  each save.
- Real enforcement of the pickup slot at checkout: strict re-validation under a named MySQL
  advisory lock (`GET_LOCK`) bracketing the core order creation on `TheliaEvents::ORDER_PAY`,
  aborting the checkout with `InvalidDeliveryException` when the chosen slot is full, stale,
  off-grid or forged — closing the check-then-insert race between two concurrent checkouts.
  Enforcement is scoped to configurable delivery module codes
  (`dealer_pickup_delivery_modules`, default `LocalPickup`), so a stale session pickup choice
  cannot block a home-delivery order.
- Pickup slot change on the order edit page (`POST /admin/module/Dealer/order-pickup/update`):
  pick one of the slots currently offered by the engine, or force a free datetime — for
  rescheduling pickups by hand after an exceptional closure.
- Booked pickup slot (dealer, address, datetime) displayed on the BO order detail page via a hook.
- Per-dealer `timezone` column (default `Europe/Paris`), editable from the pickup settings tab;
  anchors every pickup slot computation to the store's own local time.
- Explicit `exception` boolean column on `dealer_shedules`, replacing the former inference from
  nullable period/closed/recurring columns (which made a weekly exceptional opening with no period
  indistinguishable from a base row).

### Changed

- Loops, the `/api/3/dealer` payload, and the legacy `SchedulesService` adapters now resolve
  applicability through the shared `ScheduleApplicability` rules (weekday, period, yearly
  recurrence, open-ended periods) instead of each reimplementing it, and run on one query instead
  of three or four per scanned day.
- Overlap validation on exceptional entries now compares real calendar applicability (weekday,
  period, weekly/yearly recurrence) instead of exact same-period rows only. Openings now require
  both hours, period ends cannot precede their begin, yearly recurrences require a date, and base
  rows require a weekday.
- Pickup settings save (prep delay, orderable days, slot duration, quota, timezone) is now
  transactional; an invalid timezone is reported as a visible error instead of a silent no-op.
- BO choice lists (countries, contents, folders, brands, dealers) and the contacts tab now
  eager-load i18n (`joinWithI18n`), cutting roughly 2000 queries per display down to under a
  hundred.
- Dealer BO menu link now highlights correctly (active state) and the "Default Schedules" column
  label is hidden for base schedule rows.

### Removed

- The schedule clone flow, left dead after its trigger button was removed.

### Fixed

- Pickup slot computation is anchored to each dealer's own timezone; a server running in another
  timezone (e.g. UTC) no longer keeps already-past slots on offer.
- The booked-pickup block on the order detail page now renders on a hook the back-office actually
  dispatches.
- Dealer geocoding on creation now runs only when the dealer has no coordinates yet, is bounded to
  3 seconds, sends the optional `google_api_key` module config value, logs every non-nominal
  outcome, and never double-saves the dealer or aborts its creation.
- Schedule creation errors are now surfaced through the session flash instead of being lost in the
  redirect chain.
- Misspelled opening-hours translation key.

### Deprecated

- `SchedulesService::getOpenDays()`, `findOpenDay()`, `findHardHours()`, `findOpenHours()`, and
  `DealerUtils` (including `getDealerSchedules()`) — in favour of
  `PickupSlotService::getAvailableSlots()`.
- `DealerShedulesQuery::filterByPeriodNotNull()` / `filterByPeriodNull()` — in favour of
  `filterByException(true)` / `filterByException(false)`.

### Migration

`Config/update/4.0.4.sql` adds `dealer_shedules.exception` (backfilled to `1` for every row that
was closed, weekly-recurring, or had a period bound — i.e. anything previously inferred as
exceptional) and a unique constraint on `dealer_order_pickup.order_id` (one reservation per
order). It runs automatically when the module is updated through the back-office.

## [4.0.3]

### Added

- `dealer.timezone` column (default `Europe/Paris`).

## [4.0.2]

### Added

- Drive pickup slots, per-dealer pickup configuration (`dealer_pickup_config`) and schedule
  validation.
- `dealer_order_pickup` table: the pickup slot is persisted and exposed on the order.
- `dealer_shedules.title` free-text label column.

### Fixed

- Field labels rendered correctly on the Dealer back-office config tabs.
- Pickup slot validation hardened, with clearer error handling.

## [4.0.1]

### Added

- Yearly recurrence flag (`recurring`) on exceptional schedules (e.g. "every December 25th").

## [4.0.0] and earlier

Not reconstructed in detail for this changelog. See the module's git history and tags (`2.x`,
`3.0.x` lineage) for the pre-4.0 history of this module.
