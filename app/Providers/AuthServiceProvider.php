<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\QuoteRequest;
use App\Models\Proposal;


class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        /*
        Gate::define('manage-products', function (User $user) {
            return $user->isAdmin() || $user->isDesigner();
        });

        Gate::define('view-all-quote-requests', function (User $user) {
            return $user->isAdmin() || $user->isDesigner();
        });

        Gate::define('create-proposal', function (User $user) {
            return $user->isDesigner();
        });

        Gate::define('manage-proposals', function (User $user, Proposal $proposal = null) {
            if ($user->isAdmin()) {
                return true;
            }
            return $user->isDesigner() && ($proposal ? $proposal->designer_id === $user->id : true);
        });

        Gate::define('view-quote-request', function (User $user, QuoteRequest $quoteRequest) {
            $isAllowed = $user->isAdmin() || $user->isDesigner() || $user->id === $quoteRequest->user_id;
            // dd("Gate 'view-quote-request' za korisnika " . $user->email . " (Uloga: " . $user->role . ") dozvoljeno: " . ($isAllowed ? 'DA' : 'NE') . " | Vlasnik zahteva ID: " . $quoteRequest->user_id . " | Korisnik ID: " . $user->id);
            return $isAllowed;
        });

        Gate::define('view-proposal', function (User $user, Proposal $proposal) {
            return $user->isAdmin() || ($user->isDesigner() && $proposal->designer_id === $user->id) || ($user->isCustomer() && $user->id === $proposal->quoteRequest->user_id);
        });

        Gate::define('view-customer-proposal', function (User $user, Proposal $proposal) {
            return $user->isCustomer() && $user->id === $proposal->quoteRequest->user_id;
        });
        */
    }
}
