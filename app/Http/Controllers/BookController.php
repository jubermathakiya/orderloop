<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Repositories\BookRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{

    protected $bookRepository;

    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

      /**
     * @OA\Get(
     *     path="/api/books",
     *     summary="Get all books",
     *     tags={"Books"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of books",
     *          @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Server error")
     *         )
     *     )
     * 
     * )
     */
    public function index()
    {
        $books = $this->bookRepository->getAllBooks();
        $data = Cache::remember('books', 60, fn() => $books);
        
        return response()->json([
            "status" => true,
            "data" => $data
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/books",
     *     summary="Create a new book",
     *     tags={"Books"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "author"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="author", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Book created"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'author' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            
            $book = $this->bookRepository->createBook($request->all());
            $book->refresh();

            Cache::forget('books');
            return response()->json([
                "status" => true,
                "data" => new BookResource($book)
            ], 201);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new ApiException("Book create failed", 500);
        }
    }

     /**
     * @OA\Get(
     *     path="/api/books/{id}",
     *     summary="Get book details",
     *     tags={"Books"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book details",
     *     )
     * )
     */
    public function show($book_id)
    {
        try {
            $book = Book::findOrFail($book_id);
            return response()->json([
                "status" => true,
                "data" => new BookResource($book)
            ], 201);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new ApiException("Book show failed", 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/books/{id}",
     *     summary="Update book details",
      *     tags={"Books"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "author"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="author", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book updated",
     *     )
     * )
     */
    public function update(Request $request,$book_id)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'author' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $book = Book::findOrFail($book_id);
            $this->bookRepository->updateBook($book, $request->all());
            Cache::forget('books');
            return response()->json([
                "status" => true,
                "data" => new BookResource($book)
            ], 201);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new ApiException("Book update failed", 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/books/{id}",
     *     summary="Delete a book",
     *     tags={"Books"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book deleted"
     *     )
     * )
     */
    public function destroy(Book $book)
    {
        try {
            $book->delete();
            Cache::forget('books');
            return response()->json(["status" => true,'message' => 'Book deleted']);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new ApiException("Book delete failed", 500);
        }
    }

}
