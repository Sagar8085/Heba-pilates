<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\BlogRequest;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    /**
     * Fetch library of all blog posts.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function library()
    {
        $posts = Blog::latest()->paginate(25);

        return response()->json($posts);
    }

    /**
     * Store a new blog resource.
     *
     * @param \App\Http\Requests\BlogRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(BlogRequest $request)
    {
        $post = Blog::create([
            'title' => $request->title,
            'slug' => Helper::generateSlug(Blog::class, $request->title),
            'content' => $request->content,
            'created_by' => auth()->user()->id,
        ]);

        return response()->json($post);
    }

    /**
     * Load a single blog resource.
     *
     * @param App\Models\Blog $blog
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function single(Blog $blog)
    {
        return response()->json($blog);
    }

    /**
     * Update main blog resource.
     *
     * @param \App\Models\Blog $blog
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Blog $blog, Request $request)
    {
        $blog->update([
            'title' => $request->title,
            'content' => $request->content,
            'price' => $request->price,
        ]);

        return response()->json([
            'status' => 'success',
        ]);
    }

    /**
     * Process image to store in S3.
     *
     * @param \App\Models\Blog $blog
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage(Blog $blog, Request $request)
    {
        if ($request->has('files')) {
            foreach ($request->file('files') as $file) {

                /**
                 * Upload the image to the AWS S3 Bucket and set a public visibility.
                 */
                $file_path = Storage::disk('s3')->putFile('blogs/images', $file, 'public');

                /**
                 * Save the root image path against the category.
                 */
                $blog->update([
                    'image_path' => $file_path,
                ]);
            }
        }

        return response()->json($blog);
    }

    /**
     * Soft Delete a blog resource.
     *
     * @param \App\Models\Blog $blog
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Blog $blog)
    {
        $blog->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }
}
