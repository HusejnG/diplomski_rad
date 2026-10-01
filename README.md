# Solarko - sistem za projektovanje i procjenu isplativosti solarnih elektrana

Web aplikacija za samostalno projektovanje malih fotonaponskih (solarnih) sistema i procjenu
njihove finansijske isplativosti, sa mogućnošću slanja narudžbe projektantu na odobrenje.

Ovo je redizajnirana verzija originalnog diplomskog rada: originalna aplikacija je funkcionalno
liči na web shop (katalog proizvoda, ručno slanje zahtjeva, projektant ručno sastavlja ponudu od
proizvoda). Ova verzija vraća fokus na prvobitnu ideju - **automatizovano projektovanje sistema i
proračun povrata investicije** - uz zadržavanje toka narudžbe i odobravanja koji je bio dio ideje
za informacioni sistem (korisnik naruči jednim klikom, projektant pregleda/odobri/zakaže ugradnju).

## Kako aplikacija radi

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
`rejected` u bilo kojem trenutku prije `completed`).

## Katalog opreme

Ne postoji jedan pouzdan besplatan javni API koji vraća ažurne maloprodajne cijene i specifikacije
solarnih panela/invertora za tržište BiH, pa je `database/seeders/EquipmentSeeder.php` popunjen
reprezentativnim, tržišno realističnim modelima (JA Solar, Longi, Jinko, Huawei, Growatt, Fronius,
SolarEdge, Deye...) i orijentacionim cijenama u BAM. Administrator kasnije uređuje katalog kroz
`/admin/panels` i `/admin/inverters` kako bi cijene bile ažurne - to je namjerna arhitektonska
odluka, ne propust.

## Uloge

- **Korisnik (customer)** - pravi proračune, šalje narudžbe, prati status svojih projekata.
- **Projektant (designer)** - obrađuje pristigle narudžbe (`/projektant`), prilagođava sistem,
  odobrava/odbija, zakazuje ugradnju.
- **Administrator (admin)** - sve što i projektant, plus upravljanje katalogom opreme
  (`/admin/panels`, `/admin/inverters`).

## Tehnologije

- Backend: Laravel 12 (PHP 8.4)
- Baza podataka: SQLite podrazumijevano za razvoj (lako se prebaci na MySQL u `.env`)
- Frontend: Blade, Bootstrap 5 (kompajlirano preko Vite iz `resources/scss/app.scss`),
  Leaflet.js (mapa + odabir lokacije), Chart.js (grafovi proizvodnje i novčanog toka),
  Nominatim/OpenStreetMap (pretraga lokacije)
- Eksterni API: PVGIS v5.2 (Evropska komisija) za podatke o sunčevom zračenju i proizvodnji

## Pokretanje

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

## Testovi

```bash
php artisan test
```

`tests/Feature/SolarCalculationTest.php` pokriva cijeli tok: javni proračun, validaciju granica
površine, snimanje projekta i slanje narudžbe od strane korisnika, preuzimanje/odobravanje/
zakazivanje od strane projektanta i autorizaciju (korisnik ne može vidjeti tuđi projekat). PVGIS
poziv je mokovan (`Http::fake()`) jer testno okruženje ne treba zavisiti od dostupnosti eksternog
servisa.

## Napomena o proračunu isplativosti

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
predaje u mrežu) korisnik može ručno unijeti, ili ostaviti sistemu da ga procijeni na osnovu
odnosa veličine sistema i potrošnje.
