<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContributorRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Outhebox\TranslationsUI\Enums\RoleEnum;

class TranslationsController extends Controller
{
    /**
     * Display the translations management dashboard
     */
    public function index(Request $request)
    {
        $contributors = $this->getContributors();
        $translationsUrl = url('/translations');

        if ($request->get('view') === 'card') {
            return view('pages.admin.translations.cards', compact('contributors', 'translationsUrl'));
        }

        return view('pages.admin.translations.index', compact('contributors', 'translationsUrl'));
    }

    /**
     * Store a new contributor
     */
    public function storeContributor(ContributorRequest $request)
    {
        $validated = $request->validated();

        DB::table('ltu_contributors')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('translations.index')
            ->with('success', 'Contributor created successfully.');
    }

    /**
     * Update an existing contributor
     */
    public function updateContributor(ContributorRequest $request, $id)
    {
        $validated = $request->validated();

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'updated_at' => now(),
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        DB::table('ltu_contributors')
            ->where('id', $id)
            ->update($updateData);

        return redirect()->route('translations.index')
            ->with('success', 'Contributor updated successfully.');
    }

    /**
     * Create or update contributor via single AJAX endpoint.
     */
    public function saveContributor(ContributorRequest $request)
    {
        $id = $request->input('id');

        $validated = $request->validated();

        if ($id) {
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'updated_at' => now(),
            ];
            if (! empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }
            DB::table('ltu_contributors')->where('id', $id)->update($updateData);

            return response()->json(['success' => true, 'message' => __('Contributor updated successfully.')]);
        }

        DB::table('ltu_contributors')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => __('Contributor created successfully.')]);
    }

    /**
     * Fetch single contributor (AJAX)
     */
    public function getContributor($id)
    {
        $row = DB::table('ltu_contributors')->select('id', 'name', 'email', 'role', 'created_at')->where('id', $id)->first();
        if (! $row) {
            return response()->json(['success' => false, 'message' => 'Not found'], 404);
        }

        return response()->json(['success' => true, 'data' => $row]);
    }

    /**
     * Delete a contributor
     */
    public function deleteContributor($id)
    {
        try {
            $contributor = DB::table('ltu_contributors')->where('id', $id)->first();

            if (! $contributor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contributor not found.',
                ], 404);
            }

            DB::table('ltu_contributors')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contributor deleted successfully.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete contributor: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all contributors from the translations system
     */
    private function getContributors()
    {
        return DB::table('ltu_contributors')
            ->select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($contributor) {
                $contributor->role_name = $this->getRoleName($contributor->role);

                return $contributor;
            });
    }

    /**
     * Get role name from role ID
     */
    private function getRoleName($role)
    {
        return match ($role) {
            RoleEnum::owner->value => 'Owner',
            RoleEnum::translator->value => 'Translator',
            default => 'Unknown',
        };
    }

    /**
     * Get role options for forms
     */
    public function getRoleOptions()
    {
        return [
            RoleEnum::owner->value => 'Owner',
            RoleEnum::translator->value => 'Translator',
        ];
    }
}
