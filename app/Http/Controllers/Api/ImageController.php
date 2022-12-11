<?php

namespace App\Http\Controllers\Api;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * @group Picture
 *
 * API endpoints for managing Picture
 */

class ImageController extends Controller
{
    /**
     *
     * GET Picture List
     *
     * @response [
     *   {
     *      "id"     :  1,
     *      "path"   : "oguz.jpg",
     *      "sort"   :  1,
     *   },
     *   {
     *      "id"     :  2,
     *      "path"   : "test.jpg",
     *      "sort"   :  2,
     *   }
     *   ]
     */
    public function index()
    {
        $data = Image::select('id', 'path', 'sort')->orderBy('sort', 'asc')->get();
        return response($data, 200);
    }

    /**
     * POST Add Picture.
     *
     * @bodyParam   path    file    required    The Image.
     * @bodyParam   sort    integer required    The Image sort.       Example: 999
     *
     * @response {
     *  "path"  : "images/oguz.png",
     *  "sort"  :  999,
     *  "status":  1
     * }
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'path' => 'required',
            'path.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'sort' => 'required'
        ]);
        $image = new Image();
        if ($request->hasFile('path')) {
            $destinationPath = 'images/';
            $filename = 'img_'.uniqid() . "." . $request->path->getClientOriginalExtension();
            $request->path->move(public_path($destinationPath), $filename);
            $fullPath = $destinationPath . $filename;
            $image->path = $fullPath;
        }
        $image->sort = $request->sort;
        $image->save();
        return response('Success! Image Save.', 200);
    }

    /**
     * GET Picture Filtered by id.
     * @bodyParam id integer
     */
    public function show(Image $image)
    {
        return response($image, 200);
    }

    //image update
    public function update(Request $request, Image $image)
    {
    }


    /**
     * DELETE Remove Picture.
     * @bodyParam id integer
     */
    public function destroy(Image $image)
    {
        if (file_exists($image->path)) {
            unlink($image->path);
        }
        $image->delete();
        return response('Success! Delete İmage.', 200);
    }

    /**
     * UPDATE Order the pictures by sort.
     *
     * @bodyParam data array
     * @bodyParam data[0]['id'] integer
     * @bodyParam data[0]['sort'] integer
     *
     * @response {
     *  data [{
     *    "id"    : 2
     *    "sort"  : 1,
     *    }]
     * }
     */
    public function sort(Request $request)
    {
        $request->validate(['data' => 'required']);
        foreach ($request['data'] as $v) {
            if ($v['key'] && $v['sort']) {
                Image::whereId($v['key'])->update(['sort' => $v['sort']]);
            } else {
                return response('Invalid request.', 400);
            }
        }
        return response('Success! Sort Update.', 200);
    }
}
