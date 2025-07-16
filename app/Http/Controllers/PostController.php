<?php

namespace App\Http\Controllers;


use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Helpers\SlugGeneretor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{

    use SlugGeneretor;
    public function index()
    {
        $posts = Post::with('tags')->paginate(10);

        if ($posts->isEmpty()) {
            return $this->error(404, 'No posts found');
        }

        return $this->success(200, 'Posts retrieved successfully', $posts);
    }

    public function show($id)
    {

        $post = Post::find($id);

        if (!$post) {
            return $this->error(404, 'Post not found');
        }

        return $this->success(200, 'Post retrieved successfully', $post);
    }

    public function store(Request $request)
    {


        $validatedData = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',

            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'status' => 'in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'cover_image' => 'nullable|image|max:2048',
            'seo_meta' => 'nullable|array',
            'seo_meta.title' => 'nullable|string|max:255',
            'seo_meta.description' => 'nullable|string|max:500',
            'media' => 'nullable|image|jpg,png,web|max:2048',

        ]);


        if ($validatedData->fails()) {
            return $this->error(422, 'Validation failed', $validatedData->errors());
        }



        $excerpt = strip_tags($request->content);
        $excerpt = substr($excerpt, 0, 150) . '...';

        // Logic to store a new post
        $post = new Post();
        $post->title = $request->title;
        $post->content = $request->content;
        $post->excerpt = $excerpt;
        $post->slug = $this->generateSlug($request->title);

        $post->status = $request->status ?? 'draft';
        $post->published_at = $request->published_at;

        $post->seo_meta = $request->seo_meta;
        $post->user_id = Auth::user()->id;
        $post->save();

        $post->tags()->attach($request->tags);

        if ($request->hasFile('media')) {
            // Handle cover image upload
            $mediaImage = $request->file('media');
            $mediaName = time() . '_' . $mediaImage->getClientOriginalName();
            $mediaSlug = $this->generateSlug($mediaName);
             $mediaImage->storeAs('posts/media', $mediaName, 'public');
             $mediaPath = asset('storage/posts/media/' . $mediaName);
            $post->media()->create([
                'file_name' => $mediaName,
                'file_path' => $mediaPath,
                'file_type' => $mediaImage->getClientMimeType(),
                'file_size' => $mediaImage->getSize(),
                'slug' => $mediaSlug,
                'user_id' => Auth::user()->id,
                'post_id' => $post->id,
            ]);
           
          
        }


        if (!$post) {
            return $this->error(500, 'Failed to create post');
        } else {
            return $this->success(201, 'Post created successfully', $post);
        }






    }








    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return $this->error(404, 'Post not found');
        }

        $validatedData = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'status' => 'sometimes|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'cover_image' => 'nullable|image|max:2048',
            'seo_meta' => 'nullable|array',
            'seo_meta.title' => 'nullable|string|max:255',
            'seo_meta.description' => 'nullable|string|max:500',
            'media' => 'nullable|image|jpg,png,web|max:2048',
             'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        if ($validatedData->fails()) {
            return $this->error(422, 'Validation failed', $validatedData->errors());
        }

        $post->title = $request->input('title', $post->title);
        $post->content = $request->input('content', $post->content);
        $post->status = $request->input('status', $post->status);
        $post->published_at = $request->input('published_at', $post->published_at);
        $post->seo_meta = $request->input('seo_meta', $post->seo_meta);

        if ($request->hasFile('media')) {
            $mediaImage = $request->file('media');
            $mediaName = time() . '_' . $mediaImage->getClientOriginalName();
            $mediaSlug = $this->generateSlug($mediaName);
            $mediaImage->storeAs('posts/media', $mediaName, 'public');
            $mediaPath = asset('storage/posts/media/' . $mediaName);
            $post->media()->updateOrCreate(
                ['post_id' => $post->id],
                [
                    'file_name' => $mediaName, 
                    'file_path' => $mediaPath,
                    'file_type' => $mediaImage->getClientMimeType(),
                    'file_size' => $mediaImage->getSize(),
                    'slug' => $mediaSlug,
                    'user_id' => Auth::user()->id,
                
                ]);
           
        }


        $post->tags()->sync($request->input('tags', []));
        $post->save();

        return $this->success(200, 'Post updated successfully', $post);





    }

    public function destroy($id)
    {

        $post = Post::find($id);

        if (!$post) {
            return $this->error(404, 'Post not found');
        }

        $post->delete();

        return $this->success(200, 'Post deleted successfully');
    }

    public function search($query)
    {

        $posts = Post::where('title', 'like', '%' . $query . '%')
            ->orWhere('content', 'like', '%' . $query . '%')
            ->get();

        return $this->success(200, 'Search results', $posts);
    }



    public function getByTag($tagId)
    {
        // Logic to get posts by tag ID
    }
}
