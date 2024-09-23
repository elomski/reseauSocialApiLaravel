<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest\AdminRequest;
use App\Http\Requests\GroupRequest\GroupCreateRequest;
use App\Http\Requests\GroupRequest\MemberCreateRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\MemberResource;
use App\Interfaces\GroupInterface;
use App\Models\File;
use App\Models\Member;
use App\Responses\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class GroupController extends Controller
{
    private GroupInterface $groupInterface;

    public function __construct(GroupInterface $groupInterface)
    {
        $this->groupInterface = $groupInterface;
    }

    public function registerGroup(GroupCreateRequest $request){
        $data = [
            'admin_id' => $request->admin_id,
            'name' => $request->name,
            'description' => $request->description
        ];

        DB::beginTransaction();

        try{

        $group = $this->groupInterface->groupCreate($data);
        DB::commit();
        return ApiResponses::sendResponse(
            true,
            [new GroupResource($group)],
            'Operation effectuer.',
            200);

        }catch(Throwable $th){
            return $th;
            return ApiResponses::rollback($th);

        }
    }

    public function createAdministrator(AdminRequest $request){
        $data = [
            'user_id' => $request->user_id,
            'fullname' => $request->fullname
        ];

        DB::beginTransaction();

        try{

            $admin = $this->groupInterface->creationAdmin($data);
            DB::commit();
            return ApiResponses::sendResponse(
                true,
                [new AdminResource($admin)],
                'Operation effectuer.',
                200
            );

        }catch(\Throwable $th){
            return ApiResponses::rollback($th);
        }

    }

    public function createMember(MemberCreateRequest $request){
        $data = [
            'user_id' => $request->user_id,
            'group_id' => $request->group_id,
            'pseudo' => $request->pseudo
        ];


        DB::beginTransaction();
        try{
            $member = $this->groupInterface->creationMember($data);
            DB::commit();
            return ApiResponses::sendResponse(
                true,
                [new MemberResource($member)],
                'Operation effectuer.',
                200
            );
        }catch(\Throwable $th){
            return $th;

            return ApiResponses::rollback($th);
        }
    }



    public function uploadFile(Request $request) {


        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file')->store('services', 'public');
            $data['file'] = $file;

            $fichier = File::create([

                'file' => $file,
                'group_id' => $request->group_id,
            ]);
        }

        // Ajout du fichier


        return response()->json([
            'message' => 'Fichier ajouté au groupe avec succès!',
            'file' => $fichier
        ], 201);

    }

    public function allMembers(){
        return ApiResponses::sendResponse(
            true,
            [new MemberResource(Member::all())],
            'Operation effectue',
            201
        );

    }



}
