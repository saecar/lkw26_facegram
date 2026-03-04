<?php

namespace App\Http\Controllers;

use App\Models\follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function follow(Request $request, $username)
    {
        $userToFollow = User::where('username', $username)->first();
        $me = $request->user();

        if (!$userToFollow) return response()->json(['message' => 'User not found'], 404);

        if ($me->id === $userToFollow->id) {
            return response()->json(['message' => 'You are not allowed to follow yourself'], 422);
        }

        // Cek status follow yang sudah ada
        $existing = follow::where('follower_id', $me->id)
                          ->where('following_id', $userToFollow->id)
                          ->first();

        if ($existing) {
            return response()->json([
                'message' => 'You are already followed',
                'status' => $existing->is_accepted ? 'following' : 'requested'
            ], 422);
        }

        // LOGIC PRIVATE: Jika akun target private, status = 0 (requested)
        $isAccepted = $userToFollow->is_private ? false : true;

        Follow::create([
            'follower_id' => $me->id,
            'following_id' => $userToFollow->id,
            'is_accepted' => $isAccepted
        ]);

        return response()->json([
            'message' => 'Follow success',
            'status' => $isAccepted ? 'following' : 'requested'
        ], 200);
    }

    public function unfollow(Request $request, $username)
    {
        $userToUnfollow = User::where('username', $username)->first();
        if (!$userToUnfollow) return response()->json(['message' => 'User not found'], 404);

        $follow = Follow::where('follower_id', $request->user()->id)
                        ->where('following_id', $userToUnfollow->id)
                        ->first();

        if (!$follow) {
            return response()->json(['message' => 'You are not following the user'], 422);
        }

        $follow->delete();
        return response()->json([], 204); // Sesuai kisi-kisi: 204 No Content
    }



    public function accept(Request $request, $username){
        $follower = User::where('username', $username)->first();
        if (!$follower) return response()->json(['message' => 'User not found'], 404);

        // Cari request follow di mana 'SAYA' adalah target yang di-follow
        $followRequest = Follow::where('follower_id', $follower->id)
                               ->where('following_id', $request->user()->id)
                               ->first();

        if (!$followRequest || $followRequest->is_accepted) {
            return response()->json(['message' => 'Follow request not found or already accepted'], 422);
        }

        $followRequest->update(['is_accepted' => true]);

        return response()->json(['message' => 'Follow request accepted'], 200);
    }

    public function getfollower(Request $request, $username){
        $user = User::where('username', $username)->first();
        if (!$user) return response()->json(['message' => 'user not found'], 404);
        $followers = $user ->followers()->wherePivot('is_accepted', true)->get(['full_name', 'username', 'is_private']);

        return response()->json(['followers' => $followers], 200);
    }



}
