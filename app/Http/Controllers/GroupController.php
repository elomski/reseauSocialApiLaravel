<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest\AdminRequest;
use App\Http\Requests\GroupRequest\FileRequest;
use App\Http\Requests\GroupRequest\GroupCreateRequest;
use App\Http\Requests\GroupRequest\MemberCreateRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\MemberResource;
use App\Interfaces\GroupInterface;
use App\Mail\GroupInviteMail;
use App\Mail\NewFileUploaded as MailNewFileUploaded;
use App\Mail\OtpcodeMail;
use App\Models\File;
use App\Models\Group;
use App\Models\Invite;
use App\Models\Member;
use App\Notifications\NewFileUploaded;
use App\Responses\ApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

class GroupController extends Controller
{
    private GroupInterface $groupInterface;

    public function __construct(GroupInterface $groupInterface)
    {
        $this->groupInterface = $groupInterface;
    }

    public function registerGroup(GroupCreateRequest $request)
    {
        $data = [
            'name' => $request->name,
            'description' => $request->description
        ];

        DB::beginTransaction();

        try {

            $group = $this->groupInterface->createGroup($data);
            DB::commit();
            return ApiResponses::sendResponse(
                true,
                [new GroupResource($group)],
                'Operation effectuer.',
                200
            );
        } catch (Throwable $th) {
            return $th;
            return ApiResponses::rollback($th);
        }
    }

    public function createAdministrator(AdminRequest $request)
    {
        $data = [
            'user_id' => $request->user_id,
            'fullname' => $request->fullname
        ];

        DB::beginTransaction();

        try {

            $admin = $this->groupInterface->creationAdmin($data);
            DB::commit();
            return ApiResponses::sendResponse(
                true,
                [new AdminResource($admin)],
                'Operation effectuer.',
                200
            );
        } catch (\Throwable $th) {
            return ApiResponses::rollback($th);
        }
    }






    public function uploadFile(Request $request)
    {

        $request->validate([
            'file' => 'required|file|mimes:png,jpg,pdf,doc,docx,zip',
            'group_id' => 'required|exists:groups,id'
        ]);

       

        $isMember = Member::where('group_id', $request->group_id)
            ->where('user_id', auth()->user()->id)
            ->exists();


        if (!$isMember) {
            return response()->json([
                'message' => 'Vous ne faites pas partie de ce groupe.',
            ], 403);
        }


        if ($request->hasFile('file') && $request->file('file')->isValid()) {

            $file = $request->file('file')->store('services', 'public');

            // $data['file'] = $file;

            $fichier = File::create([

                'file' => $file,
                'group_id' => $request->group_id,
            ]);

            $groupMembers = Member::where('group_id', $request->group_id)->get();

            foreach ($groupMembers as $member) {

                if ($member->user && $member->user->email) {
                    Mail::to($member->user->email)->send(
                        new MailNewFileUploaded(
                            $fichier,
                            $member->group
                        )
                    );
                }
            }

            // Ajout du fichier

            return response()->json([
                'message' => 'Fichier ajouté au groupe avec succès!',
                'file' => $fichier
            ], 201);
        }

        return response()->json([
            'message' => 'erreur lors du telechargement du fichier.',
        ], 400);
    }




    public function allMembers()
    {
        return ApiResponses::sendResponse(
            true,
            [new MemberResource(Member::all())],
            'Operation effectue',
            201
        );
    }

    public function addMember(Request $request, string $groupId)
    {
        $data = [
            'user_id' => $request->user_id
        ];


        DB::beginTransaction();

        try {
            $member = $this->groupInterface->addMember($data, $groupId);
            DB::commit();
            return ApiResponses::sendResponse(
                true,
                [new MemberResource($member)],
                'Operation effectuer.',
                200
            );
        } catch (\Throwable $th) {
            return $th;

            return ApiResponses::rollback($th);
        }
    }



    public function invite(Request $request, $groupId)
    {
        // Utilise $groupId ici
        $request->validate([
            'email' => 'required|email'
        ]);

        // Rechercher une invitation existante
        $existingInvite = Invite::where('email', $request->email)
            ->where('group_id', $groupId)
            ->first();

        $group = Group::find($groupId);

        if ($existingInvite) {
            return response()->json(['message' => 'L\'utilisateur est déjà membre de ce groupe']);
        }

        Invite::create([
            'email' => $request->email,
            'group_id' => $groupId,
        ]);


        Mail::to($request->email)->send(new GroupInviteMail($request->email, $groupId));

        return response()->json(['message' => 'Invitation envoyée avec succès']);
    }
}
