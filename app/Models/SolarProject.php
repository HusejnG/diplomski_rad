<?php

namespace App\Models;

use App\Notifications\ProjectStatusChanged;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Centralni entitet aplikacije: jedan solarni projekat.
 *
 * Obuhvata cijeli životni ciklus - od samostalnog proračuna isplativosti
 * koji korisnik napravi preko čarobnjaka, preko slanja narudžbe, do pregleda
 * i odobravanja od strane projektanta i zakazivanja ugradnje.
 */
class SolarProject extends Model
{
    use HasFactory;

    // Statusi radnog toka
    public const STATUS_DRAFT = 'draft';
    public const STATUS_CALCULATED = 'calculated';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_SCHEDULED = 'scheduled';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_REJECTED = 'rejected';

    public const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Nacrt',
        self::STATUS_CALCULATED => 'Izračunato',
        self::STATUS_SUBMITTED => 'Poslana narudžba',
        self::STATUS_UNDER_REVIEW => 'U obradi kod projektanta',
        self::STATUS_APPROVED => 'Odobreno',
        self::STATUS_SCHEDULED => 'Ugradnja zakazana',
        self::STATUS_COMPLETED => 'Završeno',
        self::STATUS_REJECTED => 'Odbijeno',
    ];

    // Dozvoljeni prelazi između statusa. Sve ostalo se odbija - npr. odobravanje
    // projekta koji niko nije preuzeo, zakazivanje neodobrenog projekta ili
    // ponovno otvaranje završenog ili odbijenog.
    public const TRANSITIONS = [
        self::STATUS_DRAFT => [self::STATUS_CALCULATED],
        self::STATUS_CALCULATED => [self::STATUS_SUBMITTED],
        self::STATUS_SUBMITTED => [self::STATUS_UNDER_REVIEW],
        self::STATUS_UNDER_REVIEW => [self::STATUS_APPROVED, self::STATUS_REJECTED],
        self::STATUS_APPROVED => [self::STATUS_SCHEDULED, self::STATUS_REJECTED],
        self::STATUS_SCHEDULED => [self::STATUS_COMPLETED, self::STATUS_REJECTED],
        self::STATUS_COMPLETED => [],
        self::STATUS_REJECTED => [],
    ];

    // Statusi o kojima kupac dobija email (ostale promjene pravi sam).
    public const NOTIFY_CUSTOMER_ON = [
        self::STATUS_UNDER_REVIEW,
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_SCHEDULED,
        self::STATUS_COMPLETED,
    ];

    public const SURFACE_TYPES = [
        'kosi_krov' => 'Kosi krov kuće',
        'ravni_krov_kuce' => 'Ravni krov kuće',
        'krov_zgrade' => 'Ravni krov stambene/poslovne zgrade',
        'zemljiste_ravno' => 'Ravno zemljište / poljana',
        'zemljiste_brdovito' => 'Brdovit teren',
        'ostalo' => 'Ostalo',
    ];

    // Podrazumijevani nagib (stepeni) po tipu površine - koristi se dok korisnik ne prilagodi
    public const DEFAULT_TILT = [
        'kosi_krov' => 35,
        'ravni_krov_kuce' => 15,
        'krov_zgrade' => 15,
        'zemljiste_ravno' => 30,
        'zemljiste_brdovito' => 30,
        'ostalo' => 30,
    ];

    // PVGIS mountingplace parametar: 'building' (na/uz objekat) ili 'free' (samostojeći)
    public const MOUNTING_PLACE = [
        'kosi_krov' => 'building',
        'ravni_krov_kuce' => 'building',
        'krov_zgrade' => 'building',
        'zemljiste_ravno' => 'free',
        'zemljiste_brdovito' => 'free',
        'ostalo' => 'free',
    ];

    // Faktor iskoristivosti raspoložive površine (razmaci, prepreke, orijentacija)
    public const AREA_UTILISATION = [
        'kosi_krov' => 0.85,
        'ravni_krov_kuce' => 0.55, // ravni krov: red-razmaci zbog samosjenčenja
        'krov_zgrade' => 0.55,
        'zemljiste_ravno' => 0.45, // slobodnostojeći redovi trebaju veće razmake
        'zemljiste_brdovito' => 0.40,
        'ostalo' => 0.5,
    ];

    public const SHADING_LOSS = [
        'nema' => 0.0,
        'malo' => 0.05,
        'srednje' => 0.12,
        'mnogo' => 0.25,
    ];

    protected $fillable = [
        'user_id',
        'designer_id',
        'name',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address',
        'city',
        'country',
        'latitude',
        'longitude',
        'surface_type',
        'available_area_sqm',
        'usable_area_sqm',
        'tilt_deg',
        'azimuth_deg',
        'shading',
        'mounting_place',
        'avg_monthly_consumption_kwh',
        'electricity_price_bam_kwh',
        'panel_id',
        'panel_quantity',
        'inverter_id',
        'inverter_quantity',
        'system_power_kwp',
        'annual_production_kwh',
        'monthly_production',
        'equipment_cost_bam',
        'installation_cost_bam',
        'total_investment_bam',
        'self_consumption_percent',
        'annual_savings_year1_bam',
        'simple_payback_years',
        'npv_25y_bam',
        'cashflow_25y',
        'status',
        'customer_notes',
        'designer_notes',
        'installation_scheduled_at',
        'submitted_at',
        'reviewed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'designer_id' => 'integer',
            'monthly_production' => 'array',
            'cashflow_25y' => 'array',
            'installation_scheduled_at' => 'date',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Prvi zapis u historiji: status u kojem je projekat nastao.
        static::created(function (SolarProject $project) {
            $project->statusChanges()->create([
                'from_status' => null,
                'to_status' => $project->status,
                'user_id' => Auth::id(),
            ]);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusChanges()
    {
        return $this->hasMany(ProjectStatusChange::class)->oldest()->oldest('id');
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id');
    }

    public function panel()
    {
        return $this->belongsTo(Panel::class);
    }

    public function inverter()
    {
        return $this->belongsTo(Inverter::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getSurfaceTypeLabelAttribute(): string
    {
        return self::SURFACE_TYPES[$this->surface_type] ?? $this->surface_type;
    }

    public function isEditableByCustomer(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_CALCULATED], true);
    }

    public function canTransitionTo(string $status): bool
    {
        return in_array($status, self::TRANSITIONS[$this->status] ?? [], true);
    }

    /**
     * Prevodi projekat u novi status (uz dodatne atribute, npr. datum
     * ugradnje), zapisuje prelaz u historiju i po potrebi šalje email
     * kupcu. Baca izuzetak ako taj prelaz nije dozvoljen.
     *
     * @throws DomainException
     */
    public function transitionTo(string $status, array $attributes = [], ?string $note = null): ProjectStatusChange
    {
        if (! $this->canTransitionTo($status)) {
            throw new DomainException(sprintf(
                'Projekat sa statusom "%s" ne može preći u status "%s".',
                $this->status_label,
                self::STATUS_LABELS[$status] ?? $status,
            ));
        }

        $from = $this->status;

        $change = DB::transaction(function () use ($status, $attributes, $note, $from) {
            $this->update(array_merge($attributes, ['status' => $status]));

            return $this->statusChanges()->create([
                'from_status' => $from,
                'to_status' => $status,
                'user_id' => Auth::id(),
                'note' => $note,
            ]);
        });

        if (in_array($status, self::NOTIFY_CUSTOMER_ON, true) && $this->user) {
            $this->user->notify(new ProjectStatusChanged($this, $change));
        }

        return $change;
    }
}
