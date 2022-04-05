<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UpdateController extends Controller
{

    /**
     * Update the database with the new migrations
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDatabase()
    {
        $migrateDatabase = $this->migrateDatabase();
        if ($migrateDatabase !== true) {
            return back()->with('error', 'Failed to migrate the database. ' . $migrateDatabase);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Database migrated successfully');
    }

    /**
     * Migrate the database
     *
     * @return bool|string
     */
    private function migrateDatabase()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
			Artisan::call('db:seed');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function createAdmin()
    {
        try {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'status' => 'active',
                'group' => 'admin',
                'password' => bcrypt('admin12345'),
                'available_minutes' => 1000,
                'email_verified_at' => now(),
                'language_file' => config('stt.language.file'),
                'language_live' => config('stt.language.live'),
                'referral_id' => '12343242',
                'job_role' => 'Administrator'
            ]);

            $user->assignRole('admin');

        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

}
