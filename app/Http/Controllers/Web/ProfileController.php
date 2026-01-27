<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Spatie\Activitylog\Models\Activity;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function index()
    {
        $user = auth()->user()->load(['company', 'roles']);
        
        // Get user statistics
        $stats = [
            'assessments_created' => $user->createdAssessments()->count(),
            'answers_submitted' => \App\Models\AssessmentAnswer::where('answered_by', $user->id)->count(),
        ];
        
        // Get recent activities
        $recentActivities = Activity::where('causer_id', $user->id)
            ->latest('id')
            ->take(10)
            ->get();
        
        return view('profile.index', compact('user', 'stats', 'recentActivities'));
    }

    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = auth()->user()->load('company');
        $companies = \App\Models\Company::orderBy('name')->get();
        
        return view('profile.edit', compact('user', 'companies'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_path'] = $avatarPath;
        }

        $user->update($validated);

        activity()
            ->performedOn($user)
            ->withProperties($validated)
            ->log('Profile updated');

        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show change password form
     */
    public function changePassword()
    {
        return view('profile.change-password');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        activity()
            ->performedOn($user)
            ->log('Password changed');

        return redirect()->route('profile.index')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Show user activity history
     */
    public function activity()
    {
        $user = auth()->user();
        
        $activities = Activity::where('causer_id', $user->id)
            ->with('subject')
            ->latest('id')
            ->paginate(20);
        
        return view('profile.activity', compact('activities'));
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        $user = auth()->user();
        
        return view('profile.settings', compact('user'));
    }

    /**
     * Update user settings
     */
    public function updateSettings(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|in:en,id',
            'email_notifications' => 'boolean',
            'assessment_reminders' => 'boolean',
            'weekly_summary' => 'boolean',
        ]);

        // Store settings in user preferences (you may need to add a preferences column or separate table)
        $user->update([
            'preferences' => array_merge($user->preferences ?? [], $validated)
        ]);

        activity()
            ->performedOn($user)
            ->withProperties($validated)
            ->log('Settings updated');

        return redirect()->route('profile.settings')
            ->with('success', 'Settings updated successfully.');
    }
}

