<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Helpers\SlugGenerator;

class TagController extends Controller
{

    use SlugGenerator;
   protected function rules($id = null)
{
    return [
        'name' => 'required|string|max:255|unique:tags,name' . ($id ? ',' . $id : ''),
        // 'description' => 'nullable|string|max:500',
    ];
}
    public function index()
    {
        $tags = Tag::all();

        if ($tags->isEmpty()) {
            return $this->error(404, 'No tags found');
        }

        return $this->success(200, 'Tags retrieved successfully', $tags);
    }

    public function showTagById($id)
    {

        $tag = Tag::find($id);

        if (!$tag) {
            return $this->error(404, 'Tag not found');
        }

        return $this->success(200, 'Tag retrieved successfully', $tag);
    }

    public function TagStore(Request $request)
    {


        $validatedData =   $this->validateRequest(404, $request->all(), $this->rules());

        if ($validatedData) {
            return $validatedData;
        }
        //    dd($request->all());
        $tag = new Tag();
        $tag->name = $request->name;
        $tag->slug = $this->generateSlug($request->name);
        $tag->save();

        return $this->success(201, 'Tag created successfully', $tag);



    }






    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return $this->error(404, 'Tag not found');
        }

        $validatedData = $this->validateRequest(422, $request->all(), $this->rules($id));

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
