<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserDeleteRequest;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

/**
 * Class UsersController.
 */
class UserController extends Controller
{
    /**
     * GET api/admin/users.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $query = User::getSpatieQueryBuilder();

        return UserResource::collection($this->getPaginatedResult($query));
    }

    /**
     * PUT api/admin/users.
     *
     * @param UserStoreRequest $request
     *
     * @return UserResource
     * @throws ValidationException
     */
    public function store(UserStoreRequest $request): UserResource
    {
        $user = User::query()->where('email', $request->validated()['email'])->onlyTrashed()->first();

        if ($user) {
            $user->restore();
            $user->update($request->validated());
        } else {
            $this->validate($request, [
                'email' => 'unique:users,email',
                'name'  => 'unique:users,name'
            ]);

            $user = User::create($request->validated() + ['password' => bcrypt(Str::random(8))]);
            Password::sendResetLink(
                $request->only('email')
            );
        }

        $roles = Role::findById($request->get('role_id'), 'api');

        $user->assignRole($roles);

        return new UserResource($user);
    }

    /**
     * SHOW api/admin/users.
     *
     * @param User $user
     *
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    /**
     * PUT api/admin/users.
     *
     * @param UserUpdateRequest $request
     * @param int $user_id
     * @return UserResource
     */
    public function update(UserUpdateRequest $request, int $user_id): UserResource
    {
        $updatedUser = User::query()->findOrFail($user_id);

        $updateData = collect();
        $updatedUser->fill($updateData->toArray());

        // Not allowed to update your own role
        if ($request->user()->id === $updatedUser->getKey()) {
            $updateData->forget('role_id');
        } else {
            $updatedUser->syncRoles([$request->validated()['role_id']]);
        }

        $updatedUser->save();

        return new UserResource($updatedUser);
    }

    /**
     * DELETE api/admin/users.
     *
     * @param UserDeleteRequest $request
     * @param int $user_id_to_delete
     * @return UserResource
     */
    public function destroy(UserDeleteRequest $request, int $user_id_to_delete): UserResource
    {
        abort_if($user_id_to_delete === $request->user()->getKey(), 403, 'You can not delete yourself');

        $user = User::query()->findOrFail($user_id_to_delete);

        $user->delete();

        return UserResource::make($user);
    }
}
