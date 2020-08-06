<?php

namespace App\Repositories\Articles;

use App\Model\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Repositories\Articles\ArticlesRepositoryInterface;

class ArticlesRepository implements ArticlesRepositoryInterface{
    
    /**
     * @OA\Get(
     *     tags={"Article Module"},
     *     path="/api/articles",
     *     summary="Get 25 Article Data Per Page",
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="403", description="Forbidden"),
     *     @OA\Response(response="404", description="Not Found"), 
     *     @OA\Response(response="422", description="Unprocessable Entity"), 
     *     @OA\Response(response="500", description="Internal Server Error"), 
     * )
     */
    public function getDataPagination(){
        // $articles = Article::latest()->get();
        $articles = Article::orderBy('updated_at','desc')->paginate(25);
        return response([
            'success' => true,
            'message' => 'Here is the Data!',
            'data' => $articles
        ], 200);
    }

    /**
     * @OA\Get(
     *     tags={"Article Module"},
     *     path="/api/articles/{id}",
     *     summary="Get Article Data by ID",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description = "id",
     *          required=true,
     *          @OA\Schema(
     *                 type="string",
     *         )
     *     ),
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Response(response="201", description="Created"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="403", description="Forbidden"),
     *     @OA\Response(response="404", description="Not Found"), 
     *     @OA\Response(response="422", description="Unprocessable Entity"), 
     *     @OA\Response(response="500", description="Internal Server Error"), 
     * )
     */
    public function getById($id){
        $article = Article::whereId($id)->first();

        if ($article) {
            return response()->json([
                'success' => true,
                'message' => 'Here is the Details!',
                'data'    => $article
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Data Not Found!',
                'data'    => ''
            ], 404);
        }
    }

    /**
     * @OA\Post(
     *     tags={"Article Module"},
     *     path="/api/articles",
     *     summary="Create Article Data",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"title","content","file"},
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="file",
     *                      type="file",
     *                  ),
     *              ),
     *          ),
     *      ),
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Response(response="201", description="Created"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="403", description="Forbidden"),
     *     @OA\Response(response="404", description="Not Found"), 
     *     @OA\Response(response="422", description="Unprocessable Entity"), 
     *     @OA\Response(response="500", description="Internal Server Error"), 
     * )
     */
    public function create(Request $request){
        //validate data
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
        ],
            [
                'title.required' => 'Title is Required!',
                'content.required' => 'Content is Required!',
            ]
        );

        if($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Fill Up Required Fields!',
                'data'    => $validator->errors()
            ],400);

        }

        else {
            $currentFile = 'uploads/images/user.png';
            if ($request->file != $currentFile) {
                if ($request->file) {
                    $extension = $request->file('file')->extension();
                    if ($extension != 'jpg' && $extension != 'png' && $extension != 'jpeg') {
                        $data = [
                            'status'    => false,
                            'message'   => 'File should be jpg, png, or jpeg but you use '.strtoupper($extension),
                        ];
                        return response()->json($data, 422);
                    }else{
                        $validator = Validator::make($request->all(), [
                            'file'     => 'max:1024'
                        ]);

                        if($validator->fails()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Max Size is 1024Kb!'
                            ],422);
                        }
                    }
                }else{
                    $data = [
                        'status'    => false,
                        'message'   => 'File is Required!'
                    ];
                    return response()->json($data, 422);
                }
            } 

            // ADD FILE
            if($request->file == $currentFile){
                $fileName = 'user.png';
                $path = Storage::putFileAs('public/images/articles', $request->file('file'), $fileName);
            }else{ 
                $fileName = date('dmyHis').'.'.$extension;
                $path = Storage::putFileAs('public/images/articles', $request->file('file'), $fileName);
            }
            // END FILE

            $article = Article::create([
                'id'        => Uuid::uuid4()->getHex(),
                'title'     => $request->input('title'),
                'content'   => $request->input('content'),
                'file'      => $fileName,
            ]);

            if ($article) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Created!',
                ], 201);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed!',
                ], 400);
            }
        }
    }

    /**
     * @OA\Post(
     *     tags={"Article Module"},
     *     path="/api/articles/{id}",
     *     summary="Update Article Data by ID",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description = "id",
     *          required=true,
     *          @OA\Schema(
     *                 type="string",
     *         )
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"title","content"},
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="file",
     *                      type="file",
     *                  ),
     *              ),
     *          ),
     *      ),
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Response(response="201", description="Created"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="403", description="Forbidden"),
     *     @OA\Response(response="404", description="Not Found"), 
     *     @OA\Response(response="422", description="Unprocessable Entity"), 
     *     @OA\Response(response="500", description="Internal Server Error"), 
     * )
     */
    public function update(Request $request, $id){
        //validate data
        $validator = Validator::make($request->all(), [
            'title'     => 'required',
            'content'   => 'required',
            'file'      => 'nullable',
        ],
            [
                'title.required' => 'Title is Required!',
                'content.required' => 'Content is Required!',
                'file.required' => 'File is Required!',
            ]
        );

        if($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => 'Fill Up Required Fields!',
                'data'    => $validator->errors()
            ],400);

        } else {
            // dd($request->all());
            $currentFile = 'user.png';
            $dataExist = Article::find($id);

            if ($request->hasFile('file')) {
                if ($request->file) {
                    $extension = $request->file('file')->extension();
                    if ($extension != 'jpg' && $extension != 'png' && $extension != 'jpeg') {
                        $data = [
                            'status'    => false,
                            'message'   => 'File should be jpg, png, or jpeg but you use '.strtoupper($extension),
                        ];
                        return response()->json($data, 422);
                    }else{
                        $validator = Validator::make($request->all(), [
                            'file'     => 'max:1024'
                        ]);

                        if($validator->fails()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Max Size is 1024Kb!'
                            ],422);
                        }
                    }
                }else{
                    $data = [
                        'status'    => false,
                        'message'   => 'File is Required!'
                    ];
                    return response()->json($data, 422);
                }
            }

            if (!$dataExist) {
                $data = [
                    'status'    => false,
                    'message'   => 'Data Not Found!'
                ];
                return response()->json($data, 404);
            }

            // ADD FILE
            if($dataExist == $currentFile){
                if (storage_path('app/public/images/articles/'.$dataExist->file) != $request->file) {
                    if ($request->file != '' && $request->file != null) {
                        $fileName = date('dmyHis').'.'.$extension;
                        $path = Storage::putFileAs('public/images/articles', $request->file('file'), $fileName);
                        
                        if ($dataExist->file != $currentFile) {
                            @unlink(storage_path('app/public/images/articles/'.$dataExist->file));
                        }
                    }
                }else{
                    $fileName = 'user.png';
                }
            }else if(storage_path('app/public/images/articles/'.$dataExist->file) != $request->file){
                if ($request->file != '' && $request->file != null) {
                        $fileName = date('dmyHis').'.'.$extension;
                        $path = Storage::putFileAs('public/images/articles', $request->file('file'), $fileName);
                        
                        if ($dataExist->file != $currentFile) {
                            @unlink(storage_path('app/public/images/articles/'.$dataExist->file));
                        }
                }else{
                    $fileName = $dataExist->file;
                }
            }else if(storage_path('app/public/images/articles/'.$dataExist->file) == $request->file){
                $fileName = $dataExist->file;
            }
            // END FILE

            $article = Article::findOrFail($id)->update([
                'title'     => $request->title,
                'content'   => $request->content,
                'file'      => $fileName,
            ]);

            if ($article) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Updated!',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Update Failed!',
                ], 500);
            }
        }
    }

    /**
     * @OA\Delete(
     *     tags={"Article Module"},
     *     path="/api/articles/{id}",
     *     summary="Delete Article Data by ID",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description = "id",
     *          required=true,
     *          @OA\Schema(
     *                 type="string",
     *         )
     *     ),
     *     @OA\Response(response="200", description="OK"),
     *     @OA\Response(response="201", description="Created"),
     *     @OA\Response(response="401", description="Unauthorized"),
     *     @OA\Response(response="403", description="Forbidden"),
     *     @OA\Response(response="404", description="Not Found"), 
     *     @OA\Response(response="422", description="Unprocessable Entity"), 
     *     @OA\Response(response="500", description="Internal Server Error"), 
     * )
     */
    public function delete($id){
        $article = Article::find($id);

        if (!$article){
            return response()->json([
                'success' => false,
                'message' => 'Data Not Found!',
            ], 404);
        } else if ($article->file) {
            @unlink(storage_path('app/public/images/articles/'.$article->file));
            $article->delete();
        }else{
            $article->delete();
        }
        
        if ($article) {
            return response()->json([
                'success' => true,
                'message' => 'Data Deleted!',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Delete Failed!',
            ], 500);
        }
    }

}