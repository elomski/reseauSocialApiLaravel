<?php

namespace App\Repositories;

use App\Http\Requests\GroupRequest\GroupCreateRequest;
use App\Interfaces\GroupInterface;
use App\Models\Admin;
use App\Models\Group;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

use function PHPUnit\Framework\returnSelf;

class GroupRepository implements GroupInterface
{

    public function creationAdmin(array $data)
    {
        return Admin::create($data);
    }

    public function creationMember(array $data)
    {
        return Member::create($data);
    }



    public function createGroup(array $data)
    {
        $group = Group::create([
            'name' => $data['name'],
            'description' => $data['description'],
            'created_by' => auth()->user()->id
        ]);

        Member::create([
            'group_id' => $group->id,
            'user_id' => auth()->user()->id,
            'is_admin' => true
        ]);

        return $group;
    }


    public function addMember(array $data, $groupId)
    {
        $existingMember = Member::where('group_id', $groupId)
            ->where('user_id', $data['user_id'])
            ->first();



        if ($existingMember) {
            return response()->json(['message' => 'L\'utilisateur est deja membre de ce groupe']);
        }

        $member = Member::create([
            'group_id' => $groupId,
            'user_id' => $data['user_id'],
            'is_admin' => false
        ]);

        return $member;
    }
}
