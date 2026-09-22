<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PasswordResetRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Models\ConsultantTarget;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->select(['id', 'role_id', 'name', 'email', 'phone', 'designation', 'is_active', 'created_at'])
            ->with('role:id,name,slug')
            ->withCount(['assignedLeads AS open_leads' => fn ($q) => $q->open()])
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%' . $request->string('q') . '%')
                ->orWhere('email', 'like', '%' . $request->string('q') . '%')))
            ->when($request->filled('role'), fn ($q) => $q->where('role_id', $request->integer('role')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'user' => new User(['is_active' => true]),
            'roles' => Role::orderBy('name')->get(['id', 'name', 'slug']),
            'target' => null,
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            // The 'hashed' cast on the model hashes the password on save.
            $user = User::create($request->safe()->except(['target_deals', 'target_amount', 'password_confirmation']));
            $this->saveTarget($user, $request);
        });

        return redirect()->route('admin.users.index')->with('success', 'Account created.');
    }

    public function edit(User $user): View
    {
        $user->load('role:id,slug');

        return view('admin.users.edit', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(['id', 'name', 'slug']),
            'target' => $user->targets()->where('period', ConsultantTarget::currentPeriod())->first(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->except(['target_deals', 'target_amount']);

        // An admin cannot lock themselves out by accident.
        if ($user->is($request->user())) {
            unset($data['is_active'], $data['role_id']);
        }

        DB::transaction(function () use ($user, $data, $request) {
            $user->update($data);
            $this->saveTarget($user->fresh('role'), $request);
        });

        return redirect()->route('admin.users.index')->with('success', 'Account updated.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('success', $user->is_active ? 'Account activated.' : 'Account deactivated. They are signed out on their next request.');
    }

    public function password(PasswordResetRequest $request, User $user): RedirectResponse
    {
        $user->update(['password' => $request->validated('password')]);

        return back()->with('success', "Password reset for {$user->name}. Share it with them securely.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->assignedLeads()->open()->exists()) {
            return back()->with('error', 'This person still has open leads. Reassign them first, or deactivate the account instead.');
        }

        $user->delete();

        return back()->with('success', 'Account removed.');
    }

    private function saveTarget(User $user, UserRequest $request): void
    {
        $user->loadMissing('role:id,slug');

        if (! $user->isConsultant() || ! $request->filled('target_deals') && ! $request->filled('target_amount')) {
            return;
        }

        ConsultantTarget::updateOrCreate(
            ['user_id' => $user->id, 'period' => ConsultantTarget::currentPeriod()],
            [
                'target_deals' => (int) $request->input('target_deals', 0),
                'target_amount' => (float) $request->input('target_amount', 0),
            ]
        );
    }
}
