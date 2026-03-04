<?php

namespace App\Http\Controllers;

use App\Models\post;
use App\Models\post_attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $me = $request->user();
        // $followingids = $me->following()->wherePivot('is_accepted', true)->pluck('users.id');

        // $allid = $followingids->push($me->id);

        // $post = post::whereIn('user_id', $allid)->with('user', 'attachments')->latest()->paginate(10);
        
        $post = post::all();
        return response()->json(['message' => 'aku kecil', 'post' => $post], 200);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'caption' => 'required', // Tambahkan validasi array untuk file
        ]);
    
        if ($validator->fails()) {
        return response()->json(['message' => 'invalid field', 'errors' => $validator->errors()], 422);
        }

        // 1. Simpan Post Utama dulu
        $post = post::create([
        'caption' => $request->caption, 
        'user_id' => $request->user()->id
        ]);

        // 2. PROSES GAMBAR (Harus sebelum RETURN)
        if ($request->hasFile('post_attachments')) {
            foreach ($request->file('post_attachments') as $file) {
            $path = $file->store('post', 'public');

                post_attachment::create([
                'post_id' => $post->id, // Gunakan variabel $posts hasil create di atas
                'storage_path' => $path
                ]);
            }
        }

        // 3. RETURN HANYA SEKALI DI AKHIR
        return response()->json(['message' => 'create post successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(post $post, $id)
    {
        $post = post::find($id);
        if(!$post)
            {
                return response()->json(['message'=>'post not found'],404);
            }
        if($post->user_id !== Auth::id())
            {
                return response()->json(['message'=>'forbidden access']);
            }
        $post->delete();
        return response()->json(['message'=>'delete successfull'],200);
    }
}
