# Dealer 4.0.4

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thelia-modules/Dealer/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/thelia-modules/Dealer/?branch=master)

author : Penalver Antony <apenalver@openstudio.fr>

This module Thelia generates a CRUD interface for dealers (points of sale / drives), their opening
hours, and — since 4.0.x — a pickup slot engine for click & collect / drive checkout.

## Compatibility

Thelia >= 2.1.3
Developped for : Thelia 2.2.1

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Dealer.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/dealer-module:~2.0
```

### Updating from an earlier version

`Dealer::update()` runs every `Config/update/*.sql` file whose name is greater than the
currently installed version, in order. Updating to 4.0.4 runs, among others:

* `4.0.2.sql` — creates `dealer_pickup_config` and `dealer_order_pickup` (fresh installs before
  4.0.2 were missing these tables even though the code already used them).
* `4.0.3.sql` — adds `dealer.timezone` (default `Europe/Paris`).
* `4.0.4.sql` — adds `dealer_shedules.exception` and backfills it (`1` for any row that was
  closed, weekly-recurring, or had a period bound — i.e. anything that used to be inferred as an
  exceptional entry), and adds a unique constraint on `dealer_order_pickup.order_id` (one pickup
  reservation per order).

No manual step is required; activating/updating the module through the back-office triggers it.

## Usage

You can find main Dealer interface on modules admin menu.

## Opening hours & exceptions

A dealer's opening hours live in a single table, `dealer_shedules`, distinguished by the explicit
`exception` column — it is not inferred from the other columns, because an exceptional weekly
opening with no period bound would otherwise look exactly like a base row:

* **Base weekly hours** (`exception = false`): the recurring hours for a weekday (`day`, `begin`,
  `end`). This is what the back-office weekly grid manages.
* **Exceptional entries** (`exception = true`): a closure or an exceptional opening, each with its
  own recurrence:
  * a single date (`period_begin` = `period_end`, or either left open-ended),
  * a period (`period_begin` … `period_end`),
  * weekly, unbounded in time (a `day` with no period — e.g. "every Sunday, all summer" needs a
    period; "every Sunday" without one is open-ended),
  * yearly (`recurring = true`): applies every year on `period_begin`'s month/day, the year is
    ignored (e.g. December 25th).
  * A closure with no `begin`/`end` closes the whole day; a closure with hours only removes that
    time range, and exceptional openings can restore hours inside or outside the base grid.

`Dealer\Service\ScheduleApplicability::appliesOn()` is the single source of truth for "does this
row apply on this calendar date?" (weekday, period, yearly recurrence) — the pickup slot engine,
the schedule validation and the legacy adapters all resolve against it, so they agree.

### Back-office

The schedules tab (dealer edit page) is organized around three blocks:

* **Weekly grid** — one row per weekday, inline time slots, add/remove, copy-to-other-days; saved
  as a whole in a single AJAX call (`POST .../schedules/week`, see below) that replaces every base
  row in one transaction. Validation covers the whole week in one round-trip and errors are
  attached to their weekday.
* **Exceptions list** — closures and exceptional openings together, with badges and a human
  summary of the recurrence; a modal lets you pick the type, the repetition (single date / period
  / weekly / yearly), a whole-day switch for closures, with inline validation errors.
* **Customer preview** — the upcoming pickup days and slots exactly as the engine computes them
  (`PickupSlotService::getAvailableSlots()`), refreshed after each save, so a manager can check
  the effect of hours, exceptions and pickup settings without leaving the tab.

## Pickup slots

`dealer_pickup_config` (one row per dealer) drives the slot grid:

| Column                   | Meaning                                                                     |
|---------------------------|------------------------------------------------------------------------------|
| `prep_delay_minutes`       | minimum delay between "now" and the earliest offered slot                   |
| `orderable_days`           | number of days **with at least one available slot** to return (not calendar days — a fully closed day does not count towards it) |
| `slot_duration_minutes`    | length of one slot                                                          |
| `max_orders_per_slot`      | quota per slot; `0` = unlimited                                             |

`dealer.timezone` (default `Europe/Paris`) anchors the computation: schedule hours are naive
wall-clock times in the dealer's own timezone, so "now" and every generated slot datetime are
computed in it — otherwise a server running in another timezone (e.g. UTC) offsets "now" and keeps
already-past slots on offer.

### Algorithm (`Dealer\Service\PickupSlotService::getAvailableSlots()`)

1. Resolve "now" (or an explicit `from`) in the dealer's timezone, add `prep_delay_minutes`.
2. Scan forward day by day, up to a **hard cap of 60 days**, until `orderable_days` days with at
   least one slot have been collected.
3. For each day, resolve the effective open ranges: base weekly hours, minus exceptional closures
   (whole day or partial), plus exceptional openings — via `ScheduleApplicability`.
4. Cut each open range into slots of `slot_duration_minutes`, drop any slot before the prep-delay
   cutoff.
5. Decrement each slot's remaining capacity against orders already booked on it, **excluding
   cancelled and refunded orders** (`dealer_order_pickup` joined to `order`/`order_status`, one
   query per scan window instead of one per slot).

Two Twig functions (`Dealer\Twig\DealerPickupExtension`) expose this to front and back templates:

```twig
{% set days = dealer_pickup_slots(dealer_id) %}          {# or dealer_pickup_slots(dealer_id, from) #}
{% set pickup = dealer_order_pickup(order_id) %}         {# null if the order has no drive pickup #}
```

### Front contract (checkout)

The theme is responsible for letting the customer pick a slot and storing the choice in session
before payment:

```php
$session->set(Dealer::SESSION_PICKUP_DEALER_ID, $dealerId);
$session->set(Dealer::SESSION_PICKUP_DATETIME, $datetime); // 'Y-m-d H:i:s'
```

`Dealer\EventListener\OrderListener` (subscribed on `TheliaEvents::ORDER_PAY`) then:

1. **Before** the core order creation — re-validates the slot strictly (same grid, prep delay,
   window and capacity as what the engine currently offers) under a named MySQL advisory lock
   (`GET_LOCK`, keyed by dealer + slot, 5s timeout) so two concurrent checkouts cannot both take
   the last place. A slot that is full, stale, off-grid or forged raises
   `Thelia\Domain\Checkout\Exception\InvalidDeliveryException` and aborts the checkout — nothing is
   charged and no order is created without a slot.
2. **After** the order is created — persists the reservation into `dealer_order_pickup`, still
   under the same lock, then releases it and clears the session keys. A persistence failure at
   this point is logged but never aborts the order (the slot was already validated under lock).

The enforcement only applies when the order's delivery module is one of the module's
`dealer_pickup_delivery_modules` config value (comma-separated module codes, default
`LocalPickup`) — a stale session pickup choice cannot block a home-delivery order placed later in
the same session.

## Order pickup management

The BO order detail page shows the booked pickup slot (dealer, address, datetime) via a hook
rendered on the order-view template.

`Dealer\Controller\OrderPickupController` (`POST /admin/module/Dealer/order-pickup/update`) lets a
manager move it, in two modes:

* **slot** (default): pick one of the slots the engine currently offers for that dealer — validated
  through the same `isSlotAvailable()` check as a customer choice.
* **custom**: force a free datetime, bypassing hours and quota — meant for the case that motivates
  this feature: an exceptional closure was just added and existing pickups need rescheduling by
  hand. The manager takes responsibility for the datetime; the customer is not notified
  automatically.

## Endpoints (back-office)

| Method | Route                                              | Purpose                                                     |
|--------|-----------------------------------------------------|--------------------------------------------------------------|
| POST   | `/admin/module/Dealer/schedules/week`               | Replace the whole base weekly grid in one call. JSON body `{dealer_id, week: {0: [{begin, end}, …], …}}`, `_token` query param. Responds with per-weekday validation errors on failure. |
| GET    | `/admin/module/Dealer/schedules/preview`            | `?dealer_id=` — returns the upcoming pickup days/slots as computed by `PickupSlotService`, for the customer preview block. |
| POST   | `/admin/module/Dealer/order-pickup/update`          | Move the pickup slot booked on an order. Params: `order_id`, `mode` (`slot`\|`custom`), `pickup_datetime` or `custom_datetime`, `_token` query param. |

All three require the `admin.dealer.schedules` resource and the corresponding access level
(`UPDATE` or `VIEW`), and check a CSRF token via `Thelia\Tools\TokenProvider` on the `_token` query
parameter.

## Deprecated

Kept for backward compatibility with the legacy Smarty front and existing integrations, but no
longer the source of truth — new code should target `PickupSlotService`:

* `Dealer\Service\SchedulesService::getOpenDays()` / `findOpenDay()` / `findHardHours()` /
  `findOpenHours()` — replaced by `PickupSlotService::getAvailableSlots()`.
* `Dealer\Service\DealerUtils` (and its `getDealerSchedules()`) — ignores the exceptional entries
  entirely; replaced by the dealer loops for presentation, or `PickupSlotService` for effective
  opening.
* `Dealer\Model\DealerShedulesQuery::filterByPeriodNotNull()` / `filterByPeriodNull()` — replaced
  by `filterByException(true)` / `filterByException(false)`: the base/exceptional distinction is
  now an explicit column, not inferred from the presence of a period.

## Loop

### DealerLoop

### Input arguments

|Argument           |Description                                                |
|---                |---                                                        |
|**id**             | filter by id                                              |
|**city**           | filter by city                                            |
|**country_id**     | filter by country                                         |
|**order**          | order result by "id","id-reverse","date","date-reverse"	|


### Output arguments

|Variable       |Description                |
|---            |---                        |
|$ID            | id                        |
|$TITLE      	| Name 			            |
|$ADDRESS1      | First element address     |
|$ADDRESS2      | Second element address    |
|$ADDRESS3      | Third element address  	|
|$ZIPCODE       | Address zip code          |
|$CITY          | City name                 |
|$COUNTRY_ID    | Country id-reverse        |
|$DESCRIPTION   | Dealer description        |
|$LAT   		| Latitude                  |
|$LON 		 	| Longitude                 |
|$CREATE_DATE	| Creation date             |
|$UPDATE_DATE	| Last update date          |

### ContactLoop

### Input arguments

|Argument           |Description                                                |
|---                |---                                                        |
|**id**             | filter by id                                              |
|**dealer_id**      | filter by dealer                                          |
|**order**          | order result by "id","id-reverse","label","label-reverse"	|


### Output arguments

|Variable       |Description                |
|---            |---                        |
|$ID            | id                        |
|$DEALER_ID    	| Associated Dealer id      |
|$IS_DEFAULT    | Boolean					|
|$LABEL     	| Contact group name 	    |

### ContactInfoLoop

### Input arguments

|Argument           |Description                                                |
|---                |---                                                        |
|**id**             | filter by id                                              |
|**contact_id**     | filter by contact                                         |
|**order**          | order result by "id","id-reverse","value","value-reverse"	|


### Output arguments

|Variable       	|Description                |
|---            	|---                        |
|$ID            	| id                        |
|$CONTACT_ID    	| Associated Contact id     |
|$CONTACT_TYPE   	| Contact type 				|
|$CONTACT_TYPE_ID   | Contact type id 			|
|$VALUE     		| Contact value 	 	    |

### SchedulesLoop

### Input arguments

|Argument           |Description                                                                                                                |
|---                |---                                                                                                                        |
|**id**             | filter by id                                                                                                              |
|**dealer_id**      | filter by dealer                                                                                                          |
|**default_period** | boolean filter by default schedule (base weekly hours); its opposite selects the exceptional entries                     |
|**hide_past**      | boolean for hide past schedules (default: false). Applies to exceptional entries only: an open-ended period or a yearly recurrence is never considered past |
|**closed**         | boolean for closed or open schedule (default: false)                                                                      |
|**day** 			| filter by day 		                                                                                                    |
|**order**          | order result by "id","id-reverse","day","day-reverse","begin","begin-reverse","period-begin","period-begin-reverse"		|


### Output arguments

|Variable       	|Description                |
|---            	|---                        |
|$ID            	| id                        |
|$DEALER_ID    		| Associated Dealer id      |
|$DAY    			| Day value (null for a dated exceptional entry with no weekday) |
|$DAY_LABEL   		| Day label	(null when $DAY is null)	|
|$BEGIN 			| Schedules start 			|
|$END    			| Schedules end 	 	    |
|$PERIOD_BEGIN 		| Schedules period start	|
|$PERIOD_END    	| Schedules period end		|
|$CLOSED    		| Boolean: closure vs opening |
|$EXCEPTION    		| Boolean: exceptional entry vs base weekly hours |
|$RECURRING    		| Boolean: yearly recurrence (period_begin's month/day, year ignored) |
|$TITLE    		    | Free-text label of the entry |

### RegularSchedulesLoop

### Input arguments

|Argument               |Description                                                                            |
|---                    |---                                                                                    |
|**id**                 | filter by id                                                                          |
|**dealer_id**          | filter by dealer                                                                      |
|**day** 			    | filter by day 		                                                                |
|**hour_separator**     | separator between hours for ouput formatted_hours (default: ' - ') 		            |
|**half_day_separator** | separator between half day for ouput formatted_hours (default: ' / ') 	            |
|**merge_day**          | boolean to allow concatenated hours of schedule with the same day (default: true)     |
|**order**              | order result by "id","id-reverse","day","day-reverse","begin","begin-reverse"         |

### Output arguments

|Variable       	|Description                                                                        |
|---            	|---                                                                                |
|$ID            	| id                                                                                |
|$DEALER_ID    		| Associated Dealer id                                                              |
|$DAY    			| Day value     			                                                        |
|$DAY_LABEL   		| Day label					                                                        |
|$BEGIN 			| Schedules start 			                                                        |
|$END    			| Schedules end (end of the afternoon when merge_day input argument is true	 	    |
|$FORMATTED_HOURS   | Formatted hours when merge_day input argument is true 	 	                    |

### ExtraSchedulesLoop

### Input arguments

|Argument               |Description                                                                                                                |
|---                    |---                                                                                                                        |
|**id**                 | filter by id                                                                                                              |
|**dealer_id**          | filter by dealer                                                                                                          |
|**day** 			    | filter by day 		                                                                                                    |
|**hour_separator**     | separator between hours for ouput formatted_hours (default:  ' - ') 		                                                |
|**half_day_separator** | separator between half day for ouput formatted_hours (default:  ' / ') 	                                                |
|**merge_day**          | boolean to allow concatenated hours of schedule with the same periods (default = true)                                    |
|**hide_past**          | boolean for hide past schedules (default: false)                                                                          |
|**closed**             | boolean for closed or open schedule (default: false)                                                                      |
|**order**              | order result by "id","id-reverse","day","day-reverse","begin","begin-reverse","period-begin","period-begin-reverse"       |
