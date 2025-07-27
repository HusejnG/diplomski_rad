<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\QuoteRequest;
use App\Models\Proposal;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Definiši Gate za upravljanje proizvodima (samo admin i projektant)
        Gate::define('manage-products', function (User $user) {
            return $user->isAdmin() || $user->isDesigner();
        });

        // Gate za pregled svih zahteva za ponudu (samo admin i projektant)
        Gate::define('view-all-quote-requests', function (User $user) {
            return $user->isAdmin() || $user->isDesigner();
        });

        // Gate za kreiranje ponude (samo projektant)
        Gate::define('create-proposal', function (User $user) {
            return $user->isDesigner();
        });

        // Gate za upravljanje ponudama (projektant može da kreira/menja svoje, admin sve)
        Gate::define('manage-proposals', function (User $user, Proposal $proposal = null) {
            if ($user->isAdmin()) {
                return true;
            }
            // Projektant može da upravlja samo svojim kreiranim ponudama
            return $user->isDesigner() && ($proposal ? $proposal->designer_id === $user->id : true);
        });

        // Gate za pregled specifičnog zahteva za ponudu (vlasnik, admin, projektant)
        Gate::define('view-quote-request', function (User $user, QuoteRequest $quoteRequest) {
            $isAllowed = $user->isAdmin() || $user->isDesigner() || $user->id === $quoteRequest->user_id;

            // --- DEBUG LINIJA START ---
            // Ova dd() izjava će zaustaviti izvršavanje i prikazati rezultat
            // PROVERI ŠTA OVO PRIKAZUJE KADA KLIKNEŠ NA "PREGLEDAJ" KAO DIZAJNER!
            dd("Gate 'view-quote-request' za korisnika " . $user->email . " (Uloga: " . $user->role . ") dozvoljeno: " . ($isAllowed ? 'DA' : 'NE') . " | Vlasnik zahteva ID: " . $quoteRequest->user_id . " | Korisnik ID: " . $user->id);
            // --- DEBUG LINIJA END ---

            return $isAllowed;
        });

        // Gate za pregled specifične ponude (vlasnik zahteva, projektant koji ju je kreirao, admin)
        Gate::define('view-proposal', function (User $user, Proposal $proposal) {
            return $user->isAdmin() || ($user->isDesigner() && $proposal->designer_id === $user->id) || ($user->isCustomer() && $user->id === $proposal->quoteRequest->user_id);
        });

        // Dodatni Gate za pregled ponuda od strane kupca (ako nije kreator zahteva)
        // Ovo je redundantno sa 'view-proposal' ali može biti korisno za jasnije razdvajanje
        Gate::define('view-customer-proposal', function (User $user, Proposal $proposal) {
            return $user->isCustomer() && $user->id === $proposal->quoteRequest->user_id;
        });
    }
}
