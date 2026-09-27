# Fuel Distribution & Rationing Management System (FDMS)
**Client:** Municipality of Sebha, Libya
**Status:** Phase 1 planning — this document is the onboarding brief for the implementation agent.

---

## 1. Background & Problem

Sebha is experiencing a fuel shortage. Citizens have been over-ordering and stockpiling fuel beyond their actual need, which worsens the shortage for everyone else. The Municipality has commissioned a system to manage and ration fuel distribution to citizens fairly.

A previous version of this system existed and was largely successful, but it had software/process errors that created loopholes for **corruption by system managers/admins** (manual overrides, unlogged actions, quota manipulation, etc.). Preventing a repeat of that is a first-class requirement, not an afterthought — see Section 5.

## 2. Project Category

This is an **identity-linked resource rationing and subsidy management system** — the same category as national fuel-card rationing systems (e.g., Iran's smart-card fuel system, Egypt's subsidized fuel quota cards). Functionally it combines:
- E-government / civic tech (citizen-facing registration & identity)
- Resource rationing / allocation management
- Supply-chain / demand management (stations, fuel flow)

Short name: **FDMS** (Fuel Distribution Management System) or, more precisely, **Fuel Distribution and Rationing Management System**.

## 3. Phase 1 Scope (this build)

Phase 1 is intentionally simple to make adoption easy — strict controls are planned for later phases once citizens trust the system. **Do not build ahead of this scope without confirming with the project owner.**

### 3.1 Rationing rule
- **Cooldown strategy**: each vehicle may fill up once every **4 days**, regardless of quantity.
- The cooldown period must be an **admin-configurable setting**, not hardcoded (default: 4 days).
- The system enforces **frequency only** (who is eligible to fill today). It does **not** enforce or cap the litres dispensed — that is handled physically by station security/staff, out of scope for the software.
- Cooldown must be tracked as an exact rolling window (e.g., 96 hours from last fill-up timestamp), not a loose calendar-day reset, to prevent early/late-edge gaming.
- Cooldown is tracked **per vehicle**, not per person, to prevent multi-vehicle households from bypassing it.

### 3.2 Core entities
- **Citizens** — name, phone, national ID, passport number, linked vehicle(s).
- **Vehicles** — plate number, owner (citizen ID), vehicle type (capture now even though not used for tiering yet — needed for Phase 2).
- **Stations** — name, location (address/coordinates), assigned staff/admin.
- **Fill-up records** — vehicle ID, station ID, timestamp, staff/attendant ID who approved it. This is both the cooldown-enforcement record and the audit trail.
- **Cooldown config** — the period value, with a full change history (who changed it, when, old → new value).

### 3.3 Registration flow
1. Citizen submits: name, phone, national ID, passport number, car data.
2. Citizen uploads documents: passport image, national ID image, selfie, car images.
3. Registration enters a **`pending`** state.
4. An admin manually reviews the submitted documents and **approves or rejects** the registration.
5. Only `approved` registrations/vehicles become eligible for the fill-up/cooldown system.
6. Every approval/rejection is logged with reviewer ID + timestamp + decision (see Section 5 — this review step is itself a corruption risk point).

### 3.4 Reporting (municipality-facing)
- Per-station fill-up counts and timestamps.
- Per-vehicle fill-up history (for dispute resolution).
- Flagged anomalies: attempted fill-ups before cooldown expiry (log even when denied — repeated attempts are a signal).
- Admin/config-change log (cooldown period edits, approvals/rejections) as a separate audit-facing report.

## 4. Explicitly Out of Scope for Phase 1

These are planned but must **not** be built now — keeping the first release simple is a deliberate adoption strategy:
- Per-visit volume/litre caps.
- Vehicle-type tiering (different quotas for taxis, commercial, private, etc.).
- Automated/system-controlled pump dispensing.
- Real-time online verification requiring constant connectivity (design for offline/degraded-connectivity resilience where practical).

## 5. Corruption-Prevention Requirements (carried over from the previous system's failure)

The prior system's technical/process gaps enabled admin-side corruption. These are non-negotiable design constraints, not nice-to-haves:

- **Maker-checker / two-person approval** for any manual override or exception — no single admin should unilaterally approve/reject/override.
- **Immutable, timestamped audit logs** for every admin action (approvals, rejections, cooldown-period changes, manual overrides), tied to individual credentials — no shared logins.
- **No silent voids/backdating** — transactions and approvals cannot be deleted or retroactively edited without leaving a visible trail.
- **Time-bound exemptions** — any VIP/exempt list entries must expire and require re-approval, never stand as permanent silent entries.
- **Anomaly detection hooks** — flag unusual patterns (repeated override use, same admin rejecting/approving at unusual rates, same station voiding transactions repeatedly) for review by a party outside daily operations.
- **Separation of duties** — the person who can grant an exception should not also be the person who executes and audits it.

## 6. Infrastructure Constraints

- **Budget covers a single server only** — no object storage (e.g., S3) available for Phase 1.
- Uploaded documents (ID/passport/selfie/car images) are stored **on local disk, outside the web root**, with paths/references stored in the database — not the files themselves inline in the DB.
- **Aggressive image compression/resizing is required** at upload (client-side and/or server-side) to keep disk usage sane at scale (~500,000 vehicles from the previous system's registered base).
- File processing (compression, validation, virus scan if feasible) should be **queued/asynchronous**, not done inline in the request cycle, even on a single server.
- **Registration bursts must be expected and controlled** — do not open public registration to all citizens at once. Use a phased rollout (by district, by ID digit, scheduled windows) and/or a submission queue, since manual admin approval will already naturally throttle activation regardless of submission volume.
- **Automated regular backups** (DB + uploaded files) to a separate location are required — single-server means single point of failure, and this dataset (citizen ID documents) is highly sensitive to loss.

## 7. Tech Stack Direction

- **Backend**: Laravel (PHP).
- **Frontend**: Blade (server-rendered views) + Alpine.js for lightweight interactivity (e.g., toggling fields, table filters, confirm dialogs) — chosen over Inertia+Vue for lower complexity, faster load on low-end/weak-connection citizen devices, single build pipeline, and consistency with the single-server budget constraint. Applies to both citizen-facing pages and admin/staff dashboards.
