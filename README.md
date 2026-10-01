# Solarko: solar system design and payback calculator

[![Tests](https://github.com/HusejnG/diplomski_rad/actions/workflows/tests.yml/badge.svg)](https://github.com/HusejnG/diplomski_rad/actions/workflows/tests.yml)

A Laravel web application for designing small photovoltaic systems and
estimating whether they pay off. It grew out of my bachelor thesis
(B.Sc. Software Engineering, University of Zenica, 2025); this is the
redesigned version. The user interface is in Bosnian.

## What it does

1. **Location and surface.** The user marks a location on a map (or
   searches for it), picks the kind of surface (pitched roof, flat roof,
   building roof, flat or hilly ground) and enters the available area and
   the average monthly electricity consumption.
2. **Automatic system design.** `SystemDesignService` works out the
   usable area for that surface type, picks a panel and an inverter from
   the equipment catalogue, and sizes the system so it fits the area and
   roughly matches the consumption without oversizing.
3. **Energy production.** `PvgisService` calls the European Commission's
   PVGIS API (v5.2) with the exact location, power, tilt and orientation
   and returns the monthly and yearly production; shading is applied as
   an extra loss.
4. **Payback.** `FinancialCalculationService` models 25 years of cash
   flow: panel degradation, rising electricity prices, self-consumed vs.
   exported energy, maintenance and one inverter replacement. It returns
   the simple payback period and the NPV.
5. **Order workflow.** A logged-in customer saves the calculation as a
   project and submits it with one click. A designer claims it, can
   adjust the equipment (everything is recalculated), approves or rejects
   it, schedules the installation and marks it completed. Admins also
   manage the equipment catalogue.

The workflow is enforced in one place: `SolarProject::TRANSITIONS` lists
the allowed status changes, and every change goes through
`transitionTo()`, which records it in the status history (shown as a
timeline to the customer and the designer) and emails the customer.

```
calculated → submitted → under_review → approved → scheduled → completed
                               └────────────┴──────────┴──→ rejected
```

## Tech stack

Laravel 12 (PHP 8.2+), SQLite by default (MySQL via `.env`), Blade,
Bootstrap 5 and Vite, Leaflet.js with OpenStreetMap/Nominatim for the
map, Chart.js for production and cash-flow charts, PVGIS API v5.2.

## Running locally

```bash
composer install
npm install && npm run build

cp .env.example .env
php artisan key:generate
touch database/database.sqlite

php artisan migrate --seed
php artisan serve
```

Demo accounts created by the seeder (password `password` for all):
`admin@solar.test`, `projektant@solar.test` (designer),
`korisnik@solar.test` (customer). Emails go to the log
(`MAIL_MAILER=log`), so they show up in `storage/logs/laravel.log`.

## Tests

```bash
php artisan test
```

62 tests: the calculator and its validation, the order workflow and
who may perform which step, status history and customer emails, the
automatic system design, and the financial model. PVGIS is faked with
`Http::fake()`, so the tests don't depend on the external service.
GitHub Actions runs them on every push.

## Assumptions in the financial model

Constants in `FinancialCalculationService`: 25-year horizon, 0.5 %
panel degradation per year, 2 % yearly electricity price increase,
exported energy paid at 50 % of the retail price, 120 BAM yearly
maintenance, 5 % discount rate for NPV, one inverter replacement when
its warranty ends. The equipment catalogue is seeded with representative
models and prices in BAM and is maintained by the admin.

## Opis na bosanskom: Solarko - sistem za projektovanje i procjenu isplativosti solarnih elektrana

Web aplikacija za samostalno projektovanje malih fotonaponskih (solarnih) sistema i procjenu
njihove finansijske isplativosti, sa mogućnošću slanja narudžbe projektantu na odobrenje.

Ovo je redizajnirana verzija originalnog diplomskog rada: originalna aplikacija je funkcionalno
liči na web shop (katalog proizvoda, ručno slanje zahtjeva, projektant ručno sastavlja ponudu od
proizvoda). Ova verzija vraća fokus na prvobitnu ideju - **automatizovano projektovanje sistema i
proračun povrata investicije** - uz zadržavanje toka narudžbe i odobravanja koji je bio dio ideje
za informacioni sistem (korisnik naruči jednim klikom, projektant pregleda/odobri/zakaže ugradnju).

### Kako aplikacija radi

1. **Lokacija i površina** - korisnik označi lokaciju na mapi (ili je pretraži), odabere tip
   površine (kosi krov, ravni krov kuće, ravni krov zgrade, ravno zemljište, brdovit teren,
   ostalo) i unese raspoloživu površinu u m² i prosječnu mjesečnu potrošnju struje.
2. **Automatsko projektovanje** - `SystemDesignService` na osnovu tipa površine izračunava
   iskoristivu površinu (faktor razmaka/prepreka po tipu površine), bira panel i invertor iz
   internog kataloga opreme i određuje broj panela tako da sistem staje na raspoloživu površinu i
   otprilike odgovara potrošnji (bez nepotrebnog predimenzionisanja).
3. **Proizvodnja energije** - `PvgisService` poziva zvanični PVGIS API Evropske komisije
   (https://re.jrc.ec.europa.eu/api/v5_2/PVcalc) sa tačnom lokacijom, snagom, nagibom i
   orijentacijom sistema i vraća mjesečnu/godišnju proizvodnju energije.
4. **Isplativost** - `FinancialCalculationService` računa investiciju, godišnju uštedu,
   jednostavan period povrata (linearnom interpolacijom unutar godine), NPV na 25 godina i
   kompletnu projekciju novčanog toka (uzima u obzir degradaciju panela, rast cijene struje,
   samopotrošnju vs. predaju viška u mrežu, troškove održavanja i zamjenu invertora).
5. **Narudžba i odobravanje** - prijavljeni korisnik može sačuvati proračun kao projekat i jednim
   klikom poslati narudžbu. Projektant (ili admin) je vidi u redu čekanja, preuzima je u obradu,
   po potrebi prilagođava predloženu opremu (ponovni proračun), odobrava ili odbija, zakazuje
   datum ugradnje i na kraju označava projekat završenim.

Cijeli životni ciklus jednog projekta je jedan model, `App\Models\SolarProject`, sa statusima:
`draft → calculated → submitted → under_review → approved → scheduled → completed` (ili
`rejected` nakon što projektant preuzme narudžbu, a prije `completed`). Dozvoljeni prelazi su
definisani u `SolarProject::TRANSITIONS`; svaki prelaz se zapisuje u historiju statusa, a kupac
dobija email kada projektant preuzme, odobri, odbije, zakaže ili završi projekat.

### Katalog opreme

Ne postoji jedan pouzdan besplatan javni API koji vraća ažurne maloprodajne cijene i specifikacije
solarnih panela/invertora za tržište BiH, pa je `database/seeders/EquipmentSeeder.php` popunjen
reprezentativnim, tržišno realističnim modelima (JA Solar, Longi, Jinko, Huawei, Growatt, Fronius,
SolarEdge, Deye...) i orijentacionim cijenama u BAM. Administrator kasnije uređuje katalog kroz
`/admin/panels` i `/admin/inverters` kako bi cijene bile ažurne - to je namjerna arhitektonska
odluka, ne propust.

### Uloge

- **Korisnik (customer)** - pravi proračune, šalje narudžbe, prati status svojih projekata.
- **Projektant (designer)** - obrađuje pristigle narudžbe (`/projektant`), prilagođava sistem,
  odobrava/odbija, zakazuje ugradnju.
- **Administrator (admin)** - sve što i projektant, plus upravljanje katalogom opreme
  (`/admin/panels`, `/admin/inverters`).

### Tehnologije

- Backend: Laravel 12 (PHP 8.4)
- Baza podataka: SQLite podrazumijevano za razvoj (lako se prebaci na MySQL u `.env`)
- Frontend: Blade, Bootstrap 5 (kompajlirano preko Vite iz `resources/scss/app.scss`),
  Leaflet.js (mapa + odabir lokacije), Chart.js (grafovi proizvodnje i novčanog toka),
  Nominatim/OpenStreetMap (pretraga lokacije)
- Eksterni API: PVGIS v5.2 (Evropska komisija) za podatke o sunčevom zračenju i proizvodnji

### Pokretanje

```bash
composer install
npm install && npm run build   # ili "npm run dev" tokom razvoja

cp .env.example .env
php artisan key:generate
touch database/database.sqlite   # ako koristite sqlite (podrazumijevano)

php artisan migrate --seed
php artisan serve
```

Seeder kreira demo naloge (lozinka za sve: `password`):

| Uloga      | Email                     |
|------------|---------------------------|
| Admin      | admin@solar.test          |
| Projektant | projektant@solar.test     |
| Korisnik   | korisnik@solar.test       |

### Testovi

```bash
php artisan test
```

- `tests/Feature/SolarCalculationTest.php` - javni proračun, validacija, snimanje projekta i
  slanje narudžbe, ručno unesena samopotrošnja.
- `tests/Feature/ProjectWorkflowTest.php` - redoslijed statusa, ko smije koji korak, historija
  statusa i email obavještenja kupcu.
- `tests/Feature/SystemDesignServiceTest.php` i `tests/Unit/FinancialCalculationServiceTest.php` -
  automatsko projektovanje sistema i finansijski model.

PVGIS poziv je mokovan (`Http::fake()`) jer testovi ne treba da zavise od dostupnosti eksternog
servisa. GitHub Actions pokreće sve testove na svaki push.

### Napomena o proračunu isplativosti

Finansijski model (`FinancialCalculationService`) koristi sljedeće pretpostavke, koje su
dokumentovane kao konstante u kodu i mogu se lako izmijeniti:

- horizont posmatranja: 25 godina (tipična garancija proizvodnje panela)
- degradacija panela: 0.5% godišnje
- rast cijene struje: 2% godišnje
- otkupna cijena viška energije predate u mrežu: 50% maloprodajne cijene
- godišnje održavanje: 120 BAM (raste sa istom stopom kao cijena struje)
- diskontna stopa za NPV: 5%
- zamjena invertora jednokratno nakon isteka njegove garancije

Udio samopotrošnje (koliko se proizvedene energije odmah potroši u domaćinstvu, a koliko se
predaje u mrežu) korisnik može ručno unijeti u naprednim postavkama, ili ostaviti prazno da ga
sistem procijeni na osnovu odnosa veličine sistema i potrošnje.
