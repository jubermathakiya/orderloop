<?php

namespace App\Http\Controllers;

use App\Events\BookBorrowed;
use App\Events\BookReturned;
use App\Exceptions\ApiException;
use App\Models\Book;
use App\Models\Borrow;
use App\Repositories\BookRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class BorrowingController extends Controller
{
    protected $bookRepository;

    public function __construct(BookRepositoryInterface $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * @OA\Post(
     *     path="/api/borrow/{book}",
     *     summary="Borrow a book",
     *     tags={"Books"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="book",
     *         in="path",
     *         required=true,
     *         description="ID of the book to borrow",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book borrowed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Book borrowed successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Book is already borrowed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Book is already borrowed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Book borrow failed"
     *     )
     * )
     */
    public function borrow($book_id)
    {
        try {
            $book = Book::findOrFail($book_id);
            if (!$book->isAvailable()) {
                return response()->json(['status' => true, 'message' => 'Book is already borrowed'], 400);
            }

            $user = auth()->user();
            
            $this->bookRepository->borrowBook([ 
                'user_id' => $user->id,
                'book_id' => $book->id,
                'borrowed_at' => now(),
            ]);
            $book->markAsBorrowed();
            Cache::forget('books');
            event(new BookBorrowed($user, $book));

            return response()->json(['status'=>true, 'message' => 'Book borrowed successfully']);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new ApiException("Book borrow failed", 500);
        }
    }

        /**
     * @OA\Post(
     *     path="/api/return/{book}",
     *     summary="Return a borrowed book",
     *     tags={"Books"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(
     *         name="book",
     *         in="path",
     *         required=true,
     *         description="ID of the book to return",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Book returned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Book returned successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No borrowed record found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No borrowed record found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Book return failed"
     *     )
     * )
     */
    public function returnBook($book_id)
    {
        try {
            $book = Book::findOrFail($book_id);
            $user = auth()->user();
            $record = $this->bookRepository->getBorrowByUserId($user->id, $book->id);

            if (!$record) {
                return response()->json(['message' => 'No borrowed record found'], 400);
            }
            $record->update(['returned_at' => now()]);
            $book->markAsAvailable();
            Cache::forget('books');
            event(new BookReturned($book, $user));

            return response()->json(['status'=>true, 'message' => 'Book returned successfully']);
        } catch (Exception $e) {
            Log::info($e->getMessage());
            throw new ApiException("Book return failed", 500);
        }
    }
}
