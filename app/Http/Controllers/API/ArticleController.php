<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use http\Url;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function Ramsey\Uuid\v4;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => Article::all()
        ]);
    }

    /**
     * Display a listing of the deleted resource.
     *
     * @return JsonResponse
     */
    public function getArchived()
    {
        return response()->json([
            'status' => 'success',
            'data' => Article::onlyTrashed()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'required'
        ]);

        $article = new Article();
        $article->name = $request->input('name');
        $article->description = $request->input('description');
        $article->price = $request->input('price');
        $article->user_id = auth('api')->id() || 1;

        $base64_str = substr($request->input('image'), strpos($request->input('image'), ",") + 1);
        $image = base64_decode($base64_str);
        $image_path = 'public/articles/';
        $extension = explode('/', mime_content_type($request->input('image')))[1];
        $file_name = v4() . '.' . $extension;
        Storage::disk('local')->put($image_path . $file_name, $image);

        $image_url = asset(Storage::url($image_path . $file_name));

        $article->image = $image_url;
        $article->thumbnail = url($article->image);
        $article->save();

        return response()->json([
            'status' => 'success',
            'data' => $article
        ]);
    }

    public function removeImage($url)
    {
        $path = str_replace("storage", "public", $url);
        $index  = strpos($path, "public");
        if ($index) {
            $path = substr($path, $index);
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
            return response()->json([
                'status' =>  'success'
            ]);
        }
        return response()->json([
            'status' =>  'error',
            'message' => __("File not found on server.")
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function getArticle(Article $article)
    {
        return response()->json([
            'status' => 'success',
            'data' => $article
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Article $article
     * @return JsonResponse
     */
    public function update(Request $request, Article $article)
    {
        $this->validate($request, [
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'required'
        ]);

        $article->name = $request->input('name');
        $article->description = $request->input('description');
        $article->price = $request->input('price');

        // Only upload image if url has changed
        if ($request->input('image') != $article->image) {
            $this->removeImage($request->input('image'));
            $base64_str = substr($request->input('image'), strpos($request->input('image'), ",") + 1);
            $image = base64_decode($base64_str);
            $image_path = 'public/articles/';
            $extension = explode('/', mime_content_type($request->input('image')))[1];
            $file_name = v4() . '.' . $extension;
            Storage::disk('local')->put($image_path . $file_name, $image);

            $image_url = asset(Storage::url($image_path . $file_name));

            $article->image = $image_url;
            $article->thumbnail = url($article->image);
        }


        $article->update();

        return response()->json([
            'status' => 'success',
            'data' => $article
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Article $article
     * @return JsonResponse
     * @throws \Exception
     */
    public function trashArticle(Article $article)
    {
        $article->delete();
        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param Article $article
     * @return JsonResponse
     * @throws \Exception
     */
    public function restoreArticle($article)
    {
        $article = Article::onlyTrashed()->find($article);
        $article->restore();
        return response()->json([
            'status' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function destroyArticle($article)
    {
        $article = Article::onlyTrashed()->find($article);
        $this->removeImage($article->image);
        $article->forceDelete();
        return response()->json([
            'status' => 'success'
        ]);
    }
}
