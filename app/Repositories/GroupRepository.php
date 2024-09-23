<?php

namespace App\Repositories;

use App\Interfaces\GroupInterface;
use App\Mail\AddMemberMail;
use App\Models\Admin;
use App\Models\administrateur;
use App\Models\Group;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class GroupRepository implements GroupInterface
{
    public function groupCreate(array $data){
        return Group::create($data);
    }

    public function creationAdmin(array $data){
        return Admin::create($data);
    }

    public function creationMember(array $data){
        return Member::create($data);
    }

    public function addMember(array $data){

        $user = User::where('email', $data['email'])->first();


        if(!$user){
           $Nuser = User::create([
                $data['name'],
                $data['email'],
                $data['password'],
            ]);
            return $Nuser;

            $user_id = $Nuser()->id->get();

            if($user !== null) {
                $member = Member::create(
                    $user_id,
                    $data['group_id'],
                    $data['fullname']
                );
               return $member;
            }
        }

        Mail::to($data['email'])->send(new AddMemberMail(
            $data['email'],
            $data[''],
            $data['']
        ));
        }
}
