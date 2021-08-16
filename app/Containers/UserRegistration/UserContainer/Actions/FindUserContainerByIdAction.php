<?php

namespace App\Containers\UserRegistration\UserContainer\Actions;

use App\Containers\UserRegistration\UserContainer\Models\UserContainer;
use App\Containers\UserRegistration\UserContainer\Tasks\FindUserContainerByIdTask;
use App\Ship\Parents\Actions\Action;
use App\Ship\Parents\Requests\Request;

class FindUserContainerByIdAction extends Action
{
    public function run(Request $request): UserContainer
    {
        return app(FindUserContainerByIdTask::class)->run($request->id);
    }
}
