<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use App\Models\Media;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Expectation;
use PhpParser\Node\Stmt\TryCatch;

class PostController extends Controller
{
    public function index(Request $request){
            $query= Post::with('user','category','image')->orderByDesc('created_at');
           
            if($request->category_id){
                $query->where('category_id',$request->category_id);
            }

            if($request->search){
                $query->where(function($q1) use ($request){
                    $q1->where('title','like','%'.$request->search.'%')
                    ->orWhere('body','like','%'.$request->search.'%');

                });
            }

            $posts=$query->paginate(10);
            return PostResource::collection($posts)->additional(['message'=>'success']);
    }


    public function create(Request $request){
        $request->validate([
            'title'=>'required',
            'body'=>'required|string',
            'category_id'=>'required',     
        ],
    [
        'category_id.required'=>'Category field is required.'
    ]);
        DB::beginTransaction();
        try{
            $file_name=null;
            if($request->hasFile('image')){
                $file=$request->file('image');
                $file_name=uniqid().'-'.date('Y-m-d-h-i-s').'.'. $file->getClientOriginalExtension();
                Storage::put('media/'.$file_name,file_get_contents($file));
            }

            $post = new Post();
            $post->user_id=auth()->user()->id;
            $post->category_id= $request->category_id;
            $post->title = $request->title;
            $post->body = $request->body;
           
            $post->save();
    
            $media = new Media();
            $media->file_name=$file_name;
            $media->file_type='image';
            $media->model_id= $post->id;
            $media->model_type= Post::class;
            $media->save();
            DB::commit();
            return ResponseHelper::success([],'Successfully Uploaded');
        }catch(Expectation $e){
            DB::rollBack();
            return ResponseHelper::fail($e->getExceptionMessage());
        }
      
    }

    public function show($id){
        $post=Post::with('user','category','image')->where('id',$id)->firstOrFail();
        return ResponseHelper::success(new PostDetailResource($post));
    }
}
