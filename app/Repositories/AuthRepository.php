<?php

namespace App\Repositories;

use App\Interfaces\AuthInterface;
use App\Mail\OtpCodeMail;
use App\Models\Invite;
use App\Models\Member;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthRepository implements AuthInterface
{
    // public function register(array $data)
    // {
    //    $user = User::create($data);
    //     $otp_code = [
    //        'email' => $data['email'],
    //        'code' => rand(111111, 999999)
    //     ];

    //    OtpCode::where('email', $data['email'])->delete();
    //    OtpCode::create($otp_code);

    //    $invitations = Invite::where('email',$user->email)->where('status', 'pending')->get();

    //     foreach($invitations as $invitation){
    //         Member::create([
    //             'group_id' => $invitation->group_id,
    //             'user_id' => $user->id,
    //             'is_admin' => false
    //         ]);

    //     }


    //     // Member::create([
    //     //     'group_id' => $invitation->group_id,
    //     //     'user_id' => $user->id,
    //     //     'is_admin' => false
    //     // ]);


    //     $invitation->update(['status' => 'accepted']);


    //     Mail::to($data['email'])->send(new OtpCodeMail(
    //         $data['name'],
    //         $data['email'],
    //         $otp_code['code'],
    //     ));


    // }
    public function register(array $data)
{
    // Création de l'utilisateur
    $user = User::create($data);

    // Générer un OTP (One-Time Password)
    $otp_code = [
        'email' => $data['email'],
        'code' => rand(111111, 999999)
    ];

    // Supprimer les anciens codes OTP associés à cet email
    OtpCode::where('email', $data['email'])->delete();

    // Enregistrer le nouveau code OTP
    OtpCode::create($otp_code);

    // Récupérer les invitations en attente pour cet email
    $invitations = Invite::where('email', $user->email)->where('status', 'pending')->get();

    // Boucle sur les invitations pour ajouter l'utilisateur comme membre dans chaque groupe
    foreach ($invitations as $invite) {
        Member::create([
            'group_id' => $invite->group_id,
            'user_id' => $user->id,
            'is_admin' => false // Par défaut, pas administrateur
        ]);

        // Mettre à jour le statut de l'invitation
        $invite->update(['status' => 'accepted']);
    }

    // Envoyer l'email contenant le code OTP
    Mail::to($data['email'])->send(new OtpCodeMail(
        $data['name'],
        $data['email'],
        $otp_code['code'],
    ));
}



    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if(!$user) {
            return false;
        }
        if(!Hash::check($data ['password'], $user->password)) {
            return false;
        }
        $user->tokens()->delete();
        $user->token = $user->createToken($user->id)->plainTextToken;

        return $user;
    }


    public function checkOtpCode(array $data)
    {
        $otp_code = OtpCode::where('email', $data['email'])->first();

        if(!$otp_code) {
            return false;
        }
        if(Hash::check($data['code'], $otp_code['code'])) {
            $user = User::where('email', $data['email'])->first();

            $user->update(['is_confirmed' => true]);

            $otp_code->delete();

            $user->token = $user->createToken($user->id)->plainTextToken;
            return $user;
        }

        return false;
    }


}

