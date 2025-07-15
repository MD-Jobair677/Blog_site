<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Helpers\SlugGeneretor;

class TagController extends Controller
{

    use SlugGeneretor;
    protected $rules = [
        'name' => 'required|string|max:255|unique:tags,name',
        'description' => 'nullable|string|max:500',
    ];
    public function index()
    {
        $tags = Tag::all();

        if ($tags->isEmpty()) {
            return $this->error(404, 'No tags found');
        }

        return $this->success(200, 'Tags retrieved successfully', $tags);
    }

    public function show($id)
    {
      
        $tag = Tag::find($id);

        if (!$tag) {
            return $this->error(404, 'Tag not found');
        }

        return $this->success(200, 'Tag retrieved successfully', $tag);
    }

    public function TagStore(Request $request)
    {
        return  $this->validateRequest(404,$request->all(), $this->rules);

        $tag = new Tag();
        $tag->name = $request->name;
        $tag->slug = $this->generateSlug($request->name);
        $tag->save();

    }

    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return $this->error(404, 'Tag not found');
        }

        $validatedData = $this->validateRequest(422, $request->all(), $this->rules);

        if ($validatedData) {
            return $validatedData;
        }

        $tag->name = $request->name;
        $tag->slug = $this->generateSlug($request->name);
    
        $tag->save();

        return $this->success(200, 'Tag updated successfully', $tag);
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return $this->error(404, 'Tag not found');
        }

        $tag->delete();

        return $this->success(200, 'Tag deleted successfully');
    }
}
